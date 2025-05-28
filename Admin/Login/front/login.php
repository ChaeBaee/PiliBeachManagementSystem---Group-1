<?php
session_start();
require_once '../../../PHP/db.php';

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'Admin') {
    header("Location: /Proj/Admin/Dashboard/front/index.php");
    exit;
}

$email = $password = "";
$emailMsg = $passwordMsg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["user_email"] ?? "");
    $password = trim($_POST["user_password"] ?? "");


    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailMsg = "Please enter a valid email.";
    }

    if ($password === "") {
        $passwordMsg = "Password is required";
    }


    if ($emailMsg === "" && $passwordMsg === "") {
        try {

            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = :email AND user_role = 'Admin' LIMIT 1");
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($user && password_verify($password, $user['user_password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_role'] = $user['user_role'];

                header("Location: /Proj/Admin/Dashboard/front/index.php");
                exit;
            } else {
                $emailMsg = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $emailMsg = "An error occurred. Please try again later.";

        }
    }
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

        <form action="" method="POST" class="space-y-5">

            <div>
                <label for="user_email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="user_email" name="user_email" value="<?= htmlspecialchars($email) ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-md focus:ring-primary focus:border-primary transition" />
                <span class="text-red-600 text-sm"><?= $emailMsg ?></span>
            </div>

            <div>
                <label for="user_password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <div class="relative">
                    <input type="password" id="user_password" name="user_password"
                        class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-md focus:ring-primary focus:border-primary transition" />
                    <button type="button" onclick="togglePassword('user_password', this)"
                        class="absolute right-3 top-3 text-gray-500 hover:text-primary">
                        👁️
                    </button>
                    <span class="text-red-600 text-sm"><?= $passwordMsg ?></span>
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