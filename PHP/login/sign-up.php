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

    <form id="signupForm" action="#" method="POST" class="space-y-4">

      <div>
        <label class="block text-sm font-medium text-gray-700">Username</label>
        <input type="text" name="username" required class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="john_doe" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" required class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="you@example.com" />
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Contact Number</label>
        <input type="tel" name="contact" required pattern="^(09|\+639)\d{9}$" class="w-full mt-1 px-3 py-2 border rounded-lg" placeholder="09xxxxxxxxx or +639xxxxxxxxx" />
      </div>

      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" id="password" name="password" required minlength="8" class="w-full mt-1 px-3 py-2 border rounded-lg pr-10" placeholder="Enter your password" />
        <span onclick="togglePassword('password')" class="absolute right-3 top-9 cursor-pointer text-sm text-blue-500">Show</span>
      </div>

      <div class="relative">
        <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
        <input type="password" id="confirm_password" required class="w-full mt-1 px-3 py-2 border rounded-lg pr-10" placeholder="Re-enter your password" />
        <span onclick="togglePassword('confirm_password')" class="absolute right-3 top-9 cursor-pointer text-sm text-blue-500">Show</span>
        <p id="passwordError" class="text-red-600 text-sm mt-1 hidden">Passwords do not match</p>
      </div>

      <div class="flex items-center">
        <input id="terms" type="checkbox" required class="mr-2" />
        <label for="terms" class="text-sm text-gray-600">I agree to the terms and conditions</label>
      </div>

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

    document.getElementById('signupForm').addEventListener('submit', function(e) {
      const pw = document.getElementById('password').value;
      const cpw = document.getElementById('confirm_password').value;
      const error = document.getElementById('passwordError');

      if (pw.length < 8) {
        e.preventDefault();
        error.textContent = "Password must be at least 8 characters.";
        error.classList.remove('hidden');
        return;
      }

      if (pw !== cpw) {
        e.preventDefault();
        error.classList.remove('hidden');
      } else {
        error.classList.add('hidden');
      }
    });
  </script>
</body>
</html>
