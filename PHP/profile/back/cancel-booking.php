<?php
session_start();
require_once '../../db.php';


if (!isset($_SESSION['user_id']) || !isset($_POST['booking_id'])) {
    header("Location: ../profile.php");
    exit();
}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

$user_id = $_SESSION['user_id'];
$booking_id = $_POST['booking_id'];

$stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ? AND user_id = ?");
$stmt->execute([$booking_id, $user_id]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if ($booking) {
    $conn->beginTransaction();

    try {
        $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Canceled' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);

        $stmt = $conn->prepare("SELECT room_id FROM bookings WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $room_id = $stmt->fetchColumn();

        if ($room_id) {
            $stmt = $conn->prepare("UPDATE rooms SET room_status = 'available' WHERE room_id = ?");
            $stmt->execute([$room_id]);
        }

        $stmt = $conn->prepare("SELECT payment_id FROM payments WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        $paymentIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if ($paymentIds) {
            $inQuery = implode(',', array_fill(0, count($paymentIds), '?'));
            $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE payment_id IN ($inQuery)");
            $stmt->execute($paymentIds);

            $stmt = $conn->prepare("UPDATE invoice SET invoice_status = 'canceled' WHERE payment_id IN ($inQuery)");
            $stmt->execute($paymentIds);
        }

        $conn->commit();
        $_SESSION['message'] = "Booking, payment, invoice cancelled and room made available successfully.";

    } catch (Exception $e) {
        $conn->rollBack();
        $_SESSION['message'] = "Failed to cancel booking: " . $e->getMessage();
    }
} else {
    $_SESSION['message'] = "Invalid booking cancellation request.";
}

header("Location: ../profile.php");
exit();