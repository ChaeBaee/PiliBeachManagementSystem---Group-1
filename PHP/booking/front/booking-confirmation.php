<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Booking Confirmation</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            peach: '#faebe1',
          }
        }
      }
    }
  </script>
</head>
<body class="bg-peach min-h-screen flex flex-col">

  <?php include('../../elements/navbar.php'); ?>

  <main class="flex-grow flex flex-col items-center justify-start px-4 pt-24 pb-12 relative overflow-hidden min-h-[800px]">
    
   
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/20 rounded-full blur-3xl z-0"></div>

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-2xl z-0 mt-0 opacity-90">
      <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-8">Review & Confirm Your Booking</h2>

      <div class="space-y-5 text-gray-700 text-base">
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Full Name:</span>
          <span>Juan Dela Cruz</span>
        </div>
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Email:</span>
          <span>juan@example.com</span>
        </div>
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Room Type:</span>
          <span>Deluxe Suite</span>
        </div>
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Check-in:</span>
          <span>May 20, 2025</span>
        </div>
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Check-out:</span>
          <span>May 23, 2025</span>
        </div>
        <div class="flex justify-between border-b pb-3">
          <span class="font-semibold">Guests:</span>
          <span>2 Adults</span>
        </div>
        <div class="flex justify-between text-lg font-bold mt-6">
          <span>Total Amount:</span>
          <span class="text-green-600">₱9,000.00</span>
        </div>
      </div>

      <div class="mt-10 flex justify-between gap-4">
        <a href="/Proj/PHP/booking/front/edit-booking.php" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-xl text-center font-medium hover:bg-gray-300 transition duration-200">
          Edit Details
        </a>
        <a href="/Proj/PHP/payment.php" class="flex-1 bg-green-500 text-white py-3 rounded-xl text-center font-semibold hover:bg-green-600 transition duration-200">
          Confirm & Pay
        </a>
      </div>
    </div>
  </main>

  <?php include('../../elements/footer.php'); ?>

</body>
</html>
