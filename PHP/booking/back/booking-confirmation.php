<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/front/login.php");
    exit();
}

if (!isset($_SESSION['booking_data'])) {
    header("Location: ../rooms.php");
    exit();
}

$booking = $_SESSION['booking_data'];

// Fetch user id from session
$user_id = $_SESSION['user_id'];

try {
    $stmt = $conn->prepare("
        INSERT INTO bookings (user_id, room_id, booking_check_in, booking_check_out, guests)
        VALUES (:user_id, :room_id, :checkin, :checkout, :guests)
    ");

    $stmt->execute([
        ':user_id' => $user_id,
        ':room_id' => $booking['room_id'],
        ':checkin' => $booking['checkin'],
        ':checkout' => $booking['checkout'],
    ]);

    // Clear booking data after successful insert
    unset($_SESSION['booking_data']);

    // Redirect or display confirmation
    header("Location: ../booking/front/booking-success.php");
    exit();

} catch (PDOException $e) {
    echo "Booking error: " . $e->getMessage();
    exit();
}
?>
