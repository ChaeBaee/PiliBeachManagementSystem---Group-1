<?php
session_start();
require_once '../PHP/db.php';

// Set current user ID for audit logging
$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Set current user ID for audit logging
        $currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $conn->exec("SET @current_user_id = $currentUserId");

        switch ($_POST['action']) {
            case 'add':
                try {
                    // Get booking details for audit log
                    $stmt = $conn->prepare("SELECT b.*, u.user_name, r.room_number FROM bookings b 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE b.booking_id = ?");
                    $stmt->execute([$_POST['booking_id']]);
                    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before insert
                    $conn->exec("SET @current_user_id = $currentUserId");

                    $stmt = $conn->prepare("INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['booking_id'],
                        $_POST['payment_amount'],
                        $_POST['payment_date'],
                        $_POST['payment_status']
                    ]);
                    $_SESSION['success'] = "Payment added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding payment: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    // Get current payment data for audit log
                    $stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $currentPayment = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before update
                    $conn->exec("SET @current_user_id = $currentUserId");

                    // Start transaction
                    $conn->beginTransaction();

                    // Update payment status
                    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_status'], $_POST['payment_id']]);

                    // If payment status is changed to paid, update corresponding invoice status
                    if ($_POST['payment_status'] === 'paid') {
                        $stmt = $conn->prepare("UPDATE invoice SET invoice_status = 'paid' WHERE payment_id = ?");
                        $stmt->execute([$_POST['payment_id']]);
                    }

                    $conn->commit();
                    $_SESSION['success'] = "Payment updated successfully!";
                } catch (PDOException $e) {
                    $conn->rollBack();
                    $_SESSION['error'] = "Error updating payment: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    // Get payment data for audit log before deleting
                    $stmt = $conn->prepare("SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
                                          FROM payments p 
                                          JOIN bookings b ON p.booking_id = b.booking_id 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE p.payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $paymentToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before delete
                    $conn->exec("SET @current_user_id = $currentUserId");

                    $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $_SESSION['success'] = "Payment deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting payment: " . $e->getMessage();
                }
                break;
        }
        header("Location: payments.php");
        exit();
    }
}

// Fetch all payments with booking details
$stmt = $conn->query("
    SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
    FROM payments p 
    JOIN bookings b ON p.booking_id = b.booking_id 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available bookings for new payment
$stmt = $conn->query("
    SELECT b.booking_id, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
    FROM bookings b 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    WHERE b.booking_id NOT IN (SELECT booking_id FROM payments)
");
$availableBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);