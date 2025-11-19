<?php
require_once '../../db.php';
session_start();

// Clear any existing error from previous requests
unset($error);

// Debug: Log the request method and any existing error
error_log("Booking confirmation backend - Request method: " . $_SERVER['REQUEST_METHOD']);
error_log("Booking confirmation backend - Session error: " . (isset($_SESSION['error']) ? $_SESSION['error'] : 'none'));

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/front/login.php");
    exit();
}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

// Debug: Log the incoming parameters
error_log("Booking confirmation backend - GET parameters: " . print_r($_GET, true));

if (!isset($_GET['room_id']) || !isset($_GET['check_in']) || !isset($_GET['check_out'])) {
    error_log("Missing required parameters - redirecting to bookings.php");
    header("Location: ../bookings.php");
    exit();
}

if (!isset($_SESSION['booking_token'])) {
    $_SESSION['booking_token'] = bin2hex(random_bytes(32));
}

$room_id = $_GET['room_id'];
$check_in = $_GET['check_in'];
$check_out = $_GET['check_out'];
$checkin_time = $_GET['checkin_time'] ?? '14:00'; // Default to 2:00 PM if not provided

// Debug: Log the parameters we're working with
error_log("Processing booking: room_id=$room_id, check_in=$check_in, check_out=$check_out, checkin_time=$checkin_time");

// Enhanced validation function
function validateBookingDates($conn, $room_id, $check_in, $check_out, $checkin_time = '14:00', $existing_pending = null) {
    // Check if dates are valid
    if (!strtotime($check_in) || !strtotime($check_out)) {
        return ['valid' => false, 'message' => 'Invalid date format'];
    }
    
    // Check if check-out is after check-in
    if (strtotime($check_out) <= strtotime($check_in)) {
        return ['valid' => false, 'message' => 'Check-out date must be after check-in date'];
    }
    
    // Check if check-in is not in the past
    if (strtotime($check_in) < strtotime(date('Y-m-d'))) {
        return ['valid' => false, 'message' => 'Check-in date cannot be in the past'];
    }
    
    // Enhanced availability check with time gap consideration
    try {
        // Get all conflicting bookings for the room - simplified logic
        $query = "SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time 
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
        $stmt->bindParam(':current_user_id', $_SESSION['user_id']);
        $stmt->execute();
        $conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug: Log conflicting bookings
        error_log("Found " . count($conflicting_bookings) . " conflicting bookings for room $room_id");
        error_log("Checking dates: check_in=$check_in, check_out=$check_out");
        error_log("Current user ID: " . $_SESSION['user_id']);
        if (!empty($conflicting_bookings)) {
            error_log("Conflicting bookings: " . print_r($conflicting_bookings, true));
        }
        
        // If user has existing pending booking for same dates, allow them to proceed
        if (isset($existing_pending) && $existing_pending && !empty($existing_pending)) {
            error_log("User has existing pending booking for same dates - allowing to proceed despite conflicts");
            return ['valid' => true];
        }
        
        if (empty($conflicting_bookings)) {
            return ['valid' => true];
        }
        
        // Check each conflicting booking to see if there's sufficient time gap
        foreach ($conflicting_bookings as $booking) {
            $booking_check_in = $booking['booking_check_in'];
            $booking_check_out = $booking['booking_check_out'];
            $booking_checkin_time = $booking['booking_checkin_time'] ?? '14:00';
            $booking_checkout_time = $booking['booking_checkout_time'] ?? '12:00';
            
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
                    return ['valid' => false, 'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'];
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
                    return ['valid' => false, 'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'];
                }
            }
            
            // For overlapping dates (not same-day), room is not available
            return ['valid' => false, 'message' => 'Room is already booked for the selected dates'];
        }
        
        // If we get here, all conflicts have sufficient time gaps
        return ['valid' => true];
        
    } catch (PDOException $e) {
        error_log("Error checking booking availability: " . $e->getMessage());
        return ['valid' => false, 'message' => 'Error checking room availability'];
    }
}

// Check for existing pending bookings by the same user for the same room and dates
$stmt = $conn->prepare("SELECT booking_id, booking_status FROM bookings 
                       WHERE user_id = ? AND room_id = ? 
                       AND booking_check_in = ? AND booking_check_out = ?
                       AND booking_status = 'Pending'");
$stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $check_out]);
$existing_pending = $stmt->fetch(PDO::FETCH_ASSOC);

if ($existing_pending) {
    error_log("Found existing pending booking: " . print_r($existing_pending, true));
    // If there's already a pending booking for the same dates, we can proceed
    // The user is likely just confirming their existing booking
}

$stmt = $conn->prepare("SELECT * FROM rooms 
WHERE room_id = ? 
  AND (room_status = 'available' OR room_status = 'booked');
");
$stmt->execute([$room_id]);
$room = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$room) {
    error_log("Room not found - redirecting to bookings.php");
    header("Location: ../bookings.php");
    exit();
}

// Debug: Log that we found the room
error_log("Room found: " . $room['room_type'] . " - " . $room['room_number'] . " - Status: " . $room['room_status']);

$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$check_in_date = new DateTime($check_in);
$check_out_date = new DateTime($check_out);
$nights = $check_in_date->diff($check_out_date)->days;
$total_price = $room['room_price'] * $nights;

// Debug: Log the calculated values
error_log("Calculated: nights=$nights, total_price=$total_price");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (!isset($_POST['booking_token']) || $_POST['booking_token'] !== $_SESSION['booking_token']) {
            throw new Exception("Invalid booking session. Please start over.");
        }

        // Enhanced validation before processing
        $validation = validateBookingDates($conn, $room_id, $check_in, $check_out, $checkin_time, $existing_pending);
        if (!$validation['valid']) {
            throw new Exception($validation['message']);
        }

        $conn->beginTransaction();

        // Double-check room status
        $stmt = $conn->prepare("SELECT room_status FROM rooms WHERE room_id = ? FOR UPDATE");
        $stmt->execute([$room_id]);
        $room_status = $stmt->fetchColumn();

        if ($room_status === 'maintenance') {
            throw new Exception("Room is not available. Please select another room.");
        }

        // Final availability check with row locking and time gap consideration
        $stmt = $conn->prepare("SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time 
                               FROM bookings 
                               WHERE room_id = ? 
                               AND booking_status NOT IN ('Canceled', 'Checked-out')
                               AND user_id != ?
                               AND (
                                   (booking_check_in < ? AND booking_check_out > ?)
                               )
                               FOR UPDATE");
        $stmt->execute([$room_id, $_SESSION['user_id'], $check_out, $check_in]);
        $conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Debug: Log final availability check
        error_log("Final availability check - Found " . count($conflicting_bookings) . " conflicting bookings");
        error_log("Final check dates: check_in=$check_in, check_out=$check_out");
        error_log("Final check - Current user ID: " . $_SESSION['user_id']);
        if (!empty($conflicting_bookings)) {
            error_log("Final conflicting bookings: " . print_r($conflicting_bookings, true));
        }

        if (!empty($conflicting_bookings)) {
            // Check each conflicting booking for time gap
            foreach ($conflicting_bookings as $booking) {
                $booking_check_in = $booking['booking_check_in'];
                $booking_check_out = $booking['booking_check_out'];
                $booking_checkin_time = $booking['booking_checkin_time'] ?? '14:00';
                $booking_checkout_time = $booking['booking_checkout_time'] ?? '12:00';
                
                // If the new booking is on the same day as an existing checkout
                if ($check_in === $booking_check_out) {
                    $checkout_datetime = new DateTime($booking_check_out . ' ' . $booking_checkout_time);
                    $checkin_datetime = new DateTime($check_in . ' ' . $checkin_time);
                    
                    $time_gap = $checkout_datetime->diff($checkin_datetime);
                    $gap_hours = $time_gap->h + ($time_gap->days * 24);
                    
                    if ($gap_hours < 2) {
                        throw new Exception("Insufficient time gap between checkout and check-in. Need at least 2 hours gap.");
                    }
                }
                
                // If the new booking checkout is on the same day as an existing check-in
                if ($check_out === $booking_check_in) {
                    $new_checkout_datetime = new DateTime($check_out . ' 12:00');
                    $existing_checkin_datetime = new DateTime($booking_check_in . ' ' . $booking_checkin_time);
                    
                    $time_gap = $new_checkout_datetime->diff($existing_checkin_datetime);
                    $gap_hours = $time_gap->h + ($time_gap->days * 24);
                    
                    if ($gap_hours < 2) {
                        throw new Exception("Insufficient time gap between checkout and check-in. Need at least 2 hours gap.");
                    }
                }
                
                // For overlapping dates (not same-day), room is not available
                throw new Exception("Room is no longer available for the selected dates. Please choose different dates.");
            }
        }

        // Create the booking
        $stmt = $conn->prepare("INSERT INTO bookings (user_id, room_id, booking_check_in, booking_checkin_time, booking_check_out, booking_checkout_time, booking_total_price, booking_status) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->execute([$_SESSION['user_id'], $room_id, $check_in, $checkin_time, $check_out, '12:00', $total_price]);
        $booking_id = $conn->lastInsertId();

        // Update room status to booked
        $stmt = $conn->prepare("UPDATE rooms SET room_status = 'booked' WHERE room_id = ?");
        $stmt->execute([$room_id]);

        $_SESSION['booking_flow'] = [
            'booking_id' => $booking_id,
            'room_id' => $room_id,
            'check_in' => $check_in,
            'check_out' => $check_out,
            'total_price' => $total_price
        ];

        $conn->commit();
        
        // Log successful booking
        error_log("Booking created successfully: booking_id=$booking_id, room_id=$room_id, user_id=" . $_SESSION['user_id']);
        
        header("Location: /Proj/PHP/payments/front/payments.php?booking_id=" . $booking_id);
        exit();
    } catch (Exception $e) {
        $conn->rollBack();
        $error = $e->getMessage();
        error_log("Booking error: " . $error);
        error_log("Booking error set - Error variable: " . $error);
    }
}

// Debug: Log final state
error_log("Booking confirmation backend - Final error state: " . (isset($error) ? $error : 'none'));
?>