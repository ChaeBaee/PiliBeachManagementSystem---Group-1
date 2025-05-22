<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: /Proj/Admin/index.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sign Up</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        danger: '#dc2626',
                        success: '#16a34a'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 space-y-6">
        <h2 class="text-2xl font-bold text-center text-primary">Create an Account</h2>

        <form action="/Proj/Admin/actions/sign-up.php" method="POST" class="space-y-4" id="signupForm">

            <div>
                <label for="user_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="user_name" name="user_name" required
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary">
            </div>


            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="user_email" name="user_email" required
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary">
            </div>


            <div>
                <label for="user_phone" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" id="user_phone" name="user_phone" required
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary"
                    pattern="^(09\d{9}|639\d{9})$" title="Must start with 09 or 639 and be 11 digits total">
            </div>


            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="user_password" name="user_password" required
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary">
                    <button type="button" onclick="togglePassword('user_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                </div>
            </div>


            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password" required
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary">
                    <button type="button" onclick="togglePassword('confirm_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                </div>
            </div>


            <div>
                <button type="submit"
                    class="w-full bg-primary text-white font-semibold rounded-lg px-4 py-2 hover:bg-blue-600 transition">
                    Sign Up
                </button>
            </div>


            <p class="text-sm text-center text-gray-600">
                Already have an account?
                <a href="login.php" class="text-primary font-medium hover:underline">Login here</a>
            </p>
        </form>
    </div>


    <script>
        function togglePassword(id, button) {
            const input = document.getElementById(id);
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            button.textContent = isPassword ? '🙈' : '👁️';
        }

        document.getElementById('signupForm').addEventListener('submit', function (e) {
            const pw = document.getElementById('user_password').value;
            const confirmPw = document.getElementById('confirm_password').value;
            const phone = document.getElementById('user_phone').value;

            const phonePattern = /^(09\d{9}|639\d{9})$/;

            if (!phonePattern.test(phone)) {
                alert('Contact number must start with 09 or 639 and be 11 digits.');
                e.preventDefault();
                return;
            }

            if (pw !== confirmPw) {
                alert('Passwords do not match.');
                e.preventDefault();
            }
        });
    </script>

</body>

</html>