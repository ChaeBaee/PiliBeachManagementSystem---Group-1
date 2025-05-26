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
        $checkInMsg = "Please enter check-in date.<br>";
        $hasError = true;
    } elseif ($checkIn < $today) {
        $checkInMsg = "Check-in date cannot be in the past.<br>";
        $hasError = true;
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please enter check-out date.<br>";
        $hasError = true;
    } elseif ($checkOut < $today) {
        $checkOutMsg = "Check-out date cannot be in the past.<br>";
        $hasError = true;
    }

    if (empty($guest) || intval($guest) < 1) {
        $guestMsg = "Guests must be at least 1.<br>";
        $hasError = true;
    }

    if (!$hasError) {
        $_SESSION['checkIn'] = $checkIn;
        $_SESSION['checkOut'] = $checkOut;
        $_SESSION['guest'] = $guest;

        try {
            $sql = "
                SELECT * FROM rooms 
                WHERE room_status = 'available'
                AND room_capacity >= :guests
            ";

            $params = [
                ':guests' => $guest
            ];

            if (!empty($roomType)) {
                $sql .= " AND room_type = :room_type";
                $params[':room_type'] = $roomType;
            }

            if ($checkIn && $checkOut) {
                $sql .= " AND room_id NOT IN (
                    SELECT room_id FROM bookings 
                    WHERE NOT (
                        booking_check_out <= :checkin OR 
                        booking_check_in >= :checkout
                    )
                )";

                $params[':checkin'] = $checkIn;
                $params[':checkout'] = $checkOut;
            }

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
            echo "Database query error: " . $e->getMessage();
        }
    }
}
?>