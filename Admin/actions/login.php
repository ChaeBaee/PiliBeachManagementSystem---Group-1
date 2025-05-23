<?php
session_start();
require_once '../../PHP/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['user_email'] ?? '';
    $password = $_POST['user_password'] ?? '';

    if (!empty($email) && !empty($password)) {
        try {
            // Only check for users with Admin role
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = :email AND user_role = 'Admin' LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['user_password'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_role'] = $user['user_role'];

                header("Location: /Proj/Admin/index.php");
                exit;
            } else {
                $_SESSION['login_error'] = "Invalid email or password.";
                header("Location: /Proj/Admin/login.php");
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['login_error'] = "An error occurred. Please try again later.";
            // For debugging: error_log($e->getMessage());
            header("Location: /Proj/Admin/login.php");
            exit;
        }
    } else {
        $_SESSION['login_error'] = "Please fill in all fields.";
        header("Location: /Proj/Admin/login.php");
        exit;
    }
}
?>