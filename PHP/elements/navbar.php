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
            <a href="/Proj/PHP/home.php" class="text-white text-lg font-bold">Resort</a>
            <ul class="hidden md:flex space-x-4 text-white" id="menu">
                <li><a href="/Proj/PHP/home.php" class="hover:text-gray-300">Home</a></li>
                <li><a href="/Proj/PHP/room/rooms.php" class="hover:text-gray-300">Rooms</a></li>
                <li><a href="/Proj/PHP/booking/booking.php" class="hover:text-gray-300">Booking</a></li>
                <li><a href="/Proj/PHP/feedback/feedback.php" class="hover:text-gray-300">Feedback</a></li>
                <li><a href="/Proj/PHP/room/about.php" class="hover:text-gray-300">About Us</a></li>
            </ul>
            <div class="hidden md:block">
                <a href="/Proj/PHP/login/login.php" class="bg-white text-blue-500 px-4 py-2 rounded hover:bg-gray-100">Login</a>
                <a href="/Proj/PHP/login/sign-up.php" class="bg-blue-700 text-white px-4 py-2 rounded hover:bg-blue-600">Sign Up</a>
            </div>   
    </nav>
</body>
</html>