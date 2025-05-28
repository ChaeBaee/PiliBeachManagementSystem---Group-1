<?php
session_start();
include '../../db.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'Guest') {
    header("Location: ../../home.php");
    exit;
}

$emailMsg = $_SESSION['emailMsg'] ?? "";
$passwordMsg = $_SESSION['passwordMsg'] ?? "";
$fullNameMsg = $_SESSION['fullNameMsg'] ?? "";
$contactNoMsg = $_SESSION['contactNoMsg'] ?? "";
$termsMsg = $_SESSION['termsMsg'] ?? "";

$email = $_SESSION['old_email'] ?? "";
$fullName = $_SESSION['old_name'] ?? "";
$contactNo = $_SESSION['old_phone'] ?? "";
$termsAccepted = $_SESSION['old_terms'] ?? false;

unset(
    $_SESSION['emailMsg'],
    $_SESSION['passwordMsg'],
    $_SESSION['fullNameMsg'],
    $_SESSION['contactNoMsg'],
    $_SESSION['termsMsg'],
    $_SESSION['old_email'],
    $_SESSION['old_name'],
    $_SESSION['old_phone'],
    $_SESSION['old_terms']
);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Account - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f2ff 0%, #f8fafc 100%);
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .animate-fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .checkbox-custom {
            transition: all 0.3s ease;
        }

        .checkbox-custom:checked {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
<div class="signup-container w-full max-w-md rounded-2xl p-8 animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Create Account</h2>
        <p class="text-gray-600">Join us for an unforgettable experience</p>
    </div>

        <form method="POST" action="../back/sign-up-back.php" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-user"></i>
                    </span>
                <input type="text" name="name" value="<?php echo htmlspecialchars($fullName); ?>"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="John Doe" />
            </div>
            <span class="text-red-600 text-sm block mt-2"><?php echo $fullNameMsg; ?></span>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
            <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-envelope"></i>
                    </span>
                <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="you@example.com" />
            </div>
            <span class="text-red-600 text-sm block mt-2"><?php echo $emailMsg; ?></span>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">Contact Number</label>
            <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-phone"></i>
                    </span>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($contactNo); ?>"
                       pattern="^(09|\+639)\d{9}$"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="09xxxxxxxxx or +639xxxxxxxxx" />
            </div>
            <span class="text-red-600 text-sm block mt-2"><?php echo $contactNoMsg; ?></span>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-lock"></i>
                    </span>
                <input type="password" id="password" name="password" minlength="8"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="••••••••" />
                <span onclick="togglePassword('password')"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-sm text-gray-400 hover:text-gray-600">
                        <i class="far fa-eye"></i>
                    </span>
            </div>
            <span class="text-red-600 text-sm block mt-2"><?php echo $passwordMsg; ?></span>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
            <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-lock"></i>
                    </span>
                <input type="password" id="confirm_password" name="confirm_password"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="••••••••" />
                <span onclick="togglePassword('confirm_password')"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-sm text-gray-400 hover:text-gray-600">
                        <i class="far fa-eye"></i>
                    </span>
            </div>
        </div>

        <div class="flex items-start space-x-3">
            <input id="terms" name="terms" type="checkbox"
                   class="checkbox-custom h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-300 rounded mt-1"
                <?php echo $termsAccepted ? "checked" : ""; ?> />
            <label for="terms" class="text-sm text-gray-600">
                I agree to the <a href="#" class="text-blue-600 hover:text-blue-500">Terms and Conditions</a> and
                <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>
            </label>
        </div>
        <span class="text-red-600 text-sm block"><?php echo $termsMsg; ?></span>

        <button type="submit" class="btn-gradient w-full text-white py-3 rounded-xl font-semibold">
            Create Account
        </button>
    </form>

    <p class="text-center text-gray-600 mt-8">
        Already have an account?
        <a href="login.php" class="text-blue-600 hover:text-blue-500 font-semibold">Sign in</a>
    </p>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        const icon = input.nextElementSibling.querySelector('i');
        input.type = input.type === "password" ? "text" : "password";
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }
</script>
</body>

</html>
