<?php
session_start();
require_once '../../../PHP/db.php';

$conn->exec("SET @current_user_id = 0");

$emailMsg = $passwordMsg = $fullNameMsg = $contactNoMsg = $confirmPassword = "";
$email = $password = $fullName = $contactNo = $confirmPasswordMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["user_email"] ?? "");
    $password = $_POST["user_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $fullName = trim($_POST["user_name"] ?? "");
    $contactNo = trim($_POST["user_phone"] ?? "");

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailMsg = "Please enter a valid email.";
    }

    if (empty($password)) {
        $passwordMsg = "Password cannot be empty.";
    } else if (strlen($password) < 8) {
        $passwordMsg = "Password must be at least 8 characters.";
    }

    if ($password != $confirmPassword) {
        $confirmPasswordMsg = "Password do not match.";
    }

    if (empty($fullName)) {
        $fullNameMsg = "Full name cannot be empty.";
    }

    if (empty($contactNo)) {
        $contactNoMsg = "Contact number cannot be empty.";
    } elseif (!preg_match("/^(09\d{9}|639\d{9})$/", $contactNo)) {
        $contactNoMsg = "Contact number must start with 09 or 639 and be 11 digits.";
    }

    if ($emailMsg === "" && $passwordMsg === "" && $fullNameMsg === "" && $contactNoMsg === "" && $confirmPasswordMsg === "") {
        try {

            $stmt = $conn->prepare("SELECT user_email FROM users WHERE user_email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $emailMsg = "Email is already registered.";
            } else {

                $stmt = $conn->prepare("SELECT COUNT(*) as admin_count FROM users WHERE user_role = 'Admin'");
                $stmt->execute();
                $adminCount = $stmt->fetch(PDO::FETCH_ASSOC)['admin_count'];

                if ($adminCount >= 3) {
                    $emailMsg = "Maximum number of admin accounts (3) has been reached.";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $user_role = 'Admin';

                    $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role, user_phone) VALUES (?, ?, ?, ?, ?)");
                    $success = $stmt->execute([$fullName, $email, $hashed_password, $user_role, $contactNo]);

                    if ($success) {
                        $user_id = $conn->lastInsertId();

                        $_SESSION['user_id'] = $user_id;
                        $_SESSION['user_name'] = $fullName;
                        $_SESSION['user_role'] = $user_role;

                        $currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
                        $conn->exec("SET @current_user_id = $currentUserId");

                        // Return success response
                        $response = [
                            'success' => true,
                            'redirect' => 'index.php'
                        ];
                        header('Content-Type: application/json');
                        echo json_encode($response);
                        exit();
                    } else {
                        $emailMsg = "Registration failed. Please try again.";
                    }
                }
            }
        } catch (PDOException $e) {
            $emailMsg = "An error occurred. Please try again later.";
        }
    }
}

$response = [
    'success' => false,
    'emailMsg' => $emailMsg,
    'passwordMsg' => $passwordMsg,
    'fullNameMsg' => $fullNameMsg,
    'contactNoMsg' => $contactNoMsg,
    'confirmPasswordMsg' => $confirmPasswordMsg
];

header('Content-Type: application/json');
echo json_encode($response);