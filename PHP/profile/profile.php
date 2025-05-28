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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f3f8ff 0%, #e6f0ff 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .input-focus {
            transition: all 0.3s ease;
        }

        .input-focus:focus {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body class="min-h-screen flex flex-col">
    <?php include('../elements/navbar.php'); ?>

    <div class="max-w-6xl mx-auto py-16 px-6 mt-8">
        <div class="text-center mb-16">
            <h1 class="text-5xl font-bold text-blue-900 mb-4">Your Profile</h1>
            <p class="text-gray-600 text-lg">Manage your account settings and view your bookings</p>
        </div>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 rounded mb-8 shadow-lg animate-fade-in">
                <?= $_SESSION['message'];
                unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-10">
            <div class="glass-card rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Profile Information
                </h2>
                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="form_name" value="profile_update">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                        <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']); ?>"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($fullNameMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $fullNameMsg; ?></span><?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                        <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']); ?>"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($emailMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $emailMsg; ?></span><?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                        <input type="text" name="user_phone" value="<?= htmlspecialchars($user['user_phone']); ?>"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($phoneNoMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $phoneNoMsg; ?></span><?php endif; ?>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                            <input type="text" value="<?= $user['user_role']; ?>" disabled
                                class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Joined</label>
                            <input type="text" value="<?= $user['user_date_created']; ?>" disabled
                                class="w-full p-3 bg-gray-50 border border-gray-200 rounded-lg text-gray-600">
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                        Save Changes
                    </button>
                </form>
            </div>

            <div class="glass-card rounded-2xl shadow-xl p-8 transform hover:scale-[1.02] transition-all duration-300">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Change Password
                </h2>
                <form action="" method="POST" class="space-y-6">
                    <input type="hidden" name="form_name" value="password_change">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                        <input type="password" name="current_password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($currentPasswordMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $currentPasswordMsg; ?></span><?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                        <input type="password" name="new_password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($newPasswordMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $newPasswordMsg; ?></span><?php endif; ?>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                        <input type="password" name="confirm_password"
                            class="w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus">
                        <?php if ($confirmPasswordMsg): ?><span
                                class="text-red-500 text-sm mt-1 block"><?= $confirmPasswordMsg; ?></span><?php endif; ?>
                    </div>

                    <button type="submit"
                        class="w-full mt-6 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                        Update Password
                    </button>
                </form>
            </div>

            <div class="glass-card rounded-2xl shadow-xl p-8 mt-10 md:col-span-2">
                <h2 class="text-2xl font-semibold text-gray-800 mb-6 flex items-center">
                    <svg class="w-6 h-6 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    Your Bookings
                </h2>
                <?php
                $stmt = $conn->prepare("SELECT b.*, r.room_number, r.room_type FROM bookings b JOIN rooms r ON b.room_id = r.room_id WHERE b.user_id = ?");
                $stmt->execute([$user_id]);
                $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if ($bookings): ?>
                    <div class="space-y-4 max-h-[600px] overflow-y-auto pr-2">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-all duration-300">
                                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                                    <div class="space-y-2">
                                        <h3 class="text-lg font-semibold text-gray-800">Room <?= $booking['room_number']; ?> -
                                            <?= $booking['room_type']; ?></h3>
                                        <div class="flex flex-wrap gap-4 text-sm text-gray-600">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                                <?= $booking['booking_check_in']; ?> to <?= $booking['booking_check_out']; ?>
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Status: <?= $booking['booking_status']; ?>
                                            </span>
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Total: ₱<?= $booking['booking_total_price']; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <?php if (!in_array($booking['booking_status'], ['Canceled', 'Checked-in', 'Checked-out'])): ?>
                                        <form action="./back/cancel-booking.php" method="POST" class="w-full md:w-auto">
                                            <input type="hidden" name="booking_id" value="<?= $booking['booking_id']; ?>">
                                            <button type="submit"
                                                class="w-full md:w-auto bg-red-500 hover:bg-red-600 text-white px-6 py-2 rounded-lg transition-all duration-300 transform hover:scale-[1.02] hover:shadow-lg">
                                                Cancel Booking
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span
                                            class="text-gray-500 italic"><?= $booking['booking_status'] === 'Canceled' ? 'Cancelled' : 'Cancellation unavailable'; ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-8">
                        <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-500 text-lg">You have no bookings yet.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php include('../elements/footer.php'); ?>
</body>

</html>