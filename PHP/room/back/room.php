<?php
require_once '../../db.php';
session_start();

try {
    $sql = "SELECT * FROM rooms";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rooms = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Room query error: " . $e->getMessage());
    echo "Database error: " . $e->getMessage();
    exit();
}
?>