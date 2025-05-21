<?php
include('../../elements/navbar.php');

$fullName = $roomType = $checkIn = $checkOut = $guest = "";
$fullNameMsg = $roomTypeMsg = $checkInMsg = $checkOutMsg = $guestMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fullName = $_POST["fullName"] ?? "";
    $roomType = $_POST["room_type"] ?? "";
    $checkIn = $_POST["checkin"] ?? "";
    $checkOut = $_POST["checkout"] ?? "";
    $guest = $_POST["guests"] ?? "";

    if (empty($fullName)) {
        $fullNameMsg = "Full Name cannot be empty";
        $fullName = "";
    }

    if (empty($roomType)) {
        $roomTypeMsg = "Please select a room type.";
        $roomType = "";
    }

    if (empty($checkIn)) {
        $checkInMsg = "Please select a check-in date.";
    }

    if (empty($checkOut)) {
        $checkOutMsg = "Please select a check-out date.";
    }

    if (empty($guest)) {
        $guestMsg = "Please enter the number of guests.";
        $guest = "";
    }

    if ($checkIn && $checkOut && $checkOut <= $checkIn) {
        $checkOutMsg = "Check-out date must be after check-in date.";
    }

    if ($fullNameMsg === "" && $roomTypeMsg === "" && $checkInMsg === "" && $checkOutMsg === "" && $guestMsg === "") {
        // Update the 'booking' session array with new values
        $_SESSION['booking']['full_name'] = $fullName;
        $_SESSION['booking']['room_type'] = $roomType;
        $_SESSION['booking']['checkin'] = $checkIn;
        $_SESSION['booking']['checkout'] = $checkOut;
        $_SESSION['booking']['guests'] = $guest;

        // Update user_name session if needed
        $_SESSION['user_name'] = $fullName;

        header('Location: booking-confirmation.php');
        exit();
    }
}

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
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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

        <form action="" method="POST" class="space-y-5 text-gray-700 text-base" novalidate>
            <div>
                <label class="font-semibold block mb-1">Full Name</label>
                <input type="text"
                       name="fullName"
                       value="<?= htmlspecialchars($fullName ?: $user_name) ?>"
                       class="w-full border px-4 py-2 rounded-xl bg-white">
                <span class="text-red-600 text-sm"><?= $fullNameMsg ?></span>
            </div>

            <div>
                <label class="font-semibold block mb-1">Email</label>
                <input
                        type="email"
                        name="email"
                        value="<?= htmlspecialchars($user_email) ?>"
                        class="w-full border px-4 py-2 rounded-xl bg-gray-100 cursor-not-allowed" readonly>
            </div>

            <div>
                <label class="font-semibold block mb-1">Room Type</label>
                <select name="room_type" class="w-full border px-4 py-2 rounded-xl">
                    <option value="" disabled <?= empty($booking['room_type']) && empty($roomType) ? 'selected' : '' ?>>-- Select Room Type --</option>
                    <option value="Deluxe Suite" <?= ($roomType == 'Deluxe Suite' || ($booking['room_type'] == 'Deluxe Suite' && empty($roomType))) ? 'selected' : '' ?>>Deluxe Suite</option>
                    <option value="Standard Room" <?= ($roomType == 'Standard Room' || ($booking['room_type'] == 'Standard Room' && empty($roomType))) ? 'selected' : '' ?>>Standard Room</option>
                    <option value="Family Room" <?= ($roomType == 'Family Room' || ($booking['room_type'] == 'Family Room' && empty($roomType))) ? 'selected' : '' ?>>Family Room</option>
                </select>
                <span class="text-red-600 text-sm"><?= $roomTypeMsg ?></span>
            </div>

            <div class="flex gap-4">
                <div class="flex-1">
                    <label class="font-semibold block mb-1">Check-in</label>
                    <input
                            type="date"
                            name="checkin"
                            value="<?= htmlspecialchars($checkIn ?: ($booking['checkin'] ?? '')) ?>"
                            class="w-full border px-4 py-2 rounded-xl"
                            min="<?= date('Y-m-d') ?>"
                    >
                    <span class="text-red-600 text-sm"><?= $checkInMsg ?></span>
                </div>
                <div class="flex-1">
                    <label class="font-semibold block mb-1">Check-out</label>
                    <input
                            type="date"
                            name="checkout"
                            value="<?= htmlspecialchars($checkOut ?: ($booking['checkout'] ?? '')) ?>"
                            class="w-full border px-4 py-2 rounded-xl"
                            min="<?= date('Y-m-d') ?>"
                    >
                    <span class="text-red-600 text-sm"><?= $checkOutMsg ?></span>
                </div>
            </div>

            <div>
                <label class="font-semibold block mb-1">Guests</label>
                <input
                        type="text"
                        name="guests"
                        value="<?= htmlspecialchars($guest ?: ($booking['guests'] ?? '')) ?>"
                        class="w-full border px-4 py-2 rounded-xl"
                >
                <span class="text-red-600 text-sm"><?= $guestMsg ?></span>
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
