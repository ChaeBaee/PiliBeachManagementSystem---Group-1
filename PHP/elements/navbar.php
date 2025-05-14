<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Navbar</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <nav class="bg-blue-500 p-4 fixed top-0 w-full z-10">
        <div class="container mx-auto flex justify-between items-center">
            <a href="../home.php" class="text-white text-lg font-bold">Resort</a>
            <ul class="hidden md:flex space-x-4 text-white" id="menu">
                <li><a href="../home.php" class="hover:text-gray-300">Home</a></li>
                <li><a href="../room/rooms.php" class="hover:text-gray-300">Rooms</a></li>
                <li><a href="../booking/booking.php" class="hover:text-gray-300">Booking</a></li>
                <li><a href="../feedback/feedback.php" class="hover:text-gray-300">Feedback</a></li>
                <li><a href="../room/about.php" class="hover:text-gray-300">About Us</a></li>
            </ul>
            <div class="hidden md:block">
                <a href="#" class="bg-white text-blue-500 px-4 py-2 rounded hover:bg-gray-100">Login</a>
                <a href="#" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600">Sign Up</a>
            </div>
            <button class="text-white md:hidden" id="menu-toggle">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                </svg>
            </button>
        </div>
        <ul class="md:hidden bg-blue-500 text-white space-y-2 p-4 hidden" id="mobile-menu">
            <li><a href="#" class="block hover:text-gray-300">Home</a></li>
            <li><a href="#" class="block hover:text-gray-300">Rooms</a></li>
            <li><a href="#" class="block hover:text-gray-300">Booking</a></li>
            <li><a href="#" class="block hover:text-gray-300">Feedback</a></li>
            <li><a href="#" class="block hover:text-gray-300">About Us</a></li>
            <li>
                <a href="#" class="block bg-white text-blue-500 px-4 py-2 rounded hover:bg-gray-100">Login</a>
                <a href="#" class="block bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600 mt-2">Sign Up</a>
            </li>
        </ul>
    </nav>

    <script>
        const menuToggle = document.getElementById('menu-toggle');
        const mobileMenu = document.getElementById('mobile-menu');

        menuToggle.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
        });
    </script>
</body>
</html>