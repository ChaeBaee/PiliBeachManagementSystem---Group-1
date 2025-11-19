<?php
require_once '../../db.php';
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$room_id = $_GET['room_id'] ?? null;
$check_in = $_GET['check_in'] ?? null;
$check_out = $_GET['check_out'] ?? null;
$checkin_time = $_GET['checkin_time'] ?? '14:00'; // Default check-in time

if (!$room_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Room ID is required']);
    exit();
}

// Enhanced availability checking function that considers time gaps
function checkAvailabilityWithTimeGap($conn, $room_id, $check_in, $check_out, $checkin_time) {
    try {
        // Get all conflicting bookings for the room
        $query = "SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time 
                 FROM bookings 
                 WHERE room_id = :room_id 
                 AND booking_status NOT IN ('Canceled', 'Checked-out')
                 AND (
                     (booking_check_in <= :check_out AND booking_check_out >= :check_in)
                 )
                 ORDER BY booking_check_in";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->bindParam(':check_in', $check_in);
        $stmt->bindParam(':check_out', $check_out);
        $stmt->execute();
        $conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($conflicting_bookings)) {
            return ['available' => true, 'message' => 'Room is available'];
        }
        
        // Check each conflicting booking to see if there's sufficient time gap
        foreach ($conflicting_bookings as $booking) {
            $booking_check_in = $booking['booking_check_in'];
            $booking_check_out = $booking['booking_check_out'];
            $booking_checkin_time = $booking['booking_checkin_time'];
            $booking_checkout_time = $booking['booking_checkout_time'];
            
            // If the new booking is on the same day as an existing checkout
            if ($check_in === $booking_check_out) {
                // Calculate time gap between checkout and check-in
                $checkout_datetime = new DateTime($booking_check_out . ' ' . $booking_checkout_time);
                $checkin_datetime = new DateTime($check_in . ' ' . $checkin_time);
                
                $time_gap = $checkout_datetime->diff($checkin_datetime);
                $gap_hours = $time_gap->h + ($time_gap->days * 24);
                
                // Allow booking if there's at least 2 hours gap
                if ($gap_hours >= 2) {
                    continue; // This conflict is acceptable, check next one
                } else {
                    return [
                        'available' => false, 
                        'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'
                    ];
                }
            }
            
            // If the new booking checkout is on the same day as an existing check-in
            if ($check_out === $booking_check_in) {
                // Calculate time gap between new checkout and existing check-in
                $new_checkout_datetime = new DateTime($check_out . ' 12:00'); // Default checkout time
                $existing_checkin_datetime = new DateTime($booking_check_in . ' ' . $booking_checkin_time);
                
                $time_gap = $new_checkout_datetime->diff($existing_checkin_datetime);
                $gap_hours = $time_gap->h + ($time_gap->days * 24);
                
                // Allow booking if there's at least 2 hours gap
                if ($gap_hours >= 2) {
                    continue; // This conflict is acceptable, check next one
                } else {
                    return [
                        'available' => false, 
                        'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'
                    ];
                }
            }
            
            // For overlapping dates (not same-day), room is not available
            return [
                'available' => false, 
                'message' => 'Room is not available for selected dates'
            ];
        }
        
        // If we get here, all conflicts have sufficient time gaps
        return ['available' => true, 'message' => 'Room is available with time gap consideration'];
        
    } catch (PDOException $e) {
        error_log("Error checking availability with time gap: " . $e->getMessage());
        return ['available' => false, 'message' => 'Error checking room availability'];
    }
}

try {
    // If specific dates are provided, check availability for those dates
    if ($check_in && $check_out) {
        $availability_result = checkAvailabilityWithTimeGap($conn, $room_id, $check_in, $check_out, $checkin_time);
        
        echo json_encode([
            'available' => $availability_result['available'],
            'message' => $availability_result['message']
        ]);
    } else {
        // Get all booked dates for this room (for calendar disabling)
        $query = "SELECT booking_check_in, booking_check_out 
                 FROM bookings 
                 WHERE room_id = :room_id 
                 AND booking_status NOT IN ('Canceled', 'Checked-out')
                 AND booking_check_out >= CURDATE()
                 ORDER BY booking_check_in";
        
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':room_id', $room_id);
        $stmt->execute();
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Generate array of all booked dates
        $booked_dates = [];
        foreach ($bookings as $booking) {
            $start = new DateTime($booking['booking_check_in']);
            $end = new DateTime($booking['booking_check_out']);
            $interval = new DateInterval('P1D');
            $period = new DatePeriod($start, $interval, $end);
            
            foreach ($period as $date) {
                $booked_dates[] = $date->format('Y-m-d');
            }
        }
        
        echo json_encode([
            'booked_dates' => array_unique($booked_dates),
            'bookings' => $bookings
        ]);
    }
    
} catch (PDOException $e) {
    error_log("Availability check error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Database error occurred']);
}
?> 