<?php
require_once 'backend/bookings_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book a Room - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .hero-pattern {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('images/hero-bg.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .room-card {
            transition: all 0.3s ease;
            border: 1px solid rgba(0, 0, 0, 0.1);
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-2px);
        }

        .price-tag {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }

        .availability-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include 'elements/navbar.php'; ?>

    <div class="hero-pattern text-white py-40">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl md:text-6xl font-bold mb-6 animate-fade-in">Find Your Perfect Stay</h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-200 animate-fade-in" style="animation-delay: 0.2s">Experience
                luxury and comfort at Pili Beach Resort</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12 -mt-24">

        <div class="search-container rounded-2xl shadow-xl p-6 w-full lg:w-auto">
            <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Type</label>
                    <select name="room_type"
                        class="input-focus w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Room Types</option>
                        <?php foreach ($room_types as $type): ?>
                            <option value="<?php echo htmlspecialchars($type); ?>">
                                <?php echo htmlspecialchars($type); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check In</label>
                    <div class="relative">
                        <input type="date" name="check_in" id="check_in"
                            class="input-focus w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check Out</label>
                    <div class="relative">
                        <input type="date" name="check_out" id="check_out"
                            class="input-focus w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500 pr-10">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Guests</label>
                    <input type="number" name="guests" placeholder="Number of guests" min="1"
                        class="input-focus w-full rounded-xl border-gray-300 focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl px-6 py-3.5 hover:from-blue-700 hover:to-blue-800 transition duration-200 flex items-center justify-center font-semibold shadow-lg hover:shadow-xl">
                        <i class="fas fa-search mr-2"></i>
                        Search Rooms
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-12">
            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-xl shadow-md">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php elseif (isset($rooms)): ?>
                <?php if (empty($rooms)): ?>
                    <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-xl shadow-md">
                        <i class="fas fa-exclamation-circle mr-2"></i>
                        No rooms available for the selected criteria.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-card bg-white rounded-xl overflow-hidden">
                                <?php if ($room['room_image']): ?>
                                    <div class="relative">
                                        <img src="assets/<?php echo htmlspecialchars($room['room_image']); ?>"
                                            alt="<?php echo htmlspecialchars($room['room_type']); ?>" class="w-full h-64 object-cover">
                                        <div
                                            class="absolute top-4 right-4 availability-badge text-white px-4 py-1.5 rounded-full text-sm font-semibold shadow-lg">
                                            Available
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-3 text-gray-800">
                                        <?php echo htmlspecialchars($room['room_type']); ?>
                                    </h3>
                                    <div class="flex items-center text-gray-600 mb-2">
                                        <i class="fas fa-door-open mr-2 text-blue-500"></i>
                                        Room <?php echo htmlspecialchars($room['room_number']); ?>
                                    </div>
                                    <div class="flex items-center text-gray-600 mb-4">
                                        <i class="fas fa-users mr-2 text-blue-500"></i>
                                        Up to <?php echo htmlspecialchars($room['room_capacity']); ?> guests
                                    </div>
                                    <div class="flex items-center justify-between mb-6">
                                        <span class="text-2xl font-bold text-blue-600">
                                            ₱<?php echo number_format($room['room_price']); ?>
                                        </span>
                                        <span class="text-gray-500">per night</span>
                                    </div>
                                    <a href="booking-confirmation.php?room_id=<?php echo $room['room_id']; ?>&check_in=<?php echo $check_in; ?>&check_out=<?php echo $check_out; ?>"
                                        class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white text-center py-3 rounded-xl hover:from-blue-700 hover:to-blue-800 transition duration-200 font-semibold shadow-lg hover:shadow-xl">
                                        Book Now
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const checkInInput = document.getElementById('check_in');
        const checkOutInput = document.getElementById('check_out');

        const today = new Date().toISOString().split('T')[0];
        checkInInput.min = today;

        checkInInput.addEventListener('change', function () {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOutInput.min = nextDay.toISOString().split('T')[0];

            if (checkOutInput.value && new Date(checkOutInput.value) <= checkInDate) {
                checkOutInput.value = nextDay.toISOString().split('T')[0];
            }
        });

        checkOutInput.addEventListener('change', function () {
            const checkInDate = new Date(checkInInput.value);
            const checkOutDate = new Date(this.value);

            if (checkOutDate <= checkInDate) {
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                this.value = nextDay.toISOString().split('T')[0];
            }
        });
    </script>
</body>

</html>