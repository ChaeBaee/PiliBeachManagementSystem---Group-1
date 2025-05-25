<?php
session_start();
require_once '../../PHP/db.php';

// Set current user ID for audit triggers
$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $invoiceId = $_POST['invoice_id'];

        // Start transaction
        $conn->beginTransaction();

        // Get payment ID before deleting invoice
        $stmt = $conn->prepare("SELECT payment_id FROM invoice WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new Exception("Invoice not found");
        }

        // Delete invoice
        $stmt = $conn->prepare("DELETE FROM invoice WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);

        // Delete associated payment
        $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $stmt->execute([$invoice['payment_id']]);

        // Commit transaction
        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Invoice deleted successfully']);

    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}