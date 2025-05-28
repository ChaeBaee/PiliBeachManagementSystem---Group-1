<?php
session_start();

include '../../db.php';

$conn->exec("SET @current_user_id = 0");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Get and sanitize input
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $termsAccepted = isset($_POST['terms']);

    // Store old input for form repopulation
    $_SESSION['old_name'] = $name;
    $_SESSION['old_email'] = $email;
    $_SESSION['old_phone'] = $phone;
    $_SESSION['old_terms'] = $termsAccepted;

    // Initialize error messages
    $hasErrors = false;

    // Validate email
    if (empty($email)) {
        $_SESSION['emailMsg'] = "Email is required.";
        $hasErrors = true;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['emailMsg'] = "Please enter a valid email address.";
        $hasErrors = true;
    }

    // Validate password
    if (empty($password)) {
        $_SESSION['passwordMsg'] = "Password is required.";
        $hasErrors = true;
    } elseif (strlen($password) < 8) {
        $_SESSION['passwordMsg'] = "Password must be at least 8 characters long.";
        $hasErrors = true;
    } elseif ($password !== $confirmPassword) {
        $_SESSION['passwordMsg'] = "Passwords do not match.";
        $hasErrors = true;
    }

    // Validate name
    if (empty($name)) {
        $_SESSION['fullNameMsg'] = "Full name is required.";
        $hasErrors = true;
    } elseif (strlen($name) < 2) {
        $_SESSION['fullNameMsg'] = "Name must be at least 2 characters long.";
        $hasErrors = true;
    }

    // Validate phone number
    if (empty($phone)) {
        $_SESSION['contactNoMsg'] = "Contact number is required.";
        $hasErrors = true;
    } elseif (!preg_match('/^(09|\+639)\d{9}$/', $phone)) {
        $_SESSION['contactNoMsg'] = "Please enter a valid Philippine mobile number (09xxxxxxxxx or +639xxxxxxxxx).";
        $hasErrors = true;
    }

    // Validate terms
    if (!$termsAccepted) {
        $_SESSION['termsMsg'] = "You must agree to the terms and conditions.";
        $hasErrors = true;
    }

    // If there are validation errors, redirect back to sign-up page
    if ($hasErrors) {
        header("Location: ../front/sign-up.php");
        exit;
    }

    try {
        // Check if email already exists
        $checkSql = "SELECT * FROM users WHERE user_email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            $_SESSION['emailMsg'] = "Email already exists.";
            header("Location: ../front/sign-up.php");
            exit;
        }

        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $sql = "INSERT INTO users (user_name, user_email, user_password, user_phone, user_role) 
                VALUES (?, ?, ?, ?, 'Guest')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $hashedPassword, $phone]);

        // Get the new user's ID
        $userId = $conn->lastInsertId();

        // Set session variables
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;
        $_SESSION['user_role'] = 'Guest';
        $_SESSION['user_email'] = $email;

        // Clear any old input data
        unset($_SESSION['old_name'], $_SESSION['old_email'], $_SESSION['old_phone'], $_SESSION['old_terms']);

        // Redirect to home page
        header("Location: /Proj/PHP/home.php");
        exit;

    } catch (PDOException $e) {
        $_SESSION['error'] = "Signup failed: " . $e->getMessage();
        header("Location: ../front/sign-up.php");
        exit;
    }
} else {
    // If not POST request, redirect to sign-up page
    header("Location: ../front/sign-up.php");
    exit;
}
?>