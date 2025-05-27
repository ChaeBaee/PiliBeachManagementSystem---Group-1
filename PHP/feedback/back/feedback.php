<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/front/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = 2;

$rating = $_POST['feedback_rating'] ?? 0;
$comment = $_POST['feedback_comments'] ?? '';
$date = date('Y-m-d');

try {
    $sql = "INSERT INTO feedback (user_id, room_id, feedback_rating, feedback_comments, feedback_date_created)
            VALUES (:user_id, :room_id, :rating, :comment, :date_created)";

    $stmt = $conn->prepare($sql);

    $stmt->execute([
        ':user_id' => $user_id,
        ':room_id' => $room_id,
        ':rating' => $rating,
        ':comment' => $comment,
        ':date_created' => $date,
    ]);

    header("Location: ../front/feedback.php?success=1");
    exit();

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}