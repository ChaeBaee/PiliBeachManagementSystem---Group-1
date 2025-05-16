<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#F4E2D8]">
<div class="h-16"></div>
<?php include('../elements/navbar.php'); ?>


<div class="px-4 sm:px-8 lg:px-16 py-6">
    <h2 class="text-3xl font-bold text-[#004D4D]">About Us</h2>
    <p class="text-lg text-gray-700 mb-4">
        Welcome to <span class="font-semibold text-[#004D4D]">Pili Resort</span>, where relaxation meets nature. Nestled in a scenic location, our resort offers comfort, serenity, and warm local hospitality.
    </p>
    <p class="text-lg text-gray-700 mb-10">
        Pili Beach Resort, nestled on the serene shores of Tablas Island, Romblon, offers a tranquil escape for discerning travelers seeking refuge from the bustling crowds of Boracay. Embracing the essence of understated luxury, our resort beckons guests to unwind amidst pristine white sands and crystalline waters. Catering to both short and long-term rentals, our resort warmly welcomes both foreigners and locals alike, providing a haven of relaxation and rejuvenation for all guests. Whether you're seeking a brief getaway or planning an extended stay, every aspect of your experience is tailored to exceed expectations, with personalized service at the forefront.    </p>

    
    <h3 class="text-2xl font-semibold text-[#004D4D] mb-4">Meet the Team</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6 mb-10">
    
        <div class="text-center">
            <img src="../assets/james.jpg" alt="James" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">James Andrew Ocuaman</h4>
            <p class="text-sm text-gray-600">Developer</p>
        </div>
        <div class="text-center">
            <img src="../assets/pogi.jpg" alt="Luis" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Luis Gabriel Ronatay</h4>
            <p class="text-sm text-gray-600">Developer</p>
        </div>
        <div class="text-center">
            <img src="../assets/pol1.jpg" alt="Paul Vincent" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Paul Vincent Lunaspe</h4>
            <p class="text-sm text-gray-600">Developer</p>
        </div>
        <div class="text-center">
            <img src="../assets/castor.png" alt="Paul Castor" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Paul Castor Janobas</h4>
            <p class="text-sm text-gray-600">Developer</p>
        </div>
        <div class="text-center">
            <img src="../assets/wes3.jpg" alt="John Wesly" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">John Wesly Caburog</h4>
            <p class="text-sm text-gray-600">Developer</p>
        </div>
        <div class="text-center">
            <img src="../assets/logo.jpg" alt="Logo" class="w-full h-72 object-cover rounded-lg mb-4">
            <h4 class="text-lg font-semibold text-gray-800">Pili Beach Resort Agmanic</h4>
            <p class="text-sm text-gray-600">Company</p>
        </div>
    </div>

    
    <h3 class="text-2xl font-semibold text-[#004D4D] mb-2">Our Location</h3>
    <p class="text-gray-700 mb-4">We are located at Sitio Ilaya, Brgy. Agmanic, Santa Fe, Romblon, Philippines, 5508 — easily accessible and surrounded by breathtaking nature.</p>
    <div class="w-full h-64 rounded-xl overflow-hidden">
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