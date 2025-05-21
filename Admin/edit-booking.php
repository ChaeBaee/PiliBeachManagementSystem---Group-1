<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Edit Booking #0012</title>
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

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6 font-sans">

    <div class="bg-white shadow-lg rounded-lg max-w-lg w-full p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Edit Booking #0012</h1>

        <form class="space-y-6" action="#" method="POST">
            <!-- User -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select id="user_id" name="user_id" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    <option value="" disabled>Select user</option>
                    <option value="1" selected>John Doe</option>
                    <option value="2">Jane Smith</option>
                    <option value="3">Alice Johnson</option>
                </select>
            </div>

            <!-- Room -->
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                <select id="room_id" name="room_id" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    <option value="" disabled>Select room</option>
                    <option value="101" selected>101 - Deluxe Suite</option>
                    <option value="102">102 - Standard Room</option>
                    <option value="103">103 - Family Room</option>
                </select>
            </div>

            <!-- Check-in Date -->
            <div>
                <label for="booking_check_in" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                <input type="date" id="booking_check_in" name="booking_check_in" required value="2025-06-01"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
            </div>

            <!-- Check-out Date -->
            <div>
                <label for="booking_check_out" class="block text-sm font-medium text-gray-700 mb-2">Check-out
                    Date</label>
                <input type="date" id="booking_check_out" name="booking_check_out" required value="2025-06-05"
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition" />
            </div>

            <!-- Status -->
            <div>
                <label for="booking_status" class="block text-sm font-medium text-gray-700 mb-2">Booking Status</label>
                <select id="booking_status" name="booking_status" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary transition">
                    <option value="" disabled>Select status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed" selected>Confirmed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-primary hover:bg-blue-700 text-white font-semibold rounded-md py-3 transition shadow-md">
                    Save Changes
                </button>
            </div>
        </form>
    </div>

</body>

</html>