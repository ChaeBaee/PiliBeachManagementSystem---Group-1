<?php
/**
 * API Handler for Vercel Deployment
 * Routes API requests to appropriate backend handlers
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, DELETE, PUT');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Parse the request path
$request_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_path = str_replace('/api/', '', $request_path);

// Route requests
$routes = [
    'auth/login' => 'PHP/login/back/login_handler.php',
    'auth/signup' => 'PHP/login/back/sign-up-back.php',
    'auth/logout' => 'PHP/logout.php',
    
    'bookings' => 'PHP/booking/back/bookings_backend.php',
    'booking/check-availability' => 'PHP/booking/back/check_availability.php',
    
    'rooms' => 'Admin/Room/back/room-back.php',
    'room' => 'Admin/Room/back/get_room.php',
    
    'users' => 'Admin/Users/back/users_backend.php',
    
    'payments' => 'Admin/Payment/back/payments_backend.php',
    
    'contact' => 'PHP/contact/back/contact_backend.php',
];

// Find matching route
$handler = null;
foreach ($routes as $route => $file) {
    if (strpos($request_path, $route) !== false) {
        $handler = $file;
        break;
    }
}

if ($handler && file_exists($handler)) {
    include $handler;
} else {
    http_response_code(404);
    echo json_encode(['error' => 'API endpoint not found']);
}

