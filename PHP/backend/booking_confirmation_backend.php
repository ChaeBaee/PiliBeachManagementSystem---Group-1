<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login/front/login.php");
    exit();
}
//if (!isset($_SESSION['user_id'])) {
//    header("Location: ../login.php");
//    exit();
//}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

if (!isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    header("Location: ../bookings.php");
    exit();
}

if (!isset($_SESSION['booking_token'])) {
    $_SESSION['booking_token'] = bin2hex(random_bytes(32));
}

$room_id = $_GET['room_id'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];

$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ? AND room_status = 'available'");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    header("Location: ../bookings.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
$nights = $check_in_date->diff($check_out_date)->days;
$total_price = $room['room_price'] * $nights;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['booking_token']) || $_POST['booking_token'] !== $_SESSION['booking_token']) {
            throw new Exception("Invalid booking session. Please start over.");
        }

        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT room_status FROM rooms WHERE room_id = ? FOR UPDATE");
        $stmt->execute([$room_id]);
        $room_status = $stmt->fetchColumn();

        if ($room_status !== 'available') {
            throw new Exception("Room is no longer available. Please select another room.");
        }

        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, booking_check_in, booking_check_out, booking_total_price, booking_status) VALUES (?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out, $total_price]);
        $booking_id = $conn->lastInsertId();

        $stmt = $conn->prepare("UPDATE rooms SET room_status = 'booked' WHERE room_id = ?");
        $stmt->execute([$room_id]);

        $_SESSION['booking_flow'] = [
            'booking_id' => $booking_id,
            'room_id' => $room_id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'total_price' => $total_price
        ];

        $conn->commit();
        header("Location: ../PHP/payments.php?booking_id=" . $booking_id);
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>