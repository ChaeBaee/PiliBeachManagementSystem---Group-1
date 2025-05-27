<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login/front/login.php");
    exit();
}
//if (!isset($_SESSION['user_id'])) {
//    header("Location: ../login.php");
//    exit();
//}

if (isset($_GET['room_type']) || isset($_GET['check_in']) || isset($_GET['check_out'])) {
    unset($_SESSION['booking_flow']);
    unset($_SESSION['booking_token']);
}

$stmt = $conn->query("SELECT DISTINCT room_type FROM rooms WHERE room_status = 'available'");
$room_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

if (isset($_GET['check_in']) && isset($_GET['check_out'])) {
    $room_type = $_GET['room_type'] ?? '';
    $check_in = $_GET['check_in'];
    $check_out = $_GET['check_out'];
    $guests = $_GET['guests'] ?? 1;

    $check_in_date = new DateTime($check_in);
    $check_out_date = new DateTime($check_out);
    $today = new DateTime();
    $today->setTime(0, 0, 0);

    if ($check_in_date < $today) {
        $error = "Check-in date cannot be in the past.";
    } elseif ($check_in_date >= $check_out_date) {
        $error = "Check-out date must be after check-in date.";
    } else {
        $query = "SELECT r.* FROM rooms r 
                 WHERE r.room_status IN ('available', 'Booked')
                 AND NOT EXISTS (
                     SELECT 1 FROM bookings b 
                     WHERE b.room_id = r.room_id 
                     AND b.booking_status != 'Canceled'
                     AND (
                         (b.booking_check_in <= :check_out AND b.booking_check_out >= :check_in)
                     )
                 )";
        if ($room_type) {
            $query .= " AND r.room_type = :room_type";
        }
        if ($guests) {
            $query .= " AND r.room_capacity >= :guests";
        }

        $stmt = $conn->prepare($query);
        $stmt->bindParam(':check_in', $check_in);
        $stmt->bindParam(':check_out', $check_out);
        if ($room_type) {
            $stmt->bindParam(':room_type', $room_type);
        }
        if ($guests) {
            $stmt->bindParam(':guests', $guests);
        }
        $stmt->execute();
        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>