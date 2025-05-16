<?php
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = password_hash(password: $_POST['password'], algo: PASSWORD_DEFAULT);

    try {
        $sql = "INSERT INTO users (user_name, user_email, user_password, user_phone)
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$name, $email, $password, $phone]);

        echo "Signup successful!";
    } catch (PDOException $e) {
        echo "Signup failed: " . $e->getMessage();
    }
}
?>
