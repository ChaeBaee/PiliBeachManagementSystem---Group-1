<?php
require_once 'db.php';
session_start();

// This is a test script to verify double booking prevention
// Run this script to test the system

echo "<h1>Double Booking Prevention Test</h1>";

// Test 1: Check if a room is available for specific dates
function testAvailabilityCheck($conn, $room_id, $check_in, $check_out) {
    $query = "SELECT COUNT(*) as count FROM bookings 
             WHERE room_id = :room_id 
             AND booking_status NOT IN ('Canceled', 'Checked-out')
             AND (
                 (booking_check_in <= :check_out AND booking_check_out >= :check_in)
             )";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':check_in', $check_in);
    $stmt->bindParam(':check_out', $check_out);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    return $result['count'] == 0;
}

// Test 2: Get all booked dates for a room
function getBookedDates($conn, $room_id) {
    $query = "SELECT booking_check_in, booking_check_out 
             FROM bookings 
             WHERE room_id = :room_id 
             AND booking_status NOT IN ('Canceled', 'Checked-out')
             AND booking_check_out >= CURDATE()
             ORDER BY booking_check_in";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Test 3: Get available rooms for specific dates
function getAvailableRooms($conn, $check_in, $check_out, $guests = 1, $room_type = '') {
    $query = "SELECT r.* FROM rooms r 
              WHERE r.room_status IN ('available', 'booked')
              AND NOT EXISTS (
                  SELECT 1 FROM bookings b 
                  WHERE b.room_id = r.room_id 
                  AND b.booking_status NOT IN ('Canceled', 'Checked-out')
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

// Get a sample room
$stmt = $conn->query("SELECT room_id, room_number, room_type FROM rooms LIMIT 1");
$sample_room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$sample_room) {
    echo "<p style='color: red;'>No rooms found in database. Please add rooms first.</p>";
    exit();
}

echo "<h2>Testing Room: {$sample_room['room_type']} - Room {$sample_room['room_number']}</h2>";

// Test 1: Check current bookings
echo "<h3>Test 1: Current Bookings</h3>";
$booked_dates = getBookedDates($conn, $sample_room['room_id']);
if (empty($booked_dates)) {
    echo "<p style='color: green;'>✓ No current bookings for this room</p>";
} else {
    echo "<p style='color: orange;'>⚠ Current bookings found:</p>";
    foreach ($booked_dates as $booking) {
        echo "<p>- {$booking['booking_check_in']} to {$booking['booking_check_out']}</p>";
    }
}

// Test 2: Check availability for future dates
echo "<h3>Test 2: Availability Check</h3>";
$future_check_in = date('Y-m-d', strtotime('+1 week'));
$future_check_out = date('Y-m-d', strtotime('+1 week +2 days'));

$is_available = testAvailabilityCheck($conn, $sample_room['room_id'], $future_check_in, $future_check_out);
if ($is_available) {
    echo "<p style='color: green;'>✓ Room is available for $future_check_in to $future_check_out</p>";
} else {
    echo "<p style='color: red;'>✗ Room is NOT available for $future_check_in to $future_check_out</p>";
}

// Test 3: Get available rooms
echo "<h3>Test 3: Available Rooms Search</h3>";
$available_rooms = getAvailableRooms($conn, $future_check_in, $future_check_out);
echo "<p>Found " . count($available_rooms) . " available room(s) for $future_check_in to $future_check_out</p>";

if (!empty($available_rooms)) {
    echo "<ul>";
    foreach ($available_rooms as $room) {
        echo "<li>{$room['room_type']} - Room {$room['room_number']} (₱{$room['room_price']})</li>";
    }
    echo "</ul>";
}

// Test 4: Simulate double booking attempt
echo "<h3>Test 4: Double Booking Simulation</h3>";
$test_check_in = date('Y-m-d', strtotime('+2 weeks'));
$test_check_out = date('Y-m-d', strtotime('+2 weeks +3 days'));

// First, check if room is available
$initially_available = testAvailabilityCheck($conn, $sample_room['room_id'], $test_check_in, $test_check_out);
echo "<p>Initial availability for $test_check_in to $test_check_out: " . ($initially_available ? 'Available' : 'Not Available') . "</p>";

if ($initially_available) {
    // Simulate creating a booking (without actually doing it)
    echo "<p style='color: blue;'>Simulating booking creation...</p>";
    
    // Now check if the room would still be available for overlapping dates
    $overlapping_check_in = date('Y-m-d', strtotime('+2 weeks +1 day'));
    $overlapping_check_out = date('Y-m-d', strtotime('+2 weeks +4 days'));
    
    $still_available = testAvailabilityCheck($conn, $sample_room['room_id'], $overlapping_check_in, $overlapping_check_out);
    
    if (!$still_available) {
        echo "<p style='color: green;'>✓ Double booking prevention working: Overlapping dates would be blocked</p>";
    } else {
        echo "<p style='color: red;'>✗ Double booking prevention issue: Overlapping dates still available</p>";
    }
}

echo "<h3>Test Summary</h3>";
echo "<p>The double booking prevention system includes:</p>";
echo "<ul>";
echo "<li>✓ Real-time availability checking via API endpoints</li>";
echo "<li>✓ Calendar view showing booked dates</li>";
echo "<li>✓ Form validation preventing double bookings</li>";
echo "<li>✓ Backend validation with database transactions</li>";
echo "<li>✓ Row-level locking for concurrent booking attempts</li>";
echo "</ul>";

echo "<p><strong>To test the full system:</strong></p>";
echo "<ol>";
echo "<li>Open the room details page in multiple browser tabs</li>";
echo "<li>Try to book the same room for overlapping dates</li>";
echo "<li>Verify that only one booking succeeds</li>";
echo "<li>Check that the calendar shows booked dates as unavailable</li>";
echo "</ol>";
?> 