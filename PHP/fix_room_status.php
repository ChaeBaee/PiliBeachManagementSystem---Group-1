<?php
require_once 'db.php';

echo "<h2>Room Status Check and Fix</h2>";

// Get all rooms
$stmt = $conn->prepare("SELECT room_id, room_number, room_type, room_status FROM rooms");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1'>";
echo "<tr><th>Room ID</th><th>Room Number</th><th>Room Type</th><th>Current Status</th><th>Active Bookings</th><th>Should Be</th><th>Action</th></tr>";

foreach ($rooms as $room) {
    $room_id = $room['room_id'];
    
    // Check for active bookings (not canceled or checked out)
    $stmt = $conn->prepare("SELECT COUNT(*) as active_bookings FROM bookings 
                           WHERE room_id = ? AND booking_status NOT IN ('Canceled', 'Checked-out')");
    $stmt->execute([$room_id]);
    $active_bookings = $stmt->fetchColumn();
    
    // Determine what the status should be
    $should_be = $active_bookings > 0 ? 'booked' : 'available';
    
    // Check if status needs to be updated
    $needs_update = $room['room_status'] !== $should_be;
    
    echo "<tr>";
    echo "<td>$room_id</td>";
    echo "<td>" . $room['room_number'] . "</td>";
    echo "<td>" . $room['room_type'] . "</td>";
    echo "<td>" . $room['room_status'] . "</td>";
    echo "<td>$active_bookings</td>";
    echo "<td>$should_be</td>";
    echo "<td>";
    
    if ($needs_update) {
        echo "<a href='?fix_room=$room_id&new_status=$should_be'>Fix Status</a>";
    } else {
        echo "OK";
    }
    
    echo "</td>";
    echo "</tr>";
}

echo "</table>";

// Handle status fixes
if (isset($_GET['fix_room']) && isset($_GET['new_status'])) {
    $room_id = $_GET['fix_room'];
    $new_status = $_GET['new_status'];
    
    $stmt = $conn->prepare("UPDATE rooms SET room_status = ? WHERE room_id = ?");
    $stmt->execute([$new_status, $room_id]);
    
    echo "<p><strong>Updated room $room_id status to $new_status</strong></p>";
    echo "<p><a href='fix_room_status.php'>Refresh</a></p>";
}
?> 