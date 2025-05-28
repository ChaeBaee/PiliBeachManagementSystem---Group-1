<?php
require_once '../back/bookings_backend.php';

$checkIn = $checkOut = $guest = $roomType = "";
$checkInMsg = $checkOutMsg = $guestMsg = "";
$today = date('Y-m-d');
$hasError = false;
$rooms = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $checkIn = $_POST["checkin"] ?? "";
    $checkOut = $_POST["checkout"] ?? "";
    $guest = $_POST["guests"] ?? "";
    $roomType = $_POST["room_type"] ?? "";

    if (empty($checkIn)) {
        $checkInMsg = "Please enter check-in date.";
        $hasError = true;
    } elseif ($checkIn < $today) {
        $checkInMsg = "Check-in date cannot be in the past.";
        $hasError = true;
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please enter check-out date.";
        $hasError = true;
    } elseif ($checkOut <= $checkIn) {
        $checkOutMsg = "Check-out date must be after check-in date.";
        $hasError = true;
    }

    if (empty($guest) || intval($guest) < 1) {
        $guestMsg = "Guests must be at least 1.";
        $hasError = true;
    }

    // If no errors, fetch rooms
    if (!$hasError) {
        $rooms = getAvailableRooms($checkIn, $checkOut, $guest, $roomType);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Book a Room - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .hero-pattern {
            background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)),
                url('../../assets/home.jpg');
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
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
                0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .search-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .availability-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }

        .price-tag {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include('../../elements/navbar.php'); ?>

    <div class="hero-pattern text-white py-40">
        <div class="container mx-auto px-4 text-center">
            <h1 class="text-5xl font-bold mb-6">Find Your Perfect Stay</h1>
            <p class="text-xl text-gray-200">Experience luxury and comfort at Pili Beach Resort</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12 -mt-24">
        <div class="search-container rounded-2xl shadow-xl p-6 w-full lg:w-auto">
            <form action="" method="POST" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Room Type</label>
                    <select name="room_type"
                        class="w-full rounded-xl border focus:border-blue-500 focus:ring focus:ring-blue-300 focus:ring-opacity-50">
                        <option value="">All Room Types</option>
                        <?php foreach ($room_types as $type): ?>
                            <option value="<?= htmlspecialchars($type) ?>" <?= ($roomType === $type) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($type) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check In</label>
                    <input type="date" name="checkin" id="check_in" value="<?= htmlspecialchars($checkIn) ?>"
                        class="w-full rounded-xl border = focus:border-blue-500 focus:ring focus:ring-blue-300 focus:ring-opacity-50" />
                    <?php if ($checkInMsg): ?>
                        <span class="text-sm text-red-500"><?= $checkInMsg ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Check Out</label>
                    <input type="date" name="checkout" id="check_out" value="<?= htmlspecialchars($checkOut) ?>"
                        class="w-full rounded-xl border focus:border-blue-500 focus:ring focus:ring-blue-300 focus:ring-opacity-50" />
                    <?php if ($checkOutMsg): ?>
                        <span class="text-sm text-red-500"><?= $checkOutMsg ?></span>
                    <?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Guests</label>
                    <input type="number" name="guests" value="<?= htmlspecialchars($guest) ?>" min="1"
                        class="w-full rounded-xl border focus:border-blue-500 focus:ring focus:ring-blue-300 focus:ring-opacity-50" />
                    <?php if ($guestMsg): ?>
                        <span class="text-sm text-red-500"><?= $guestMsg ?></span>
                    <?php endif; ?>
                </div>

                <div class="flex items-end">
                    <button type="submit"
                        class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-xl px-6 py-3.5 font-semibold shadow-lg hover:shadow-xl transition duration-200">
                        <i class="fas fa-search mr-2"></i> Search Rooms
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-12">
            <?php if (!$hasError && $_SERVER["REQUEST_METHOD"] == "POST"): ?>
                <?php if (empty($rooms)): ?>
                    <div
                        class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-6 py-4 rounded-xl shadow-md flex items-center">
                        <i class="fas fa-exclamation-circle mr-2"></i> No rooms available for the selected criteria.
                    </div>
                <?php else: ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-card bg-white rounded-xl overflow-hidden shadow">
                                <img src="../../assets/<?= htmlspecialchars($room['room_image']) ?>" alt=""
                                    class="w-full h-64 object-cover" />
                                <div class="p-6">
                                    <h3 class="text-xl font-bold mb-3"><?= htmlspecialchars($room['room_type']) ?></h3>
                                    <div class="mb-2 text-gray-600 flex items-center">
                                        <i class="fas fa-door-open text-blue-500 mr-2"></i>
                                        Room <?= htmlspecialchars($room['room_number']) ?>
                                    </div>
                                    <div class="mb-4 text-gray-600 flex items-center">
                                        <i class="fas fa-users text-blue-500 mr-2"></i>
                                        Up to <?= htmlspecialchars($room['room_capacity']) ?> guests
                                    </div>
                                    <div class="flex justify-between items-center mb-6">
                                        <span
                                            class="text-2xl font-bold text-blue-600">₱<?= number_format($room['room_price']) ?></span>
                                        <span class="text-gray-500">per night</span>
                                    </div>
                                    <a href="booking-confirmation.php?room_id=<?= $room['room_id'] ?>&check_in=<?= $checkIn ?>&check_out=<?= $checkOut ?>"
                                        class="block bg-blue-600 hover:bg-blue-700 text-white text-center py-3 rounded-xl shadow hover:shadow-lg transition">
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
    </script>
</body>

</html>