<?php
session_start();
require_once 'db.php';

// Fetch recent feedback with user and room details
try {
    $stmt = $conn->query("
        SELECT f.feedback_rating, f.feedback_comments, f.feedback_date_created, u.user_name,
               r.room_type
        FROM feedback f
        JOIN users u ON f.user_id = u.user_id
        JOIN rooms r ON f.room_id = r.room_id
        WHERE f.feedback_comments != ''
        ORDER BY f.feedback_date_created DESC
        LIMIT 4
    ");
    $recentFeedback = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentFeedback = [];
}

// Get average rating
try {
    $stmt = $conn->query("SELECT AVG(feedback_rating) as avg_rating FROM feedback");
    $avgRating = number_format($stmt->fetch(PDO::FETCH_ASSOC)['avg_rating'], 1);
} catch (PDOException $e) {
    $avgRating = "0.0";
}

// Fetch available rooms
try {
    $stmt = $conn->query("
        SELECT room_id, room_type, room_price, room_capacity, room_image, room_description 
        FROM rooms 
        WHERE room_status = 'available' 
        ORDER BY room_price ASC 
        LIMIT 3
    ");
    $availableRooms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $availableRooms = [];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Paradise Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
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

        .hero-overlay {
            background: linear-gradient(135deg,
                    rgba(29, 59, 157, 0.75) 0%,
                    rgba(15, 91, 255, 0.65) 50%,
                    rgba(29, 59, 157, 0.75) 100%);
            mix-blend-mode: multiply;
        }

        .hero-text {
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
    </style>
</head>

<body class="flex flex-col min-h-screen bg-gray-50">

    <?php include('elements/navbar.php'); ?>

    <!-- Hero Section -->
    <section class="relative h-screen" data-aos="fade-in">
        <div class="absolute inset-0">
            <img src="assets/home.jpg" alt="Resort View" class="w-full h-full object-cover">
            <div class="absolute inset-0 hero-overlay"></div>
        </div>
        <div class="relative flex items-center justify-center h-full">
            <div class="text-center text-white px-4 max-w-4xl">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight tracking-tight hero-text">PILI BEACH RESORT
                    AGMANIC</h1>
                <p class="text-xl md:text-2xl mb-8 text-gray-200 font-light hero-text">Experience Luxury and Serenity by
                    the Beach</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="../PHP/booking/front/bookings.php"
                        class="bg-[#1D4ED8] hover:bg-[#93C5FD] text-[#ffffff] font-bold py-4 px-8 rounded-lg text-lg transition-all duration-300 transform hover:scale-105">
                        Book Your Stay
                    </a>
                    <a href="room/front/rooms.php"
                        class="bg-transparent hover:bg-white/10 border-2 border-white text-white font-bold py-4 px-8 rounded-lg text-lg transition-all duration-300 transform hover:scale-105">
                        Explore Rooms
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Rooms -->
    <section class="py-20 px-4 bg-white" data-aos="fade-up">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">Luxurious Accommodations</h2>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">Experience comfort and elegance in our carefully
                    designed rooms, each offering a unique blend of modern amenities and coastal charm.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php foreach ($availableRooms as $index => $room): ?>
                    <div class="group relative overflow-hidden rounded-2xl shadow-lg" data-aos="zoom-in"
                        data-aos-delay="<?= ($index + 1) * 100 ?>">
                        <img src="assets/<?= htmlspecialchars($room['room_image']) ?>"
                            alt="<?= htmlspecialchars($room['room_type']) ?>"
                            class="w-full h-64 object-cover transform group-hover:scale-110 transition-transform duration-500">
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-black/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <div class="absolute bottom-0 p-6 text-white">
                                <h3 class="text-2xl font-bold mb-2"><?= htmlspecialchars($room['room_type']) ?></h3>
                                <p class="text-lg mb-2">₱<?= number_format($room['room_price']) ?> / night</p>
                                <p class="text-sm mb-4">Capacity: <?= $room['room_capacity'] ?> persons</p>
                                <a href="room/front/rooms.php"
                                    class="inline-block bg-white text-gray-900 px-6 py-2 rounded-lg font-semibold hover:bg-gray-100 transition-colors">View
                                    Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="mt-12 text-center" data-aos="fade-up" data-aos-delay="400">
                <a href="room/front/rooms.php"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105">
                    View All Rooms
                </a>
            </div>
        </div>
    </section>

    <!-- Book Your Stay -->
    <section class="py-20 px-4 bg-gradient-to-r from-blue-600 to-indigo-700" data-aos="fade-left">
        <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <h2 class="text-4xl font-bold mb-6">Ready for Your Perfect Getaway?</h2>
                <p class="text-lg mb-8 text-blue-100">Experience the perfect blend of luxury and comfort at Pili Beach
                    Resort Agmanic. Book your stay now and create unforgettable memories.</p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="../PHP/booking/front/bookings.php"
                        class="bg-white text-blue-600 font-semibold py-4 px-8 rounded-lg shadow-lg hover:bg-blue-50 transition-all duration-300 transform hover:scale-105">
                        Book Now
                    </a>
                    <a href="room/about.php"
                        class="bg-transparent border-2 border-white text-white font-semibold py-4 px-8 rounded-lg hover:bg-white/10 transition-all duration-300 transform hover:scale-105">
                        Learn More
                    </a>
                </div>
            </div>
            <div class="hidden md:block" data-aos="zoom-in" data-aos-delay="200">
                <img src="assets/home.jpg" alt="Resort View"
                    class="w-full h-[400px] object-cover rounded-2xl shadow-2xl">
            </div>
        </div>
    </section>

    <!-- What Our Guests Say -->
    <section class="py-20 px-4 bg-gray-50" data-aos="fade-up">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-bold text-gray-800 mb-4">What Our Guests Say</h2>
                <div class="flex items-center justify-center gap-2 mb-6">
                    <div class="text-3xl font-bold text-yellow-400"><?= $avgRating ?></div>
                    <div class="text-yellow-400 text-2xl">
                        <?php
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= round($avgRating) ? "★" : "☆";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <?php foreach ($recentFeedback as $feedback): ?>
                    <div class="glass-card p-8 rounded-2xl shadow-lg" data-aos="fade-up"
                        data-aos-delay="<?= $loop * 100 ?>">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center">
                                <span
                                    class="text-blue-600 font-semibold text-lg"><?= substr(htmlspecialchars($feedback['user_name']), 0, 1) ?></span>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900"><?= htmlspecialchars($feedback['user_name']) ?></h4>
                                <p class="text-sm text-gray-500">
                                    <?= date("M d, Y", strtotime($feedback['feedback_date_created'])) ?>
                                </p>
                            </div>
                        </div>
                        <div class="text-yellow-400 mb-4">
                            <?php
                            for ($i = 1; $i <= 5; $i++) {
                                echo $i <= $feedback['feedback_rating'] ? "★" : "☆";
                            }
                            ?>
                        </div>
                        <p class="text-gray-700 mb-4"><?= htmlspecialchars($feedback['feedback_comments']) ?></p>
                        <p class="text-sm text-gray-500">
                            <span class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <?= htmlspecialchars($feedback['room_type']) ?>
                            </span>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-12 text-center" data-aos="fade-up" data-aos-delay="300">
                <a href="feedback/front/feedback.php"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-8 rounded-lg shadow-lg transition-all duration-300 transform hover:scale-105">
                    See All Reviews
                </a>
            </div>
        </div>
    </section>

    <?php include('elements/footer.php'); ?>

    <!-- AOS JS -->
    <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true,
            duration: 800,
        });
    </script>

</body>

</html>