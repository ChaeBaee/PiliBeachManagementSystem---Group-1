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
        
        .success-banner {
            animation: slideDown 0.5s ease-out;
        }
        
        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .loading-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        .pulse-glow {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }
        
        .qr-code-container {
            position: relative;
            overflow: hidden;
        }
        
        .qr-code-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            animation: shimmer 2s infinite;
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }
        
        .upload-area {
            transition: all 0.3s ease;
        }
        
        .upload-area:hover {
            border-color: #3b82f6;
            background-color: #f8fafc;
        }
        
        .upload-area.dragover {
            border-color: #3b82f6;
            background-color: #eff6ff;
            transform: scale(1.02);
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
                    <div class="progress-step active w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="w-16 h-1 bg-gray-300"></div>
                    <div class="progress-step w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 font-semibold">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="text-center mb-2">
                <span class="text-sm text-gray-600">Step 2 of 3</span>
            </div>
            <h1 class="text-4xl font-bold text-center mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Complete Your Payment
            </h1>
            <p class="text-gray-600 text-center mb-8">Secure payment via GCash - Fast and reliable</p>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-8">
        <div class="max-w-6xl mx-auto">
            <?php if (isset($error)): ?>
                <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6 rounded-r-lg fade-in">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700"><?php echo htmlspecialchars($error); ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Left Column: Booking Summary -->
                <div class="lg:col-span-1">
                    <!-- Booking Summary Card -->
                    <div class="glass-effect rounded-2xl shadow-xl p-6 mb-6 card-hover fade-in">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-receipt text-white text-xl"></i>
                            </div>
                            <h2 class="text-xl font-bold text-gray-800">Booking Summary</h2>
                        </div>
                        
                        <div class="space-y-4">
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-bed text-blue-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Room Details</span>
                                </div>
                                <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['room_type']); ?></p>
                                <p class="text-sm text-gray-600">Room <?php echo htmlspecialchars($booking['room_number']); ?></p>
                            </div>
                            
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-calendar-alt text-green-500 mr-2"></i>
                                    <span class="text-sm text-gray-600">Stay Duration</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <p class="text-xs text-gray-500">Check In</p>
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['booking_check_in']); ?></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-gray-500">Check Out</p>
                                        <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($booking['booking_check_out']); ?></p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-lg font-semibold text-gray-800">Total Amount:</span>
                                    <span class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                        ₱<?php echo number_format($booking['booking_total_price']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Status Card -->
                    <div class="glass-effect rounded-2xl shadow-xl p-6 card-hover fade-in">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-yellow-500 to-orange-600 rounded-xl flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">Payment Status</h3>
                        </div>
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                <span class="text-sm font-medium text-yellow-800">Pending Payment</span>
                            </div>
                            <p class="text-xs text-yellow-700 mt-1">Please complete your payment to confirm your booking</p>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Payment Instructions -->
                <div class="lg:col-span-2">
                    <!-- Payment Instructions Card -->
                    <div class="glass-effect rounded-2xl shadow-xl p-8 card-hover fade-in">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center mr-4">
                                <i class="fas fa-mobile-alt text-white text-xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-800">Payment via GCash</h2>
                        </div>

                        <!-- Instructions -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 mb-8 border border-blue-100">
                            <h3 class="text-lg font-semibold text-blue-800 mb-4 flex items-center">
                                <i class="fas fa-info-circle mr-2"></i>
                                How to Pay
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <ol class="space-y-3 text-gray-700">
                                    <li class="flex items-start">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">1</span>
                                        <span>Open your GCash app</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">2</span>
                                        <span>Tap "Pay QR"</span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">3</span>
                                        <span>Scan the QR code</span>
                                    </li>
                                </ol>
                                <ol class="space-y-3 text-gray-700" start="4">
                                    <li class="flex items-start">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">4</span>
                                        <span>Enter amount: <span class="font-semibold text-blue-600">₱<?php echo number_format($booking['booking_total_price']); ?></span></span>
                                    </li>
                                    <li class="flex items-start">
                                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-sm mr-3 flex-shrink-0">5</span>
                                        <span>Upload proof below</span>
                                    </li>
                                </ol>
                            </div>
                        </div>

                        <!-- QR Code Section -->
                        <div class="flex flex-col items-center mb-8">
                            <div class="text-center mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 mb-2">Scan QR Code to Pay</h3>
                                <p class="text-sm text-gray-600">Use your GCash app to scan this QR code</p>
                            </div>
                            <div class="qr-code-container bg-white p-6 rounded-2xl shadow-lg border border-gray-200">
                                <img src="../../assets/qr.jpg" alt="GCash QR Code" class="w-64 h-64 object-contain">
                            </div>
                            <div class="mt-4 text-center">
                                <div class="flex items-center justify-center space-x-2 text-sm text-gray-600">
                                    <i class="fas fa-shield-alt text-green-500"></i>
                                    <span>Secure Payment Gateway</span>
                                </div>
                            </div>
                        </div>

                        <!-- Upload Section -->
                        <form method="POST" enctype="multipart/form-data" class="space-y-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">Upload Payment Proof</label>
                                <div id="uploadArea" class="upload-area mt-1 flex justify-center px-6 pt-8 pb-8 border-2 border-gray-300 border-dashed rounded-xl hover:border-blue-500 transition-all duration-300">
                                    <div class="space-y-3 text-center">
                                        <div class="w-16 h-16 bg-gradient-to-r from-blue-100 to-purple-100 rounded-full flex items-center justify-center mx-auto">
                                            <i class="fas fa-cloud-upload-alt text-blue-500 text-2xl"></i>
                                        </div>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="payment_proof" class="relative cursor-pointer bg-white rounded-lg font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500 px-4 py-2 border border-blue-300 hover:border-blue-400 transition-all duration-300">
                                                <span>Choose File</span>
                                                <input id="payment_proof" name="payment_proof" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.pdf" required>
                                            </label>
                                            <p class="pl-3 py-2">or drag and drop</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, PDF up to 10MB</p>
                                        <div id="fileInfo" class="hidden">
                                            <div class="flex items-center justify-center space-x-2 text-sm text-green-600">
                                                <i class="fas fa-check-circle"></i>
                                                <span id="fileName"></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                                <button type="button" onclick="confirmCancellation()" class="flex items-center justify-center px-6 py-3 border-2 border-red-300 rounded-xl text-red-700 hover:bg-red-50 hover:border-red-400 transition-all duration-300 font-semibold">
                                    <i class="fas fa-times mr-2"></i>Cancel Booking
                                </button>
                                <button type="submit" id="submitBtn" class="flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
                                    <i class="fas fa-paper-plane mr-2"></i>Submit Payment
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Confirmation Modal -->
    <div id="cancellationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Cancel Booking</h3>
                    <button onclick="closeCancellationModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="mb-6">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                            <span class="text-sm font-medium text-red-800">Important Notice</span>
                        </div>
                        <ul class="text-sm text-red-700 mt-2 space-y-1">
                            <li>• No refunds will be provided</li>
                            <li>• Cancellation fees may apply</li>
                            <li>• Booking cannot be reinstated</li>
                        </ul>
                    </div>
                    <p class="text-gray-600">Are you sure you want to cancel this booking?</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button onclick="closeCancellationModal()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300">
                        Keep Booking
                    </button>
                    <a href="?cancel=1" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all duration-300">
                        Cancel Booking
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('payment_proof');
        const fileInfo = document.getElementById('fileInfo');
        const fileName = document.getElementById('fileName');
        const submitBtn = document.getElementById('submitBtn');

        // File upload handling
        fileInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                fileName.textContent = file.name;
                fileInfo.classList.remove('hidden');
                uploadArea.classList.add('border-green-500', 'bg-green-50');
                uploadArea.classList.remove('border-gray-300');
            }
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function (e) {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });

        uploadArea.addEventListener('dragleave', function (e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function (e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                const file = files[0];
                fileName.textContent = file.name;
                fileInfo.classList.remove('hidden');
                uploadArea.classList.add('border-green-500', 'bg-green-50');
                uploadArea.classList.remove('border-gray-300');
            }
        });

        // Form submission with loading state
        document.querySelector('form').addEventListener('submit', function(e) {
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('Please select a payment proof file.');
                return;
            }
            
            submitBtn.innerHTML = '<i class="fas fa-spinner loading-spinner mr-2"></i>Processing...';
            submitBtn.disabled = true;
        });

        // Cancellation modal functions
        function confirmCancellation() {
            document.getElementById('cancellationModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeCancellationModal() {
            document.getElementById('cancellationModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const modal = document.getElementById('cancellationModal');
            if (event.target === modal) {
                closeCancellationModal();
            }
        }

        // Add fade-in animation to cards
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.fade-in');
            cards.forEach((card, index) => {
                card.style.animationDelay = (index * 0.1) + 's';
            });
        });
    </script>
</body>

</html>