<?php
require_once '../../PHP/db.php';

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($room) {
            header('Content-Type: application/json');
            echo json_encode($room);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Room not found']);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Database error']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Room ID not provided']);
}