<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resort Rooms</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">

    <div class="h-16"></div>
    <?php include('../elements/navbar.php'); ?>
    <?php require_once('../db.php'); ?>

    <div class="container mx-auto py-16 px-4">
        <h1 class="text-3xl font-bold text-center mb-8">Our Rooms</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php
            try {
                $stmt = $conn->query("SELECT * FROM rooms WHERE room_status = 'available'");
                $rooms = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($rooms as $room) {
                    echo '<div class="bg-white shadow-md rounded-lg overflow-hidden">';
                    echo '  <img src="../uploads/' . htmlspecialchars($room['room_type']) . '.jpg" alt="' . htmlspecialchars($room['room_type']) . '" class="w-full h-48 object-cover">';
                    echo '  <div class="p-4">';
                    echo '      <h2 class="text-xl font-semibold mb-2">' . htmlspecialchars($room['room_type']) . '</h2>';
                    echo '      <p class="text-gray-700 mb-4">$' . htmlspecialchars($room['room_price']) . ' per night</p>';
                    echo '      <button class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Book Now</button>';
                    echo '  </div>';
                    echo '</div>';
                }

            } catch (PDOException $e) {
                echo '<p class="text-red-500">Failed to fetch rooms: ' . $e->getMessage() . '</p>';
            }
            ?>
        </div>
    </div>

    <?php include('../elements/footer.php'); ?>

</body>
</html>
