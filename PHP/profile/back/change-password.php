<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /Proj/PHP/login/front/login.php");
    exit();
}

$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $hasErrors = false;

    if (empty($current_password)) {
        $_SESSION['currentPasswordMsg'] = "Current password is required.";
        $hasErrors = true;
    }

    if (empty($new_password)) {
        $_SESSION['newPasswordMsg'] = "New password is required.";
        $hasErrors = true;
    } elseif (strlen($new_password) < 8) {
        $_SESSION['newPasswordMsg'] = "New password must be at least 8 characters long.";
        $hasErrors = true;
    } elseif ($new_password === $current_password) {
        $_SESSION['newPasswordMsg'] = "New password must be different from current password.";
        $hasErrors = true;
    }

    if (empty($confirm_password)) {
        $_SESSION['confirmPasswordMsg'] = "Please confirm your new password.";
        $hasErrors = true;
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['confirmPasswordMsg'] = "Passwords do not match.";
        $hasErrors = true;
    }

    if ($hasErrors) {
        header("Location: ../profile.php");
        exit();
    }

    try {
        $stmt = $conn->prepare("SELECT user_password FROM users WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($current_password, $user['user_password'])) {
            $_SESSION['currentPasswordMsg'] = "Current password is incorrect.";
            header("Location: ../profile.php");
            exit();
        }

        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
        $result = $update->execute([$hashed_password, $user_id]);

        if ($result) {
            $_SESSION['message'] = "Password updated successfully.";
        } else {
            $_SESSION['message'] = "Failed to update password.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "An error occurred while updating your password.";
    }
}

header("Location: ../profile.php");
exit();
?>