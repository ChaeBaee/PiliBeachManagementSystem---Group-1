<?php
session_start();
require_once '../../../PHP/db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

// CSRF Protection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_SESSION['csrf_token']) || !isset($_POST['csrf_token']) || $_SESSION['csrf_token'] !== $_POST['csrf_token']) {
        echo json_encode(['success' => false, 'message' => 'Invalid request. Please refresh the page and try again.']);
        exit;
    }
}

// Generate CSRF token if not exists
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

class BookingManager
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getBookingsWithSearch($search = '', $statusFilter = '', $dateFilter = '')
    {
        try {
            $query = "SELECT 
                        b.*, 
                        u.user_name, 
                        u.user_email,
                        u.user_phone,
                        r.room_number, 
                        r.room_type,
                        CASE 
                            WHEN b.booking_status = 'Confirmed' THEN 'text-green-600 font-semibold'
                            WHEN b.booking_status = 'Pending' THEN 'text-yellow-600'
                            WHEN b.booking_status = 'Checked-in' THEN 'text-blue-600'
                            WHEN b.booking_status = 'Canceled' THEN 'text-red-600'
                            WHEN b.booking_status = 'Checked-out' THEN 'text-gray-600'
                            ELSE 'text-gray-600'
                        END as status_class
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.user_id 
                     JOIN rooms r ON b.room_id = r.room_id 
                     WHERE b.booking_status NOT IN ('Canceled', 'Checked-out')";
            
            $params = [];
            
            // Search functionality
            if (!empty($search)) {
                $query .= " AND (u.user_name LIKE ? OR u.user_email LIKE ? OR u.user_phone LIKE ? 
                              OR r.room_number LIKE ? OR r.room_type LIKE ? OR b.booking_id LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            }
            
            // Status filter (but exclude checked-out from filter options)
            if (!empty($statusFilter) && $statusFilter !== 'Checked-out') {
                $query .= " AND b.booking_status = ?";
                $params[] = $statusFilter;
            }
            
            // Date filter
            if (!empty($dateFilter)) {
                switch ($dateFilter) {
                    case 'today':
                        $query .= " AND DATE(b.booking_check_in) = CURDATE()";
                        break;
                    case 'tomorrow':
                        $query .= " AND DATE(b.booking_check_in) = DATE_ADD(CURDATE(), INTERVAL 1 DAY)";
                        break;
                    case 'this_week':
                        $query .= " AND YEARWEEK(b.booking_check_in, 1) = YEARWEEK(CURDATE(), 1)";
                        break;
                    case 'this_month':
                        $query .= " AND YEAR(b.booking_check_in) = YEAR(CURDATE()) AND MONTH(b.booking_check_in) = MONTH(CURDATE())";
                        break;
                    case 'upcoming':
                        $query .= " AND b.booking_check_in >= CURDATE()";
                        break;
                    case 'past':
                        $query .= " AND b.booking_check_in < CURDATE()";
                        break;
                }
            }
            
            $query .= " ORDER BY b.booking_check_in DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching bookings: " . $e->getMessage());
            return false;
        }
    }

    public function getAllBookings()
    {
        try {
            $query = "SELECT b.*, u.user_name, r.room_number, r.room_type 
                     FROM bookings b 
                     JOIN users u ON b.user_id = u.user_id 
                     JOIN rooms r ON b.room_id = r.room_id 
                     WHERE b.booking_status NOT IN ('Canceled', 'Checked-out')
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

    public function getAvailableRooms($checkIn, $checkOut, $currentBookingId = null, $checkinTime = '14:00')
    {
        try {
            // First, get all rooms
            $query = "SELECT r.room_id, r.room_number, r.room_type, r.room_price, r.room_capacity, r.room_status FROM rooms r ORDER BY r.room_number";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $allRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $availableRooms = [];
            
            foreach ($allRooms as $room) {
                // Check if this room is available for the given dates with time gap consideration
                $availabilityResult = $this->checkRoomAvailability($room['room_id'], $checkIn, $checkOut, $currentBookingId, $checkinTime);
                
                if ($availabilityResult['success']) {
                    $availableRooms[] = $room;
                }
            }
            
            return $availableRooms;
        } catch (PDOException $e) {
            error_log("Error fetching available rooms: " . $e->getMessage());
            return false;
        }
    }

    public function getAllRooms()
    {
        try {
            $query = "SELECT r.room_id, r.room_number, r.room_type, r.room_price, r.room_capacity, r.room_status FROM rooms r ORDER BY r.room_number";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching all rooms: " . $e->getMessage());
            return false;
        }
    }

    public function createUser($userName, $userEmail = null, $userPhone)
    {
        try {
            // Generate a placeholder email if none is provided
            if (empty($userEmail)) {
                $userEmail = 'guest_' . time() . '@manual.booking';
            }
            
            // Check if user already exists with the same email
            $checkQuery = "SELECT user_id FROM users WHERE user_email = :user_email";
            $checkStmt = $this->conn->prepare($checkQuery);
            $checkStmt->bindParam(':user_email', $userEmail);
            $checkStmt->execute();
            
            if ($checkStmt->fetch()) {
                // If email already exists, generate a unique one
                $userEmail = 'guest_' . time() . '_' . rand(1000, 9999) . '@manual.booking';
            }
            
            // Generate a random password for the new user
            $password = password_hash('guest123', PASSWORD_DEFAULT);
            
            $query = "INSERT INTO users (user_name, user_email, user_password, user_role, user_phone) 
                     VALUES (:user_name, :user_email, :user_password, 'Guest', :user_phone)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_name', $userName);
            $stmt->bindParam(':user_email', $userEmail);
            $stmt->bindParam(':user_password', $password);
            $stmt->bindParam(':user_phone', $userPhone);
            
            if ($stmt->execute()) {
                return $this->conn->lastInsertId();
            } else {
                error_log("Failed to execute user creation query");
                return false;
            }
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            return false;
        }
    }

    public function addBooking($userId, $roomId, $checkIn, $checkOut, $totalPrice, $guestName = null, $guestEmail = null, $guestPhone = null, $checkinTime = '14:00', $checkoutTime = '12:00')
    {
        try {
            // If guestName is provided, create a new user
            if ($guestName && !$userId) {
                $userId = $this->createUser($guestName, $guestEmail, $guestPhone);
                if (is_array($userId) && !$userId['success']) {
                    return $userId; // Return the error message from createUser
                }
                if (!$userId) {
                    return ['success' => false, 'message' => 'Failed to create user account'];
                }
            }

            if (!$userId || !$roomId || !$checkIn || !$checkOut || !$totalPrice) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            if (strtotime($checkIn) >= strtotime($checkOut)) {
                return ['success' => false, 'message' => 'Check-out date must be after check-in date'];
            }

            $today = date('Y-m-d');
            if (strtotime($checkIn) < strtotime($today)) {
                return ['success' => false, 'message' => 'Check-in date cannot be in the past'];
            }

            // Check if room exists
            $roomQuery = "SELECT room_id FROM rooms WHERE room_id = :room_id";
            $roomStmt = $this->conn->prepare($roomQuery);
            $roomStmt->bindParam(':room_id', $roomId);
            $roomStmt->execute();
            if (!$roomStmt->fetch()) {
                return ['success' => false, 'message' => 'Selected room does not exist'];
            }

            // Check if user exists
            $userQuery = "SELECT user_id FROM users WHERE user_id = :user_id";
            $userStmt = $this->conn->prepare($userQuery);
            $userStmt->bindParam(':user_id', $userId);
            $userStmt->execute();
            if (!$userStmt->fetch()) {
                return ['success' => false, 'message' => 'User does not exist'];
            }

            // Enhanced availability check with time gap consideration
            $availabilityResult = $this->checkRoomAvailability($roomId, $checkIn, $checkOut, null, $checkinTime);
            if (!$availabilityResult['success']) {
                return ['success' => false, 'message' => $availabilityResult['message']];
            }

            // Start transaction
            $this->conn->beginTransaction();

            try {
                // Insert booking
                $query = "INSERT INTO bookings (user_id, room_id, booking_check_in, booking_checkin_time, booking_check_out, booking_checkout_time, 
                         booking_total_price, booking_status) 
                         VALUES (:user_id, :room_id, :check_in, :checkin_time, :check_out, :checkout_time, :total_price, 'Checked-in')";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $userId);
                $stmt->bindParam(':room_id', $roomId);
                $stmt->bindParam(':check_in', $checkIn);
                $stmt->bindParam(':checkin_time', $checkinTime);
                $stmt->bindParam(':check_out', $checkOut);
                $stmt->bindParam(':checkout_time', $checkoutTime);
                $stmt->bindParam(':total_price', $totalPrice);

                if (!$stmt->execute()) {
                    throw new Exception('Failed to add booking');
                }

                $bookingId = $this->conn->lastInsertId();

                // Update room status to booked
                if (!$this->updateRoomStatus($roomId, 'Checked-in')) {
                    throw new Exception('Failed to update room status');
                }

                // Create payment record
                $paymentQuery = "INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status) 
                                VALUES (:booking_id, :payment_amount, :payment_date, 'paid')";
                
                $paymentStmt = $this->conn->prepare($paymentQuery);
                $paymentStmt->bindParam(':booking_id', $bookingId);
                $paymentStmt->bindParam(':payment_amount', $totalPrice);
                $paymentStmt->bindParam(':payment_date', $today);

                if (!$paymentStmt->execute()) {
                    throw new Exception('Failed to create payment record');
                }

                $paymentId = $this->conn->lastInsertId();

                // Create invoice record
                $invoiceQuery = "INSERT INTO invoice (invoice_total_amount, invoice_status, invoice_date_created, payment_id) 
                                VALUES (:total_amount, 'paid', :date_created, :payment_id)";
                
                $invoiceStmt = $this->conn->prepare($invoiceQuery);
                $invoiceStmt->bindParam(':total_amount', $totalPrice);
                $invoiceStmt->bindParam(':date_created', $today);
                $invoiceStmt->bindParam(':payment_id', $paymentId);

                if (!$invoiceStmt->execute()) {
                    throw new Exception('Failed to create invoice record');
                }

                // Commit transaction
                $this->conn->commit();

                return ['success' => true, 'message' => 'Booking added successfully and guest is checked in'];

            } catch (Exception $e) {
                // Rollback transaction on error
                $this->conn->rollBack();
                error_log("Transaction failed: " . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to complete booking: ' . $e->getMessage()];
            }

        } catch (PDOException $e) {
            error_log("Error adding booking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()];
        }
    }

    public function updateBooking($bookingId, $roomId, $checkIn, $checkOut, $totalPrice, $status, $checkinTime = '14:00', $checkoutTime = '12:00')
    {
        try {
            if (!$bookingId || !$roomId || !$checkIn || !$checkOut || !$totalPrice || !$status) {
                return ['success' => false, 'message' => 'All fields are required'];
            }

            // Fetch current booking details
            $query = "SELECT room_id, booking_check_in, booking_check_out, booking_status FROM bookings WHERE booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->execute();
            $currentBooking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$currentBooking) {
                return ['success' => false, 'message' => 'Booking not found'];
            }

            $isRoomChanged = $currentBooking['room_id'] != $roomId;
            $isCheckInChanged = $currentBooking['booking_check_in'] != $checkIn;
            $isCheckOutChanged = $currentBooking['booking_check_out'] != $checkOut;
            $isStatusChanged = $currentBooking['booking_status'] != $status;

            // Only validate dates if room, check-in, or check-out are being changed
            if ($isRoomChanged || $isCheckInChanged || $isCheckOutChanged) {
                if (strtotime($checkIn) >= strtotime($checkOut)) {
                    return ['success' => false, 'message' => 'Check-out date must be after check-in date'];
                }
                $today = date('Y-m-d');
                if (strtotime($checkIn) < strtotime($today)) {
                    return ['success' => false, 'message' => 'Check-in date cannot be in the past'];
                }
            }

            // Enhanced availability check with time gap consideration
            $availabilityResult = $this->checkRoomAvailability($roomId, $checkIn, $checkOut, $bookingId, $checkinTime);
            if (!$availabilityResult['success']) {
                return ['success' => false, 'message' => $availabilityResult['message']];
            }

            // Start transaction
            $this->conn->beginTransaction();

            try {
                // Update booking
                $query = "UPDATE bookings 
                         SET room_id = :room_id, 
                             booking_check_in = :check_in, 
                             booking_checkin_time = :checkin_time,
                             booking_check_out = :check_out, 
                             booking_checkout_time = :checkout_time,
                             booking_total_price = :total_price, 
                             booking_status = :status 
                         WHERE booking_id = :booking_id";

                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':booking_id', $bookingId);
                $stmt->bindParam(':room_id', $roomId);
                $stmt->bindParam(':check_in', $checkIn);
                $stmt->bindParam(':checkin_time', $checkinTime);
                $stmt->bindParam(':check_out', $checkOut);
                $stmt->bindParam(':checkout_time', $checkoutTime);
                $stmt->bindParam(':total_price', $totalPrice);
                $stmt->bindParam(':status', $status);

                if (!$stmt->execute()) {
                    throw new Exception('Failed to update booking');
                }

                // Update room status based on booking status change
                if ($isStatusChanged) {
                    if (!$this->updateRoomStatus($roomId, $status)) {
                        throw new Exception('Failed to update room status');
                    }
                }

                // If status changed to 'Canceled' or 'Checked-out', delete the booking
                if ($isStatusChanged && in_array(strtolower($status), ['canceled', 'checked-out'])) {
                    // Get payment IDs associated with this booking
                    $paymentQuery = "SELECT payment_id FROM payments WHERE booking_id = :booking_id";
                    $paymentStmt = $this->conn->prepare($paymentQuery);
                    $paymentStmt->bindParam(':booking_id', $bookingId);
                    $paymentStmt->execute();
                    $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

                    // Delete invoice records first (due to foreign key constraint)
                    foreach ($payments as $payment) {
                        $invoiceQuery = "DELETE FROM invoice WHERE payment_id = :payment_id";
                        $invoiceStmt = $this->conn->prepare($invoiceQuery);
                        $invoiceStmt->bindParam(':payment_id', $payment['payment_id']);
                        
                        if (!$invoiceStmt->execute()) {
                            throw new Exception('Failed to delete invoice records');
                        }
                    }

                    // Delete payment records
                    $paymentDeleteQuery = "DELETE FROM payments WHERE booking_id = :booking_id";
                    $paymentDeleteStmt = $this->conn->prepare($paymentDeleteQuery);
                    $paymentDeleteStmt->bindParam(':booking_id', $bookingId);
                    
                    if (!$paymentDeleteStmt->execute()) {
                        throw new Exception('Failed to delete payment records');
                    }

                    // Delete booking (this will trigger audit log)
                    $bookingDeleteQuery = "DELETE FROM bookings WHERE booking_id = :booking_id";
                    $bookingDeleteStmt = $this->conn->prepare($bookingDeleteQuery);
                    $bookingDeleteStmt->bindParam(':booking_id', $bookingId);
                    
                    if (!$bookingDeleteStmt->execute()) {
                        throw new Exception('Failed to delete booking');
                    }

                    // Commit transaction
                    $this->conn->commit();

                    return ['success' => true, 'message' => 'Booking ' . strtolower($status) . ' and moved to activity logs'];
                }

                // If room was changed, update status for both old and new room
                if ($isRoomChanged) {
                    $oldRoomId = $currentBooking['room_id'];
                    
                    // Update old room status (check if it has other active bookings)
                    if (!$this->updateRoomStatus($oldRoomId, 'Checked-out')) {
                        throw new Exception('Failed to update old room status');
                    }
                    
                    // Update new room status
                    if (!$this->updateRoomStatus($roomId, $status)) {
                        throw new Exception('Failed to update new room status');
                    }
                }

                // If status changed to 'Confirmed', update payment and invoice
                if ($isStatusChanged && $status === 'Confirmed') {
                    // Check if payment exists
                    $paymentQuery = "SELECT payment_id FROM payments WHERE booking_id = :booking_id";
                    $paymentStmt = $this->conn->prepare($paymentQuery);
                    $paymentStmt->bindParam(':booking_id', $bookingId);
                    $paymentStmt->execute();
                    $payment = $paymentStmt->fetch(PDO::FETCH_ASSOC);

                    if (!$payment) {
                        // Create payment record
                        $paymentQuery = "INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status) 
                                        VALUES (:booking_id, :payment_amount, :payment_date, 'paid')";
                        
                        $paymentStmt = $this->conn->prepare($paymentQuery);
                        $paymentStmt->bindParam(':booking_id', $bookingId);
                        $paymentStmt->bindParam(':payment_amount', $totalPrice);
                        $paymentStmt->bindParam(':payment_date', date('Y-m-d'));

                        if (!$paymentStmt->execute()) {
                            throw new Exception('Failed to create payment record');
                        }

                        $paymentId = $this->conn->lastInsertId();

                        // Create invoice record
                        $invoiceQuery = "INSERT INTO invoice (invoice_total_amount, invoice_status, invoice_date_created, payment_id) 
                                        VALUES (:total_amount, 'paid', :date_created, :payment_id)";
                        
                        $invoiceStmt = $this->conn->prepare($invoiceQuery);
                        $invoiceStmt->bindParam(':total_amount', $totalPrice);
                        $invoiceStmt->bindParam(':date_created', date('Y-m-d'));
                        $invoiceStmt->bindParam(':payment_id', $paymentId);

                        if (!$invoiceStmt->execute()) {
                            throw new Exception('Failed to create invoice record');
                        }
                    }
                }

                // Commit transaction
                $this->conn->commit();

                return ['success' => true, 'message' => 'Booking updated successfully'];

            } catch (Exception $e) {
                // Rollback transaction on error
                $this->conn->rollBack();
                error_log("Transaction failed: " . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to update booking: ' . $e->getMessage()];
            }

        } catch (PDOException $e) {
            error_log("Error updating booking: " . $e->getMessage());
            return ['success' => false, 'message' => 'Database error occurred: ' . $e->getMessage()];
        }
    }

    public function deleteBooking($bookingId)
    {
        try {
            // Check if booking exists and get its details
            $query = "SELECT booking_status, room_id FROM bookings WHERE booking_id = :booking_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':booking_id', $bookingId);
            $stmt->execute();
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$booking) {
                return ['success' => false, 'message' => 'Booking not found'];
            }

            if ($booking['booking_status'] === 'Confirmed') {
                return ['success' => false, 'message' => 'Cannot delete confirmed bookings'];
            }

            $roomId = $booking['room_id'];

            // Start transaction
            $this->conn->beginTransaction();

            try {
                // Get payment IDs associated with this booking
                $paymentQuery = "SELECT payment_id FROM payments WHERE booking_id = :booking_id";
                $paymentStmt = $this->conn->prepare($paymentQuery);
                $paymentStmt->bindParam(':booking_id', $bookingId);
                $paymentStmt->execute();
                $payments = $paymentStmt->fetchAll(PDO::FETCH_ASSOC);

                // Delete invoice records first (due to foreign key constraint)
                foreach ($payments as $payment) {
                    $invoiceQuery = "DELETE FROM invoice WHERE payment_id = :payment_id";
                    $invoiceStmt = $this->conn->prepare($invoiceQuery);
                    $invoiceStmt->bindParam(':payment_id', $payment['payment_id']);
                    
                    if (!$invoiceStmt->execute()) {
                        throw new Exception('Failed to delete invoice records');
                    }
                }

                // Delete payment records
                $paymentDeleteQuery = "DELETE FROM payments WHERE booking_id = :booking_id";
                $paymentDeleteStmt = $this->conn->prepare($paymentDeleteQuery);
                $paymentDeleteStmt->bindParam(':booking_id', $bookingId);
                
                if (!$paymentDeleteStmt->execute()) {
                    throw new Exception('Failed to delete payment records');
                }

                // Delete booking
                $bookingQuery = "DELETE FROM bookings WHERE booking_id = :booking_id";
                $bookingStmt = $this->conn->prepare($bookingQuery);
                $bookingStmt->bindParam(':booking_id', $bookingId);
                
                if (!$bookingStmt->execute()) {
                    throw new Exception('Failed to delete booking');
                }

                // Update room status after deletion
                // Check if there are other active bookings for this room
                $activeBookingsQuery = "SELECT COUNT(*) FROM bookings 
                                       WHERE room_id = :room_id 
                                       AND booking_status NOT IN ('Canceled', 'Checked-out')";
                $activeBookingsStmt = $this->conn->prepare($activeBookingsQuery);
                $activeBookingsStmt->bindParam(':room_id', $roomId);
                $activeBookingsStmt->execute();
                $activeBookings = $activeBookingsStmt->fetchColumn();
                
                // Update room status based on remaining active bookings
                $newRoomStatus = $activeBookings > 0 ? 'booked' : 'available';
                $roomStatusQuery = "UPDATE rooms SET room_status = :room_status WHERE room_id = :room_id";
                $roomStatusStmt = $this->conn->prepare($roomStatusQuery);
                $roomStatusStmt->bindParam(':room_status', $newRoomStatus);
                $roomStatusStmt->bindParam(':room_id', $roomId);
                
                if (!$roomStatusStmt->execute()) {
                    throw new Exception('Failed to update room status after deletion');
                }

                // Commit transaction
                $this->conn->commit();

                return ['success' => true, 'message' => 'Booking and related records deleted successfully'];

            } catch (Exception $e) {
                // Rollback transaction on error
                $this->conn->rollBack();
                error_log("Transaction failed: " . $e->getMessage());
                return ['success' => false, 'message' => 'Failed to delete booking: ' . $e->getMessage()];
            }

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

    public function checkRoomAvailability($roomId, $checkIn, $checkOut, $currentBookingId = null, $checkinTime = '14:00')
    {
        try {
            // Get all conflicting bookings for the room (excluding deleted ones)
            $query = "SELECT booking_check_in, booking_check_out, booking_checkin_time, booking_checkout_time 
                     FROM bookings 
                     WHERE room_id = :room_id 
                     AND booking_status NOT IN ('Canceled', 'Checked-out')
                     AND (
                         (booking_check_in <= :check_out AND booking_check_out >= :check_in)
                     )";

            if ($currentBookingId) {
                $query .= " AND booking_id != :current_booking_id";
            }

            $query .= " ORDER BY booking_check_in";

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':room_id', $roomId);
            $stmt->bindParam(':check_in', $checkIn);
            $stmt->bindParam(':check_out', $checkOut);

            if ($currentBookingId) {
                $stmt->bindParam(':current_booking_id', $currentBookingId);
            }

            $stmt->execute();
            $conflicting_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($conflicting_bookings)) {
                return ['success' => true, 'message' => 'Room is available'];
            }

            // Check each conflicting booking to see if there's sufficient time gap
            foreach ($conflicting_bookings as $booking) {
                $booking_check_in = $booking['booking_check_in'];
                $booking_check_out = $booking['booking_check_out'];
                $booking_checkin_time = $booking['booking_checkin_time'];
                $booking_checkout_time = $booking['booking_checkout_time'];
                
                // If the new booking is on the same day as an existing checkout
                if ($checkIn === $booking_check_out) {
                    // Calculate time gap between checkout and check-in
                    $checkout_datetime = new DateTime($booking_check_out . ' ' . $booking_checkout_time);
                    $checkin_datetime = new DateTime($checkIn . ' ' . $checkinTime);
                    
                    $time_gap = $checkout_datetime->diff($checkin_datetime);
                    $gap_hours = $time_gap->h + ($time_gap->days * 24);
                    
                    // Allow booking if there's at least 2 hours gap
                    if ($gap_hours >= 2) {
                        continue; // This conflict is acceptable, check next one
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'
                        ];
                    }
                }
                
                // If the new booking checkout is on the same day as an existing check-in
                if ($checkOut === $booking_check_in) {
                    // Calculate time gap between new checkout and existing check-in
                    $new_checkout_datetime = new DateTime($checkOut . ' 12:00'); // Default checkout time
                    $existing_checkin_datetime = new DateTime($booking_check_in . ' ' . $booking_checkin_time);
                    
                    $time_gap = $new_checkout_datetime->diff($existing_checkin_datetime);
                    $gap_hours = $time_gap->h + ($time_gap->days * 24);
                    
                    // Allow booking if there's at least 2 hours gap
                    if ($gap_hours >= 2) {
                        continue; // This conflict is acceptable, check next one
                    } else {
                        return [
                            'success' => false,
                            'message' => 'Insufficient time gap between checkout and check-in. Need at least 2 hours gap.'
                        ];
                    }
                }
                
                // For overlapping dates (not same-day), room is not available
                return [
                    'success' => false,
                    'message' => 'Room is already booked for the selected dates'
                ];
            }

            // If we get here, all conflicts have sufficient time gaps
            return ['success' => true, 'message' => 'Room is available with time gap consideration'];
            
        } catch (PDOException $e) {
            error_log("Error checking room availability: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error checking room availability'
            ];
        }
    }

    public function updateRoomStatus($roomId, $bookingStatus)
    {
        try {
            $roomStatus = 'available'; // Default status
            
            // Determine room status based on booking status
            switch (strtolower($bookingStatus)) {
                case 'pending':
                case 'confirmed':
                case 'checked-in':
                    $roomStatus = 'booked';
                    break;
                case 'canceled':
                case 'checked-out':
                    // Check if there are other active bookings for this room
                    $query = "SELECT COUNT(*) FROM bookings 
                             WHERE room_id = :room_id 
                             AND booking_status NOT IN ('Canceled', 'Checked-out')";
                    $stmt = $this->conn->prepare($query);
                    $stmt->bindParam(':room_id', $roomId);
                    $stmt->execute();
                    $activeBookings = $stmt->fetchColumn();
                    
                    // Only set to available if no other active bookings exist
                    if ($activeBookings == 0) {
                        $roomStatus = 'available';
                    } else {
                        $roomStatus = 'booked';
                    }
                    break;
                default:
                    $roomStatus = 'available';
            }
            
            // Update room status
            $updateQuery = "UPDATE rooms SET room_status = :room_status WHERE room_id = :room_id";
            $updateStmt = $this->conn->prepare($updateQuery);
            $updateStmt->bindParam(':room_status', $roomStatus);
            $updateStmt->bindParam(':room_id', $roomId);
            
            return $updateStmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating room status: " . $e->getMessage());
            return false;
        }
    }

    public function getActivityLogs($limit = 50)
    {
        try {
            $query = "SELECT al.*, u.user_name 
                     FROM audit_logs al 
                     LEFT JOIN users u ON al.changed_by = u.user_id 
                     WHERE al.table_name = 'bookings' 
                     AND al.action = 'DELETE'
                     ORDER BY al.timestamp DESC 
                     LIMIT :limit";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching activity logs: " . $e->getMessage());
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

        case 'get_bookings_with_search':
            $search = $_POST['search'] ?? '';
            $statusFilter = $_POST['status_filter'] ?? '';
            $dateFilter = $_POST['date_filter'] ?? '';
            $bookings = $bookingManager->getBookingsWithSearch($search, $statusFilter, $dateFilter);
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
            $checkinTime = $_POST['checkin_time'] ?? '14:00';
            $rooms = $bookingManager->getAvailableRooms($checkIn, $checkOut, $currentBookingId, $checkinTime);
            echo json_encode(['success' => true, 'data' => $rooms]);
            break;

        case 'get_all_rooms':
            $rooms = $bookingManager->getAllRooms();
            echo json_encode(['success' => true, 'data' => $rooms]);
            break;

        case 'add_booking':
            $result = $bookingManager->addBooking(
                $_POST['user_id'] ?? '',
                $_POST['room_id'] ?? '',
                $_POST['check_in'] ?? '',
                $_POST['check_out'] ?? '',
                $_POST['total_price'] ?? '',
                $_POST['guest_name'] ?? null,
                null,
                $_POST['guest_phone'] ?? null,
                $_POST['checkin_time'] ?? '14:00',
                $_POST['checkout_time'] ?? '12:00'
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
                $_POST['status'] ?? '',
                $_POST['checkin_time'] ?? '14:00',
                $_POST['checkout_time'] ?? '12:00'
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

        case 'check_room_availability':
            $result = $bookingManager->checkRoomAvailability(
                $_POST['room_id'] ?? '',
                $_POST['check_in'] ?? '',
                $_POST['check_out'] ?? '',
                $_POST['current_booking_id'] ?? null,
                $_POST['checkin_time'] ?? '14:00'
            );
            echo json_encode($result);
            break;

        case 'get_activity_logs':
            $limit = $_POST['limit'] ?? 50;
            $logs = $bookingManager->getActivityLogs($limit);
            echo json_encode(['success' => true, 'data' => $logs]);
            break;

        case 'getCSRFToken':
            echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'getCSRFToken') {
        echo json_encode(['csrf_token' => $_SESSION['csrf_token']]);
    }
}
?>