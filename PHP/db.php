<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pilibeach_db";

try {
    $conn = new PDO("mysql:host=localhost;port=3007;dbname=pilibeach_db", "root", "");    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>