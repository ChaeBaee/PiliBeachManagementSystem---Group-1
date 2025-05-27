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

$fullNameMsg = $emailMsg = $phoneNoMsg = "";
$currentPasswordMsg = $newPasswordMsg = $confirmPasswordMsg = "";
$successMessage = "";

// Handle form submissions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $formName = $_POST['form_name'] ?? '';

    if ($formName === 'profile_update') {
        $fullName = trim($_POST["user_name"]);
        $email = trim($_POST["user_email"]);
        $phoneNo = trim($_POST["user_phone"]);

        $isValid = true;

        if (empty($fullName)) {
            $fullNameMsg = "Full name is required.";
            $isValid = false;
        }

        if (empty($email)) {
            $emailMsg = "Email is required.";
            $isValid = false;
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailMsg = "Invalid email format.";
            $isValid = false;
        }

        if (empty($phoneNo)) {
            $phoneNoMsg = "Phone number is required.";
            $isValid = false;
        } elseif (!preg_match('/^[0-9]{10,15}$/', $phoneNo)) {
            $phoneNoMsg = "Phone number must be 10-15 digits.";
            $isValid = false;
        }

        if ($isValid) {
            $stmt = $conn->prepare("UPDATE users SET user_name = ?, user_email = ?, user_phone = ? WHERE user_id = ?");
            $stmt->execute([$fullName, $email, $phoneNo, $user_id]);

            $_SESSION['message'] = "Profile updated successfully.";
            header("Location: profile.php");
            exit();
        }
    }

    if ($formName === 'password_change') {
        $currentPassword = $_POST["current_password"];
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];

        $isValid = true;

        if (!password_verify($currentPassword, $user['user_password'])) {
            $currentPasswordMsg = "Current password is incorrect.";
            $isValid = false;
        }

        if (empty($newPassword)) {
            $newPasswordMsg = "New password is required.";
            $isValid = false;
        } elseif ($newPassword === $currentPassword) {
            $newPasswordMsg = "New password must be different from current password.";
            $isValid = false;
        } elseif (strlen($newPassword) < 6) {
            $newPasswordMsg = "New password must be at least 6 characters.";
            $isValid = false;
        }

        if ($newPassword !== $confirmPassword) {
            $confirmPasswordMsg = "Passwords do not match.";
            $isValid = false;
        }

        if ($isValid) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET user_password = ? WHERE user_id = ?");
            $stmt->execute([$hashedPassword, $user_id]);

            $_SESSION['message'] = "Password changed successfully.";
            header("Location: profile.php");
            exit();
        }
    }
}
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
            <?= $_SESSION['message']; unset($_SESSION['message']); ?>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-2 gap-8">
        <!-- Profile Information -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Profile Information</h2>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="form_name" value="profile_update">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Full Name</label>
                    <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']); ?>" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($fullNameMsg): ?><span class="text-red-500 text-sm"><?= $fullNameMsg; ?></span><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']); ?>" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($emailMsg): ?><span class="text-red-500 text-sm"><?= $emailMsg; ?></span><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" name="user_phone" value="<?= htmlspecialchars($user['user_phone']); ?>" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($phoneNoMsg): ?><span class="text-red-500 text-sm"><?= $phoneNoMsg; ?></span><?php endif; ?>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Role</label>
                        <input type="text" value="<?= $user['user_role']; ?>" disabled class="w-full p-2 bg-gray-100 border border-gray-200 rounded-md text-gray-600">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Joined</label>
                        <input type="text" value="<?= $user['user_date_created']; ?>" disabled class="w-full p-2 bg-gray-100 border border-gray-200 rounded-md text-gray-600">
                    </div>
                </div>

                <button type="submit" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-all">
                    Save Changes
                </button>
            </form>
        </div>

        <!-- Change Password -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Change Password</h2>
            <form action="" method="POST" class="space-y-4">
                <input type="hidden" name="form_name" value="password_change">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Current Password</label>
                    <input type="password" name="current_password" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($currentPasswordMsg): ?><span class="text-red-500 text-sm"><?= $currentPasswordMsg; ?></span><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">New Password</label>
                    <input type="password" name="new_password" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($newPasswordMsg): ?><span class="text-red-500 text-sm"><?= $newPasswordMsg; ?></span><?php endif; ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" name="confirm_password" class="w-full p-2 border border-gray-300 rounded-md focus:ring-blue-500">
                    <?php if ($confirmPasswordMsg): ?><span class="text-red-500 text-sm"><?= $confirmPasswordMsg; ?></span><?php endif; ?>
                </div>

                <button type="submit" class="w-full mt-4 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition-all">
                    Update Password
                </button>
            </form>
        </div>

        <!-- User Bookings -->
        <div class="bg-white rounded-lg shadow-lg p-6 mt-10 md:col-span-2">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Your Bookings</h2>
            <?php
            $stmt = $conn->prepare("SELECT b.*, r.room_number, r.room_type FROM bookings b JOIN rooms r ON b.room_id = r.room_id WHERE b.user_id = ?");
            $stmt->execute([$user_id]);
            $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>

            <?php if ($bookings): ?>
                <ul class="divide-y">
                    <?php foreach ($bookings as $booking): ?>
                        <li class="py-4 flex justify-between items-center">
                            <div>
                                <p class="font-medium text-gray-700">Room <?= $booking['room_number']; ?> - <?= $booking['room_type']; ?></p>
                                <p class="text-sm text-gray-500"><?= $booking['booking_check_in']; ?> to <?= $booking['booking_check_out']; ?></p>
                                <p class="text-sm text-gray-500">Status: <?= $booking['booking_status']; ?> | Total: ₱<?= $booking['booking_total_price']; ?></p>
                            </div>
                            <?php if (!in_array($booking['booking_status'], ['Canceled', 'Checked-in', 'Checked-out'])): ?>
                                <form action="./back/cancel-booking.php" method="POST">
                                    <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">Cancel Booking</button>
                                </form>
                            <?php else: ?>
                                <span class="text-gray-500 italic"><?= $booking['booking_status'] === 'Canceled' ? 'Cancelled' : 'Cancellation unavailable'; ?></span>
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
