<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
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

    /* Page transition animations */
    .page-transition {
      opacity: 0;
      animation: smoothFadeIn 1.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }

    @keyframes smoothFadeIn {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }

    /* Loading animation */
    .loading-overlay {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.95);
      display: flex;
      justify-content: center;
      align-items: center;
      z-index: 9999;
      opacity: 0;
      visibility: hidden;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .loading-overlay.active {
      opacity: 1;
      visibility: visible;
    }

    .loading-spinner {
      width: 50px;
      height: 50px;
      border: 4px solid #f3f3f3;
      border-top: 4px solid #3b82f6;
      border-radius: 50%;
      animation: spin 1s linear infinite;
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Hover animations for navigation links */
    .nav-link {
      position: relative;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link::after {
      content: '';
      position: absolute;
      bottom: -5px;
      left: 0;
      width: 0;
      height: 2px;
      background: #3b82f6;
      transition: width 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .nav-link:hover::after {
      width: 100%;
    }

    /* Logo animation */
    .logo {
      transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .logo:hover {
      transform: scale(1.05);
    }

    /* Button animations */
    .btn-animate {
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .btn-animate::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .btn-animate:hover::before {
      left: 100%;
    }

    /* Mobile menu animations */
    .mobile-menu {
      transform: translateY(-10px);
      opacity: 0;
      transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .mobile-menu.show {
      transform: translateY(0);
      opacity: 1;
    }
  </style>
</head>

<body>
  <!-- Loading Overlay -->
  <div class="loading-overlay" id="loadingOverlay">
    <div class="loading-spinner"></div>
  </div>

  <nav class="bg-white shadow-lg p-4 fixed top-0 w-full z-10">
    <div class="container mx-auto flex justify-between items-center">
      <a href="/Proj/PHP/home.php" class="logo text-black text-2xl font-bold tracking-wider">
        <img src="/Proj/PHP/assets/logo.png" alt="PILI Resort Logo" class="h-8 w-auto">
      </a>

      <ul class="hidden md:flex space-x-8 text-black justify-center flex-1 mx-8" id="desktop-menu">
        <li><a href="/Proj/PHP/home.php" class="nav-link hover:text-[#3b82f6] transition duration-200">Home</a></li>
        <li><a href="/Proj/PHP/room/front/rooms.php" class="nav-link hover:text-[#3b82f6] transition duration-200">Rooms</a></li>
        <li><a href="/Proj/PHP/booking/front/bookings.php" class="nav-link hover:text-[#3b82f6] transition duration-200">Book</a></li>
        <li><a href="/Proj/PHP/feedback/front/feedback.php" class="nav-link hover:text-[#3b82f6] transition duration-200">Reviews</a></li>
        <li><a href="/Proj/PHP/room/about.php" class="nav-link hover:text-[#3b82f6] transition duration-200">About Us</a></li>
        <li><a href="/Proj/PHP/contact/front/contact.php" class="nav-link hover:text-[#3b82f6] transition duration-200">Contact</a></li>
        <?php if (isset($_SESSION['user_id'])): ?>
        <li><a href="/Proj/PHP/profile/cancel-booking.php" class="nav-link hover:text-[#22c55e] transition duration-200">My Bookings</a></li>
        <?php endif; ?>
      </ul>

      <div class="hidden md:flex items-center space-x-4">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="/Proj/PHP/profile/profile.php"
            class="group relative overflow-hidden border-2 border-blue-500 text-blue-500 px-4 py-2 rounded-lg hover:bg-blue-500 hover:text-white transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-1">
            <div class="absolute inset-0 bg-blue-500 opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <i class="fas fa-user-circle text-xl mr-2"></i>
            <span class="font-semibold">Profile</span>
          </a>
          <a href="/Proj/PHP/logout.php"
            class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-red-600 text-white px-4 py-2 rounded-lg transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-lg hover:shadow-red-500/25 hover:-translate-y-1">
            <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
            <i class="fas fa-sign-out-alt mr-2"></i>
            <span class="font-semibold">Logout</span>
          </a>
        <?php else: ?>
          <a href="/Proj/PHP/login/front/login.php"
            class="group relative overflow-hidden border-2 border-[#3B82F6] text-[#3B82F6] px-4 py-2 rounded-lg hover:bg-[#3B82F6] hover:text-white transition-all duration-300 hover:shadow-lg hover:shadow-blue-500/25 hover:-translate-y-1">
            <div class="absolute inset-0 bg-[#3B82F6] opacity-0 group-hover:opacity-10 transition-opacity duration-300"></div>
            <i class="fas fa-sign-in-alt mr-2"></i>
            <span class="font-semibold">Login</span>
          </a>
        <?php endif; ?>
      </div>
    </div>

    <div id="mobile-menu" class="mobile-menu hidden md:hidden mt-4 bg-white rounded-lg shadow-lg">
      <ul class="py-2">
        <li><a href="/Proj/PHP/home.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">Home</a></li>
        <li><a href="/Proj/PHP/room/front/rooms.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">Rooms</a></li>
        <li><a href="/Proj/PHP/booking/front/bookings.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">Book</a></li>
        <li><a href="/Proj/PHP/feedback/front/feedback.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">Reviews</a></li>
        <li><a href="/Proj/PHP/room/about.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">About Us</a></li>
        <li><a href="/Proj/PHP/contact/front/contact.php" class="block px-4 py-2 hover:bg-gray-100 transition duration-200">Contact</a></li>
      </ul>
      <div class="border-t border-gray-200 py-2 flex flex-col gap-2">
        <?php if (isset($_SESSION['user_id'])): ?>
          <a href="/Proj/PHP/profile/profile.php" class="block px-4 py-2 hover:bg-gray-100 flex items-center transition duration-200">
            <i class="fas fa-user-circle mr-2"></i>Profile
          </a>
          <a href="/Proj/PHP/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-100 flex items-center transition duration-200">
            <i class="fas fa-sign-out-alt mr-2"></i>Logout
          </a>
        <?php else: ?>
          <a href="/Proj/PHP/login/front/login.php"
            class="block px-4 py-2 border-2 border-[#3B82F6] text-[#3B82F6] rounded-lg hover:bg-[#3B82F6] hover:text-white transition duration-200 flex items-center">
            <i class="fas fa-sign-in-alt mr-2"></i>Login
          </a>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <script>
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const loadingOverlay = document.getElementById('loadingOverlay');

    // Mobile menu toggle with animation
    mobileMenuButton.addEventListener('click', () => {
      mobileMenu.classList.toggle('hidden');
      if (!mobileMenu.classList.contains('hidden')) {
        mobileMenu.classList.add('show');
      } else {
        mobileMenu.classList.remove('show');
      }
    });

    document.addEventListener('click', (e) => {
      if (!mobileMenuButton.contains(e.target) && !mobileMenu.contains(e.target)) {
        mobileMenu.classList.add('hidden');
        mobileMenu.classList.remove('show');
      }
    });

    // Page transition animations
    function showLoading() {
      loadingOverlay.classList.add('active');
    }

    function hideLoading() {
      loadingOverlay.classList.remove('active');
    }

    // Add page transition class to main content with optimized timing
    document.addEventListener('DOMContentLoaded', function() {
      // Small delay to ensure smooth transition
      requestAnimationFrame(() => {
        const mainContent = document.querySelector('main') || document.body;
        mainContent.classList.add('page-transition');
      });
    });

    // Handle navigation links with loading animation - OPTIMIZED
    document.addEventListener('click', function(e) {
      const link = e.target.closest('a');
      if (link && link.href && !link.href.includes('javascript:') && !link.href.includes('#')) {
        // Don't show loading for same page links or back button navigation
        if (link.href !== window.location.href && !link.href.includes('#')) {
          // Only show loading for actual page navigation, not back button
          if (!e.isTrusted || e.detail > 0) { // Check if it's a real click
            showLoading();
            
            // Reduced timeout for better performance
            setTimeout(() => {
              hideLoading();
            }, 1000);
          }
        }
      }
    });

    // Handle form submissions with loading animation
    document.addEventListener('submit', function(e) {
      if (e.target.tagName === 'FORM') {
        showLoading();
      }
    });

    // Hide loading when page is fully loaded
    window.addEventListener('load', function() {
      hideLoading();
    });

    // Handle browser back/forward button - prevent loading overlay interference
    window.addEventListener('pageshow', function(event) {
      // Hide loading overlay when page is shown (including from back button)
      hideLoading();
    });

    window.addEventListener('beforeunload', function() {
      // Hide loading overlay before page unloads
      hideLoading();
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
          target.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }
      });
    });

    // Add hover effects to navigation items with optimized performance
    document.querySelectorAll('.nav-link').forEach(link => {
      link.addEventListener('mouseenter', function() {
        this.style.transform = 'translateY(-1px)';
      });
      
      link.addEventListener('mouseleave', function() {
        this.style.transform = 'translateY(0)';
      });
    });
  </script>
</body>

</html>