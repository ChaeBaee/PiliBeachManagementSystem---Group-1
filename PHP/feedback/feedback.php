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

<!-- Navbar -->
<?php include('../elements/navbar.php'); ?>

<!-- Main Content -->
<main class="flex-grow p-6">
    <div class="max-w-3xl mx-auto space-y-6">

        <!-- Rating Summary -->
        <section>
            <h2 class="text-2xl font-semibold mb-2">Reviews</h2>
            <div class="flex items-center gap-4">
                <div class="text-5xl font-bold leading-none">5.0</div>
                <div class="space-y-1">
                    <div class="text-yellow-400 flex gap-1">
                        <span>★</span><span>★</span><span>★</span><span>★</span><span>★</span>
                    </div>
                    <p class="text-sm text-gray-600">1,578 reviews</p>
                </div>
            </div>

            <!-- Rating distribution bars -->
            <div class="mt-4 space-y-1">
                <!-- Repeat for ratings 5 to 1 -->
                <div class="flex items-center"><span class="w-4 text-sm">5</span>
                    <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                        <div class="bg-red-400 h-1 w-[95%] rounded"></div>
                    </div>
                </div>
                <div class="flex items-center"><span class="w-4 text-sm">4</span>
                    <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                        <div class="bg-red-400 h-1 w-[60%] rounded"></div>
                    </div>
                </div>
                <div class="flex items-center"><span class="w-4 text-sm">3</span>
                    <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                        <div class="bg-red-400 h-1 w-[30%] rounded"></div>
                    </div>
                </div>
                <div class="flex items-center"><span class="w-4 text-sm">2</span>
                    <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                        <div class="bg-red-400 h-1 w-[15%] rounded"></div>
                    </div>
                </div>
                <div class="flex items-center"><span class="w-4 text-sm">1</span>
                    <div class="flex-grow mx-2 bg-gray-200 h-1 rounded">
                        <div class="bg-red-400 h-1 w-[5%] rounded"></div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Feedback Form -->
        <section class="bg-[#faebe1] p-6 rounded-xl shadow-md">
            <h3 class="text-lg font-semibold">Help Us Enhance Your Experience</h3>
            <p class="text-sm text-gray-700 mb-4">How was your experience at Pili Beach Resort? We’d love to hear what you liked and if there’s anything we can do better.</p>

            <!-- Star Rating -->
            <div class="flex gap-2 mb-4">
                <button type="button" class="rating-star text-orange-400 text-2xl" onclick="setRating(1)">★</button>
                <button type="button" class="rating-star text-orange-400 text-2xl" onclick="setRating(2)">★</button>
                <button type="button" class="rating-star text-orange-400 text-2xl" onclick="setRating(3)">★</button>
                <button type="button" class="rating-star text-orange-400 text-2xl" onclick="setRating(4)">★</button>
                <button type="button" class="rating-star text-orange-400 text-2xl" onclick="setRating(5)">★</button>
            </div>
            <input type="hidden" id="selectedRating" name="rating" value="5" />
            <textarea class="w-full h-32 p-3 border rounded-lg focus:ring focus:ring-orange-300" placeholder="Add feedback..."></textarea>
            <div class="flex gap-2 mt-4">
                <button class="flex-1 bg-white border border-gray-300 py-2 rounded-lg hover:bg-gray-100">Cancel</button>
                <button class="flex-1 bg-green-300 text-white py-2 rounded-lg hover:bg-green-400">Submit</button>
            </div>
        </section>

        <!-- Review List -->
        <section class="space-y-6">
            <div class="flex gap-4">
                <div class="w-12 h-12 bg-gray-500 rounded-full"></div>
                <div>
                    <p class="font-semibold">John Doberman</p>
                    <p class="text-xs text-gray-500">Mar 12 2020</p>
                    <div class="text-yellow-400 text-sm">★★★★★</div>
                    <p class="text-sm text-gray-700 mt-1">Lorem ipsum dolor sit amet...</p>
                </div>
            </div>

            <div class="flex gap-4 border-t pt-4">
                <div class="w-12 h-12 bg-gray-500 rounded-full"></div>
                <div>
                    <p class="font-semibold">John Doberman</p>
                    <p class="text-xs text-gray-500">Mar 12 2020</p>
                    <div class="text-yellow-400 text-sm">★★★★★</div>
                    <p class="text-sm text-gray-700 mt-1">Lorem ipsum dolor sit amet...</p>
                </div>
            </div>
        </section>

    </div>
</main>

<!-- Footer -->
<?php include('../elements/footer.php'); ?>

</body>
</html>
