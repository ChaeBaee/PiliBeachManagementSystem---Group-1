<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Secure Payment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex flex-col">


  <?php include('elements/navbar.php'); ?>
  <?php
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
      header("Location: /Proj/PHP/booking/front/2ndlogin.php");
      exit();
  }
  $booking = $_SESSION['booking'];
  ?>

  <main class="flex-grow flex items-center justify-center p-6">
    <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-2xl relative">

      
      <a href="/Proj/PHP/booking/front/booking-confirmation.php" class="absolute top-4 left-4 text-gray-600 hover:text-black text-sm flex items-center">
      
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
        </svg>
        Back
      </a>

      <h2 class="text-2xl font-bold text-gray-800 mb-6 text-center">Complete Your Payment</h2>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
       
        <div class="bg-gray-100 p-4 rounded-xl shadow-sm">
          <h3 class="text-lg font-semibold mb-3">Booking Summary</h3>
            <p class="text-sm text-gray-700">Room: <strong><?= htmlspecialchars($booking['room_type']) ?></strong></p>
            <p class="text-sm text-gray-700">Guests: <strong><?= htmlspecialchars($booking['guests']) ?></strong></p>
            <p class="text-sm text-gray-700">Check-in: <strong><?= htmlspecialchars($booking['checkin']) ?></strong></p>
            <p class="text-sm text-gray-700">Check-out: <strong><?= htmlspecialchars($booking['checkout']) ?></strong></p>

          <hr class="my-3">

          <p class="text-sm">Subtotal: <span class="float-right">₱3,000</span></p>
          <p class="text-sm">Service Fee: <span class="float-right">₱200</span></p>
          <p class="text-sm font-bold">Total: <span class="float-right">₱3,200</span></p>

          <p class="text-xs text-gray-500 mt-4">Need help? <a href="/contact.php" class="text-blue-600 underline">Contact support</a></p>
        </div>

        
        <form method="POST" action="/Proj/PHP/booking/process_payment.php" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Select Payment Method</label>
            <select name="payment_method" id="payment_method" required class="w-full px-3 py-2 border rounded-lg">
              <option value="">-- Choose Method --</option>
              <option value="gcash">Gcash</option>
              <option value="card">Credit / Debit Card</option>
              <option value="cod">Cash on Arrival</option>
            </select>
          </div>

      
          <div id="gcash_fields" class="hidden">
            <label class="block text-sm font-medium text-gray-700 mb-1">Gcash Reference Number</label>
            <input type="text" name="gcash_ref" class="w-full px-3 py-2 border rounded-lg" placeholder="e.g., 1234XXXX5678">
          </div>

       
          <div id="card_fields" class="hidden space-y-2">
            <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
            <input type="text" name="card_number" class="w-full px-3 py-2 border rounded-lg" placeholder="1234 5678 9012 3456">

            <div class="flex gap-2">
              <input type="text" name="expiry" placeholder="MM/YY" class="w-1/2 px-3 py-2 border rounded-lg">
              <input type="text" name="cvv" placeholder="CVV" class="w-1/2 px-3 py-2 border rounded-lg">
            </div>
          </div>

          <p class="text-xs text-gray-500 mt-2">
            🔒 All payments are secured with end-to-end encryption.
          </p>

          <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Pay ₱3,200
          </button>
        </form>
      </div>
    </div>
  </main>

 
  <?php include('elements/footer.php'); ?>

  <script>
    const methodSelect = document.getElementById('payment_method');
    const gcashFields = document.getElementById('gcash_fields');
    const cardFields = document.getElementById('card_fields');

    methodSelect.addEventListener('change', function () {
      gcashFields.classList.add('hidden');
      cardFields.classList.add('hidden');

      if (this.value === 'gcash') gcashFields.classList.remove('hidden');
      else if (this.value === 'card') cardFields.classList.remove('hidden');
    });
  </script>
</body>
</html>
