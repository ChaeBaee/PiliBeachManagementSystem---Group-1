<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Confirmation</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex flex-col justify-between">

  <?php include('../../elements/navbar.php'); ?>

  <main class="flex-grow flex items-center justify-center px-4 py-12">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-2xl">
      <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Confirm Your Booking</h2>

      <div class="space-y-4 text-gray-700">
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Full Name:</span>
          <span>Juan Dela Cruz</span>
        </div>
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Email:</span>
          <span>juan@example.com</span>
        </div>
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Room Type:</span>
          <span>Deluxe Suite</span>
        </div>
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Check-in:</span>
          <span>May 20, 2025</span>
        </div>
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Check-out:</span>
          <span>May 23, 2025</span>
        </div>
        <div class="flex justify-between border-b pb-2">
          <span class="font-medium">Guests:</span>
          <span>2 Adults</span>
        </div>
        <div class="flex justify-between font-semibold text-lg mt-6">
          <span>Total Amount:</span>
          <span>₱9,000.00</span>
        </div>
      </div>

      <div class="mt-8 flex justify-between">
        <a href="/Proj/PHP/booking/edit-booking.php" class="bg-gray-300 text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-400 transition">Edit</a>
        <a href="/Proj/PHP/payment/index.php" class="bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600 transition">Confirm & Pay</a>
      </div>
    </div>
  </main>

  <!-- Include footer here -->
  <?php include('../../elements/footer.php'); ?>
</body>
</html>
