<?php
require_once 'db.php';

echo "<h1>Enhanced Room Availability Testing</h1>";
echo "<p>This test demonstrates the enhanced availability checking that allows same-day bookings when there's sufficient time between checkout and check-in.</p>";

// Test scenarios
$test_scenarios = [
    [
        'name' => 'Same day booking with 2+ hours gap (should be allowed)',
        'existing_checkout' => '2024-01-15',
        'existing_checkout_time' => '12:00',
        'new_checkin' => '2024-01-15',
        'new_checkin_time' => '14:00',
        'expected' => 'allowed'
    ],
    [
        'name' => 'Same day booking with insufficient gap (should be blocked)',
        'existing_checkout' => '2024-01-15',
        'existing_checkout_time' => '12:00',
        'new_checkin' => '2024-01-15',
        'new_checkin_time' => '13:00',
        'expected' => 'blocked'
    ],
    [
        'name' => 'Different day booking (should be allowed)',
        'existing_checkout' => '2024-01-15',
        'existing_checkout_time' => '12:00',
        'new_checkin' => '2024-01-16',
        'new_checkin_time' => '14:00',
        'expected' => 'allowed'
    ],
    [
        'name' => 'Overlapping dates (should be blocked)',
        'existing_checkout' => '2024-01-16',
        'existing_checkout_time' => '12:00',
        'new_checkin' => '2024-01-15',
        'new_checkin_time' => '14:00',
        'expected' => 'blocked'
    ]
];

// Function to test availability
function testAvailability($conn, $existing_checkout, $existing_checkout_time, $new_checkin, $new_checkin_time) {
    // First, create a test booking
    $test_room_id = 1; // Assuming room ID 1 exists
    $test_user_id = 1; // Assuming user ID 1 exists
    
    // Check if test room exists
    $stmt = $conn->prepare("SELECT room_id FROM rooms WHERE room_id = ?");
    $stmt->execute([$test_room_id]);
    if (!$stmt->fetch()) {
        return "Test room not found. Please ensure room ID 1 exists.";
    }
    
    // Check if test user exists
    $stmt = $conn->prepare("SELECT user_id FROM users WHERE user_id = ?");
    $stmt->execute([$test_user_id]);
    if (!$stmt->fetch()) {
        return "Test user not found. Please ensure user ID 1 exists.";
    }
    
    // Create a test booking
    $existing_checkin = date('Y-m-d', strtotime($existing_checkout . ' -1 day'));
    $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, booking_check_in, booking_checkin_time, booking_check_out, booking_checkout_time, booking_total_price, booking_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Confirmed')");
    $stmt->execute([$test_user_id, $test_room_id, $existing_checkin, '14:00', $existing_checkout, $existing_checkout_time, 1000]);
    $test_booking_id = $conn->lastInsertId();
    
    // Test availability for new booking
    $new_checkout = date('Y-m-d', strtotime($new_checkin . ' +1 day'));
    
    // Use the enhanced availability checking logic
    $query = "SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time 
             FROM bookings 
             WHERE room_id = ? 
             AND booking_status NOT IN ('Canceled', 'Checked-out')
             AND (
                 (booking_check_in <= ? AND booking_check_out >= ?)
             )
             ORDER BY booking_check_in";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$test_room_id, $new_checkout, $new_checkin]);
    $conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $available = true;
    $message = "Available";
    
    foreach ($conflicting_bookings as $booking) {
        $booking_check_in = $booking['booking_check_in'];
        $booking_check_out = $booking['booking_check_out'];
        $booking_checkin_time = $booking['booking_checkin_time'];
        $booking_checkout_time = $booking['booking_checkout_time'];
        
        // If the new booking is on the same day as an existing checkout
        if ($new_checkin === $booking_check_out) {
            $checkout_datetime = new DateTime($booking_check_out . ' ' . $booking_checkout_time);
            $checkin_datetime = new DateTime($new_checkin . ' ' . $new_checkin_time);
            
            $time_gap = $checkout_datetime->diff($checkin_datetime);
            $gap_hours = $time_gap->h + ($time_gap->days * 24);
            
            if ($gap_hours < 2) {
                $available = false;
                $message = "Insufficient time gap: {$gap_hours} hours (need at least 2)";
                break;
            }
        }
        
        // If the new booking checkout is on the same day as an existing check-in
        if ($new_checkout === $booking_check_in) {
            $new_checkout_datetime = new DateTime($new_checkout . ' 12:00');
            $existing_checkin_datetime = new DateTime($booking_check_in . ' ' . $booking_checkin_time);
            
            $time_gap = $new_checkout_datetime->diff($existing_checkin_datetime);
            $gap_hours = $time_gap->h + ($time_gap->days * 24);
            
            if ($gap_hours < 2) {
                $available = false;
                $message = "Insufficient time gap: {$gap_hours} hours (need at least 2)";
                break;
            }
        }
        
        // For overlapping dates (not same-day), room is not available
        if ($new_checkin !== $booking_check_out && $new_checkout !== $booking_check_in) {
            $available = false;
            $message = "Overlapping dates";
            break;
        }
    }
    
    // Clean up test booking
    $stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
    $stmt->execute([$test_booking_id]);
    
    return $available ? "Available" : "Not available: " . $message;
}

// Run tests
foreach ($test_scenarios as $scenario) {
    echo "<h3>Test: {$scenario['name']}</h3>";
    echo "<p>Existing checkout: {$scenario['existing_checkout']} at {$scenario['existing_checkout_time']}</p>";
    echo "<p>New check-in: {$scenario['new_checkin']} at {$scenario['new_checkin_time']}</p>";
    
    $result = testAvailability(
        $conn, 
        $scenario['existing_checkout'], 
        $scenario['existing_checkout_time'], 
        $scenario['new_checkin'], 
        $scenario['new_checkin_time']
    );
    
    $status = strpos($result, 'Available') === 0 ? 'PASS' : 'FAIL';
    $expected_status = $scenario['expected'] === 'allowed' ? 'PASS' : 'FAIL';
    
    echo "<p><strong>Result:</strong> {$result}</p>";
    echo "<p><strong>Status:</strong> {$status} (Expected: {$expected_status})</p>";
    echo "<hr>";
}

echo "<h2>Summary</h2>";
echo "<p>The enhanced availability checking system now allows same-day bookings when there's sufficient time (at least 2 hours) between checkout and check-in times.</p>";
echo "<p>This enables more efficient room utilization while ensuring proper cleaning and preparation time.</p>";
?> 