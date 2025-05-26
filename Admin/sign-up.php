<?php
session_start();

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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

        <form id="signupForm" class="space-y-4">
            <div>
                <label for="user_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="user_name" name="user_name"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span id="fullNameMsg" class="text-red-600 text-sm"></span>
            </div>

            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="user_email" name="user_email"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span id="emailMsg" class="text-red-600 text-sm"></span>
            </div>

            <div>
                <label for="user_phone" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" id="user_phone" name="user_phone" pattern="^(09\d{9}|639\d{9})$"
                    title="Must start with 09 or 639 and be 11 digits total"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span id="contactNoMsg" class="text-red-600 text-sm"></span>
            </div>

            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="user_password" name="user_password"
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary" />
                    <button type="button" onclick="togglePassword('user_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                </div>
                <span id="passwordMsg" class="text-red-600 text-sm"></span>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary" />
                    <button type="button" onclick="togglePassword('confirm_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                    <span id="confirmPasswordMsg" class="text-red-600 text-sm"></span>
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

        document.getElementById('signupForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);

            try {
                const response = await fetch('signup_handler.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                // Clear previous error messages
                document.querySelectorAll('.text-red-600').forEach(el => el.textContent = '');

                if (data.success) {
                    // If successful, redirect immediately
                    window.location.href = data.redirect;
                } else {
                    // Display error messages if any
                    if (data.emailMsg) document.getElementById('emailMsg').textContent = data.emailMsg;
                    if (data.passwordMsg) document.getElementById('passwordMsg').textContent = data.passwordMsg;
                    if (data.fullNameMsg) document.getElementById('fullNameMsg').textContent = data.fullNameMsg;
                    if (data.contactNoMsg) document.getElementById('contactNoMsg').textContent = data.contactNoMsg;
                    if (data.confirmPasswordMsg) document.getElementById('confirmPasswordMsg').textContent = data.confirmPasswordMsg;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    </script>
</body>

</html>