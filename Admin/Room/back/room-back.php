<?php
session_start();


if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: ../Login/front/login.php");
    exit;
}


$room_id = "";
$room_number = $room_type = $room_price = $room_status = "";
$room_numberMsg = $room_typeMsg = $room_priceMsg = $room_statusMsg = "";


$isEdit = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $room_id = $_POST['room_id'] ?? "";
    $room_number = trim($_POST['room_number'] ?? "");
    $room_type = trim($_POST['room_type'] ?? "");
    $room_price = trim($_POST['room_price'] ?? "");
    $room_status = trim($_POST['room_status'] ?? "");

    $isEdit = !empty($room_id);

    if (empty($room_number)) {
        $room_numberMsg = "Room Number is required.";
    } elseif (!preg_match("/^[A-Za-z0-9-]+$/", $room_number)) {
        $room_numberMsg = "Room Number can only contain letters, numbers, and hyphens.";
    } else {

        $stmt = $conn->prepare("SELECT COUNT(*) FROM rooms WHERE room_number = ? AND room_id != ?");
        $stmt->execute([$room_number, $room_id]);
        if ($stmt->fetchColumn() > 0) {
            $room_numberMsg = "Room Number already exists.";
        }
    }

    if (empty($room_type)) {
        $room_typeMsg = "Room Type is required.";
    } elseif (!in_array($room_type, ['Deluxe Room', 'Standard Room', 'Single Room', 'Double Room'])) {
        $room_typeMsg = "Invalid room type selected.";
    }

    if (empty($room_price)) {
        $room_priceMsg = "Room Price is required.";
    } elseif (!is_numeric($room_price)) {
        $room_priceMsg = "Room Price must be a number.";
    } elseif ($room_price <= 0) {
        $room_priceMsg = "Room Price must be greater than 0.";
    } elseif ($room_price > 1000000) {
        $room_priceMsg = "Room Price cannot exceed ₱1,000,000.";
    }

    if (empty($room_status)) {
        $room_statusMsg = "Room Status is required.";
    } elseif (!in_array(strtolower($room_status), ['available', 'booked', 'maintenance'])) {
        $room_statusMsg = "Invalid room status selected.";
    }

    if (
        empty($room_numberMsg) &&
        empty($room_typeMsg) &&
        empty($room_priceMsg) &&
        empty($room_statusMsg)
    ) {
        header("Location: rooms.php");
        exit;
    }
}


$hasErrors = !empty($room_numberMsg) || !empty($room_typeMsg) || !empty($room_priceMsg) || !empty($room_statusMsg);
?>
