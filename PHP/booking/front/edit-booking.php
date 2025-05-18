<?php include('../../elements/navbar.php'); ?>

<?php

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking'])) {
    header("Location: /Proj/PHP/booking/front/2ndlogin.php");
    exit();
}

$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';
$booking = $_SESSION['booking'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Edit Booking Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        peach: '#faebe1',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-peach min-h-screen flex flex-col">

<main class="flex-grow flex flex-col items-center justify-start px-4 pt-24 pb-12 relative overflow-hidden min-h-[800px]">
    <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/20 rounded-full blur-3xl z-0"></div>

    <div class="bg-white p-10 rounded-3xl shadow-2xl w-full max-w-2xl z-10 opacity-90 relative">
        <a href="/Proj/PHP/booking/front/booking-confirmation.php" class="absolute top-4 left-4 text-gray-600 hover:text-black">
            ← Back
        </a>

        <h2 class="text-3xl font-extrabold text-center text-gray-800 mb-8">Edit Booking Details</h2>

        <form action="/Proj/PHP/booking/back/save-booking.php" method="POST" class="space-y-5 text-gray-700 text-base">
            <div>
                <label class="font-semibold block mb-1">Full Name</label>
                <input type="text" name="full_name" value="<?= htmlspecialchars($user_name) ?>" class="w-full border px-4 py-2 rounded-xl" required>
            </div>
            <div>
                <label class="font-semibold block mb-1">Email</label>
                <input type="email" name="email" value="<?= htmlspecialchars($user_email) ?>" class="w-full border px-4 py-2 rounded-xl" required>
            </div>
            <div>
                <label class="font-semibold block mb-1">Room Type</label>
                <select name="room_type" class="w-full border px-4 py-2 rounded-xl" required>
                    <option <?= $booking['room_type'] == 'Deluxe Suite' ? 'selected' : '' ?>>Deluxe Suite</option>
                    <option <?= $booking['room_type'] == 'Standard Room' ? 'selected' : '' ?>>Standard Room</option>
                    <option <?= $booking['room_type'] == 'Family Room' ? 'selected' : '' ?>>Family Room</option>
                </select>
            </div>
            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="font-semibold block mb-1">Check-in</label>
                    <input type="date" name="checkin" value="<?= htmlspecialchars($booking['checkin']) ?>" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
                <div class="flex-1">
                    <label class="font-semibold block mb-1">Check-out</label>
                    <input type="date" name="checkout" value="<?= htmlspecialchars($booking['checkout']) ?>" class="w-full border px-4 py-2 rounded-xl" required>
                </div>
            </div>
            <div>
                <label class="font-semibold block mb-1">Guests</label>
                <input type="text" name="guests" value="<?= htmlspecialchars($booking['guests']) ?>" class="w-full border px-4 py-2 rounded-xl" required>
            </div>

            <div class="mt-10 flex justify-between gap-4">
                <a href="/Proj/PHP/booking/front/booking-confirmation.php" class="flex-1 bg-gray-200 text-gray-800 py-3 rounded-xl text-center font-medium hover:bg-gray-300 transition duration-200">
                    Cancel
                </a>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-xl text-center font-semibold hover:bg-blue-700 transition duration-200">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</main>

<?php include('../../elements/footer.php'); ?>
</body>
</html>