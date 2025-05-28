<?php
session_start();
require_once '../../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {

        $bookingId = $_POST['booking_id'];

        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT booking_total_price FROM bookings WHERE booking_id = ?");
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$booking) {
            throw new Exception("Booking not found");
        }

        $stmt = $conn->prepare("
            INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status, payment_proof) 
            VALUES (?, ?, CURDATE(), 'pending', '')
        ");
        $stmt->execute([$bookingId, $booking['booking_total_price']]);
        $paymentId = $conn->lastInsertId();

        $stmt = $conn->prepare("
            INSERT INTO invoice (invoice_total_amount, invoice_status, invoice_date_created, payment_id) 
            VALUES (?, 'unpaid', CURDATE(), ?)
        ");
        $stmt->execute([$booking['booking_total_price'], $paymentId]);

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Invoice created successfully']);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}