<?php
require_once '../back/booking_confirmation_backend.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirm Booking - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>

<body class="bg-gray-100">
    <?php include('../../elements/navbar.php'); ?>

    <div class="container mx-auto px-4 py-8 mt-16">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-3xl font-bold mb-8">Confirm Your Booking</h1>

            <?php if (isset($error)): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-xl font-semibold">Room Details</h2>
                    <button onclick="openEditModal()" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-edit mr-1"></i>Edit Details
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <?php if ($room['room_image']): ?>
                        <img src="../../assets/<?php echo htmlspecialchars($room['room_image']); ?>"
                            alt="<?php echo htmlspecialchars($room['room_type']); ?>"
                            class="w-full h-64 object-cover rounded-lg">
                    <?php endif; ?>
                    <div>
                        <p class="text-lg font-semibold"><?php echo htmlspecialchars($room['room_type']); ?></p>
                        <p class="text-gray-600">Room <?php echo htmlspecialchars($room['room_number']); ?></p>
                        <p class="text-gray-600">Capacity: <?php echo htmlspecialchars($room['room_capacity']); ?>
                            guests</p>
                        <p class="text-gray-600 mt-2"><?php echo htmlspecialchars($room['room_description']); ?></p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Guest Information</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['user_name']); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['user_email']); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['user_phone']); ?>"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            readonly>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Booking Details</h2>
                <form method="POST" action="" class="space-y-4">
                    <input type="hidden" name="booking_token" value="<?php echo $_SESSION['booking_token']; ?>">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check In</label>
                            <input type="date" name="check_in" id="check_in"
                                value="<?php echo htmlspecialchars($check_in); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Check Out</label>
                            <input type="date" name="check_out" id="check_out"
                                value="<?php echo htmlspecialchars($check_out); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                readonly>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Number of Nights</label>
                            <input type="text" value="<?php echo $nights; ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                readonly>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Price per Night</label>
                            <input type="text" value="₱<?php echo number_format($room['room_price']); ?>"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                readonly>
                        </div>
                    </div>

                    <div class="border-t pt-4">
                        <div class="flex justify-between items-center">
                            <span class="text-lg font-semibold">Total Amount:</span>
                            <span
                                class="text-2xl font-bold text-blue-600">₱<?php echo number_format($total_price); ?></span>
                        </div>
                    </div>

                    <div class="flex justify-end space-x-4">
                        <a href="bookings.php"
                            class="px-6 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Proceed to Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Booking Details</h3>
                <form id="editForm" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check In</label>
                        <input type="date" id="modal_check_in" name="modal_check_in"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            min="<?php echo date('Y-m-d'); ?>">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Check Out</label>
                        <input type="date" id="modal_check_out" name="modal_check_out"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    </div>
                    <div class="flex justify-end space-x-4 mt-6">
                        <button type="button" onclick="closeEditModal()"
                            class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">
                            Cancel
                        </button>
                        <button type="button" onclick="updateBookingDetails()"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const modalCheckIn = document.getElementById('modal_check_in');
        const modalCheckOut = document.getElementById('modal_check_out');

        modalCheckIn.value = document.getElementById('check_in').value;
        modalCheckOut.value = document.getElementById('check_out').value;

        modalCheckIn.addEventListener('change', function () {
            const checkInDate = new Date(this.value);
            const nextDay = new Date(checkInDate);
            nextDay.setDate(nextDay.getDate() + 1);
            modalCheckOut.min = nextDay.toISOString().split('T')[0];

            if (modalCheckOut.value && new Date(modalCheckOut.value) <= checkInDate) {
                modalCheckOut.value = nextDay.toISOString().split('T')[0];
            }
        });

        modalCheckOut.addEventListener('change', function () {
            const checkInDate = new Date(modalCheckIn.value);
            const checkOutDate = new Date(this.value);

            if (checkOutDate <= checkInDate) {
                const nextDay = new Date(checkInDate);
                nextDay.setDate(nextDay.getDate() + 1);
                this.value = nextDay.toISOString().split('T')[0];
            }
        });

        function openEditModal() {
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function updateBookingDetails() {
            const newCheckIn = modalCheckIn.value;
            const newCheckOut = modalCheckOut.value;

            document.getElementById('check_in').value = newCheckIn;
            document.getElementById('check_out').value = newCheckOut;

            const checkInDate = new Date(newCheckIn);
            const checkOutDate = new Date(newCheckOut);
            const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
            const totalPrice = nights * <?php echo $room['room_price']; ?>;

            document.querySelector('input[value="<?php echo $nights; ?>"]').value = nights;
            document.querySelector('.text-2xl.font-bold.text-blue-600').textContent =
                '₱' + totalPrice.toLocaleString();

            closeEditModal();
        }

        window.onclick = function (event) {
            const modal = document.getElementById('editModal');
            if (event.target === modal) {
                closeEditModal();
            }
        }
    </script>
</body>

</html>