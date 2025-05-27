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

if (!isset($_GET['payment_id'])) {
    header("Location: ../bookings.php");
    exit();
}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

$payment_id = $_GET['payment_id'];

$stmt = $conn->prepare("
    SELECT i.*, p.payment_date, p.payment_status, p.payment_proof,
           b.booking_id, b.booking_check_in, b.booking_check_out, b.booking_status,
           r.room_type, r.room_number, r.room_price,
           u.user_name, u.user_email, u.user_phone
    FROM invoice i
    JOIN payments p ON i.payment_id = p.payment_id
    JOIN bookings b ON p.booking_id = b.booking_id
    JOIN rooms r ON b.room_id = r.room_id
    JOIN users u ON b.user_id = u.user_id
    WHERE i.payment_id = ? AND b.user_id = ?
");
$stmt->execute([$payment_id, $_SESSION['user_id']]);
$invoice = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$invoice) {
    header("Location: ../bookings.php");
    exit();
}

$check_in = new DateTime($invoice['booking_check_in']);
$check_out = new DateTime($invoice['booking_check_out']);
$nights = $check_in->diff($check_out)->days;
?>