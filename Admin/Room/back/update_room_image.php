<?php
require_once '../../../PHP/db.php';


function generateImageFilename($room_id)
{
    $timestamp = date('Ymd');
    $random = substr(md5(uniqid()), 0, 6);
    return "room_{$room_id}_{$timestamp}_{$random}.jpg";
}


function validateImage($file)
{
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!in_array($file['type'], $allowed_types)) {
        return "Only JPG, PNG, and WEBP images are allowed.";
    }

    if ($file['size'] > $max_size) {
        return "Image size must be less than 5MB.";
    }

    return null;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['room_id'])) {
    $room_id = $_POST['room_id'];
    $response = ['success' => false, 'message' => ''];

    try {

        if (isset($_FILES['room_image']) && $_FILES['room_image']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['room_image'];


            $error = validateImage($file);
            if ($error) {
                throw new Exception($error);
            }


            $stmt = $conn->prepare("SELECT room_image FROM rooms WHERE room_id = ?");
            $stmt->execute([$room_id]);
            $current_image = $stmt->fetchColumn();


            $new_filename = generateImageFilename($room_id);
            $upload_path = '/Proj/PHP/assets/' . $new_filename;


            if ($current_image && file_exists('/Proj/PHP/assets/' . $current_image)) {
                unlink('/Proj/PHP/assets/' . $current_image);
            }


            if (!move_uploaded_file($file['tmp_name'], $upload_path)) {
                throw new Exception("Failed to upload image.");
            }


            $stmt = $conn->prepare("UPDATE rooms SET room_image = ? WHERE room_id = ?");
            $stmt->execute([$new_filename, $room_id]);

            $response['success'] = true;
            $response['message'] = 'Room image updated successfully.';
            $response['new_image'] = $new_filename;
        } else {
            $response['message'] = 'No new image uploaded.';
        }
    } catch (Exception $e) {
        $response['message'] = $e->getMessage();
    }


    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}