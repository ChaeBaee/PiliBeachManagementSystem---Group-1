<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Paradise Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- AOS CSS -->
    <link href="https://unpkg.com/aos@2.3.4/dist/aos.css" rel="stylesheet" />
</head>

<body class="flex flex-col min-h-screen">

    <?php include('elements/navbar.php'); ?>

    <!-- Hero Section -->
    <section class="bg-cover bg-center h-[90vh] sm:h-screen" style="background-image: url('assets/home.jpg');"
        data-aos="fade-in">
        <div class="flex items-center justify-center h-full bg-black bg-opacity-50">
            <div class="text-center text-white px-4 max-w-4xl mx-auto">
                <h1 class="text-3xl sm:text-4xl md:text-5xl lg:text-6xl font-bold mb-3 sm:mb-4">Pili Beach Resort
                    Agmanic</h1>
                <p class="text-base sm:text-lg md:text-xl lg:text-2xl mb-6 sm:mb-8">Relax, Unwind, and Experience Luxury
                </p>
                <a href="booking/booking.php"
                    class="inline-block bg-[#7FFFD4] hover:bg-[#6EEFBF] text-[#484848] font-bold py-2 sm:py-3 px-4 sm:px-6 rounded-lg text-base sm:text-lg transition duration-300 shadow-lg hover:shadow-xl">
                    Book Now
                </a>
            </div>
        </div>
    </section>

    <!-- Our Rooms -->
    <section class="py-12 sm:py-16 px-4 sm:px-6 lg:px-8 space-y-6 sm:space-y-8" data-aos="fade-up">
        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-gray-800 text-center mb-6 sm:mb-8">Our Rooms</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 max-w-7xl mx-auto">
            <div class="bg-white shadow-md rounded-lg overflow-hidden transform hover:scale-105 transition duration-300"
                data-aos="zoom-in" data-aos-delay="100">
                <img src="../PHP/assets/r1.jpg" alt="Room 1" class="w-full h-48 sm:h-56 object-cover">
                <div class="p-4 sm:p-6">
                    <h3 class="font-semibold text-lg sm:text-xl mb-2">Deluxe Room</h3>
                    <p class="text-gray-500 text-base sm:text-lg">₱3,000 / night</p>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden transform hover:scale-105 transition duration-300"
                data-aos="zoom-in" data-aos-delay="200">
                <img src="../PHP/assets/r2.jpg" alt="Room 2" class="w-full h-48 sm:h-56 object-cover">
                <div class="p-4 sm:p-6">
                    <h3 class="font-semibold text-lg sm:text-xl mb-2">Family Suite</h3>
                    <p class="text-gray-500 text-base sm:text-lg">₱4,500 / night</p>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden transform hover:scale-105 transition duration-300"
                data-aos="zoom-in" data-aos-delay="300">
                <img src="../PHP/assets/r3.jpg" alt="Room 3" class="w-full h-48 sm:h-56 object-cover">
                <div class="p-4 sm:p-6">
                    <h3 class="font-semibold text-lg sm:text-xl mb-2">Honeymoon Villa</h3>
                    <p class="text-gray-500 text-base sm:text-lg">₱6,000 / night</p>
                </div>
            </div>
        </div>
        <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="400">
            <a href="room/front/rooms.php"
                class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition duration-300 hover:shadow-lg text-base sm:text-lg">
                View All Rooms
            </a>
        </div>
    </section>

    <!-- Book Your Stay -->
    <section class="bg-gradient-to-r from-blue-400 to-indigo-600 text-white py-12 sm:py-16" data-aos="fade-left">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div class="text-center md:text-left">
                <h2 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-4">Ready for Your Getaway?</h2>
                <p class="mb-6 text-base sm:text-lg">Reserve your room in just a few clicks and prepare for an
                    unforgettable experience at Pili Beach Resort Agmanic!</p>
                <a href="booking/booking.php"
                    class="inline-block bg-white text-indigo-600 font-semibold py-2 sm:py-3 px-4 sm:px-6 rounded-lg shadow-lg hover:bg-gray-100 transition duration-300 text-base sm:text-lg">
                    Reserve Now
                </a>
            </div>
            <div class="hidden md:block" data-aos="zoom-in" data-aos-delay="200">
                <img src="../PHP/assets/home.jpg" alt="Book your stay"
                    class="w-full h-auto rounded-lg shadow-lg object-cover">
            </div>
        </div>
    </section>

    <!-- What Our Guests Say -->
    <section class="bg-gray-100 py-12 sm:py-16 px-4 sm:px-6 lg:px-8" data-aos="fade-up">
        <div class="max-w-6xl mx-auto">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-center text-gray-800 mb-8 sm:mb-12">What Our
                Guests Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 sm:gap-8">
                <blockquote
                    class="bg-white p-4 sm:p-6 rounded-lg shadow-lg border-l-4 border-blue-500 transform hover:scale-105 transition duration-300"
                    data-aos="fade-right" data-aos-delay="100">
                    <p class="text-gray-700 italic mb-2 text-base sm:text-lg">"The view was amazing and the staff was so
                        kind. Will definitely come back!"</p>
                    <footer class="text-sm sm:text-base text-gray-500 text-right">– Maria D.</footer>
                </blockquote>
                <blockquote
                    class="bg-white p-4 sm:p-6 rounded-lg shadow-lg border-l-4 border-green-500 transform hover:scale-105 transition duration-300"
                    data-aos="fade-left" data-aos-delay="200">
                    <p class="text-gray-700 italic mb-2 text-base sm:text-lg">"Perfect for a family weekend. The rooms
                        were clean and spacious."</p>
                    <footer class="text-sm sm:text-base text-gray-500 text-right">– Juan C.</footer>
                </blockquote>
            </div>
            <div class="mt-8 text-center" data-aos="fade-up" data-aos-delay="300">
                <a href="feedback/front/feedback.php"
                    class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-lg shadow transition duration-300 hover:shadow-lg text-base sm:text-lg">
                    See More Feedback
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
            offset: 100,
            delay: 100
        });
    </script>

</body>

</html>