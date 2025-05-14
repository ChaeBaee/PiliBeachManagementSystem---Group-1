<?php
// index.php
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Paradise Resort</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="flex flex-col min-h-screen">

  <!-- Navbar -->
  <?php include('elements/navbar.php'); ?>

  <!-- Hero Section -->
  <section class="bg-cover bg-center h-screen" style="background-image: url('assets/images/hero-bg.jpg');">
    <div class="flex items-center justify-center h-full bg-black bg-opacity-50">
      <div class="text-center text-white px-4">
        <h1 class="text-4xl md:text-6xl font-bold mb-4">Welcome to Paradise Resort</h1>
        <p class="text-lg md:text-2xl mb-8">Relax, Unwind, and Experience Luxury</p>
        <a href="booking/booking.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-lg text-lg">
          Book Now
        </a>
      </div>
    </div>
  </section>

  <!-- Main Content Previews -->
  <main class="max-w-6xl mx-auto py-16 px-4 space-y-16">

    <!-- Rooms Preview -->
    <section>
      <h2 class="text-3xl font-bold text-gray-800 mb-6">Our Rooms</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <!-- Copilot prompt: "Tailwind card for resort room with image, title, price" -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <img src="assets/images/room1.jpg" alt="Room 1" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="font-semibold text-lg">Deluxe Room</h3>
            <p class="text-gray-500">₱3,000 / night</p>
          </div>
        </div>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <img src="assets/images/room2.jpg" alt="Room 2" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="font-semibold text-lg">Family Suite</h3>
            <p class="text-gray-500">₱4,500 / night</p>
          </div>
        </div>
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
          <img src="assets/images/room3.jpg" alt="Room 3" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="font-semibold text-lg">Honeymoon Villa</h3>
            <p class="text-gray-500">₱6,000 / night</p>
          </div>
        </div>
      </div>
      <div class="mt-4 text-right">
        <a href="room/rooms.php" class="text-blue-600 font-medium hover:underline">View all rooms →</a>
      </div>
    </section>

    <!-- Booking Preview -->
    <section class="bg-blue-50 p-6 rounded-lg shadow-sm">
      <h2 class="text-2xl font-bold text-blue-800 mb-2">Book Your Stay</h2>
      <p class="text-gray-700 mb-4">Ready for a getaway? Reserve your room with ease using our quick booking form.</p>
      <a href="booking/booking.php" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Go to Booking Page
      </a>
    </section>

    <!-- Feedback Preview -->
    <section>
      <h2 class="text-2xl font-bold text-gray-800 mb-4">What Our Guests Say</h2>
      <div class="space-y-4">
        <blockquote class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
          <p class="text-gray-700 italic">"The view was amazing and the staff was so kind. Will definitely come back!"</p>
          <footer class="text-sm text-gray-500 mt-2">– Maria D.</footer>
        </blockquote>
        <blockquote class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
          <p class="text-gray-700 italic">"Perfect for a family weekend. The rooms were clean and spacious."</p>
          <footer class="text-sm text-gray-500 mt-2">– Juan C.</footer>
        </blockquote>
      </div>
      <div class="mt-4 text-right">
        <a href="feedback/feedback.php" class="text-blue-600 font-medium hover:underline">See more feedback →</a>
      </div>
    </section>

  </main>

  <!-- Footer -->
  <?php include('elements/footer.php'); ?>

</body>
</html>
