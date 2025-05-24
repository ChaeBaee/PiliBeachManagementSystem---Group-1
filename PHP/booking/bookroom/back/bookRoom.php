<?php
session_start();
require_once '../../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/front/login.php");
    exit();
}

if (!isset($_POST['room_id'])) {
    header("Location: rooms.php");
    exit();
}

$room_id = $_POST['room_id'];

try {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->execute([$room_id]);
    $room = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$room) {
        echo "Room not found.";
        exit();
    }
} catch (PDOException $e) {
    echo "DB error: " . $e->getMessage();
    exit();
}

$checkIn = $checkOut = $guest = "";
$checkInMsg = $checkOutMsg = $guestMsg = "";
$today = date('Y-m-d');
$hasError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['checkin'], $_POST['checkout'], $_POST['guests'])) {
    $checkIn = $_POST["checkin"];
    $checkOut = $_POST["checkout"];
    $guest  = $_POST["guests"];

    if (empty($checkIn)) {
        $checkInMsg = "Please enter check-in date.";
        $hasError = true;
    } elseif ($checkIn < $today) {
        $checkInMsg = "Check-in date cannot be in the past.";
        $hasError = true;
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please enter check-out date.";
        $hasError = true;
    } elseif ($checkOut <= $checkIn) {
        $checkOutMsg = "Check-out date must be after check-in date.";
        $hasError = true;
    }

    if (empty($guest) || intval($guest) < 1) {
        $guestMsg = "Guests must be at least 1.";
        $hasError = true;
    } elseif ($guest > $room['room_capacity']) {
        $guestMsg = "Guests exceed room capacity.";
        $hasError = true;
    }

    if (!$hasError) {
        $_SESSION['booking_data'] = [
            'room_id' => $room_id,
            'room_type' => $room['room_type'],
            'checkin' => $checkIn,
            'checkout' => $checkOut,
            'guests' => $guest
        ];

        header("Location: /Proj/PHP/booking/front/booking-confirmation.php");
        exit();
    }
}
?>