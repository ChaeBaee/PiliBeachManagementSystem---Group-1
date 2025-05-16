<?php
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

        echo "<script>
                alert('Signup successful!');
                window.location.href = 'login.php';
              </script>";
    } catch (PDOException $e) {
        echo "<script>alert('Signup failed: " . $e->getMessage() . "');</script>";
    }
}
?>
