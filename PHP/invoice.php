<?php
require_once 'backend/invoice_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <?php include 'elements/navbar.php'; ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-8">

                <div class="flex justify-between items-start mb-8">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Invoice</h1>
                        <p class="text-gray-600">Invoice
                            #<?php echo str_pad($invoice['invoice_id'], 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Date:
                            <?php echo date('F d, Y', strtotime($invoice['invoice_date_created'])); ?>
                        </p>
                        <p class="text-gray-600">Status: <span
                                class="font-semibold text-blue-600"><?php echo ucfirst($invoice['invoice_status']); ?></span>
                        </p>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Guest Information</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Name</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($invoice['user_name']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Email</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($invoice['user_email']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Phone</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($invoice['user_phone']); ?></p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Booking Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Room Type</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($invoice['room_type']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Room Number</p>
                            <p class="font-semibold"><?php echo htmlspecialchars($invoice['room_number']); ?></p>
                        </div>
                        <div>
                            <p class="text-gray-600">Check In</p>
                            <p class="font-semibold">
                                <?php echo date('F d, Y', strtotime($invoice['booking_check_in'])); ?>
                            </p>
                        </div>
                        <div>
                            <p class="text-gray-600">Check Out</p>
                            <p class="font-semibold">
                                <?php echo date('F d, Y', strtotime($invoice['booking_check_out'])); ?>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-8">
                    <h2 class="text-xl font-semibold mb-4">Payment Details</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-gray-600">Payment Method</p>
                            <p class="font-semibold">GCash</p>
                        </div>
                        <div>
                            <p class="text-gray-600">Payment Status</p>
                            <p class="font-semibold text-blue-600"><?php echo ucfirst($invoice['payment_status']); ?>
                            </p>
                        </div>
                        <?php if ($invoice['payment_proof']): ?>
                            <div class="col-span-2">
                                <p class="text-gray-600 mb-2">Payment Proof</p>
                                <a href="uploads/payment_proofs/<?php echo htmlspecialchars($invoice['payment_proof']); ?>"
                                    target="_blank" class="text-blue-600 hover:text-blue-800">
                                    View Payment Proof
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <h2 class="text-xl font-semibold mb-4">Price Breakdown</h2>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Room Rate (₱<?php echo number_format($invoice['room_price']); ?>
                                × <?php echo $nights; ?> nights)</span>
                            <span>₱<?php echo number_format($invoice['room_price'] * $nights); ?></span>
                        </div>
                        <div class="border-t pt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-lg font-semibold">Total Amount</span>
                                <span
                                    class="text-2xl font-bold text-blue-600">₱<?php echo number_format($invoice['invoice_total_amount']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-end space-x-4">
                    <button onclick="window.print()"
                        class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                        <i class="fas fa-print mr-2"></i>Print Invoice
                    </button>
                    <a href="bookings.php" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Back to Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    <style>
        @media print {
            body * {
                visibility: hidden;
            }

            .container,
            .container * {
                visibility: visible;
            }

            .container {
                position: absolute;
                left: 0;
                top: 0;
            }

            button {
                display: none;
            }
        }
    </style>
</body>

</html>