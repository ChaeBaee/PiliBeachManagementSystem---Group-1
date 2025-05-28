<?php
session_start();
require_once '../../PHP/db.php';


$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $invoiceId = $_POST['invoice_id'];
        $invoiceStatus = $_POST['invoice_status'];


        $validStatuses = ['paid', 'unpaid', 'canceled'];
        if (!in_array($invoiceStatus, $validStatuses)) {
            throw new Exception("Invalid invoice status");
        }


        $conn->beginTransaction();


        $stmt = $conn->prepare("UPDATE invoice SET invoice_status = ? WHERE invoice_id = ?");
        $stmt->execute([$invoiceStatus, $invoiceId]);


        if ($invoiceStatus === 'paid') {
            $stmt = $conn->prepare("
                UPDATE payments p 
                JOIN invoice i ON p.payment_id = i.payment_id 
                SET p.payment_status = 'paid' 
                WHERE i.invoice_id = ?
            ");
            $stmt->execute([$invoiceId]);
        }


        $conn->commit();

        echo json_encode(['success' => true, 'message' => 'Invoice status updated successfully']);

    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}