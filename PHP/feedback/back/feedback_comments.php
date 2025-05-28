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
    $stmt = $conn_slave->prepare("
    SELECT f.feedback_rating, f.feedback_comments, f.feedback_date_created, u.user_name,
           r.room_number, r.room_type
    FROM feedback f
    JOIN users u ON f.user_id = u.user_id
    JOIN rooms r ON f.room_id = r.room_id
    ORDER BY $orderBy
");
    $stmt->execute();

    $counter = 0;

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $commentText = trim($row['feedback_comments']);

        if ($commentText === '')
            continue;

        $counter++;
        $borderClass = $counter > 1 ? 'border-t border-gray-200 pt-6' : '';

        $starsHtml = '';
        for ($i = 1; $i <= 5; $i++) {
            $starsHtml .= $i <= $row['feedback_rating']
                ? '<span class="text-yellow-400">★</span>'
                : '<span class="text-gray-300">★</span>';
        }

        echo '
        <div class="' . $borderClass . '">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                        <span class="text-blue-600 font-semibold">' . substr(htmlspecialchars($row['user_name']), 0, 1) . '</span>
                    </div>
                </div>
                <div class="flex-grow">
                    <div class="flex items-center justify-between mb-2">
                        <div>
                            <h4 class="font-semibold text-gray-900">' . htmlspecialchars($row['user_name']) . '</h4>
                            <p class="text-sm text-gray-500">' . date("M d, Y", strtotime($row['feedback_date_created'])) . '</p>
                        </div>
                        <div class="text-yellow-400 text-lg">' . $starsHtml . '</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 mb-2">
                        <p class="text-gray-700">' . htmlspecialchars($commentText) . '</p>
                    </div>
                    <p class="text-sm text-gray-500">
                        <span class="inline-flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            Room ' . htmlspecialchars($row['room_number']) . ' (' . htmlspecialchars($row['room_type']) . ')
                        </span>
                    </p>
                </div>
            </div>
        </div>';
    }

    if ($counter === 0) {
        echo '
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
            </svg>
            <p class="text-gray-500 text-lg">No reviews yet. Be the first to share your experience!</p>
        </div>';
    }

} catch (PDOException $e) {
    echo '
    <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm text-red-700">Failed to load reviews: ' . $e->getMessage() . '</p>
            </div>
        </div>
    </div>';
}