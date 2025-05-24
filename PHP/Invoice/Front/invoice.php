<?php
include '../Back/invoice.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking Invoice</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex flex-col">

<?php include('../../elements/navbar.php'); ?>

<main class="flex-grow flex items-center justify-center py-8 px-4 pt-24">
    <div class="bg-white shadow-xl rounded-2xl p-8 w-full max-w-2xl">

        <h1 class="text-2xl font-bold text-center mb-6 text-gray-800">Booking Invoice</h1>

        <div class="flex justify-between items-center mb-4 text-sm text-gray-600">
            <div>
                <p><strong>Invoice No:</strong> <?= $invoice_no ?></p>
                <p><strong>Date:</strong> <?= $invoice_date ?></p>
            </div>
            <div class="text-right">
                <p><strong>Paid by:</strong> <?= htmlspecialchars($user_name) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user_email) ?></p>
            </div>
        </div>

        <hr class="mb-6">

        <div class="mb-6">
            <h2 class="text-lg font-semibold mb-2">Booking Summary</h2>
            <ul class="text-sm text-gray-700 space-y-1">
                <li><strong>Room Type:</strong> <?= $room_type ?></li>
                <li><strong>Guests:</strong> <?= $guests ?></li>
                <li><strong>Check-in:</strong> <?= $checkin ?></li>
                <li><strong>Check-out:</strong> <?= $checkout ?></li>
                <li><strong>Nights:</strong> <?= $nights ?></li>
                <li><strong>Payment Method:</strong> <?= ucfirst($payment_method) ?></li>
            </ul>
        </div>

        <div class="bg-gray-50 p-4 rounded-lg">
            <h3 class="text-sm font-medium mb-2">Price Details</h3>
            <div class="flex justify-between text-sm">
                <span>Room Rate (<?= $nights ?> night<?= $nights > 1 ? 's' : '' ?>)</span>
                <span>₱<?= $total_amount ?></span>
            </div>
            <!-- Add service fee if you want, or omit -->
            <!--<div class="flex justify-between text-sm">
              <span>Service Fee</span>
              <span>₱200</span>
            </div>-->
            <hr class="my-2">
            <div class="flex justify-between font-bold text-base">
                <span>Total Paid</span>
                <span>₱<?= $total_amount ?></span>
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

<?php include('../../elements/footer.php'); ?>

</body>
</html>
