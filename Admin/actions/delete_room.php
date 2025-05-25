<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../../PHP/db.php';

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    try {
        // First get the room image to delete it
        $stmt = $conn->prepare("SELECT room_image FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        // Delete the room from database
        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);

        // If room was deleted and had an image, delete the image file
        if ($stmt->rowCount() > 0 && $room['room_image']) {
            $image_path = "../../PHP/assets/" . $room['room_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        header("Location: ../rooms.php");
        exit;
    } catch (PDOException $e) {
        // Log error and redirect back
        error_log("Error deleting room: " . $e->getMessage());
        header("Location: ../rooms.php");
        exit;
    }
} else {
    header("Location: ../rooms.php");
    exit;
}