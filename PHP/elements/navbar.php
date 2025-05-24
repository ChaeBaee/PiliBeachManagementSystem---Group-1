<?php
//session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resort Navbar</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body>
  <nav class="bg-white p-4 fixed top-0 w-full z-10">
    <div class="container mx-auto flex justify-between items-center">
      <a href="Proj/PHP/home.php" class="text-black text-lg font-bold">PILI</a>
      <ul class="hidden md:flex space-x-4 text-black" id="menu">
        <li><a href="/Proj/PHP/home.php" class="hover:text-gray-300">Home</a></li>
        <li><a href="/Proj/PHP/room/rooms.php" class="hover:text-gray-300">Rooms</a></li>
        <li><a href="/Proj/PHP/booking/booking.php" class="hover:text-gray-300">Booking</a></li>
        <li><a href="/Proj/PHP/feedback/front/feedback.php" class="hover:text-gray-300">Feedbacks</a></li>
        <li><a href="/Proj/PHP/room/about.php" class="hover:text-gray-300">About Us</a></li>
      </ul>
      <div class="hidden md:flex items-center space-x-2">
        <?php if (isset($_SESSION['user_id'])): ?>

          <a href="/Proj/PHP/profile/profile.php"
            class="flex items-center bg-[#7FFFD4] text-[#484848] px-3 py-2 rounded hover:bg-gray-100">

            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
              stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M5.121 17.804A11.955 11.955 0 0112 15c2.634 0 5.065.847 7.121 2.27M15 10a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
            Profile
          </a>

          <a href="/Proj/PHP/logout.php" class="bg-[#ff7f7f] text-white px-3 py-2 rounded hover:bg-red-600">
            Logout
          </a>
        <?php else: ?>
          <a href="/Proj/PHP/login/front/login.php"
            class="bg-[#7FFFD4] text-[#484848] px-4 py-2 rounded hover:bg-gray-100">Login</a>
          <a href="/Proj/PHP/login/front/sign-up.php"
            class="bg-[#7FFFD4] text-[#484848] px-4 py-2 rounded hover:bg-gray-100">Sign Up</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
</body>

</html>