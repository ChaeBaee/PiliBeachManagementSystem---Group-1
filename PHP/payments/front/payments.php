<?php
require_once '../back/payments_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include '../../elements/navbar.php'; ?>

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
                <h2 class="text-xl font-semibold mb-6">Payment Instructions</h2>

                <div class="bg-blue-50 rounded-lg p-6 mb-8">
                    <h3 class="text-lg font-semibold text-blue-800 mb-4">How to Pay via GCash</h3>
                    <ol class="space-y-3 text-gray-700">
                        <li class="flex items-start">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">1</span>
                            <span>Open your GCash app</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">2</span>
                            <span>Tap "Pay QR"</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">3</span>
                            <span>Scan the QR code below</span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">4</span>
                            <span>Enter amount: <span
                                    class="font-semibold">₱<?php echo number_format($booking['booking_total_price']); ?></span></span>
                        </li>
                        <li class="flex items-start">
                            <span
                                class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">5</span>
                            <span>Upload proof of payment below</span>
                        </li>
                    </ol>
                </div>

                <div class="flex flex-col items-center mb-8">
                    <h3 class="text-lg font-semibold mb-4">Scan QR Code to Pay</h3>
                    <div class="bg-white p-4 rounded-lg shadow-sm border border-gray-200">
                        <img src="../../assets/qr.jpg" alt="GCash QR Code" class="w-64 h-64 object-contain">
                    </div>
                    <p class="text-sm text-gray-500 mt-2">Scan this QR code with your GCash app to make the payment</p>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Payment Proof</label>
                        <div
                            class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-500 transition-colors">
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
                            class="px-6 py-2 border border-red-300 rounded-md text-red-700 hover:bg-red-50 transition-colors">
                            Cancel Booking
                        </a>
                        <button type="submit"
                            class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
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