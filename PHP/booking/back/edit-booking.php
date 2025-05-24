<?php
session_start();
include('../../elements/navbar.php');

$fullName = $checkIn = $checkOut = $guest = "";
$fullNameMsg = $checkInMsg = $checkOutMsg = $guestMsg = "";

// Check login and booking data
if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: /Proj/PHP/booking/front/2ndlogin.php");
    exit();
}

$booking = $_SESSION['booking_data'];
$roomType = $booking['room_type'] ?? '';
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST["fullName"] ?? "";
    $checkIn = $_POST["checkin"] ?? "";
    $checkOut = $_POST["checkout"] ?? "";
    $guest = $_POST["guests"] ?? "";

    if (empty($fullName)) {
        $fullNameMsg = "Full Name cannot be empty";
    }

    if (empty($checkIn)) {
        $checkInMsg = "Please select a check-in date.";
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please select a check-out date.";
    }

    if ($checkIn && $checkOut && $checkOut <= $checkIn) {
        $checkOutMsg = "Check-out date must be after check-in date.";
    }

    if (empty($guest)) {
        $guestMsg = "Please enter the number of guests.";
    }

    if ($fullNameMsg === "" && $checkInMsg === "" && $checkOutMsg === "" && $guestMsg === "") {
        $_SESSION['booking_data']['full_name'] = $fullName;
        $_SESSION['booking_data']['checkin'] = $checkIn;
        $_SESSION['booking_data']['checkout'] = $checkOut;
        $_SESSION['booking_data']['guests'] = $guest;
        $_SESSION['user_name'] = $fullName;

        header('Location: booking-confirmation.php');
        exit();
    }
}
?>
