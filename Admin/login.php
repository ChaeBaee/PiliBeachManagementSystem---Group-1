<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        danger: '#dc2626',
                        success: '#16a34a',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center px-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg max-w-md w-full">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-6">Welcome Back</h2>

        <form action="/Proj/Admin/actions/login.php" method="POST" class="space-y-5">

            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="user_email" name="user_email" required
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-primary focus:border-primary transition" />
            </div>


            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="user_password" name="user_password" required
                        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-md focus:ring-primary focus:border-primary transition" />
                    <button type="button" onclick="togglePassword('user_password', this)"
                        class="absolute right-3 top-3 text-gray-500 hover:text-primary">
                        👁️
                    </button>
                </div>
            </div>


            <div>
                <button type="submit"
                    class="w-full bg-primary hover:bg-blue-700 text-white font-semibold rounded-md py-3 transition shadow">
                    Log In
                </button>
            </div>


            <p class="text-center text-sm text-gray-600 mt-4">
                Don't have an account?
                <a href="sign-up.php" class="text-primary hover:underline font-medium">Sign up here</a>
            </p>
        </form>
    </div>

    <script>
        function togglePassword(id, button) {
            const passwordInput = document.getElementById(id);
            const isPassword = passwordInput.type === "password";
            passwordInput.type = isPassword ? "text" : "password";
            button.textContent = isPassword ? '🙈' : '👁️';
        }
    </script>

</body>

</html>