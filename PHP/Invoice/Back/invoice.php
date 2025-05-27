<?php
session_start();
include_once '../../db.php'; // Include the DB connection

//// Redirect if booking data is missing
if (!isset($_SESSION['booking']) || !isset($_SESSION['user_name']) || !isset($_SESSION['user_email'])) {
    header("Location: ../../PHP/home.php");
    exit();
}

$booking = $_SESSION['booking'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Prepare variables safely with fallbacks
$room_type = htmlspecialchars($booking['room_type'] ?? 'N/A');
$guests = htmlspecialchars($booking['guests'] ?? 'N/A');
$checkin = htmlspecialchars($booking['checkin'] ?? 'N/A');
$checkout = htmlspecialchars($booking['checkout'] ?? 'N/A');
$payment_method = htmlspecialchars($booking['payment_method'] ?? 'N/A');
$total_amount = floatval($booking['total_amount'] ?? 0); // Use as float for DB insert

// Calculate number of nights (optional, for display)
$checkin_date = new DateTime($checkin);
$checkout_date = new DateTime($checkout);
$interval = $checkin_date->diff($checkout_date);
$nights = $interval->days > 0 ? $interval->days : 1; // At least 1 night

// Invoice No & Date
$invoice_no = "INV-" . date("YmdHis");
$invoice_date = date("Y-m-d H:i:s"); // Use DB-compatible format
$invoice_status = "Paid"; // Default status


?>