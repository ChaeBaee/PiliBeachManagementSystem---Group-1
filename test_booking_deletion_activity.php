<?php
require_once 'PHP/db.php';

echo "<h2>Testing Booking Deletion and Activity Logs</h2>";

// Test the BookingManager class
require_once 'Admin/Bookings/back/bookings_backend.php';

$bookingManager = new BookingManager($conn);

// Get current bookings
echo "<h3>Current Active Bookings:</h3>";
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

// Get current activity logs
echo "<h3>Current Activity Logs (Deleted Bookings):</h3>";
$stmt = $conn->prepare("SELECT al.*, u.user_name 
                       FROM audit_logs al 
                       LEFT JOIN users u ON al.changed_by = u.user_id 
                       WHERE al.table_name = 'bookings' 
                       AND al.action = 'DELETE'
                       ORDER BY al.timestamp DESC 
                       LIMIT 10");
$stmt->execute();
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Record ID</th><th>Action</th><th>Old Data</th><th>Changed By</th><th>Timestamp</th></tr>";
foreach ($logs as $log) {
    echo "<tr>";
    echo "<td>{$log['record_id']}</td>";
    echo "<td>{$log['action']}</td>";
    echo "<td style='max-width: 300px; word-wrap: break-word;'>{$log['old_data']}</td>";
    echo "<td>{$log['user_name']}</td>";
    echo "<td>{$log['timestamp']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test room status updates
echo "<h3>Current Room Statuses:</h3>";
$stmt = $conn->prepare("SELECT room_id, room_number, room_type, room_status FROM rooms ORDER BY room_number");
$stmt->execute();
$rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
echo "<tr><th>Room ID</th><th>Room Number</th><th>Room Type</th><th>Status</th></tr>";
foreach ($rooms as $room) {
    echo "<tr>";
    echo "<td>{$room['room_id']}</td>";
    echo "<td>{$room['room_number']}</td>";
    echo "<td>{$room['room_type']}</td>";
    echo "<td>{$room['room_status']}</td>";
    echo "</tr>";
}
echo "</table>";

// Test the activity logs method
echo "<h3>Testing Activity Logs Method:</h3>";
$activityLogs = $bookingManager->getActivityLogs(5);
if ($activityLogs) {
    echo "<p>Successfully retrieved " . count($activityLogs) . " activity logs</p>";
    foreach ($activityLogs as $log) {
        echo "<p>Booking ID: {$log['record_id']} - Action: {$log['action']} - Timestamp: {$log['timestamp']}</p>";
    }
} else {
    echo "<p>Failed to retrieve activity logs</p>";
}

echo "<h3>Test Instructions:</h3>";
echo "<ol>";
echo "<li>Go to <a href='Admin/Bookings/front/bookings.php'>Admin Bookings</a></li>";
echo "<li>Edit any booking and change its status to 'Canceled' or 'Checked-out'</li>";
echo "<li>The booking should be automatically deleted and moved to activity logs</li>";
echo "<li>Click the 'Activity Logs' button to view deleted bookings</li>";
echo "<li>Check that room status is updated correctly</li>";
echo "</ol>";

echo "<h3>Test Complete</h3>";
echo "<p><a href='Admin/Bookings/front/bookings.php'>Go to Admin Bookings</a></p>";
echo "<p><a href='Admin/ActivityLogs/front/activity-logs.php'>Go to Activity Logs</a></p>";
?> 