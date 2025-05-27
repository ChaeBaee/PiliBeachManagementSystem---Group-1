<?php
session_start();
require_once '../../db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /login/front/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = $_POST['room_id'] ?? null;

if (!$room_id) {
    die("No room selected for feedback.");
}

// Check if the user has booked this room
$stmt = $conn->prepare("SELECT COUNT(*) FROM bookings WHERE user_id = :user_id AND room_id = :room_id");
$stmt->execute([
    ':user_id' => $user_id,
    ':room_id' => $room_id,
]);
if ($stmt->fetchColumn() == 0) {
    die("Invalid room selection.");
}

$date = date('Y-m-d');

$stmt = $conn->prepare("SELECT COUNT(*) FROM feedback WHERE user_id = :user_id AND room_id = :room_id AND feedback_date_created = :date");
$stmt->execute([
    ':user_id' => $user_id,
    ':room_id' => $room_id,
    ':date' => $date,
]);

if ($stmt->fetchColumn() > 0) {
    header("Location: ../front/feedback.php?error=already_submitted");
    exit();
}

$rating = $_POST['feedback_rating'] ?? 0;
$comment = $_POST['feedback_comments'] ?? '';

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
    echo "Error:  " . $e->getMessage();
}