<?php
require_once '../../db.php';

try {
    $stmt = $conn->query("SELECT COUNT(*) AS total_reviews, AVG(feedback_rating) AS avg_rating FROM feedback");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $totalReviews = $result['total_reviews'] ?? 0;
    $avgRating = $result['avg_rating'] !== null ? number_format($result['avg_rating'], 1) : "0.0";

    $ratingCounts = array_fill(1, 5, 0);  // initialize with 0 counts
    $stmt = $conn->query("SELECT feedback_rating, COUNT(*) AS count FROM feedback GROUP BY feedback_rating");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rating = (int)$row['feedback_rating'];
        $count = (int)$row['count'];
        $ratingCounts[$rating] = $count;
    }
} catch (PDOException $e) {
    $totalReviews = 0;
    $avgRating = "0.0";
    $ratingCounts = array_fill(1, 5, 0);
}
?>