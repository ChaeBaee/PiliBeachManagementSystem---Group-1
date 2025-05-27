<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reviews - Paradise Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .rating-star {
            transition: all 0.2s ease;
        }

        .rating-star:hover {
            transform: scale(1.2);
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
    <script>
        function setRating(star) {
            const stars = document.querySelectorAll(".rating-star");
            stars.forEach((s, i) => {
                s.classList.toggle("text-yellow-400", i < star);
                s.classList.toggle("text-gray-300", i >= star);
            });
            document.getElementById("selectedRating").value = star;
        }
    </script>
</head>

<body class="min-h-screen flex flex-col text-gray-800">
    <?php include('../back/feedback_summary.php'); ?>
    <?php include('../../elements/navbar.php'); ?>

    <!-- Added decorative top section -->
    <div class="bg-gradient-to-b from-blue-50 to-transparent h-32"></div>

    <div class="max-w-6xl mx-auto w-full px-6 -mt-4">
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-6 animate-fade-in">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p>Thank you for your feedback! Your review helps us improve our services.</p>
                </div>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-3 gap-8 mb-12">
            <!-- Rating Summary -->
            <div class="glass-card rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300">
                <h2 class="text-2xl font-bold mb-6">Overall Rating</h2>
                <div class="flex items-center gap-4 mb-6">
                    <div class="text-6xl font-bold text-blue-600"><?= $avgRating ?></div>
                    <div class="space-y-2">
                        <div class="text-yellow-400 flex gap-1 text-2xl">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= round($avgRating) ? "<span>★</span>" : "<span class='text-gray-300'>★</span>";
                            }
                            ?>
                        </div>
                        <p class="text-sm text-gray-600">Based on <?= $totalReviews ?> reviews</p>
                    </div>
                </div>

                <div class="space-y-3">
                    <?php
                    for ($star = 5; $star >= 1; $star--) {
                        $count = $ratingCounts[$star] ?? 0;
                        $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        echo '
                        <div class="flex items-center gap-2">
                            <span class="w-4 text-sm font-medium">' . $star . '</span>
                            <div class="flex-grow h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="bg-yellow-400 h-full rounded-full transition-all duration-500" style="width: ' . $percent . '%;"></div>
                            </div>
                            <span class="text-sm text-gray-600 w-8 text-right">' . $count . '</span>
                        </div>';
                    }
                    ?>
                </div>
            </div>

            <!-- Feedback Form -->
            <div
                class="glass-card rounded-2xl shadow-xl p-8 md:col-span-2 transform hover:scale-[1.02] transition-all duration-300">
                <div class="mb-8">
                    <h3 class="text-2xl font-bold mb-3">Share Your Experience</h3>
                    <p class="text-gray-600">
                        We value your feedback! Help us improve our services by sharing your thoughts about your stay.
                    </p>
                </div>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <form action="../../../PHP/feedback/back/feedback.php" method="POST" class="space-y-6">
                        <?php
                        require_once '../../db.php';
                        $stmt = $conn->prepare("SELECT b.room_id, r.room_number, r.room_type
                            FROM bookings b
                            JOIN rooms r ON b.room_id = r.room_id
                            WHERE b.user_id = :user_id
                            AND b.booking_status IN ('Confirmed', 'Checked-in', 'Checked-out')");
                        $stmt->execute([':user_id' => $_SESSION['user_id']]);
                        $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        ?>

                        <?php if (count($rooms) > 0): ?>
                            <div>
                                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-2">Select Room</label>
                                <select name="room_id" id="room_id" required
                                    class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                    <?php foreach ($rooms as $room): ?>
                                        <option value="<?= $room['room_id'] ?>">
                                            Room <?= htmlspecialchars($room['room_number']) ?> -
                                            <?= htmlspecialchars($room['room_type']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        <?php else: ?>
                            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700">
                                            You need to book a room before leaving feedback.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Your Rating</label>
                            <div class="flex gap-2 justify-center">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <button type="button" class="rating-star text-yellow-400 text-3xl"
                                        onclick="setRating(<?= $i ?>)">★</button>
                                <?php endfor; ?>
                            </div>
                            <input type="hidden" id="selectedRating" name="feedback_rating" value="5" />
                        </div>

                        <div>
                            <label for="feedback_comments" class="block text-sm font-medium text-gray-700 mb-2">Your
                                Feedback</label>
                            <textarea name="feedback_comments" id="feedback_comments"
                                class="w-full h-32 p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                placeholder="Tell us about your experience..."></textarea>
                        </div>

                        <div class="flex gap-4">
                            <button type="reset"
                                class="flex-1 bg-white border border-gray-300 text-gray-700 py-3 px-6 rounded-lg hover:bg-gray-50 transition-all duration-300">
                                Reset
                            </button>
                            <button type="submit"
                                class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition-all duration-300 transform hover:scale-[1.02]">
                                Submit Review
                            </button>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Please <a href="/Proj/PHP/login/front/login.php"
                                        class="font-medium text-blue-600 hover:text-blue-500">log in</a> to submit your
                                    feedback.
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Reviews Section -->
            <div
                class="glass-card rounded-2xl shadow-xl p-8 md:col-span-3 transform hover:scale-[1.02] transition-all duration-300">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold">Customer Reviews</h2>
                    <form method="GET" class="flex items-center gap-2">
                        <label for="sort" class="text-sm font-medium text-gray-700">Sort by:</label>
                        <select name="sort" id="sort"
                            class="p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                            onchange="this.form.submit()">
                            <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Newest
                            </option>
                            <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Oldest
                            </option>
                            <option value="rating_desc" <?= ($_GET['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>
                                Highest Rated</option>
                            <option value="rating_asc" <?= ($_GET['sort'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>
                                Lowest Rated</option>
                        </select>
                    </form>
                </div>

                <div class="space-y-6 custom-scrollbar" style="max-height: 600px; overflow-y: auto;">
                    <?php include '../back/feedback_comments.php'; ?>
                </div>
            </div>
        </div>
    </div>

    <?php include('../../elements/footer.php'); ?>
</body>

</html>