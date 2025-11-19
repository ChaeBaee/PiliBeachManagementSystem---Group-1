<?php
require_once '../back/booking_confirmation_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Pili Beach Resort</title>
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
    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 min-h-screen">
    <?php include('../../elements/navbar.php'); ?>

    <!-- Progress Indicator -->
    <div class="container mx-auto px-4 py-6 mt-16">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-center mb-8">
                <div class="flex items-center space-x-4">
                    <div class="progress-step active w-10 h-10 rounded-full flex items-center justify-center bg-gradient-to-r from-blue-500 to-purple-600 text-white font-semibold">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <div class="w-16 h-1 bg-gray-300"></div>
                    <div class="progress-step w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 font-semibold">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="w-16 h-1 bg-gray-300"></div>
                    <div class="progress-step w-10 h-10 rounded-full flex items-center justify-center bg-gray-200 text-gray-600 font-semibold">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
            <div class="text-center mb-2">
                <span class="text-sm text-gray-600">Step 1 of 3</span>
            </div>
            <h1 class="text-4xl font-bold text-center mb-2 bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                Confirm Your Booking
            </h1>
            <p class="text-gray-600 text-center mb-8">Please review your booking details before proceeding to payment</p>
        </div>
    </div>

    <div class="container mx-auto px-4 pb-8">
        <div class="max-w-5xl mx-auto">
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

            <!-- Room Details Card -->
            <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 card-hover fade-in">
                <div class="flex justify-between items-center mb-6">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                            <i class="fas fa-bed text-white text-xl"></i>
                        </div>
                        <h2 class="text-2xl font-bold text-gray-800">Room Details</h2>
                    </div>
                    <button onclick="openEditModal()" class="flex items-center px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-600 text-white rounded-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 shadow-lg">
                        <i class="fas fa-edit mr-2"></i>Edit Details
                    </button>
                </div>
                
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <?php if ($room['room_image']): ?>
                        <div class="lg:col-span-1">
                            <div class="relative overflow-hidden rounded-xl shadow-lg">
                                <img src="../../assets/<?php echo htmlspecialchars($room['room_image']); ?>"
                                    alt="<?php echo htmlspecialchars($room['room_type']); ?>"
                                    class="w-full h-80 object-cover transform hover:scale-105 transition-transform duration-300">
                                <div class="absolute top-4 right-4 bg-white bg-opacity-90 rounded-lg px-3 py-1">
                                    <span class="text-sm font-semibold text-gray-800">Room <?php echo htmlspecialchars($room['room_number']); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="lg:col-span-2">
                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($room['room_type']); ?></h3>
                                <p class="text-gray-600 leading-relaxed"><?php echo htmlspecialchars($room['room_description']); ?></p>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-blue-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Capacity</p>
                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($room['room_capacity']); ?> guests</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-500 mr-3"></i>
                                        <div>
                                            <p class="text-sm text-gray-600">Room Type</p>
                                            <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($room['room_type']); ?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Guest Information Card -->
            <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 card-hover fade-in">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-teal-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-user text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Guest Information</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Full Name</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="text" value="<?php echo htmlspecialchars($user['user_name']); ?>"
                                class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                readonly>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Email Address</label>
                        <div class="relative">
                            <i class="fas fa-envelope absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="email" value="<?php echo htmlspecialchars($user['user_email']); ?>"
                                class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                readonly>
                        </div>
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Phone Number</label>
                        <div class="relative">
                            <i class="fas fa-phone absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            <input type="tel" value="<?php echo htmlspecialchars($user['user_phone']); ?>"
                                class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking Details Card -->
            <div class="glass-effect rounded-2xl shadow-xl p-8 mb-8 card-hover fade-in">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl flex items-center justify-center mr-4">
                        <i class="fas fa-calendar-alt text-white text-xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800">Booking Details</h2>
                </div>
                
                <form method="POST" action="" class="space-y-6">
                    <input type="hidden" name="booking_token" value="<?php echo $_SESSION['booking_token']; ?>">
                    
                    <!-- Dates Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Check-in Date</label>
                            <div class="relative">
                                <i class="fas fa-sign-in-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="date" name="check_in" id="check_in"
                                    value="<?php echo htmlspecialchars($check_in); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                    readonly>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Check-out Date</label>
                            <div class="relative">
                                <i class="fas fa-sign-out-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="date" name="check_out" id="check_out"
                                    value="<?php echo htmlspecialchars($check_out); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Times Section -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Check-in Time</label>
                            <div class="relative">
                                <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" value="<?php echo htmlspecialchars($_GET['checkin_time'] ?? '14:00'); ?>"
                                    class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                    readonly>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-sm font-semibold text-gray-700">Check-out Time</label>
                            <div class="relative">
                                <i class="fas fa-clock absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="text" value="12:00 PM"
                                    class="pl-10 w-full rounded-lg border-gray-200 bg-gray-50 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                                    readonly>
                            </div>
                        </div>
                    </div>

                    <!-- Pricing Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-100">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pricing Summary</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Number of Nights:</span>
                                <span class="font-semibold text-gray-800"><?php echo $nights; ?> nights</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">Price per Night:</span>
                                <span class="font-semibold text-gray-800">₱<?php echo number_format($room['room_price']); ?></span>
                            </div>
                        </div>
                        <div class="border-t border-gray-200 pt-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xl font-bold text-gray-800">Total Amount:</span>
                                <span class="text-3xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                                    ₱<?php echo number_format($total_price); ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row justify-end space-y-3 sm:space-y-0 sm:space-x-4 pt-6">
                        <a href="bookings.php"
                            class="flex items-center justify-center px-8 py-3 border-2 border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 hover:border-gray-400 transition-all duration-300 font-semibold">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Rooms
                        </a>
                        <button type="submit" class="flex items-center justify-center px-8 py-3 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-xl hover:from-blue-700 hover:to-purple-700 transition-all duration-300 font-semibold shadow-lg transform hover:scale-105">
                            <i class="fas fa-credit-card mr-2"></i>Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Enhanced Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-2xl rounded-2xl bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Edit Booking Details</h3>
                    <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <form id="editForm" class="space-y-4">
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Check-in Date</label>
                        <input type="date" id="modal_check_in" name="modal_check_in"
                            class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300"
                            min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">Check-out Date</label>
                        <input type="date" id="modal_check_out" name="modal_check_out"
                            class="w-full rounded-lg border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 transition-all duration-300">
                    </div>
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-all duration-300">
                            Cancel
                        </button>
                        <button type="button" onclick="updateBookingDetails()"
                            class="px-4 py-2 bg-gradient-to-r from-blue-600 to-purple-600 text-white rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all duration-300">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalCheckIn = document.getElementById('modal_check_in');
        const modalCheckOut = document.getElementById('modal_check_out');

        modalCheckIn.value = document.getElementById('check_in').value;
        modalCheckOut.value = document.getElementById('check_out').value;

        modalCheckIn.addEventListener('change', function () {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            modalCheckOut.min = nextDay.toISOString().split('T')[0];

            if (modalCheckOut.value && new Date(modalCheckOut.value) <= checkInDate) {
                modalCheckOut.value = nextDay.toISOString().split('T')[0];
            }
        });

        modalCheckOut.addEventListener('change', function () {
            const checkInDate = new Date(modalCheckIn.value);
            const checkOutDate = new Date(this.value);

            if (checkOutDate <= checkInDate) {
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                this.value = nextDay.toISOString().split('T')[0];
            }
        });

        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function updateBookingDetails() {
            const newCheckIn = modalCheckIn.value;
            const newCheckOut = modalCheckOut.value;

            document.getElementById('check_in').value = newCheckIn;
            document.getElementById('check_out').value = newCheckOut;

            const checkInDate = new Date(newCheckIn);
            const checkOutDate = new Date(newCheckOut);
            const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            const totalPrice = nights * <?php echo $room['room_price']; ?>;

            // Update nights display
            const nightsElement = document.querySelector('span.font-semibold.text-gray-800');
            if (nightsElement && nightsElement.textContent.includes('nights')) {
                nightsElement.textContent = nights + ' nights';
            }

            // Update total price
            const totalElement = document.querySelector('.text-3xl.font-bold');
            if (totalElement) {
                totalElement.textContent = '₱' + totalPrice.toLocaleString();
            }

            closeEditModal();
        }

        window.onclick = function (event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
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