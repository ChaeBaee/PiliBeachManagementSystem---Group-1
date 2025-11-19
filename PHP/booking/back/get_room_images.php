<?php
require_once '../../db.php';

header('Content-Type: application/json');

if (!isset($_GET['room_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Room ID is required']);
    exit;
}

$room_id = intval($_GET['room_id']);

try {
    // Fetch all images for the room, ordered by display_order and is_primary
    $stmt = $conn->prepare("
        SELECT image_id, image_filename, image_title, image_description, is_primary, display_order
        FROM room_images 
        WHERE room_id = ? 
        ORDER BY is_primary DESC, display_order ASC, image_id ASC
    ");
    $stmt->execute([$room_id]);
    $images = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // If no images found in room_images table, fall back to the main room image
    if (empty($images)) {
        $stmt = $conn->prepare("SELECT room_image FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($room && $room['room_image']) {
            $images = [[
                'image_id' => 0,
                'image_filename' => $room['room_image'],
                'image_title' => 'Room View',
                'image_description' => null,
                'is_primary' => 1,
                'display_order' => 1
            ]];
        }
    }
    
    echo json_encode([
        'success' => true,
        'images' => $images
    ]);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?> 