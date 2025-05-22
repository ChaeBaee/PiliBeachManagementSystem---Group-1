<?php

include '../../PHP/db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_name = trim($_POST['user_name'] ?? '');
    $user_email = trim($_POST['user_email'] ?? '');
    $user_password = $_POST['user_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $user_phone = trim($_POST['user_phone'] ?? '');


    if (!$user_name || !$user_email || !$user_password || !$confirm_password || !$user_phone) {
        die("All fields are required.");
    }


    if ($user_password !== $confirm_password) {
        die("Passwords do not match.");
    }


    if (!preg_match('/^(09|639)\d{9}$/', $user_phone)) {
        die("Phone number must start with 09 or 639 and be 11 digits long.");
    }


    if (!filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }


    $stmt = $conn->prepare("SELECT user_email FROM users WHERE user_email = ?");
    $stmt->execute([$user_email]);
    if ($stmt->rowCount() > 0) {
        die("Email is already registered.");
    }


    $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);


    $user_role = 'Admin';


    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role, user_phone) VALUES (?, ?, ?, ?, ?)");
    $success = $stmt->execute([$user_name, $user_email, $hashed_password, $user_role, $user_phone]);

    if ($success) {
        $user_id = $conn->lastInsertId();

        $_SESSION['user_id'] = $user_id;
        $_SESSION['user_name'] = $user_name;
        $_SESSION['user_email'] = $user_email;
        $_SESSION['user_role'] = $user_role;

        header("Location: /Proj/Admin/index.php");
        exit();
    } else {
        die("Failed to register user.");
    }
}
?>