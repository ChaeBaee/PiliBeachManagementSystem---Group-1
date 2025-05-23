<?php

require_once(__DIR__ . '/../../../db.php');

$checkIn = $_SESSION['checkIn'] ?? null;
$checkOut = $_SESSION['checkOut'] ?? null;
$guest = $_SESSION['guest'] ?? null;
$roomType = $_POST['room_type'] ?? "";

try {
    $sql = "
        SELECT * FROM rooms 
        WHERE room_status = 'available'
        AND room_capacity >= :guests
    ";

    $params = [
        ':guests' => $guest
    ];

    // Optional room type filter
    if (!empty($roomType)) {
        $sql .= " AND room_type LIKE :room_type";
        $params[':room_type'] = '%' . $roomType . '%';
    }

    // Booking overlap filter (requires bookings table)
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

    $available_rooms = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $available_rooms[] = [
            'type' => ucwords($row['room_type']),
            'description' => $row['room_description'] ?? 'No description provided.',
            'price' => $row['room_price'],
            'status' => ucfirst($row['room_status']),
            'image' => $row['room_image'] ? 'path_to_images/' . $row['room_image'] : 'https://via.placeholder.com/150'
        ];
    }

} catch (PDOException $e) {
    echo "Database query error: " . $e->getMessage();
}
?>