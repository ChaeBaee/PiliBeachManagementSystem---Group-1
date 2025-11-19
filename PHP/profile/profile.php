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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f3f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .input-focus {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
        }

        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-canceled { background: #fee2e2; color: #991b1b; }
        .status-checked-in { background: #dbeafe; color: #1e40af; }
        .status-checked-out { background: #f3e8ff; color: #7c3aed; }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .booking-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .section-header {
            position: relative;
            padding-bottom: 1rem;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .profile-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            font-weight: 600;
            margin: 0 auto 1rem;
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include('../elements/navbar.php'); ?>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 mt-16">
        <!-- Header Section -->
        <div class="text-center mb-16 animate-fade-in">
            <div class="profile-avatar">
                <?= strtoupper(substr($user['user_name'], 0, 1)); ?>
            </div>
            <h1 class="text-4xl md:text-5xl font-bold text-blue-900 mb-4 drop-shadow-lg">
                Welcome back, <?= htmlspecialchars(explode(' ', $user['user_name'])[0]); ?>!
            </h1>
            <p class="text-gray-600 text-lg md:text-xl max-w-2xl mx-auto">
                Manage your account settings, update your profile, and view your booking history
            </p>
        </div>

        <!-- Success Message -->
        <?php if (isset($_SESSION['message'])): ?>
            <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-6 rounded-lg mb-8 shadow-lg animate-fade-in">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p><?= $_SESSION['message']; unset($_SESSION['message']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-2 gap-8 mb-12">
            <!-- Profile Information Card -->
            <div>
                <div class="glass-card rounded-2xl p-8 h-fit">
                    <div class="section-header mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-user-edit text-blue-600 mr-3"></i>
                            Profile Information
                        </h2>
                        <p class="text-gray-600 mt-2">Update your personal details and contact information</p>
                    </div>
                    
                    <form action="profile.php" method="POST" class="space-y-6">
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        <input type="hidden" name="form_name" value="profile_update">
                        
                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-user text-blue-500 mr-2"></i>Full Name
                                </label>
                                <input type="text" name="user_name" value="<?= htmlspecialchars($user['user_name']); ?>"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <?php if ($fullNameMsg): ?>
                                    <span class="text-red-500 text-sm mt-2 block flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i><?= $fullNameMsg; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-envelope text-blue-500 mr-2"></i>Email Address
                                </label>
                                <input type="email" name="user_email" value="<?= htmlspecialchars($user['user_email']); ?>"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <?php if ($emailMsg): ?>
                                    <span class="text-red-500 text-sm mt-2 block flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i><?= $emailMsg; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-phone text-blue-500 mr-2"></i>Phone Number
                                </label>
                                <input type="text" name="user_phone" value="<?= htmlspecialchars($user['user_phone']); ?>"
                                    class="w-full p-4 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <?php if ($phoneNoMsg): ?>
                                    <span class="text-red-500 text-sm mt-2 block flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i><?= $phoneNoMsg; ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-3">
                                    <i class="fas fa-shield-alt text-blue-500 mr-2"></i>Account Role
                                </label>
                                <input type="text" value="<?= ucfirst($user['user_role']); ?>" disabled
                                    class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-calendar-plus text-blue-500 mr-2"></i>Member Since
                            </label>
                            <input type="text" value="<?= date('F j, Y', strtotime($user['user_date_created'])); ?>" disabled
                                class="w-full p-4 bg-gray-50 border border-gray-200 rounded-xl text-gray-600">
                        </div>

                        <button type="submit" class="w-full btn-primary text-white font-semibold py-4 px-6 rounded-xl text-lg">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </form>
                </div>
            </div>

            <!-- Password Change Card -->
            <div>
                <div class="glass-card rounded-2xl p-8 h-fit">
                    <div class="section-header mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 flex items-center">
                            <i class="fas fa-lock text-blue-600 mr-3"></i>
                            Security
                        </h2>
                        <p class="text-gray-600 mt-2">Update your password to keep your account secure</p>
                    </div>
                    
                    <form action="profile.php" method="POST" class="space-y-6">
                        <input type="hidden" name="user_id" value="<?= $user_id ?>">
                        <input type="hidden" name="form_name" value="password_change">
                        
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-key text-blue-500 mr-2"></i>Current Password
                            </label>
                            <div class="relative">
                                <input type="password" name="current_password" id="current_password"
                                    class="w-full p-4 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('current_password', 'current_password_icon')">
                                    <i id="current_password_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition-colors"></i>
                                </button>
                            </div>
                            <?php if ($currentPasswordMsg): ?>
                                <span class="text-red-500 text-sm mt-2 block flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?= $currentPasswordMsg; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-lock text-blue-500 mr-2"></i>New Password
                            </label>
                            <div class="relative">
                                <input type="password" name="new_password" id="new_password"
                                    class="w-full p-4 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('new_password', 'new_password_icon')">
                                    <i id="new_password_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition-colors"></i>
                                </button>
                            </div>
                            <?php if ($newPasswordMsg): ?>
                                <span class="text-red-500 text-sm mt-2 block flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?= $newPasswordMsg; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-check-circle text-blue-500 mr-2"></i>Confirm Password
                            </label>
                            <div class="relative">
                                <input type="password" name="confirm_password" id="confirm_password"
                                    class="w-full p-4 pr-12 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 input-focus bg-white/80">
                                <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword('confirm_password', 'confirm_password_icon')">
                                    <i id="confirm_password_icon" class="fas fa-eye text-gray-400 hover:text-gray-600 transition-colors"></i>
                                </button>
                            </div>
                            <?php if ($confirmPasswordMsg): ?>
                                <span class="text-red-500 text-sm mt-2 block flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1"></i><?= $confirmPasswordMsg; ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="w-full btn-primary text-white font-semibold py-4 px-6 rounded-xl text-lg">
                            <i class="fas fa-shield-alt mr-2"></i>Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Bookings Section -->
        <!-- Removed: Bookings section is now accessible via the main navigation bar only -->
    </div>

    <?php include('../elements/footer.php'); ?>

    <script>
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
                icon.classList.remove('text-gray-400');
                icon.classList.add('text-blue-500');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
                icon.classList.remove('text-blue-500');
                icon.classList.add('text-gray-400');
            }
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.1);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 3px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
        }
    </style>
</body>

</html>