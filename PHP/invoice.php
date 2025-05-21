<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Booking Invoice</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex flex-col">


  <?php include('elements/navbar.php'); ?>

  <main class="flex-grow flex items-center justify-center py-8 px-4 pt-24">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-2xl">
      
      <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Booking Invoice</h1>

     
      <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
        <div>
          <p><strong>Invoice No:</strong> INV-2025051701</p>
          <p><strong>Date:</strong> May 17, 2025</p>
        </div>
        <div class="text-right">
          <p><strong>Paid by:</strong> James Bautista</p>
          <p><strong>Email:</strong> james@email.com</p>
        </div>
      </div>

      <hr class="mb-6">

      
      <div class="mb-6">
        <h2 class="text-lg font-semibold mb-2">Booking Summary</h2>
        <ul class="text-sm text-gray-700 space-y-1">
          <li><strong>Room Type:</strong> Deluxe Room</li>
          <li><strong>Guests:</strong> 2 Adults</li>
          <li><strong>Check-in:</strong> May 25, 2025</li>
          <li><strong>Check-out:</strong> May 27, 2025</li>
          <li><strong>Payment Method:</strong> Gcash</li>
        </ul>
      </div>

     
      <div class="bg-gray-50 p-4 rounded-lg">
        <h3 class="text-sm font-medium mb-2">Price Details</h3>
        <div class="flex justify-between text-sm">
          <span>Room Rate (2 nights)</span>
          <span>₱3,000</span>
        </div>
        <div class="flex justify-between text-sm">
          <span>Service Fee</span>
          <span>₱200</span>
        </div>
        <hr class="my-2">
        <div class="flex justify-between font-bold text-base">
          <span>Total Paid</span>
          <span>₱3,200</span>
        </div>
      </div>

      <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">Thank you for booking with Pili Resort!</p>
        <p class="text-sm text-gray-600">A copy of this invoice has been sent to your email.</p>
      </div>

      <div class="mt-6 flex justify-center gap-4">
        <a href="/Proj/PHP/home.php" class="text-sm text-blue-600 hover:underline">Back to Home</a>
        <a href="#" onclick="window.print()" class="text-sm text-gray-700 hover:underline">Print Invoice</a>
      </div>
    </div>
  </main>

 
  <?php include('elements/footer.php'); ?>

</body>
</html>
