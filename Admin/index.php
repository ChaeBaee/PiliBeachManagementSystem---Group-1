<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

  <div class="flex min-h-screen">

  <aside class="w-64 bg-white shadow-lg hidden md:block">
      <div class="p-6 border-b">
        <h1 class="text-2xl font-bold text-blue-600">Pili AdminPanel</h1>
      </div>
      <nav class="mt-6">
        <ul>
          <li>
            <a href="#" class="block py-3 px-6 text-gray-700 hover:bg-gray-100">Dashboard</a>
          </li>
          <li>
            <a href="#" class="block py-3 px-6 text-gray-700 hover:bg-gray-100">Bookings</a>
          </li>
          <li>
            <a href="#" class="block py-3 px-6 text-gray-700 hover:bg-gray-100">Rooms</a>
          </li>
          <li>
            <a href="#" class="block py-3 px-6 text-gray-700 hover:bg-gray-100">Users</a>
          </li>
          <li>
            <a href="#" class="block py-3 px-6 text-gray-700 hover:bg-gray-100">Reports</a>
          </li>
        </ul>
      </nav>
    </aside>

    <div class="flex-1 flex flex-col">

    <header class="bg-white shadow-md px-6 py-4 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800">Dashboard</h2>
        <div class="flex items-center gap-4">
          <input type="text" placeholder="Search..." class="border rounded px-3 py-1 hidden md:block" />
          <button class="text-gray-600 hover:text-blue-600">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
              <circle cx="12" cy="12" r="10"></circle>
              <path d="M14.31 8l5.74 9.94"></path>
              <path d="M9.69 8h11.48"></path>
              <path d="M7.38 12l5.74-9.94"></path>
            </svg>
          </button>
          <img src="https://i.pravatar.cc/40" alt="avatar" class="rounded-full w-8 h-8" />
        </div>
      </header>


      <main class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

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
          <div class="bg-white rounded-xl shadow p-5">
            <h3 class="text-lg font-semibold text-gray-700 mb-4">Recent Bookings</h3>
            <table class="min-w-full text-sm">
              <thead>
                <tr class="text-left text-gray-600 border-b">
                  <th class="py-2">Guest</th>
                  <th class="py-2">Room</th>
                  <th class="py-2">Date</th>
                  <th class="py-2">Status</th>
                </tr>
              </thead>
              <tbody>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2">Jane Doe</td>
                  <td class="py-2">Room 204</td>
                  <td class="py-2">May 17, 2025</td>
                  <td class="py-2 text-green-600 font-semibold">Confirmed</td>
                </tr>
                <tr class="border-b hover:bg-gray-50">
                  <td class="py-2">John Smith</td>
                  <td class="py-2">Room 101</td>
                  <td class="py-2">May 18, 2025</td>
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
