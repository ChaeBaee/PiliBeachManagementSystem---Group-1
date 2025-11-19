<?php
require_once '../../db.php';
session_start();

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$room_id = $_GET['room_id'] ?? null;
$year = $_GET['year'] ?? date('Y');
$month = $_GET['month'] ?? date('n');

if (!$room_id) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Room ID is required']);
    exit();
}

try {
    // First, let's check if the bookings table exists
    $table_check = $conn->query("SHOW TABLES LIKE 'bookings'");
    if ($table_check->rowCount() == 0) {
        throw new Exception('Bookings table does not exist');
    }
    
    // Check table structure
    $columns = $conn->query("DESCRIBE bookings");
    $column_names = $columns->fetchAll(PDO::FETCH_COLUMN);
    
    // Log the columns for debugging
    error_log("Bookings table columns: " . implode(', ', $column_names));
    
    // Get booked dates for the room
    $query = "SELECT booking_check_in, booking_check_out 
             FROM bookings 
             WHERE room_id = :room_id 
             AND booking_status NOT IN ('Canceled', 'Checked-out')
             AND (
                 (YEAR(booking_check_in) = :year AND MONTH(booking_check_in) = :month) OR
                 (YEAR(booking_check_out) = :year AND MONTH(booking_check_out) = :month) OR
                 (booking_check_in <= :month_end AND booking_check_out >= :month_start)
             )
             ORDER BY booking_check_in";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':room_id', $room_id);
    $stmt->bindParam(':year', $year);
    $stmt->bindParam(':month', $month);
    
    // Calculate month start and end dates
    $month_start = "$year-$month-01";
    $month_end = date('Y-m-t', strtotime($month_start));
    $stmt->bindParam(':month_start', $month_start);
    $stmt->bindParam(':month_end', $month_end);
    
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Log the bookings for debugging
    error_log("Found " . count($bookings) . " bookings for room $room_id in month $month/$year");
    
    // Generate array of all booked dates for the month
    $booked_dates = [];
    foreach ($bookings as $booking) {
        $start = new DateTime($booking['booking_check_in']);
        $end = new DateTime($booking['booking_check_out']);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);
        
        foreach ($period as $date) {
            if ($date->format('Y-n') == "$year-$month") {
                $booked_dates[] = $date->format('Y-m-d');
            }
        }
    }
    
    // Generate calendar data
    $first_day = new DateTime("$year-$month-01");
    $last_day = new DateTime("$year-$month-" . $first_day->format('t'));
    $start_date = clone $first_day;
    $start_date->modify('-' . ($first_day->format('w')) . ' days');
    
    $calendar = [];
    $current_date = clone $start_date;
    
    for ($week = 0; $week < 6; $week++) {
        $week_data = [];
        for ($day = 0; $day < 7; $day++) {
            $date_str = $current_date->format('Y-m-d');
            $is_current_month = $current_date->format('Y-n') == "$year-$month";
            $is_booked = in_array($date_str, $booked_dates);
            $is_today = $date_str == date('Y-m-d');
            $is_past = $date_str < date('Y-m-d');
            
            $week_data[] = [
                'date' => $date_str,
                'day' => $current_date->format('j'),
                'is_current_month' => $is_current_month,
                'is_booked' => $is_booked,
                'is_today' => $is_today,
                'is_past' => $is_past,
                'selectable' => $is_current_month && !$is_booked && !$is_past
            ];
            
            $current_date->add(new DateInterval('P1D'));
        }
        $calendar[] = $week_data;
    }
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'calendar' => $calendar,
        'booked_dates' => array_unique($booked_dates),
        'month_name' => date('F Y', strtotime("$year-$month-01")),
        'current_month' => $month,
        'current_year' => $year,
        'debug' => [
            'room_id' => $room_id,
            'month' => $month,
            'year' => $year,
            'bookings_count' => count($bookings),
            'booked_dates_count' => count(array_unique($booked_dates))
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Calendar view error: " . $e->getMessage());
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'Database error occurred',
        'debug_message' => $e->getMessage()
    ]);
}
?> 