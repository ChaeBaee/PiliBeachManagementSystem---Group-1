<?php
session_start();
require_once '../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $invoiceId = $_POST['invoice_id'];

        $conn->beginTransaction();

        $stmt = $conn->prepare("SELECT payment_id FROM invoice WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        $invoice = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$invoice) {
            throw new Exception("Invoice not found");
        }

        $stmt = $conn->prepare("DELETE FROM invoice WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);

        $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
        $stmt->execute([$invoice['payment_id']]);

        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Invoice deleted successfully']);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}