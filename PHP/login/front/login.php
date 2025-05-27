<?php
session_start();

// Retrieve previous form input and error messages from session
$email = $_SESSION['old_email'] ?? '';
$emailMsg = $_SESSION['emailMsg'] ?? '';
$passwordMsg = $_SESSION['passwordMsg'] ?? '';

// Clear flash session data
unset($_SESSION['emailMsg'], $_SESSION['passwordMsg'], $_SESSION['old_email']);

// Redirect if already logged in
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Guest') {
    header("Location: /Proj/PHP/home.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login - Pili Beach Resort</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a365d 0%, #2d3748 100%);
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .input-field {
            transition: all 0.3s ease;
        }

        .input-field:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .btn-gradient {
            background: linear-gradient(135deg, #059669 0%, #10b981 100%);
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
    <div class="login-container w-full max-w-md rounded-2xl p-8 animate-fade-in">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
            <p class="text-gray-600">Sign in to continue your journey with us</p>
        </div>

        <form method="POST" action="../../../PHP/login/back/login.php" class="space-y-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-envelope"></i>
                    </span>
                    <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"
                        class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="you@example.com">
                </div>
                <?php if ($emailMsg): ?>
                    <span class="text-red-500 text-sm block mt-2"><?= htmlspecialchars($emailMsg) ?></span>
                <?php endif; ?>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                        <i class="far fa-lock"></i>
                    </span>
                    <input type="password" name="password"
                        class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                        placeholder="••••••••">
                </div>
                <?php if ($passwordMsg): ?>
                    <span class="text-red-500 text-sm block mt-2"><?= htmlspecialchars($passwordMsg) ?></span>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-gradient w-full text-white py-3 rounded-xl font-semibold">
                Sign In
            </button>
        </form>

        <p class="text-center text-gray-600 mt-8">
            Don't have an account?
            <a href="../../../PHP/login/front/sign-up.php"
                class="text-green-600 hover:text-green-500 font-semibold">Create one</a>
        </p>
    </div>
</body>

</html>