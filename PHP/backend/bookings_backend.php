<?php
require_once 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

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
        $query = "SELECT * FROM rooms WHERE room_status = 'available'";
        if ($room_type) {
            $query .= " AND room_type = :room_type";
        }
        if ($guests) {
            $query .= " AND room_capacity >= :guests";
        }

        $stmt = $conn->prepare($query);
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