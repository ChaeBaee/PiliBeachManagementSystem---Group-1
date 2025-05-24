<?php
include('../back/room.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Available Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
<?php include('../../elements/navbar.php'); ?>

<div class="max-w-5xl mx-auto p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
    <?php foreach ($rooms as $room): ?>
        <div class="border rounded p-4 shadow">
            <img src="../../assets/<?= htmlspecialchars($room['room_image']) ?>" alt="<?= htmlspecialchars($room['room_type']) ?>" class="w-full h-40 object-cover rounded mb-3" />
            <h2 class="text-xl font-bold"><?= htmlspecialchars($room['room_type']) ?></h2>
            <p><?= htmlspecialchars($room['room_description']) ?></p>
            <p class="mt-2 font-semibold text-blue-600">$<?= htmlspecialchars($room['room_price']) ?> / night</p>

            <form action="../../booking/bookroom/front/bookRoom.php" method="POST" class="mt-4">
                <input type="hidden" name="room_id" value="<?= $room['room_id'] ?>" />
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 w-full">
                    Book Now
                </button>
            </form>
        </div>
    <?php endforeach; ?>
</div>


</body>
<?php include('../../elements/footer.php'); ?>
</html>
