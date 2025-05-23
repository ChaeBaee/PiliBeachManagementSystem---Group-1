<?php
require_once '../../PHP/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $response = ['success' => false, 'message' => ''];

    try {
        $room_id = $_POST['room_id'] ?? null;
        $room_number = $_POST['room_number'] ?? '';
        $room_type = $_POST['room_type'] ?? '';
        $room_price = $_POST['room_price'] ?? 0;
        $room_status = $_POST['room_status'] ?? 'available';


        if (empty($room_number) || empty($room_type) || empty($room_price)) {
            throw new Exception('All fields are required');
        }

        $valid_statuses = ['available', 'booked', 'maintenance'];
        if (!in_array(strtolower($room_status), $valid_statuses)) {
            throw new Exception('Invalid room status');
        }

        $conn->beginTransaction();

        if ($room_id) {
            $stmt = $conn->prepare("UPDATE rooms SET room_number = ?, room_type = ?, room_price = ?, room_status = ? WHERE room_id = ?");
            $stmt->execute([$room_number, $room_type, $room_price, strtolower($room_status), $room_id]);
        } else {
            $stmt = $conn->prepare("INSERT INTO rooms (room_number, room_type, room_price, room_status) VALUES (?, ?, ?, ?)");
            $stmt->execute([$room_number, $room_type, $room_price, strtolower($room_status)]);
            $room_id = $conn->lastInsertId();
        }

        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
            // Validate file type
            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (!in_array($_FILES['room_image']['type'], $allowed_types)) {
                throw new Exception('Only JPG, PNG, and WEBP images are allowed.');
            }

            if ($_FILES['room_image']['size'] > 5 * 1024 * 1024) {
                throw new Exception('Image size must be less than 5MB.');
            }

            $stmt = $conn->prepare("SELECT room_image FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $current_image = $stmt->fetchColumn();

            $timestamp = date('Ymd');
            $random = substr(md5(uniqid()), 0, 6);
            $new_filename = "room_{$room_id}_{$timestamp}_{$random}.jpg";
            $upload_path = '../../PHP/assets/' . $new_filename;

            if ($current_image && file_exists('../../PHP/assets/' . $current_image)) {
                unlink('../../PHP/assets/' . $current_image);
            }

            if (!move_uploaded_file($_FILES['room_image']['tmp_name'], $upload_path)) {
                throw new Exception('Failed to upload image.');
            }

            $stmt = $conn->prepare("UPDATE rooms SET room_image = ? WHERE room_id = ?");
            $stmt->execute([$new_filename, $room_id]);
        }

        $conn->commit();

        $response['success'] = true;
        $response['message'] = $room_id ? 'Room updated successfully' : 'Room added successfully';

    } catch (Exception $e) {
        if ($conn->inTransaction()) {
            $conn->rollBack();
        }
        $response['message'] = $e->getMessage();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}