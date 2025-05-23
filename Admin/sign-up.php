<?php
session_start();
require_once '../PHP/db.php'; // Add database connection

// Redirect if already logged in as admin
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
    header("Location: index.php");
    exit;
}

// Initialize messages and input variables
$emailMsg = $passwordMsg = $fullNameMsg = $contactNoMsg = $confirmPassword = "";
$email = $password = $fullName = $contactNo = $confirmPasswordMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use the input names matching your form inputs
    $email = trim($_POST["user_email"] ?? "");
    $password = $_POST["user_password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $fullName = trim($_POST["user_name"] ?? "");
    $contactNo = trim($_POST["user_phone"] ?? "");

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailMsg = "Please enter a valid email.";
    }

    // Validate password and confirm password
    if (empty($password)) {
        $passwordMsg = "Password cannot be empty.";
    } else if (strlen($password) < 8) {
        $passwordMsg = "Password must be at least 8 characters.";
    }

    if ($password != $confirmPassword) {
        $confirmPasswordMsg = "Password do not match.";
    }

    // Validate full name
    if (empty($fullName)) {
        $fullNameMsg = "Full name cannot be empty.";
    }

    // Validate contact number with regex (same as client-side)
    if (empty($contactNo)) {
        $contactNoMsg = "Contact number cannot be empty.";
    } elseif (!preg_match("/^(09\d{9}|639\d{9})$/", $contactNo)) {
        $contactNoMsg = "Contact number must start with 09 or 639 and be 11 digits.";
    }

    // If no errors, proceed with registration
    if ($emailMsg === "" && $passwordMsg === "" && $fullNameMsg === "" && $contactNoMsg === "" && $confirmPasswordMsg === "") {
        try {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT user_email FROM users WHERE user_email = ?");
            $stmt->execute([$email]);
            if ($stmt->rowCount() > 0) {
                $emailMsg = "Email is already registered.";
            } else {
                // Hash password and create admin user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $user_role = 'Admin';

                $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_role, user_phone) VALUES (?, ?, ?, ?, ?)");
                $success = $stmt->execute([$fullName, $email, $hashed_password, $user_role, $contactNo]);

                if ($success) {
                    $user_id = $conn->lastInsertId();

                    // Set session variables
                    $_SESSION['user_id'] = $user_id;
                    $_SESSION['user_name'] = $fullName;
                    $_SESSION['user_role'] = $user_role;

                    header("Location: index.php");
                    exit();
                } else {
                    $emailMsg = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
            $emailMsg = "An error occurred. Please try again later.";
            // For debugging: error_log($e->getMessage());
        }
    }
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

        <form action="" method="POST" class="space-y-4" id="signupForm">

            <div>
                <label for="user_name" class="block text-sm font-medium text-gray-700">Full Name</label>
                <input type="text" id="user_name" name="user_name" value="<?= htmlspecialchars($fullName) ?>"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span class="text-red-600 text-sm"><?= $fullNameMsg ?></span>
            </div>

            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <input type="email" id="user_email" name="user_email" value="<?= htmlspecialchars($email) ?>"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span class="text-red-600 text-sm"><?= $emailMsg ?></span>
            </div>

            <div>
                <label for="user_phone" class="block text-sm font-medium text-gray-700">Contact Number</label>
                <input type="text" id="user_phone" name="user_phone" value="<?= htmlspecialchars($contactNo) ?>"
                    pattern="^(09\d{9}|639\d{9})$" title="Must start with 09 or 639 and be 11 digits total"
                    class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 focus:ring-primary focus:border-primary" />
                <span class="text-red-600 text-sm"><?= $contactNoMsg ?></span>
            </div>

            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-700">Password</label>
                <div class="relative">
                    <input type="password" id="user_password" name="user_password"
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary" />
                    <button type="button" onclick="togglePassword('user_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                </div>
                <span class="text-red-600 text-sm"><?= $passwordMsg ?></span>
            </div>

            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <div class="relative">
                    <input type="password" id="confirm_password" name="confirm_password"
                        class="mt-1 w-full border border-gray-300 rounded-lg shadow-sm px-4 py-2 pr-10 focus:ring-primary focus:border-primary" />
                    <button type="button" onclick="togglePassword('confirm_password', this)"
                        class="absolute inset-y-0 right-0 px-3 text-gray-500 hover:text-primary focus:outline-none">👁️</button>
                    <span class="text-red-600 text-sm"><?= $confirmPasswordMsg ?></span>

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
    </script>

</body>

</html>