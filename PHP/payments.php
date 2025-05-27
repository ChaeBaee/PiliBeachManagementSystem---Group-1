<?php
require_once 'backend/payments_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'elements/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Payment Details</h1>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Booking Summary</h2>
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Room Type</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($booking['room_type']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Room Number</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($booking['room_number']); ?></p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Check In</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($booking['booking_check_in']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Check Out</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($booking['booking_check_out']); ?></p>
                        </div>
                    </div>
                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Amount:</span>
                            <span
                                class="text-2xl font-bold text-blue-600">₱<?php echo number_format($booking['booking_total_price']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-xl font-semibold mb-4">GCash Payment</h2>
                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">GCash Account Number</label>
                        <input type="text" name="gcash_number" required
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Enter your GCash account number">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                            <div class="space-y-1 text-center">
                                <i class="fas fa-upload text-gray-400 text-3xl mb-2"></i>
                                <div class="flex text-sm text-gray-600">
                                    <label for="payment_proof"
                                        class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                        <span>Upload a file</span>
                                        <input id="payment_proof" name="payment_proof" type="file" class="sr-only"
                                            accept=".jpg,.jpeg,.png,.pdf" required>
                                    </label>
                                    <p class="pl-1">or drag and drop</p>
                                </div>
                                <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="?cancel=1" onclick="return confirm('Are you sure you want to cancel this booking?')"
                            class="px-6 py-2 border border-red-300 rounded-md text-red-700 hover:bg-red-50">
                            Cancel Booking
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Submit Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('payment_proof').addEventListener('change', function (e) {
            const fileName = e.target.files[0]?.name;
            if (fileName) {
                const label = this.parentElement;
                label.textContent = fileName;
            }
        });
    </script>
</body>

</html>