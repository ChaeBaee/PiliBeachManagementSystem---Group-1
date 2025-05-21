<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Add Booking</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center p-6 font-sans">
    <div class="bg-white shadow-lg rounded-lg max-w-lg w-full p-8">
        <h1 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Add New Booking</h1>
        <form id="addBookingForm" class="space-y-6">

            <!-- User Selection -->
            <div>
                <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">User</label>
                <select id="user_id" name="user_id" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="" disabled selected>Select user</option>
                    <option value="1">James Andrew</option>
                    <option value="2">Luis Ronatay</option>
                    <option value="3">Python</option>
                </select>
            </div>

            <!-- Room Selection -->
            <div>
                <label for="room_id" class="block text-sm font-medium text-gray-700 mb-2">Room</label>
                <select id="room_id" name="room_id" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="" disabled selected>Select room</option>
                    <option value="1">1001 - Deluxe</option>
                    <option value="2">1002 - Double Room</option>
                </select>
            </div>

            <!-- Check-in Date -->
            <div>
                <label for="booking_check_in" class="block text-sm font-medium text-gray-700 mb-2">Check-in Date</label>
                <input type="date" id="booking_check_in" name="booking_check_in" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
            </div>

            <!-- Check-out Date -->
            <div>
                <label for="booking_check_out" class="block text-sm font-medium text-gray-700 mb-2">Check-out
                    Date</label>
                <input type="date" id="booking_check_out" name="booking_check_out" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition" />
            </div>

            <!-- Booking Status -->
            <div>
                <label for="booking_status" class="block text-sm font-medium text-gray-700 mb-2">Booking Status</label>
                <select id="booking_status" name="booking_status" required
                    class="w-full border border-gray-300 rounded-md px-4 py-3 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition">
                    <option value="" disabled selected>Select status</option>
                    <option value="pending">Pending</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="canceled">Canceled</option>
                </select>
            </div>

            <!-- Submit Button -->
            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-md py-3 transition shadow-md">
                    Add Booking
                </button>
            </div>
        </form>
    </div>
</body>

</html>