<?php
session_start();
require_once '../../db.php';

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if ($new !== $confirm) {
        $_SESSION['message'] = "New passwords do not match.";
        header("Location: profile.php");
        exit();
    }

    $stmt = $conn->prepare("SELECT user_password FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($current, $user['user_password'])) {
        $_SESSION['message'] = "Current password is incorrect.";
        header(header: "Location: ../profile.php");
        exit();
    }

    $new_hashed = password_hash($new, PASSWORD_DEFAULT);
    $update = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
    $update->execute([$new_hashed, $user_id]);

    $_SESSION['message'] = "Password updated successfully.";
    header("Location: ../profile.php");
    exit();
}
