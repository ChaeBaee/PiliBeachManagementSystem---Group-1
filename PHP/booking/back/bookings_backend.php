<?php
require_once '../../db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../login/front/login.php");
    exit();
}

$stmt = $conn->query("SELECT DISTINCT room_type FROM rooms");
$room_types = $stmt->fetchAll(PDO::FETCH_COLUMN);

function getAvailableRooms($check_in, $check_out, $guests = 1, $room_type = '')
{
    global $conn;

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

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    switch ($_POST['action']) {
        case 'get_available_rooms':
            $check_in = $_POST['check_in'] ?? '';
            $check_out = $_POST['check_out'] ?? '';
            $guests = $_POST['guests'] ?? 1;
            $room_type = $_POST['room_type'] ?? '';
            
            if (empty($check_in) || empty($check_out)) {
                echo json_encode(['success' => false, 'message' => 'Check-in and check-out dates are required']);
                exit();
            }
            
            try {
                $rooms = getAvailableRooms($check_in, $check_out, $guests, $room_type);
                echo json_encode(['success' => true, 'data' => $rooms]);
            } catch (Exception $e) {
                error_log("Error getting available rooms: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error retrieving available rooms']);
            }
            break;
            
        case 'check_room_availability':
            $room_id = $_POST['room_id'] ?? '';
            $check_in = $_POST['check_in'] ?? '';
            $check_out = $_POST['check_out'] ?? '';
            
            if (empty($room_id) || empty($check_in) || empty($check_out)) {
                echo json_encode(['success' => false, 'message' => 'Room ID and dates are required']);
                exit();
            }
            
            try {
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
                
                echo json_encode([
                    'success' => true,
                    'available' => $result['count'] == 0,
                    'message' => $result['count'] > 0 ? 'Room is not available for selected dates' : 'Room is available'
                ]);
            } catch (Exception $e) {
                error_log("Error checking room availability: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error checking room availability']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
    exit();
}
?>
