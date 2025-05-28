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
    $name = trim($_POST['user_name']);
    $email = trim($_POST['user_email']);
    $phone = trim($_POST['user_phone']);

    $hasErrors = false;

    if (empty($name)) {
        $_SESSION['fullNameMsg'] = "Full name is required.";
        $hasErrors = true;
    } elseif (strlen($name) < 2) {
        $_SESSION['fullNameMsg'] = "Name must be at least 2 characters long.";
        $hasErrors = true;
    }

    if (empty($email)) {
        $_SESSION['emailMsg'] = "Email is required.";
        $hasErrors = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['emailMsg'] = "Please enter a valid email address.";
        $hasErrors = true;
    }

    if (empty($phone)) {
        $_SESSION['phoneNoMsg'] = "Phone number is required.";
        $hasErrors = true;
    } elseif (!preg_match('/^(09|\+639)\d{9}$/', $phone)) {
        $_SESSION['phoneNoMsg'] = "Please enter a valid Philippine mobile number (09xxxxxxxxx or +639xxxxxxxxx).";
        $hasErrors = true;
    }

    if ($hasErrors) {
        header("Location: ../profile.php");
        exit();
    }

    try {
        $checkStmt = $conn->prepare("SELECT user_id FROM users WHERE user_email = ? AND user_id != ?");
        $checkStmt->execute([$email, $user_id]);

        if ($checkStmt->rowCount() > 0) {
            $_SESSION['emailMsg'] = "This email is already taken by another user.";
            header("Location: ../profile.php");
            exit();
        }

        $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ?, user_phone = ? WHERE user_id = ?");
        $result = $stmt->execute([$name, $email, $phone, $user_id]);

        if ($result) {
            $_SESSION['user_name'] = $name; // Update session name
            $_SESSION['user_email'] = $email; // Update session email
            $_SESSION['message'] = "Profile updated successfully.";
        } else {
            $_SESSION['message'] = "Failed to update profile.";
        }
    } catch (PDOException $e) {
        $_SESSION['message'] = "An error occurred while updating your profile.";
    }
}

header("Location: ../profile.php");
exit();
