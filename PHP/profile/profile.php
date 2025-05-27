<?php
session_start();
require_once '../db.php';
include('../elements/navbar.php');

if (!isset($_SESSION['user_id'])) {
  header("Location: ../login.php");
  exit();
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>User Profile - Paradise Resort</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">

</head>

<body class="bg-[#f3f8ff] min-h-screen flex flex-col">

  <div class="max-w-5xl mx-auto py-12 px-6">
    <h1 class="text-4xl font-bold text-center text-blue-900 mb-12">Manage Your Account</h1>

    <?php if (isset($_SESSION['message'])): ?>
      <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6 shadow">
        <?= $_SESSION['message'];
        unset($_SESSION['message']); ?>
      </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8">

      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Profile Information</h2>
        <form action="./back/update-profile.php" method="POST" class="space-y-4">
          <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">

          <div>
            <label class="block text-sm font-medium text-gray-700">Full Name</label>
            <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']); ?>" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']); ?>" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="text" name="user_phone" value="<?= htmlspecialchars($user['user_phone']); ?>" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-gray-700">Role</label>
              <input type="text" value="<?= $user['user_role']; ?>" disabled
                class="w-full p-2 bg-gray-100 border border-gray-200 rounded-md text-gray-600">
            </div>
            <div>
              <label class="block text-sm font-medium text-gray-700">Joined</label>
              <input type="text" value="<?= $user['user_date_created']; ?>" disabled
                class="w-full p-2 bg-gray-100 border border-gray-200 rounded-md text-gray-600">
            </div>
          </div>

          <button type="submit"
            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-all">
            Save Changes
          </button>
        </form>
      </div>


      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Change Password</h2>
        <form action="./back/change-password.php" method="POST" class="space-y-4">
          <input type="hidden" name="user_id" value="<?= $user['user_id']; ?>">

          <div>
            <label class="block text-sm font-medium text-gray-700">Current Password</label>
            <input type="password" name="current_password" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">New Password</label>
            <input type="password" name="new_password" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" name="confirm_password" required
              class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
          </div>

          <button type="submit"
            class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-all">
            Update Password
          </button>
        </form>
      </div>
        <div class="bg-white rounded-lg shadow-lg p-6 mt-10">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Your Bookings</h2>

            <?php
            $stmt = $conn->prepare("SELECT b.*, r.room_number, r.room_type 
                          FROM bookings b 
                          JOIN rooms r ON b.room_id = r.room_id 
                          WHERE b.user_id = ?");
            $stmt->execute([$user_id]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($bookings): ?>
                <ul class="divide-y">
                    <?php foreach ($bookings as $booking): ?>
                        <li class="py-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-700">
                                    Room <?= $booking['room_number']; ?> - <?= $booking['room_type']; ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?= $booking['booking_check_in']; ?> to <?= $booking['booking_check_out']; ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    Status: <?= $booking['booking_status']; ?> | Total: ₱<?= $booking['booking_total_price']; ?>
                                </p>
                            </div>

                            <?php if ($booking['booking_status'] != 'Canceled'): ?>
                                <form action="./back/cancel-booking.php" method="POST">
                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                    <button type="submit"
                                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                                        Cancel Booking
                                    </button>
                                </form>
                            <?php else: ?>
                                <span class="text-red-400 font-semibold">Cancelled</span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p class="text-gray-500">You have no bookings yet.</p>
            <?php endif; ?>
        </div>
    </div>
  </div>

  <?php include('../elements/footer.php'); ?>
</body>

</html>