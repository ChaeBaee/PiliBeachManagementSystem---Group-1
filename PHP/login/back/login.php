<?php
session_start();
include '../../db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if (password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['user_email'] = $user['user_email'];

            header("Location: /Proj/PiliBeachManagementSystem---Group-1/PHP/home.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("Location: ../front/login.php");
            exit();
        }
    } else {
        $_SESSION['error'] = "Email not found.";
        header("Location: ../front/login.php");
        exit();
    }
}
?>