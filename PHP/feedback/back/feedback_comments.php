<?php
require_once '../../db.php';

$allowedSorts = [
    'rating_asc' => 'f.feedback_rating ASC',
    'rating_desc' => 'f.feedback_rating DESC',
    'date_asc' => 'f.feedback_date_created ASC',
    'date_desc' => 'f.feedback_date_created DESC'
];

$sort = $_GET['sort'] ?? 'date_desc';
$orderBy = $allowedSorts[$sort] ?? $allowedSorts['date_desc'];
try {
    $stmt = $conn->query("
        SELECT f.feedback_rating, f.feedback_comments, f.feedback_date_created, u.user_name
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        ORDER BY $orderBy
    ");

    $counter = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $commentText = trim($row['feedback_comments']);

        if ($commentText === '') continue;

        $counter++;
        $borderClass = $counter > 1 ? 'border-t pt-4' : '';

        // Generate star HTML
        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            $starsHtml .= $i <= $row['feedback_rating']
                ? '<span>★</span>'
                : '<span class="text-gray-300">★</span>';
        }

        echo '
            <div class="flex gap-4 ' . $borderClass . '">
                <div>
                    <p class="font-semibold">' . htmlspecialchars($row['user_name']) . '</p>
                    <p class="text-xs text-gray-500">' . date("M d, Y", strtotime($row['feedback_date_created'])) . '</p>
                    <div class="text-yellow-400 text-sm">' . $starsHtml . '</div>
                    <p class="text-sm text-gray-700 mt-1">' . htmlspecialchars($commentText) . '</p>
                </div>
            </div>
        ';
    }

    if ($counter === 0) {
        echo "<p class='text-gray-500'>No feedback available.</p>";
    }

} catch (PDOException $e) {
    echo "<p class='text-red-500'>Failed to load reviews: " . $e->getMessage() . "</p>";
}