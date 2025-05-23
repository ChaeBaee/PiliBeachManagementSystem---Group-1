<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'Admin') {
  header("Location: login.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Pili Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 font-sans">


  <?php include 'navbar.php'; ?>

  <div class="flex flex-col md:flex-row">

    <?php include 'sidebar.php'; ?>

    <div class="flex-1 p-4 md:p-6 pt-28 md:pt-24 md:ml-64 transition-all duration-300">
      <main class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Cards -->
        <div class="bg-white rounded-xl shadow p-5">
          <h3 class="text-lg font-semibold text-gray-700">Total Bookings</h3>
          <p class="text-3xl mt-2 text-blue-600 font-bold">1,245</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
          <h3 class="text-lg font-semibold text-gray-700">Available Rooms</h3>
          <p class="text-3xl mt-2 text-green-600 font-bold">58</p>
        </div>
        <div class="bg-white rounded-xl shadow p-5">
          <h3 class="text-lg font-semibold text-gray-700">New Users</h3>
          <p class="text-3xl mt-2 text-purple-600 font-bold">89</p>
        </div>

        <div class="col-span-1 md:col-span-2 lg:col-span-3">
          <div class="bg-white rounded-xl shadow p-5 overflow-auto">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Bookings</h3>
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 border-b">
                  <th class="py-2 pr-4">Guest</th>
                  <th class="py-2 pr-4">Room</th>
                  <th class="py-2 pr-4">Date</th>
                  <th class="py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 pr-4">Jane Doe</td>
                  <td class="py-2 pr-4">Room 204</td>
                  <td class="py-2 pr-4">May 17, 2025</td>
                  <td class="py-2 text-green-600 font-semibold">Confirmed</td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2 pr-4">John Smith</td>
                  <td class="py-2 pr-4">Room 101</td>
                  <td class="py-2 pr-4">May 18, 2025</td>
                  <td class="py-2 text-yellow-600 font-semibold">Pending</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </main>
    </div>
  </div>

</body>

</html>