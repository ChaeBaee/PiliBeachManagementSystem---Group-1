<?php
include("../back/bookRoom.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Booking - <?= htmlspecialchars($room['room_type']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<?php include('../../../elements/navbar.php'); ?>

<div class="max-w-3xl mx-auto p-8">
    <h1 class="text-3xl font-bold mb-4">Book Room: <?= htmlspecialchars($room['room_type']) ?></h1>

    <img src="../assets/<?= htmlspecialchars($room['room_image']) ?>" alt="Room Image" class="w-full h-48 object-cover rounded mb-6" />

    <p class="mb-4"><?= htmlspecialchars($room['room_description']) ?></p>
    <p class="mb-6 font-semibold text-blue-600">$<?= htmlspecialchars($room['room_price']) ?> / night</p>

    <form method="POST" action="" class="bg-white p-6 rounded shadow space-y-4">
        <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>" />

        <label class="block">
            <span class="text-gray-700">Check-in Date</span>
            <input
                type="date"
                name="checkin"
                min="<?= $today ?>"
                value="<?= htmlspecialchars($checkIn) ?>"
                required
                class="w-full border rounded px-3 py-2"
            />
            <span class="text-red-600 text-sm"><?= $checkInMsg ?></span>
        </label>

        <label class="block">
            <span class="text-gray-700">Check-out Date</span>
            <input
                type="date"
                name="checkout"
                min="<?= $today ?>"
                value="<?= htmlspecialchars($checkOut) ?>"
                required
                class="w-full border rounded px-3 py-2"
            />
            <span class="text-red-600 text-sm"><?= $checkOutMsg ?></span>
        </label>

        <label class="block">
            <span class="text-gray-700">Guests</span>
            <input
                type="number"
                name="guests"
                min="1"
                max="<?= $room['room_capacity'] ?>"
                value="<?= htmlspecialchars($guest ?: 1) ?>"
                required
                class="w-full border rounded px-3 py-2"
            />
            <small class="text-gray-500">Max guests: <?= $room['room_capacity'] ?></small>
            <span class="text-red-600 text-sm"><?= $guestMsg ?></span>
        </label>

        <button type="submit" class="bg-green-600 text-white px-5 py-2 rounded hover:bg-green-700">
            Confirm Booking
        </button>
    </form>
</div>

<?php include('../../../elements/footer.php'); ?>
</body>
</html>
