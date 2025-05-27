<?php
include('../back/room.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Available Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .room-card {
            transition: all 0.3s ease;
        }

        .room-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1),
            0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .availability-badge {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800">

<!-- ✅ Navbar -->
<?php include('../../elements/navbar.php'); ?>

<!-- ✅ Main Content -->
<main class="max-w-7xl mx-auto px-5 pt-28 pb-16">
    <h1 class="text-4xl font-bold text-center text-blue-700 mb-12">Available Rooms</h1>

    <div class="grid grid-cols-1 sm:grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
        <?php foreach ($rooms as $room): ?>
            <div class="room-card bg-white border border-gray-200 rounded-xl overflow-hidden shadow">
                <div class="relative">
                    <img src="../../assets/<?= htmlspecialchars($room['room_image']) ?>"
                         alt="<?= htmlspecialchars($room['room_type']) ?>"
                         class="w-full h-48 object-cover">

                </div>
                <div class="p-5">
                    <h2 class="text-lg font-bold mb-2"><?= htmlspecialchars($room['room_type']) ?></h2>
                    <p class="text-sm text-gray-600 mb-1">
                        <i class="fas fa-users text-blue-500 mr-1"></i>
                        Up to <?= htmlspecialchars($room['room_capacity']) ?> guest<?= $room['room_capacity'] > 1 ? 's' : '' ?>
                    </p>
                    <p class="text-sm text-gray-600 mb-4">
                        <?= htmlspecialchars($room['room_description']) ?>
                    </p>
                    <p class="text-blue-600 text-xl font-bold mb-4">₱<?= number_format($room['room_price'], 2) ?> <span class="text-sm text-gray-500">/ night</span></p>

                    <form action="/Proj/PHP/booking-confirmation.php" method="POST">
                        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>">
                        <button type="submit"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 rounded-lg font-semibold transition">
                            Book Now
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</main>

<!-- ✅ Footer -->
<?php include('../../elements/footer.php'); ?>

</body>
</html>
