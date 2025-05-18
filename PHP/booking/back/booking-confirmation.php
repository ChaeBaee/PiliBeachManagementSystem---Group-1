<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: /Proj/PHP/booking/front/3login.php");
    exit();
}



$_SESSION['booking'] = [
    'room_type' => $_POST['room_type'] ?? 'N/A',
    'checkin' => $_POST['checkin'] ?? 'N/A',
    'checkout' => $_POST['checkout'] ?? 'N/A',
    'guests' => $_POST['guests'] ?? 'N/A'
];


header("Location: /Proj/PHP/booking/front/booking-confirmation.php");
exit();