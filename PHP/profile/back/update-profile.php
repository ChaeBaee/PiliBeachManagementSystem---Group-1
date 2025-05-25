<?php
session_start();
require_once '../../db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $name = $_POST['user_name'];
    $email = $_POST['user_email'];
    $phone = $_POST['user_phone'];

    $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ?, user_phone = ? WHERE user_id = ?");
    $result = $stmt->execute([$name, $email, $phone, $user_id]);

    $_SESSION['message'] = $result ? "Profile updated successfully." : "Failed to update profile.";
}

header("Location: ../../profile/profile.php");
exit();
