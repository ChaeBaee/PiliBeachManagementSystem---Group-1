<?php
session_start();
require_once '../../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {

        switch ($_POST['action']) {
            case 'add':
                try {
                    // Get booking details including total amount
                    $stmt = $conn->prepare("SELECT b.*, u.user_name, r.room_number, r.room_price,
                                          (DATEDIFF(b.booking_check_out, b.booking_check_in) * r.room_price) as total_amount
                                          FROM bookings b 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE b.booking_id = ?");
                    $stmt->execute([$_POST['booking_id']]);
                    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                    if (!$booking) {
                        throw new Exception("Booking not found");
                    }

                    $conn->exec("SET @current_user_id = $currentUserId");

                    // Insert payment with current date and calculated total amount
                    $stmt = $conn->prepare("INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status) VALUES (?, ?, CURDATE(), ?)");
                    $stmt->execute([
                        $_POST['booking_id'],
                        $booking['total_amount'], // Use calculated total amount
                        $_POST['payment_status']
                    ]);
                    $_SESSION['success'] = "Payment added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding payment: " . $e->getMessage();
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {

                    $stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $currentPayment = $stmt->fetch(PDO::FETCH_ASSOC);

                    $conn->exec("SET @current_user_id = $currentUserId");

                    $conn->beginTransaction();

                    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_status'], $_POST['payment_id']]);

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
                    $stmt = $conn->prepare("SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
                                          FROM payments p 
                                          JOIN bookings b ON p.booking_id = b.booking_id 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE p.payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $paymentToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

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

$stmt = $conn->query("
    SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number, r.room_price,
           DATEDIFF(b.booking_check_out, b.booking_check_in) as nights
    FROM payments p 
    JOIN bookings b ON p.booking_id = b.booking_id 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("
    SELECT b.booking_id, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number, r.room_price,
           (DATEDIFF(b.booking_check_out, b.booking_check_in) * r.room_price) as total_amount,
           DATEDIFF(b.booking_check_out, b.booking_check_in) as nights
    FROM bookings b 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    WHERE b.booking_id NOT IN (SELECT booking_id FROM payments)
    AND b.booking_status = 'confirmed'
    ORDER BY b.booking_check_in DESC
");
$availableBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);