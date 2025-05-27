<?php
session_start();
require_once '../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");
class BookingManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAllBookings()
    {
        try {
            $query = "SELECT b.*, u.user_name, r.room_number, r.room_type 
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.user_id 
                     JOIN rooms r ON b.room_id = r.room_id 
                     ORDER BY b.booking_check_in DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
            return false;
        }
    }


    public function getGuestUsers()
    {
        try {
            $query = "SELECT user_id, user_name FROM users WHERE user_role = 'Guest' ORDER BY user_name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching guest users: " . $e->getMessage());
            return false;
        }
    }


    public function getAvailableRooms($checkIn, $checkOut, $currentBookingId = null)
    {
        try {
            $query = "SELECT r.*
FROM rooms r
WHERE 
    (
        r.room_status = 'available'
        OR (
            r.room_status = 'booked'
            AND (
                r.room_id NOT IN (
                    SELECT room_id
                    FROM bookings
                    WHERE booking_status != 'Canceled'
                      AND booking_check_in <= :check_out
                      AND booking_check_out >= :check_in
                      AND (
                          :current_booking_id IS NULL 
                          OR booking_id != :current_booking_id
                      )
                )
                OR r.room_id IN (
                    SELECT room_id
                    FROM bookings
                    WHERE booking_id = :current_booking_id
                )
            )
        )
    )
ORDER BY r.room_number;
";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);
            $stmt->bindParam(':current_booking_id', $currentBookingId);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching available rooms: " . $e->getMessage());
            return false;
        }
    }


    public function addBooking($userId, $roomId, $checkIn, $checkOut, $totalPrice)
    {
        try {
            if (!$userId || !$roomId || !$checkIn || !$checkOut || !$totalPrice) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            // Validate dates
            if (strtotime($checkIn) >= strtotime($checkOut)) {
                return ['success' => false, 'message' => 'Check-out date must be after check-in date'];
            }

            // Check if dates are in the past
            $today = date('Y-m-d');
            if (strtotime($checkIn) < strtotime($today)) {
                return ['success' => false, 'message' => 'Check-in date cannot be in the past'];
            }

            $query = "SELECT COUNT(*) as count FROM bookings 
                     WHERE room_id = :room_id 
                     AND booking_status != 'canceled'
                     AND ((booking_check_in <= :check_out AND booking_check_out >= :check_in))";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'Room is not available for selected dates'];
            }


            $query = "INSERT INTO bookings (user_id, room_id, booking_check_in, booking_check_out, 
                     booking_total_price, booking_status) 
                     VALUES (:user_id, :room_id, :check_in, :check_out, :total_price, 'Pending')";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);
            $stmt->bindParam(':total_price', $totalPrice);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Booking added successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to add booking'];
            }
        } catch (PDOException $e) {
            error_log("Error adding booking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }


    public function updateBooking($bookingId, $roomId, $checkIn, $checkOut, $totalPrice, $status)
    {
        try {
            if (!$bookingId || !$roomId || !$checkIn || !$checkOut || !$totalPrice || !$status) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            // Validate dates
            if (strtotime($checkIn) >= strtotime($checkOut)) {
                return ['success' => false, 'message' => 'Check-out date must be after check-in date'];
            }

            // Check if dates are in the past
            $today = date('Y-m-d');
            if (strtotime($checkIn) < strtotime($today)) {
                return ['success' => false, 'message' => 'Check-in date cannot be in the past'];
            }

            $query = "SELECT COUNT(*) as count FROM bookings 
                     WHERE room_id = :room_id 
                     AND booking_id != :booking_id
                     AND booking_status != 'Canceled'
                     AND ((booking_check_in <= :check_out AND booking_check_out >= :check_in))";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['count'] > 0) {
                return ['success' => false, 'message' => 'Room is not available for selected dates'];
            }


            $query = "UPDATE bookings 
                     SET room_id = :room_id, 
                         booking_check_in = :check_in, 
                         booking_check_out = :check_out, 
                         booking_total_price = :total_price, 
                         booking_status = :status 
                     WHERE booking_id = :booking_id";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);
            $stmt->bindParam(':total_price', $totalPrice);
            $stmt->bindParam(':status', $status);

            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Booking updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Failed to update booking'];
            }
        } catch (PDOException $e) {
            error_log("Error updating booking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred'];
        }
    }

    public function deleteBooking($bookingId)
    {
        try {

            $query = "SELECT booking_status FROM bookings WHERE booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->execute();
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($booking && $booking['booking_status'] === 'Confirmed') {
                return ['success' => false, 'message' => 'Cannot delete confirmed bookings'];
            }

            $query = "DELETE FROM bookings WHERE booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);

            return ['success' => $stmt->execute()];
        } catch (PDOException $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error'];
        }
    }

    public function getBookingDetails($bookingId)
    {
        try {
            $query = "SELECT b.*, u.user_name, r.room_number, r.room_type, r.room_price 
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.user_id 
                     JOIN rooms r ON b.room_id = r.room_id 
                     WHERE b.booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching booking details: " . $e->getMessage());
            return false;
        }
    }
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingManager = new BookingManager($conn);
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'get_bookings':
            $bookings = $bookingManager->getAllBookings();
            echo json_encode(['success' => true, 'data' => $bookings]);
            break;

        case 'get_guest_users':
            $users = $bookingManager->getGuestUsers();
            echo json_encode(['success' => true, 'data' => $users]);
            break;

        case 'get_available_rooms':
            $checkIn = $_POST['check_in'] ?? '';
            $checkOut = $_POST['check_out'] ?? '';
            $currentBookingId = $_POST['current_booking_id'] ?? null;
            $rooms = $bookingManager->getAvailableRooms($checkIn, $checkOut, $currentBookingId);
            echo json_encode(['success' => true, 'data' => $rooms]);
            break;

        case 'add_booking':
            $result = $bookingManager->addBooking(
                $_POST['user_id'] ?? '',
                $_POST['room_id'] ?? '',
                $_POST['check_in'] ?? '',
                $_POST['check_out'] ?? '',
                $_POST['total_price'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'update_booking':
            $result = $bookingManager->updateBooking(
                $_POST['booking_id'] ?? '',
                $_POST['room_id'] ?? '',
                $_POST['check_in'] ?? '',
                $_POST['check_out'] ?? '',
                $_POST['total_price'] ?? '',
                $_POST['status'] ?? ''
            );
            echo json_encode($result);
            break;

        case 'delete_booking':
            $result = $bookingManager->deleteBooking($_POST['booking_id']);
            echo json_encode($result);
            break;

        case 'get_booking_details':
            $details = $bookingManager->getBookingDetails($_POST['booking_id']);
            echo json_encode(['success' => true, 'data' => $details]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
}
?>