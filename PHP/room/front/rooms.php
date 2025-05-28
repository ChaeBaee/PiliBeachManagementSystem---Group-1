<?php
include('../back/room.php');

$room_type = isset($_GET['room_type']) ? $_GET['room_type'] : '';
$price_range = isset($_GET['price_range']) ? $_GET['price_range'] : '';
$capacity = isset($_GET['capacity']) ? $_GET['capacity'] : '';

function filterRooms($rooms, $room_type, $price_range, $capacity)
{
    return array_filter($rooms, function ($room) use ($room_type, $price_range, $capacity) {
        if ($room_type && $room['room_type'] !== $room_type) {
            return false;
        }

        if ($price_range) {
            list($min, $max) = explode('-', $price_range);
            if ($room['room_price'] < $min || ($max !== '+' && $room['room_price'] > $max)) {
                return false;
            }
        }

        if ($capacity) {
            list($min, $max) = explode('-', $capacity);
            if ($room['room_capacity'] < $min || ($max !== '+' && $room['room_capacity'] > $max)) {
                return false;
            }
        }

        return true;
    });
}

$filtered_rooms = $rooms;
if ($room_type || $price_range || $capacity) {
    $filtered_rooms = filterRooms($rooms, $room_type, $price_range, $capacity);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms | Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .room-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }

        .room-image {
            transition: transform 0.5s ease;
        }

        .room-card:hover .room-image {
            transform: scale(1.05);
        }

        .filter-btn {
            transition: all 0.3s ease;
        }

        .filter-btn:hover {
            background-color: #2563eb;
            color: white;
        }

        .filter-btn.active {
            background-color: #2563eb;
            color: white;
        }

        @media (max-width: 768px) {
            .filter-container {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                padding: 1rem;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                z-index: 50;
            }
        }
    </style>
</head>

<body class="bg-gray-50">
    <?php include('../../elements/navbar.php'); ?>

    <header class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-16 px-4 mt-16">
        <div class="max-w-7xl mx-auto">
            <h1 class="text-3xl md:text-4xl font-bold mb-4">Discover Your Perfect Stay</h1>
            <p class="text-blue-100 text-lg">Experience luxury and comfort in our carefully curated accommodations</p>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 py-8">

        <div class="md:hidden mb-4">
            <button id="filterToggle" class="w-full bg-white p-3 rounded-lg shadow flex items-center justify-between">
                <span class="font-medium">Filter Rooms</span>
                <i class="fas fa-sliders-h"></i>
            </button>
        </div>

        <div id="filterSection" class="filter-container md:static md:bg-transparent md:shadow-none md:p-0 mb-8">
            <form id="filterForm" method="GET" class="bg-white rounded-lg shadow-lg p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Room Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Room Type</label>
                        <select name="room_type"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Types</option>
                            <option value="Deluxe Room" <?= $room_type === 'Deluxe Room' ? 'selected' : '' ?>>Deluxe Room
                            </option>
                            <option value="Standard Room" <?= $room_type === 'Standard Room' ? 'selected' : '' ?>>Standard
                                Room</option>
                            <option value="Single Room" <?= $room_type === 'Single Room' ? 'selected' : '' ?>>Single Room
                            </option>
                            <option value="Double Room" <?= $room_type === 'Double Room' ? 'selected' : '' ?>>Double Room
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Price Range</label>
                        <select name="price_range"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Prices</option>
                            <option value="1000-1500" <?= $price_range === '1000-1500' ? 'selected' : '' ?>>₱1,000 - ₱1,500
                            </option>
                            <option value="1500-2000" <?= $price_range === '1500-2000' ? 'selected' : '' ?>>₱1,500 - ₱2,000
                            </option>
                            <option value="2000-2500" <?= $price_range === '2000-2500' ? 'selected' : '' ?>>₱2,000 - ₱2,500
                            </option>
                            <option value="2500-3000" <?= $price_range === '2500-3000' ? 'selected' : '' ?>>₱2,500 - ₱3,000
                            </option>
                            <option value="3000-999999" <?= $price_range === '3000-999999' ? 'selected' : '' ?>>₱3,000+
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Guest Capacity</label>
                        <select name="capacity"
                            class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">All Capacities</option>
                            <option value="1-2" <?= $capacity === '1-2' ? 'selected' : '' ?>>1-2 Guests</option>
                            <option value="3-4" <?= $capacity === '3-4' ? 'selected' : '' ?>>3-4 Guests</option>
                            <option value="5-6" <?= $capacity === '5-6' ? 'selected' : '' ?>>5-6 Guests</option>
                            <option value="7-999" <?= $capacity === '7-999' ? 'selected' : '' ?>>7+ Guests</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4 flex justify-end space-x-4">
                    <button type="reset"
                        class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-300 transition">
                        Reset Filters
                    </button>
                    <button type="submit"
                        class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition">
                        Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <div class="mb-6">
            <p class="text-gray-600">
                Showing <?= count($filtered_rooms) ?> of <?= count($rooms) ?> rooms
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php if (empty($filtered_rooms)): ?>
                <div class="col-span-full text-center py-12">
                    <p class="text-gray-500 text-lg">No rooms found matching your criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($filtered_rooms as $room): ?>
                    <div class="room-card bg-white rounded-xl overflow-hidden shadow-md">
                        <div class="relative overflow-hidden">
                            <img src="../../assets/<?= htmlspecialchars($room['room_image']) ?>"
                                alt="<?= htmlspecialchars($room['room_type']) ?>" class="room-image w-full h-64 object-cover">
                        </div>

                        <div class="p-6">
                            <h2 class="text-xl font-semibold text-gray-800 mb-2">
                                <?= htmlspecialchars($room['room_type']) ?>
                            </h2>

                            <div class="flex items-center text-gray-600 mb-4">
                                <i class="fas fa-users text-blue-500 mr-2"></i>
                                <span>Up to <?= htmlspecialchars($room['room_capacity']) ?>
                                    guest<?= $room['room_capacity'] > 1 ? 's' : '' ?>
                                </span>
                            </div>

                            <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                                <?= htmlspecialchars($room['room_description']) ?>
                            </p>

                            <div class="flex items-baseline mb-6">
                                <span class="text-2xl font-bold text-blue-600">
                                    ₱<?= number_format($room['room_price'], 2) ?>
                                </span>
                                <span class="text-sm text-gray-500 ml-1">/ night</span>
                            </div>

                            <form action="/Proj/PHP/booking/front/bookings.php" method="GET">
                                <input type="hidden" name="room_type" value="<?= htmlspecialchars($room['room_type']) ?>">
                                <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-lg font-medium transition duration-300">
                                    Book Now
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </main>

    <?php include('../../elements/footer.php'); ?>

    <script>
        const filterToggle = document.getElementById('filterToggle');
        const filterSection = document.getElementById('filterSection');

        if (filterToggle) {
            filterToggle.addEventListener('click', () => {
                filterSection.classList.toggle('hidden');
            });
        }

        function handleFilterVisibility() {
            if (window.innerWidth >= 768) {
                filterSection.classList.remove('hidden');
            } else {
                filterSection.classList.add('hidden');
            }
        }

        window.addEventListener('load', handleFilterVisibility);
        window.addEventListener('resize', handleFilterVisibility);
    </script>
</body>

</html>