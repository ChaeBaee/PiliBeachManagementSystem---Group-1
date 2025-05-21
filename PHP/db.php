<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pili_db";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}


try {
    $conn_slave = new PDO("mysql:host=$servername;port=3307;dbname=$dbname", $username, $password);
    $conn_slave->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Slave connection failed: " . $e->getMessage();
}
?>