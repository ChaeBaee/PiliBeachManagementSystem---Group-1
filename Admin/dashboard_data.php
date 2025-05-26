<?php
require_once '../PHP/db.php';

try {
    if (!$conn) {
        throw new PDOException("Database connection failed");
    }

    // Total Bookings
    $stmt = $conn->query("SELECT COUNT(*) as total_bookings FROM bookings");
    $totalBookings = $stmt->fetch(PDO::FETCH_ASSOC)['total_bookings'] ?? 0;

    // Total Payments Collected
    $stmt = $conn->query("SELECT COALESCE(SUM(payment_amount), 0) as total_payments FROM payments WHERE payment_status = 'paid'");
    $totalPayments = $stmt->fetch(PDO::FETCH_ASSOC)['total_payments'];

    // Pending Bookings
    $stmt = $conn->query("SELECT COUNT(*) as pending_bookings FROM bookings WHERE booking_status = 'Pending'");
    $pendingBookings = $stmt->fetch(PDO::FETCH_ASSOC)['pending_bookings'] ?? 0;

    // Total Invoices
    $stmt = $conn->query("SELECT COUNT(*) as total_invoices FROM invoice");
    $totalInvoices = $stmt->fetch(PDO::FETCH_ASSOC)['total_invoices'] ?? 0;

    // Revenue by Room Type
    $stmt = $conn->query("
        SELECT r.room_type, COALESCE(SUM(p.payment_amount), 0) as total_revenue
        FROM rooms r
        LEFT JOIN bookings b ON r.room_id = b.room_id
        LEFT JOIN payments p ON b.booking_id = p.booking_id AND p.payment_status = 'paid'
        GROUP BY r.room_type
    ");
    $revenueByRoomType = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Recent Bookings with User and Room Details
    $stmt = $conn->query("
        SELECT b.*, u.user_name, r.room_number, r.room_type
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN rooms r ON b.room_id = r.room_id
        ORDER BY b.booking_check_in DESC
        LIMIT 5
    ");
    $recentBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Room Occupancy Status
    $stmt = $conn->query("
        SELECT 
            COUNT(CASE WHEN room_status = 'available' THEN 1 END) as available_rooms,
            COUNT(CASE WHEN room_status = 'booked' THEN 1 END) as booked_rooms,
            COUNT(CASE WHEN room_status = 'maintenance' THEN 1 END) as maintenance_rooms
        FROM rooms
    ");
    $roomStatus = $stmt->fetch(PDO::FETCH_ASSOC);

    // Monthly Revenue (Last 6 months)
    $stmt = $conn->query("
        SELECT 
            DATE_FORMAT(p.payment_date, '%Y-%m') as month,
            COALESCE(SUM(p.payment_amount), 0) as revenue
        FROM payments p
        WHERE p.payment_status = 'paid'
        AND p.payment_date >= DATE_SUB(CURRENT_DATE, INTERVAL 6 MONTH)
        GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
        ORDER BY month ASC
    ");
    $monthlyRevenue = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Return all data as JSON
    $dashboardData = [
        'totalBookings' => (int) $totalBookings,
        'totalPayments' => (float) $totalPayments,
        'pendingBookings' => (int) $pendingBookings,
        'totalInvoices' => (int) $totalInvoices,
        'revenueByRoomType' => $revenueByRoomType,
        'recentBookings' => $recentBookings,
        'roomStatus' => $roomStatus,
        'monthlyRevenue' => $monthlyRevenue
    ];

    header('Content-Type: application/json');
    echo json_encode($dashboardData);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>