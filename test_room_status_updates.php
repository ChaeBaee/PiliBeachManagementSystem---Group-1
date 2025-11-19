<?php
require_once 'PHP/db.php';

echo "<h2>Testing Room Status Updates</h2>";

// Test the BookingManager class
require_once 'Admin/Bookings/back/bookings_backend.php';

$bookingManager = new BookingManager($conn);

// Get current room statuses
echo "<h3>Current Room Statuses:</h3>";
$stmt = $conn->prepare("SELECT room_id, room_number, room_type, room_status FROM rooms ORDER BY room_number");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Room ID</th><th>Room Number</th><th>Room Type</th><th>Current Status</th></tr>";
foreach ($rooms as $room) {
    echo "<tr>";
    echo "<td>{$room['room_id']}</td>";
    echo "<td>{$room['room_number']}</td>";
    echo "<td>{$room['room_type']}</td>";
    echo "<td>{$room['room_status']}</td>";
    echo "</tr>";
}
echo "</table>";

// Get current bookings
echo "<h3>Current Bookings:</h3>";
$stmt = $conn->prepare("SELECT b.booking_id, b.room_id, r.room_number, b.booking_status, b.booking_check_in, b.booking_check_out 
                       FROM bookings b 
                       JOIN rooms r ON b.room_id = r.room_id 
                       ORDER BY b.booking_id");
$stmt->execute();
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Booking ID</th><th>Room ID</th><th>Room Number</th><th>Status</th><th>Check In</th><th>Check Out</th></tr>";
foreach ($bookings as $booking) {
    echo "<tr>";
    echo "<td>{$booking['booking_id']}</td>";
    echo "<td>{$booking['room_id']}</td>";
    echo "<td>{$booking['room_number']}</td>";
    echo "<td>{$booking['booking_status']}</td>";
    echo "<td>{$booking['booking_check_in']}</td>";
    echo "<td>{$booking['booking_check_out']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test room status update function
echo "<h3>Testing Room Status Update Function:</h3>";
if (!empty($rooms)) {
    $testRoom = $rooms[0];
    echo "<p>Testing room status update for Room {$testRoom['room_number']} (ID: {$testRoom['room_id']})</p>";
    
    // Test different statuses
    $testStatuses = ['Pending', 'Confirmed', 'Checked-in', 'Canceled', 'Checked-out'];
    
    foreach ($testStatuses as $status) {
        echo "<p>Testing status: <strong>$status</strong></p>";
        
        // Get current room status
        $stmt = $conn->prepare("SELECT room_status FROM rooms WHERE room_id = ?");
        $stmt->execute([$testRoom['room_id']]);
        $currentStatus = $stmt->fetchColumn();
        echo "<p>Current room status: $currentStatus</p>";
        
        // Test the update function
        $result = $bookingManager->updateRoomStatus($testRoom['room_id'], $status);
        
        // Get new room status
        $stmt = $conn->prepare("SELECT room_status FROM rooms WHERE room_id = ?");
        $stmt->execute([$testRoom['room_id']]);
        $newStatus = $stmt->fetchColumn();
        echo "<p>New room status: $newStatus (Update result: " . ($result ? 'Success' : 'Failed') . ")</p>";
        
        echo "<hr>";
    }
}

echo "<h3>Test Complete</h3>";
echo "<p><a href='Admin/Bookings/front/bookings.php'>Go to Admin Bookings</a></p>";
echo "<p><a href='Admin/Room/front/rooms.php'>Go to Admin Rooms</a></p>";
?> 