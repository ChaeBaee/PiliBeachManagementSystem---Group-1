<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Reviews</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        function setRating(star) {
            const stars = document.querySelectorAll(".rating-star");
            stars.forEach((s, i) => {
                s.classList.toggle("text-orange-400", i < star);
                s.classList.toggle("text-gray-300", i >= star);
            });
            document.getElementById("selectedRating").value = star;
        }
    </script>
</head>

<body class="bg-[#f4ded2] min-h-screen flex flex-col text-[#333]">

    <?php include('../back/feedback_summary.php'); ?>
    <?php include('../../elements/navbar.php'); ?>


    <div style="min-height: 60px;" class="mb-4 px-6">
        <?php if (isset($_GET['success'])): ?>
            <div class="bg-green-200 text-green-800 p-4 rounded">
                Thank you for your feedback!
            </div>
        <?php endif; ?>
    </div>

    <main class="flex-grow p-6 mt-6">
        <div class="max-w-3xl mx-auto space-y-6">

            <section>
                <h2 class="text-2xl font-semibold mb-2">Reviews</h2>
                <div class="flex items-center gap-4">
                    <div class="text-5xl font-bold leading-none"><?= $avgRating ?></div>
                    <div class="space-y-1">
                        <div class="text-yellow-400 flex gap-1">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= round($avgRating) ? "<span>★</span>" : "<span class='text-gray-300'>★</span>";
                            }
                            ?>
                        </div>
                        <p class="text-sm text-gray-600"><?= $totalReviews ?> reviews</p>
                    </div>
                </div>

                <div class="mt-4 space-y-1">
                    <?php
                    for ($star = 5; $star >= 1; $star--) {
                        $count = $ratingCounts[$star] ?? 0;
                        $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        echo '
                        <div class="flex items-center">
                            <span class="w-4 text-sm">' . $star . '</span>
                            <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                                <div class="bg-red-400 h-1 rounded" style="width: ' . $percent . '%;"></div>
                            </div>
                            <span class="text-sm">' . $count . '</span>
                        </div>';
                    }
                    ?>
                </div>
            </section>

            <section class="bg-[#faebe1] p-6 rounded-xl shadow-md">
                <div class="mb-6">
                    <h3 class="font-bold text-lg mb-1">Help Us Enhance Your Experience</h3>
                    <p class="pl-4 text-sm text-gray-700">
                        How was your experience at Pili Beach Resort? We'd love to hear what you liked and if there's
                        anything we can do better. Your feedback means a lot to us!<br>
                        Can you tell us more?
                    </p>
                </div>

                <form action="../../../PHP/feedback/back/feedback.php" method="POST">
                    <div class="flex gap-2 mb-4 justify-center">
                        <button type="button" class="rating-star text-orange-400 text-2xl"
                            onclick="setRating(1)">★</button>
                        <button type="button" class="rating-star text-orange-400 text-2xl"
                            onclick="setRating(2)">★</button>
                        <button type="button" class="rating-star text-orange-400 text-2xl"
                            onclick="setRating(3)">★</button>
                        <button type="button" class="rating-star text-orange-400 text-2xl"
                            onclick="setRating(4)">★</button>
                        <button type="button" class="rating-star text-orange-400 text-2xl"
                            onclick="setRating(5)">★</button>
                    </div>

                    <input type="hidden" id="selectedRating" name="feedback_rating" value="5" />

                    <textarea name="feedback_comments"
                        class="w-full h-32 p-3 border rounded-lg focus:ring focus:ring-orange-300"
                        placeholder="Add feedback..."></textarea>

                    <div class="flex gap-2 mt-4">
                        <button type="reset"
                            class="flex-1 bg-white border border-gray-300 py-2 rounded-lg hover:bg-gray-100">Cancel</button>
                        <button type="submit"
                            class="flex-1 bg-green-300 text-white py-2 rounded-lg hover:bg-green-400">Submit</button>
                    </div>
                </form>
            </section>

            <div class="mb-4 flex justify-end">
                <form method="GET" class="flex items-center gap-2">
                    <label for="sort" class="text-sm text-gray-700">Sort by:</label>
                    <select name="sort" id="sort" class="p-2 border rounded" onchange="this.form.submit()">
                        <option value="date_desc" <?= ($_GET['sort'] ?? '') === 'date_desc' ? 'selected' : '' ?>>Newest</option>
                        <option value="date_asc" <?= ($_GET['sort'] ?? '') === 'date_asc' ? 'selected' : '' ?>>Oldest</option>
                        <option value="rating_desc" <?= ($_GET['sort'] ?? '') === 'rating_desc' ? 'selected' : '' ?>>Highest Rated</option>
                        <option value="rating_asc" <?= ($_GET['sort'] ?? '') === 'rating_asc' ? 'selected' : '' ?>>Lowest Rated</option>
                    </select>
                </form>
            </div>
            <section class="space-y-6">
                <?php include '../back/feedback_comments.php'; ?>
            </section>

        </div>
    </main>

    <?php include('../../elements/footer.php'); ?>

</body>

</html>