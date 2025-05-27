<?php
session_start();
include '../../db.php';

if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'Guest') {
    header("Location: ../../home.php");
    exit;
}

$emailMsg = $passwordMsg = $fullNameMsg = $contactNoMsg = $termsMsg = "";
$email = $password = $fullName = $contactNo = $confirmPassword = "";
$termsAccepted = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "";
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";
    $fullName = $_POST["name"] ?? "";
    $contactNo = $_POST["phone"] ?? "";
    $termsAccepted = isset($_POST["terms"]);

    // Validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $emailMsg = "Please enter a valid email.";
    }

    if (empty($password)) {
        $passwordMsg = "Password cannot be empty.";
    } elseif (strlen($password) < 8) {
        $passwordMsg = "Password must be at least 8 characters.";
    } elseif ($password !== $confirmPassword) {
        $passwordMsg = "Passwords do not match.";
    }

    if (empty($fullName)) {
        $fullNameMsg = "Full name cannot be empty.";
    }

    if (empty($contactNo)) {
        $contactNoMsg = "Contact number cannot be empty.";
    }

    if (!$termsAccepted) {
        $termsMsg = "You must agree to the terms and conditions.";
    }

    // If all inputs are valid
    if ($emailMsg === "" && $passwordMsg === "" && $fullNameMsg === "" && $contactNoMsg === "" && $termsMsg === "") {
        try {
            $checkStmt = $conn->prepare("SELECT * FROM users WHERE user_email = ?");
            $checkStmt->execute([$email]);

            if ($checkStmt->rowCount() > 0) {
                $emailMsg = "Email already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (user_name, user_email, user_password, user_phone) VALUES (?, ?, ?, ?)");
                $stmt->execute([$fullName, $email, $hashedPassword, $contactNo]);

                $_SESSION['user_id'] = $conn->lastInsertId();
                $_SESSION['user_name'] = $fullName;
                $_SESSION['user_role'] = 'Guest';
                $_SESSION['user_email'] = $email;

                header("Location: ../../home.php");
                exit();
            }
        } catch (PDOException $e) {
            echo "<script>alert('Signup failed: " . $e->getMessage() . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Signup</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#faebe1] min-h-screen flex items-center justify-center">
<div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Create Account</h2>

    <form method="POST" action="" class="space-y-4">
        <div>
            <label class="block text-sm font-medium text-gray-700">Full name</label>
            <input type="text" name="name" value="<?php echo htmlspecialchars($fullName); ?>"
                   class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="john_doe" />
            <span class="text-red-600 text-sm"><?php echo $fullNameMsg; ?></span>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Email</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($email); ?>"
                   class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="you@example.com" />
            <span class="text-red-600 text-sm"><?php echo $emailMsg; ?></span>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700">Contact Number</label>
            <input type="tel" name="phone" value="<?php echo htmlspecialchars($contactNo); ?>"
                   pattern="^(09|\+639)\d{9}$"
                   class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="09xxxxxxxxx or +639xxxxxxxxx" />
            <span class="text-red-600 text-sm"><?php echo $contactNoMsg; ?></span>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Password</label>
            <input type="password" id="password" name="password" minlength="8"
                   class="w-full mt-1 px-3 py-2 border rounded-lg pr-10" placeholder="Enter your password" />
            <span onclick="togglePassword('password')"
                  class="absolute right-3 top-9 cursor-pointer text-sm text-blue-500">Show</span>
            <span class="text-red-600 text-sm block mt-1"><?php echo $passwordMsg; ?></span>
        </div>

        <div class="relative">
            <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
            <input type="password" id="confirm_password" name="confirm_password"
                   class="w-full mt-1 px-3 py-2 border rounded-lg pr-10" placeholder="Re-enter your password" />
            <span onclick="togglePassword('confirm_password')"
                  class="absolute right-3 top-9 cursor-pointer text-sm text-blue-500">Show</span>
        </div>

        <div class="flex items-center">
            <input id="terms" name="terms" type="checkbox" class="mr-2" <?php echo $termsAccepted ? "checked" : ""; ?> />
            <label for="terms" class="text-sm text-gray-600">I agree to the terms and conditions</label>
        </div>
        <span class="text-red-600 text-sm block"><?php echo $termsMsg; ?></span>

        <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">
            Sign Up
        </button>

        <p class="text-center text-sm text-gray-600 mt-4">
            Already have an account?
            <a href="login.php" class="text-blue-500 hover:underline">Log in</a>
        </p>
    </form>
</div>

<script>
    function togglePassword(id) {
        const input = document.getElementById(id);
        input.type = input.type === "password" ? "text" : "password";
    }
</script>
</body>

</html>
