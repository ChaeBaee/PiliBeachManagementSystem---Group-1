<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<div class="h-16"></div>
    <?php include('../elements/navbar.php'); ?>

        <div class="max-w-5xl mx-auto mt-12 bg-white p-8 rounded-2xl shadow-lg">
        <!-- About Us Section -->
        <h2 class="text-3xl font-bold text-blue-600 mb-4">About Us</h2>
        <p class="text-lg text-gray-700 mb-4">
            Welcome to <span class="font-semibold text-blue-500">Pili Resort</span>, where relaxation meets nature. Nestled in a scenic location, our resort offers comfort, serenity, and warm local hospitality.
        </p>
        <p class="text-lg text-gray-700 mb-4">
            Whether you’re traveling for peace or adventure, Pili Resort is your perfect escape. Since 2025, we’ve been committed to creating memorable stays for families, solo travelers, and friends alike.
        </p>

        <!-- Meet the Team Section -->
        <h3 class="text-2xl font-semibold text-blue-600 mt-10 mb-6">Meet the Team</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gray-50 p-4 rounded-xl shadow">
            <img src="images/jane.jpg" alt="Jane - Manager" class="w-full h-48 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Jane Dela Cruz</h4>
            <p class="text-sm text-gray-600">Resort Manager</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl shadow">
            <img src="images/michael.jpg" alt="Michael - Chef" class="w-full h-48 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Michael Reyes</h4>
            <p class="text-sm text-gray-600">Executive Chef</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl shadow">
            <img src="images/anna.jpg" alt="Anna - Front Desk" class="w-full h-48 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Anna Santos</h4>
            <p class="text-sm text-gray-600">Front Desk Officer</p>
            </div>
        </div>

        <!-- Location Section -->
        <h3 class="text-2xl font-semibold text-blue-600 mt-10 mb-4">Our Location</h3>
        <p class="text-gray-700 mb-4">We are located at Sitio Ilaya, Brgy. Agmanic, Santa Fe, Romblon, Philippines, 5508 — easily accessible and surrounded by breathtaking nature.</p>
        <div class="w-full h-64 rounded-xl overflow-hidden shadow">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3900.98027165368!2d122.00487937482626!3d12.113502288128512!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a51f6855555669%3A0x90089acb04c38f1c!2sPili%20Beach%20Resort%20Agmanic!5e0!3m2!1sen!2sph!4v1747390893802!5m2!1sen!2sph"
                 width="100%" 
                 height="100%" 
                 style="border:0;" 
                 allowfullscreen="" 
                 loading="lazy" 
                 referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
        </div>

<?php include('../elements/footer.php'); ?>

</body>
</html>