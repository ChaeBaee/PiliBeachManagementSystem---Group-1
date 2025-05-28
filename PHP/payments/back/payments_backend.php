<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login/front/login.php");
    exit();
}


if (!isset($_GET['booking_id']) || !isset($_SESSION['booking_flow']) || $_GET['booking_id'] != $_SESSION['booking_flow']['booking_id']) {
    header("Location: /Proj/PHP/booking/front/bookings.php");
    exit();
}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

$booking_id = $_GET['booking_id'];

$stmt = $conn->prepare("
    SELECT b.*, r.room_type, r.room_number, r.room_price, u.user_name, u.user_email, u.user_phone
    FROM bookings b
    JOIN rooms r ON b.room_id = r.room_id
    JOIN users u ON b.user_id = u.user_id
    WHERE b.booking_id = ? AND b.user_id = ? AND b.booking_status = 'Pending'
");
$stmt->execute([$booking_id, $_SESSION['user_id']]);
$booking = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$booking) {
    header("Location: /Proj/PHP/booking/front/bookings.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $conn->prepare("SELECT payment_id FROM payments WHERE booking_id = ?");
        $stmt->execute([$booking_id]);
        if ($stmt->fetch()) {
            throw new Exception("Payment already exists for this booking.");
        }

        $conn->beginTransaction();

        $payment_proof = '';
        if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/payment_proofs/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $file_extension = strtolower(pathinfo($_FILES['payment_proof']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'pdf'];

            if (!in_array($file_extension, $allowed_extensions)) {
                throw new Exception('Invalid file type. Please upload JPG, PNG, or PDF files only.');
            }

            $file_name = uniqid('payment_') . 'backend' . $file_extension;
            $target_path = $upload_dir . $file_name;

            if (move_uploaded_file($_FILES['payment_proof']['tmp_name'], $target_path)) {
                $payment_proof = $file_name;
            } else {
                throw new Exception('Failed to upload payment proof.');
            }
        }

        $stmt = $conn->prepare("
            INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status, payment_proof)
            VALUES (?, ?, CURDATE(), 'pending', ?)
        ");
        $stmt->execute([$booking_id, $booking['booking_total_price'], $payment_proof]);
        $payment_id = $conn->lastInsertId();

        $stmt = $conn->prepare("
            INSERT INTO invoice (invoice_total_amount, invoice_status, invoice_date_created, payment_id)
            VALUES (?, 'unpaid', CURDATE(), ?)
        ");
        $stmt->execute([$booking['booking_total_price'], $payment_id]);

        $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Confirmed' WHERE booking_id = ?");
        $stmt->execute([$booking_id]);

        $conn->commit();

        unset($_SESSION['booking_flow']);
        unset($_SESSION['booking_token']);

        header("Location: /Proj/PHP/invoice/Front/invoice.php?payment_id=" . $payment_id);
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}

if (isset($_GET['cancel'])) {
    try {
        $conn->beginTransaction();

        $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Canceled' WHERE booking_id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $_SESSION['user_id']]);

        $stmt = $conn->prepare("UPDATE rooms SET room_status = 'available' WHERE room_id = ?");
        $stmt->execute([$booking['room_id']]);

        $conn->commit();

        unset($_SESSION['booking_flow']);
        unset($_SESSION['booking_token']);

        header("Location: ../PHP/booking/front/bookings.php");
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
    }
}
?>