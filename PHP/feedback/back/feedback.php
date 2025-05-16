<?php
require_once '../../db.php';  // Your PDO connection is likely stored in $conn or $pdo

$user_id = 2;  // Replace with session user ID if available
$room_id = 2;  // Replace or get dynamically if you want

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
    exit;

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}