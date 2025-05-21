<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bookings - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563eb',
                        danger: '#dc2626',
                        success: '#16a34a',
                        warning: '#ca8a04'
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 text-gray-800">

    <?php include 'sidebar.php'; ?>
    <?php include 'navbar.php'; ?>

    <main class="md:ml-64 mt-16 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold">Bookings</h1>
            <a href="add-booking.php"
                class="bg-primary text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">
                + New Booking
            </a>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-sm font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left">Booking ID</th>
                        <th class="px-6 py-4 text-left">Guest Name</th>
                        <th class="px-6 py-4 text-left">Check-in</th>
                        <th class="px-6 py-4 text-left">Check-out</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">

                    <!-- Booking Row: Confirmed -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">#0012</td>
                        <td class="px-6 py-4">John Doe</td>
                        <td class="px-6 py-4">2025-06-01</td>
                        <td class="px-6 py-4">2025-06-05</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-xs font-medium">Confirmed</span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="edit-booking.php" class="text-primary hover:underline">Edit</a>
                            <a href="#" class="text-danger hover:underline">Delete</a>
                        </td>
                    </tr>

                    <!-- Booking Row: Checked-in -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">#0013</td>
                        <td class="px-6 py-4">Jane Smith</td>
                        <td class="px-6 py-4">2025-06-03</td>
                        <td class="px-6 py-4">2025-06-07</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-xs font-medium">Checked-in</span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="#" class="text-primary hover:underline">Edit</a>
                            <a href="#" class="text-danger hover:underline">Delete</a>
                        </td>
                    </tr>

                    <!-- Booking Row: Pending -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">#0014</td>
                        <td class="px-6 py-4">Alice Johnson</td>
                        <td class="px-6 py-4">2025-06-08</td>
                        <td class="px-6 py-4">2025-06-10</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-yellow-100 text-yellow-800 px-3 py-1 rounded-full text-xs font-medium">Pending</span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="#" class="text-primary hover:underline">Edit</a>
                            <a href="#" class="text-danger hover:underline">Delete</a>
                        </td>
                    </tr>

                    <!-- Booking Row: Cancelled -->
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium">#0015</td>
                        <td class="px-6 py-4">Bob Lee</td>
                        <td class="px-6 py-4">2025-06-02</td>
                        <td class="px-6 py-4">2025-06-04</td>
                        <td class="px-6 py-4">
                            <span
                                class="bg-red-100 text-red-700 px-3 py-1 rounded-full text-xs font-medium">Cancelled</span>
                        </td>
                        <td class="px-6 py-4 space-x-2">
                            <a href="#" class="text-primary hover:underline">Edit</a>
                            <a href="#" class="text-danger hover:underline">Delete</a>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>
    </main>

</body>

</html>