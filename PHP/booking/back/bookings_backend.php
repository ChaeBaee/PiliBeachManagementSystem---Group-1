<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/front/login.php");
    exit();
}

// Get room types for dropdown
$stmt = $conn->query("SELECT DISTINCT room_type FROM rooms");
$room_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Function to fetch available rooms
function getAvailableRooms($check_in, $check_out, $guests = 1, $room_type = '')
{
    global $conn;

    $query = "SELECT r.* FROM rooms r 
              WHERE r.room_status IN ('available', 'Booked')
              AND NOT EXISTS (
                  SELECT 1 FROM bookings b 
                  WHERE b.room_id = r.room_id 
                  AND (b.booking_status != 'Canceled' OR b.booking_status != 'Checked-out')
                  AND (
                      (b.booking_check_in <= :check_out AND b.booking_check_out >= :check_in)
                  )
              )";

    if (!empty($room_type)) {
        $query .= " AND r.room_type = :room_type";
    }
    if (!empty($guests)) {
        $query .= " AND r.room_capacity >= :guests";
    }

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':check_in', $check_in);
    $stmt->bindParam(':check_out', $check_out);
    if (!empty($room_type)) {
        $stmt->bindParam(':room_type', $room_type);
    }
    if (!empty($guests)) {
        $stmt->bindParam(':guests', $guests, PDO::PARAM_INT);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
