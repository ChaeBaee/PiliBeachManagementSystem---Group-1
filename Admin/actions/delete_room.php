<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../login.php");
    exit;
}

require_once '../../PHP/db.php';

if (isset($_GET['id'])) {
    $room_id = $_GET['id'];

    try {
        $stmt = $conn->prepare("SELECT room_image FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt = $conn->prepare("DELETE FROM rooms WHERE room_id = ?");
        $stmt->execute([$room_id]);

        if ($stmt->rowCount() > 0 && $room['room_image']) {
            $image_path = "../../PHP/assets/" . $room['room_image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }

        header("Location: ../rooms.php");
        exit;
    } catch (PDOException $e) {
        error_log("Error deleting room: " . $e->getMessage());
        header("Location: ../rooms.php");
        exit;
    }
} else {
    header("Location: ../rooms.php");
    exit;
}