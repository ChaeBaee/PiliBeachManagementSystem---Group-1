<?php
require_once '../back/invoice_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .progress-step {
            transition: all 0.3s ease;
        }
        
        .progress-step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .progress-step.completed {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .fade-in {
            animation: fadeIn 0.6s ease-in-out;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-paid {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
        }
        
        .status-pending {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
        }
        
        .status-cancelled {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }
        
        .invoice-number {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            font-weight: 600;
        }
        
        .section-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }
        
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
        }
        
        .info-item {
            padding: 1rem;
            background: #f8fafc;
            border-radius: 0.5rem;
            border-left: 4px solid #3b82f6;
        }
        
        .price-breakdown {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 1rem;
            padding: 1.5rem;
        }
        
        .total-amount {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            padding: 1.5rem;
            border-radius: 1rem;
            text-align: center;
        }
        
        .print-button {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
            transition: all 0.3s ease;
        }
        
        .print-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .action-button {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            transition: all 0.3s ease;
        }
        
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        @media print {
            body * {
                visibility: hidden;
            }
            
            .invoice-container,
            .invoice-container * {
                visibility: visible;
            }
            
            .invoice-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                background: white;
            }
            
            .no-print {
                display: none !important;
            }
            
            .glass-effect {
                background: white !important;
                box-shadow: none !important;
            }
        }
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <?php include '../../elements/navbar.php'; ?>

    <!-- Progress Indicator -->
    <div class="container mx-auto px-4 py-6 mt-16">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="progress-step completed w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="w-16 h-1 bg-green-500"></div>
                    <div class="progress-step completed w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-r from-green-500 to-emerald-600 text-white font-semibold">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="w-16 h-1 bg-green-500"></div>
                    <div class="progress-step active w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold">
                        <i class="fas fa-file-invoice"></i>
                    </div>
                </div>
            </div>
            <div class="text-center mb-2">
                <span class="text-sm text-gray-600">Step 3 of 3</span>
            </div>
            <h1 class="text-4xl font-bold text-center mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Invoice Generated
            </h1>
            <p class="text-gray-600 text-center mb-8">Your official billing document is ready</p>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-8">
        <div class="max-w-5xl mx-auto">
            <div class="invoice-container">
                <!-- Invoice Header Card -->
                <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 card-hover fade-in">
                    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
                        <div class="flex items-center mb-4 lg:mb-0">
                            <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center mr-4">
                                <i class="fas fa-file-invoice text-white text-2xl"></i>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-800">Pili Beach Resort</h2>
                                <p class="text-gray-600">Official Invoice</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="invoice-number mb-2">
                                INV-<?php echo str_pad($invoice['invoice_id'], 6, '0', STR_PAD_LEFT); ?>
                            </div>
                            <p class="text-gray-600 mb-1">
                                <i class="fas fa-calendar mr-2"></i>
                                <?php echo date('F d, Y', strtotime($invoice['invoice_date_created'])); ?>
                            </p>
                            <div class="status-badge <?php echo 'status-' . strtolower($invoice['invoice_status']); ?>">
                                <?php echo ucfirst($invoice['invoice_status']); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column: Guest & Booking Info -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Guest Information Card -->
                        <div class="section-card p-6 card-hover fade-in">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-user text-white text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Guest Information</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Full Name</p>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($invoice['user_name']); ?></p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Email Address</p>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($invoice['user_email']); ?></p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Phone Number</p>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($invoice['user_phone']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Booking Details Card -->
                        <div class="section-card p-6 card-hover fade-in">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-bed text-white text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Booking Details</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Room Type</p>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($invoice['room_type']); ?></p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Room Number</p>
                                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($invoice['room_number']); ?></p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Check In</p>
                                    <p class="font-semibold text-gray-800">
                                        <i class="fas fa-sign-in-alt mr-1 text-blue-500"></i>
                                        <?php echo date('F d, Y', strtotime($invoice['booking_check_in'])); ?>
                                    </p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Check Out</p>
                                    <p class="font-semibold text-gray-800">
                                        <i class="fas fa-sign-out-alt mr-1 text-red-500"></i>
                                        <?php echo date('F d, Y', strtotime($invoice['booking_check_out'])); ?>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Card -->
                        <div class="section-card p-6 card-hover fade-in">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-credit-card text-white text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Payment Details</h3>
                            </div>
                            <div class="info-grid">
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Payment Method</p>
                                    <p class="font-semibold text-gray-800">
                                        <i class="fas fa-mobile-alt mr-2 text-green-500"></i>GCash
                                    </p>
                                </div>
                                <div class="info-item">
                                    <p class="text-sm text-gray-600 mb-1">Payment Status</p>
                                    <div class="status-badge <?php echo 'status-' . strtolower($invoice['payment_status']); ?>">
                                        <?php echo ucfirst($invoice['payment_status']); ?>
                                    </div>
                                </div>
                                <?php if ($invoice['payment_proof']): ?>
                                    <div class="info-item col-span-2">
                                        <p class="text-sm text-gray-600 mb-1">Payment Proof</p>
                                        <a href="uploads/payment_proofs/<?php echo htmlspecialchars($invoice['payment_proof']); ?>"
                                            target="_blank" class="inline-flex items-center text-blue-600 hover:text-blue-800 font-semibold">
                                            <i class="fas fa-external-link-alt mr-2"></i>
                                            View Payment Proof
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Price Breakdown -->
                    <div class="lg:col-span-1">
                        <div class="price-breakdown card-hover fade-in">
                            <div class="flex items-center mb-4">
                                <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl flex items-center justify-center mr-4">
                                    <i class="fas fa-calculator text-white text-xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-gray-800">Price Breakdown</h3>
                            </div>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <div>
                                        <p class="font-semibold text-gray-800">Room Rate</p>
                                        <p class="text-sm text-gray-600">₱<?php echo number_format($invoice['room_price']); ?> × <?php echo $nights; ?> nights</p>
                                    </div>
                                    <span class="font-semibold text-gray-800">₱<?php echo number_format($invoice['room_price'] * $nights); ?></span>
                                </div>
                                
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-sm text-gray-600">Taxes & Fees</span>
                                    <span class="font-semibold text-gray-800">₱0.00</span>
                                </div>
                                
                                <div class="flex justify-between items-center p-3 bg-white rounded-lg">
                                    <span class="text-sm text-gray-600">Discount</span>
                                    <span class="font-semibold text-green-600">-₱0.00</span>
                                </div>
                            </div>
                            
                            <div class="total-amount mt-6">
                                <p class="text-sm opacity-90 mb-1">Total Amount</p>
                                <p class="text-3xl font-bold">₱<?php echo number_format($invoice['invoice_total_amount']); ?></p>
                                <p class="text-sm opacity-90 mt-1">Philippine Peso</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row justify-center space-y-3 sm:space-y-0 sm:space-x-4 mt-8 no-print">
                    <button onclick="window.print()" class="print-button flex items-center justify-center px-8 py-3 rounded-xl font-semibold shadow-lg">
                        <i class="fas fa-print mr-2"></i>Print Invoice
                    </button>
                    <button onclick="downloadPDF()" class="print-button flex items-center justify-center px-8 py-3 rounded-xl font-semibold shadow-lg">
                        <i class="fas fa-download mr-2"></i>Download PDF
                    </button>
                    <a href="../../home.php" class="action-button flex items-center justify-center px-8 py-3 rounded-xl font-semibold shadow-lg">
                        <i class="fas fa-home mr-2"></i>Back to Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add fade-in animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.fade-in');
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
            });
        });

        // Download PDF function (placeholder)
        function downloadPDF() {
            alert('PDF download feature would be implemented here with a PDF generation library like jsPDF or a server-side solution.');
        }
    </script>
</body>

</html>