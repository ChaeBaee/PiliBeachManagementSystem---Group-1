<?php
require_once '../../db.php';

try {
    $stmt = $conn->query("
        SELECT f.feedback_rating, f.feedback_comments, f.feedback_date_created, u.user_name
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        ORDER BY f.feedback_date_created DESC
    ");

    $counter = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $counter++;
        $borderClass = $counter > 1 ? 'border-t pt-4' : '';

        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            $starsHtml .= $i <= $row['feedback_rating']
                ? '<span>★</span>'
                : '<span class="text-gray-300">★</span>';
        }

        echo '
            <div class="flex gap-4 ' . $borderClass . '">
                <div class="w-12 h-12 bg-gray-500 rounded-full"></div>
                <div>
                    <p class="font-semibold">' . htmlspecialchars($row['user_name']) . '</p>
                    <p class="text-xs text-gray-500">' . date("M d, Y", strtotime($row['feedback_date_created'])) . '</p>
                    <div class="text-yellow-400 text-sm">' . $starsHtml . '</div>
                    <p class="text-sm text-gray-700 mt-1">' . htmlspecialchars($row['feedback_comments']) . '</p>
                </div>
            </div>
        ';
    }
} catch (PDOException $e) {
    echo "<p class='text-red-500'>Failed to load reviews: " . $e->getMessage() . "</p>";
}