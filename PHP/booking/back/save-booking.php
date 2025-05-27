<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $_SESSION['user_name'] = $_POST['full_name'];
    $_SESSION['user_email'] = $_POST['email'];


    $_SESSION['booking'] = [
        'room_type' => $_POST['room_type'],
        'checkin' => $_POST['checkin'],
        'checkout' => $_POST['checkout'],
        'guests' => $_POST['guests']
    ];

    header("Location: ../../../PHP/booking/front/booking-confirmation.php");
    exit();
} else {
    header("Location: ../../../PHP/booking/front/edit-booking.php");
    exit();
}