<?php

$servername = '127.0.0.1';
$username = 'root';
$password = '';

try {
    $conn = new PDO('mysql:host=127.0.0.1;port=3307;dbname=pili_db;', username: $username, password: $password);    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>