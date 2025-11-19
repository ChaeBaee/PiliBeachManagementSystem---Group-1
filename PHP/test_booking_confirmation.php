<?php
require_once 'db.php';

// Test parameters
$room_id = $_GET['room_id'] ?? 1;
$check_in = $_GET['check_in'] ?? date('Y-m-d', strtotime('+1 day'));
$check_out = $_GET['check_out'] ?? date('Y-m-d', strtotime('+2 days'));
$user_id = $_GET['user_id'] ?? 1;

echo "<h2>Booking Confirmation Debug Test</h2>";
echo "<p><strong>Test Parameters:</strong></p>";
echo "<ul>";
echo "<li>Room ID: $room_id</li>";
echo "<li>Check In: $check_in</li>";
echo "<li>Check Out: $check_out</li>";
echo "<li>User ID: $user_id</li>";
echo "</ul>";

// Test 1: Check room status
echo "<h3>1. Room Status Check</h3>";
$stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if ($room) {
    echo "<p>Room found: " . $room['room_type'] . " - " . $room['room_number'] . " - Status: " . $room['room_status'] . "</p>";
} else {
    echo "<p>Room not found!</p>";
    exit;
}

// Test 2: Check existing bookings for this room
echo "<h3>2. Existing Bookings for Room $room_id</h3>";
$stmt = $conn->prepare("SELECT * FROM bookings WHERE room_id = ? ORDER BY booking_check_in");
$stmt->execute([$room_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($bookings)) {
    echo "<p>No existing bookings for this room.</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>Booking ID</th><th>User ID</th><th>Check In</th><th>Check Out</th><th>Status</th></tr>";
    foreach ($bookings as $booking) {
        echo "<tr>";
        echo "<td>" . $booking['booking_id'] . "</td>";
        echo "<td>" . $booking['user_id'] . "</td>";
        echo "<td>" . $booking['booking_check_in'] . "</td>";
        echo "<td>" . $booking['booking_check_out'] . "</td>";
        echo "<td>" . $booking['booking_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 3: Check for conflicting bookings using the same logic as the booking confirmation
echo "<h3>3. Conflicting Bookings Check</h3>";
$query = "SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time, user_id, booking_status
         FROM bookings 
         WHERE room_id = :room_id 
         AND booking_status NOT IN ('Canceled', 'Checked-out')
         AND user_id != :current_user_id
         AND (
             (booking_check_in < :check_out AND booking_check_out > :check_in)
         )
         ORDER BY booking_check_in";

$stmt = $conn->prepare($query);
$stmt->bindParam(':room_id', $room_id);
$stmt->bindParam(':check_in', $check_in);
$stmt->bindParam(':check_out', $check_out);
$stmt->bindParam(':current_user_id', $user_id);
$stmt->execute();
$conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<p>Query: " . $query . "</p>";
echo "<p>Parameters: room_id=$room_id, check_in=$check_in, check_out=$check_out, user_id=$user_id</p>";

if (empty($conflicting_bookings)) {
    echo "<p><strong>No conflicting bookings found!</strong></p>";
} else {
    echo "<p><strong>Found " . count($conflicting_bookings) . " conflicting bookings:</strong></p>";
    echo "<table border='1'>";
    echo "<tr><th>User ID</th><th>Check In</th><th>Check Out</th><th>Check-in Time</th><th>Check-out Time</th><th>Status</th></tr>";
    foreach ($conflicting_bookings as $booking) {
        echo "<tr>";
        echo "<td>" . $booking['user_id'] . "</td>";
        echo "<td>" . $booking['booking_check_in'] . "</td>";
        echo "<td>" . $booking['booking_check_out'] . "</td>";
        echo "<td>" . $booking['booking_checkin_time'] . "</td>";
        echo "<td>" . $booking['booking_checkout_time'] . "</td>";
        echo "<td>" . $booking['booking_status'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

// Test 4: Check for existing pending bookings by the same user
echo "<h3>4. Existing Pending Bookings by User $user_id</h3>";
$stmt = $conn->prepare("SELECT booking_id, booking_status FROM bookings 
                       WHERE user_id = ? AND room_id = ? 
                       AND booking_check_in = ? AND booking_check_out = ?
                       AND booking_status = 'Pending'");
$stmt->execute([$user_id, $room_id, $check_in, $check_out]);
$existing_pending = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_pending) {
    echo "<p><strong>Found existing pending booking:</strong></p>";
    echo "<pre>" . print_r($existing_pending, true) . "</pre>";
} else {
    echo "<p>No existing pending booking found for this user and dates.</p>";
}

echo "<h3>5. Test URL</h3>";
echo "<p>To test the booking confirmation page, use this URL:</p>";
echo "<a href='booking/front/booking-confirmation.php?room_id=$room_id&check_in=$check_in&check_out=$check_out&checkin_time=14:00'>";
echo "booking-confirmation.php?room_id=$room_id&check_in=$check_in&check_out=$check_out&checkin_time=14:00";
echo "</a>";
?> 