<?php
require_once '../../db.php';
session_start();

try {
    $sql = "SELECT * FROM rooms";
    $stmt = $conn_slave->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    exit();
}
?>