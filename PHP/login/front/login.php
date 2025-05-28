<?php
session_start();
include '../../db.php';

$email = $password = "";
$emailMsg = $passwordMsg = $loginMsg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailMsg = "Please enter a valid email.";
    }

    if (empty($password)) {
        $passwordMsg = "Password cannot be empty.";
    }

    if ($emailMsg === "" && $passwordMsg === "") {
        try {
            $stmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['user_password'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_name'] = $user['user_name'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_role'] = $user['user_role'];

                header("Location: ../../home.php");
                exit();
            } else {
                $loginMsg = "Invalid email or password.";
            }
        } catch (PDOException $e) {
            echo "<script>alert('Login failed: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Login - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #e0f2ff 0%, #f8fafc 100%);
        }

        .login-container {
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
    </style>
</head>

<body class="min-h-screen flex items-center justify-center p-4">
<div class="login-container w-full max-w-md rounded-2xl p-8 animate-fade-in">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
        <p class="text-gray-600">Login to your Pili Beach Resort account</p>
    </div>

    <?php if ($loginMsg): ?>
        <p class="text-red-600 text-sm text-center mb-4"><?php echo $loginMsg; ?></p>
    <?php endif; ?>

    <form method="POST" action="" class="space-y-6">
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

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700 mb-2">Password</label>
            <div class="relative">
          <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
            <i class="far fa-lock"></i>
          </span>
                <input type="password" id="password" name="password"
                       class="input-field w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                       placeholder="••••••••" />
                <span onclick="togglePassword('password')"
                      class="absolute right-3 top-1/2 transform -translate-y-1/2 cursor-pointer text-sm text-gray-400 hover:text-gray-600">
            <i class="far fa-eye"></i>
          </span>
            </div>
            <span class="text-red-600 text-sm block mt-2"><?php echo $passwordMsg; ?></span>
        </div>

        <button type="submit" class="btn-gradient w-full text-white py-3 rounded-xl font-semibold">
            Login
        </button>
    </form>

    <p class="text-center text-gray-600 mt-8">
        Don't have an account?
        <a href="signup.php" class="text-blue-600 hover:text-blue-500 font-semibold">Sign up</a>
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
