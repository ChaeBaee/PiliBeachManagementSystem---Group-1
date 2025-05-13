<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login / Signup</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#faebe1] min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Welcome to Resort Booking</h2>

    <!-- Google Login -->
    <a href="google-login.php" class="block w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 mb-4 flex items-center justify-center gap-2">
      <svg class="w-5 h-5" viewBox="0 0 48 48">
        <path fill="#fff" d="M44.5 20H24v8.5h11.8C34.1 33.3 29.9 36 25 36c-6.1 0-11-4.9-11-11s4.9-11 11-11c2.7 0 5.2 1 7.1 2.7l6.2-6.2C34.6 7.6 30.1 6 25 6 14.5 6 6 14.5 6 25s8.5 19 19 19c10.3 0 18.7-8.2 18.7-18.5 0-1.3-.2-2.7-.5-4z"/>
      </svg>
      Continue with Google
    </a>

    <!-- Divider -->
    <div class="flex items-center my-4">
      <div class="flex-grow h-px bg-gray-300"></div>
      <span class="px-3 text-gray-500 text-sm">or</span>
      <div class="flex-grow h-px bg-gray-300"></div>
    </div>

    <!-- Email Form -->
    <form method="POST" action="#">
      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Email</label>
        <input type="email" name="email" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" placeholder="you@example.com">
      </div>

      <div class="mb-4">
        <label class="block text-sm font-medium text-gray-700">Password</label>
        <input type="password" name="password" required class="w-full mt-1 px-3 py-2 border rounded-lg focus:outline-none focus:ring focus:ring-blue-200" placeholder="********">
      </div>

      <button type="submit" class="w-full bg-green-500 text-white py-2 rounded-lg hover:bg-green-600 transition">Login</button>
    </form>

    <p class="text-sm text-center text-gray-600 mt-4">
      Don’t have an account? 
      <a href="#" class="text-blue-600 hover:underline">Sign up</a>
    </p>
  </div>

</body>
</html>
