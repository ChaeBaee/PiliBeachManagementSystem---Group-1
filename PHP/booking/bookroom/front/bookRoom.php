<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['booking_data'])) {
    header("Location: ../../../PHP/login/front/login.php");
    exit();
}

require_once '../../../db.php';

$user_name = $_SESSION['user_name'] ?? 'N/A';
$user_email = $_SESSION['user_email'] ?? 'N/A';
$booking = $_SESSION['booking_data'];

$room_price = 0;
if (isset($booking['room_id'])) {
    try {
        $stmt = $conn->prepare("SELECT room_price FROM rooms WHERE room_id = ?");
        $stmt->execute([$booking['room_id']]);
        $room = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($room) {
            $room_price = (float) $room['room_price'];
        }
    } catch (PDOException $e) {
        $room_price = 0;
    }
}

$checkin_date = new DateTime($booking['checkin']);
$checkout_date = new DateTime($booking['checkout']);
$interval = $checkin_date->diff($checkout_date);
$days = $interval->days;

$total_price = $days * $room_price;

$_SESSION['booking'] = $booking;
$_SESSION['booking']['total_amount'] = $total_price;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Booking Confirmation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #f3f8ff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        main {
            flex: 1;
            padding-top: 150px;
        }
    </style>
</head>

<body class="text-gray-800">
<?php include('../../../elements/navbar.php'); ?>

<main class="px-4 pb-12">
    <div class="max-w-2xl mx-auto bg-white p-8 rounded-3xl shadow-xl opacity-95">
        <h2 class="text-2xl md:text-3xl font-bold text-center mb-8">Review & Confirm Your Booking</h2>

        <div class="space-y-4 text-base">
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Full Name:</span>
                <span><?= htmlspecialchars($user_name) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Email:</span>
                <span><?= htmlspecialchars($user_email) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Room Type:</span>
                <span><?= htmlspecialchars($booking['room_type'] ?? 'N/A') ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Check-in:</span>
                <span><?= htmlspecialchars($booking['checkin']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Check-out:</span>
                <span><?= htmlspecialchars($booking['checkout']) ?></span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Guests:</span>
                <span><?= htmlspecialchars($booking['guests']) ?> Guest(s)</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Number of Nights:</span>
                <span><?= $days ?> night(s)</span>
            </div>
            <div class="flex justify-between border-b pb-2">
                <span class="font-semibold">Price per Night:</span>
                <span>₱<?= number_format($room_price, 2) ?></span>
            </div>
            <div class="flex justify-between font-semibold text-lg border-t pt-3">
                <span>Total Amount:</span>
                <span>₱<?= number_format($total_price, 2) ?></span>
            </div>
        </div>

        <div class="mt-8 flex flex-col md:flex-row gap-4">
            <a href="../../../PHP/booking/front/edit-booking.php"
               class="flex-1 text-center bg-gray-100 text-gray-800 py-3 rounded-full hover:bg-gray-200 transition">
                Edit Details
            </a>
            <a href="../../../PHP/Payment/Front/payment.php"
               class="flex-1 text-center bg-blue-500 text-white py-3 rounded-full hover:bg-blue-600 transition">
                Confirm &amp; Pay
            </a>
        </div>
    </div>
</main>

<?php include('../../../elements/footer.php'); ?>
</body>

</html>
