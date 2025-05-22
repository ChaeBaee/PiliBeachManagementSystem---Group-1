<?php
session_start();
require_once '../../PHP/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['user_email'] ?? '';
    $password = $_POST['user_password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['user_password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            $_SESSION['user_role'] = $user['user_role'];

            header("Location: /Proj/Admin/index.php");
            exit;
        } else {
            $_SESSION['login_error'] = "Invalid email or password.";
        }
    } else {
        $_SESSION['login_error'] = "Please fill in all fields.";
    }

    header("Location: /Proj/Admin/index.php");
    exit;
}
?>