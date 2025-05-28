
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resort Navbar</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <style>
    html,
    body {
      max-width: 100%;
      overflow-x: hidden;
      font-family: 'Inter', sans-serif;
    }
  </style>
</head>

<body>
  <nav class="bg-white shadow-lg p-4 fixed top-0 w-full z-10">
    <div class="container mx-auto flex justify-between items-center">
      <a href="/Proj/PHP/home.php" class="text-black text-2xl font-bold tracking-wider">PILI</a>

      <button id="mobile-menu-button" class="md:hidden text-gray-600 hover:text-gray-900 focus:outline-none">
        <i class="fas fa-bars text-2xl"></i>
      </button>

      <ul class="hidden md:flex space-x-6 text-black" id="desktop-menu">
        <li><a href="/Proj/PHP/home.php" class="hover:text-[#3b82f6] transition duration-300">Home</a></li>
        <li><a href="/Proj/PHP/room/front/rooms.php" class="hover:text-[#3b82f6] transition duration-300">Rooms</a></li>
        <li><a href="/Proj/PHP/booking/front/bookings.php" class="hover:text-[#3b82f6] transition duration-300">Book</a></li>
        <li><a href="/Proj/PHP/feedback/front/feedback.php"
            class="hover:text-[#3b82f6] transition duration-300">Reviews</a></li>
        <li><a href="/Proj/PHP/room/about.php" class="hover:text-[#3b82f6] transition duration-300">About Us</a></li>
      </ul>

      <div class="hidden md:flex items-center space-x-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <div class="relative group">
            <a href="/Proj/PHP/profile/profile.php"
              class="flex items-center border-2 border-blue-500 text-blue-500 px-4 py-2 rounded-lg hover:bg-blue-500 hover:text-white transition duration-300">
              <i class="fas fa-user-circle text-xl mr-2"></i>
              <span>Profile</span>
            </a>
          </div>

          <a href="/Proj/PHP/logout.php"
            class="bg-[#ff7f7f] text-white px-4 py-2 rounded-lg hover:bg-[#ff6b6b] transition duration-300 shadow-sm flex items-center">
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span>Logout</span>
          </a>
        <?php else: ?>
          <a href="/Proj/PHP/login/front/login.php"
            class="border-2 border-[#3B82F6] text-[#3B82F6] px-4 py-2 rounded-lg hover:bg-[#3B82F6] hover:text-white transition duration-300 shadow-sm">
            Login
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden mt-4 bg-white rounded-lg shadow-lg">
      <ul class="py-2">
        <li><a href="/Proj/PHP/home.php" class="block px-4 py-2 hover:bg-gray-100">Home</a></li>
        <li><a href="/Proj/PHP/room/front/rooms.php" class="block px-4 py-2 hover:bg-gray-100">Rooms</a></li>
        <li><a href="/Proj/PHP/booking/front/bookings.php" class="block px-4 py-2 hover:bg-gray-100">Book</a></li>
        <li><a href="/Proj/PHP/feedback/front/feedback.php" class="block px-4 py-2 hover:bg-gray-100">Reviews</a></li>
        <li><a href="/Proj/PHP/room/about.php" class="block px-4 py-2 hover:bg-gray-100">About Us</a></li>
      </ul>
      <div class="border-t border-gray-200 py-2">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="/Proj/PHP/profile/profile.php" class="block px-4 py-2 hover:bg-gray-100">
            <i class="fas fa-user-circle mr-2"></i>Profile
          </a>
          <a href="/Proj/PHP/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
          </a>
        <?php else: ?>
          <a href="/Proj/PHP/login/front/login.php"
            class="block px-4 py-2 border-2 border-[#3B82F6] text-[#3B82F6] rounded-lg hover:bg-[#3B82F6] hover:text-white transition duration-300">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.add('hidden');
      }

      window.addEventListener('DOMContentLoaded', () => {
        const desktopAuth = document.querySelector('.md\\:flex.items-center');
        const mobileAuth = document.querySelector('#mobile-menu .border-t');

        if (desktopAuth && mobileAuth) {
          mobileAuth.innerHTML = desktopAuth.innerHTML;
        }
      });
    });
  </script>
</body>

</html>