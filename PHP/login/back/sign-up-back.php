<?php
session_start();

include '../../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    try {
        $checkSql = "SELECT * FROM users WHERE user_email = ?";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->execute([$email]);

        if ($checkStmt->rowCount() > 0) {
            echo "<script>
                    alert('Email already exists.');
                    window.history.back();
                  </script>";
            exit;
        }

        $sql = "INSERT INTO users (user_name, user_email, user_password, user_phone)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $password, $phone]);


        $userId = $conn->lastInsertId();
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $name;

        echo "<script>
                alert('Signup successful! Logged in as $name.');
                window.location.href = '/Proj/PiliBeachManagementSystem---Group-1/PHP/home.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>alert('Signup failed: " . $e->getMessage() . "');</script>";
    }
}
?>