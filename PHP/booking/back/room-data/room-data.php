<?php
session_start();
require_once '../db.php';

$checkIn = $checkOut = $guest = $roomType = "";
$checkInMsg = $checkOutMsg = $guestMsg = "";
$today = date('Y-m-d');
$hasError = false;
$available_rooms = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $checkIn = $_POST["checkin"] ?? "";
    $checkOut = $_POST["checkout"] ?? "";
    $guest = $_POST["guests"] ?? "";
    $roomType = $_POST["room_type"] ?? "";

    // Validation
    if (empty($checkIn)) {
        $checkInMsg = "Please enter check-in date.";
        $hasError = true;
    } elseif ($checkIn < $today) {
        $checkInMsg = "Check-in date cannot be in the past.";
        $hasError = true;
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please enter check-out date.";
        $hasError = true;
    } elseif ($checkOut <= $checkIn) {
        $checkOutMsg = "Check-out date must be after check-in date.";
        $hasError = true;
    }

    if (empty($guest) || intval($guest) < 1) {
        $guestMsg = "Guests must be at least 1.";
        $hasError = true;
    }

    if (!$hasError) {
        try {
            // Base query for available rooms
            $sql = "
                SELECT r.*, 
                       (SELECT COUNT(*) FROM bookings b 
                        WHERE b.room_id = r.room_id 
                        AND b.booking_status != 'Canceled'
                        AND NOT (
                            b.booking_check_out <= :checkin OR 
                            b.booking_check_in >= :checkout
                        )) as conflicting_bookings
                FROM rooms r
                WHERE r.room_status = 'available'
                AND r.room_capacity >= :guests
            ";

            $params = [
                ':guests' => $guest,
                ':checkin' => $checkIn,
                ':checkout' => $checkOut
            ];

            if (!empty($roomType)) {
                $sql .= " AND r.room_type = :room_type";
                $params[':room_type'] = $roomType;
            }

            $sql .= " HAVING conflicting_bookings = 0";

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $available_rooms[] = [
                    'room_id' => $row['room_id'],
                    'room_type' => ucwords($row['room_type']),
                    'room_description' => $row['room_description'] ?? 'No description provided.',
                    'room_price' => $row['room_price'],
                    'room_status' => ucfirst($row['room_status']),
                    'room_image' => $row['room_image'] ? '/Proj/PiliBeachManagementSystem---Group-1/PHP/assets/' . $row['room_image'] : 'https://via.placeholder.com/150'
                ];
            }

        } catch (PDOException $e) {
            $_SESSION['booking_error'] = "Error searching for available rooms: " . $e->getMessage();
            $hasError = true;
        }
    }
}
?>