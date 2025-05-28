<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Bookings - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
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

    <?php include '../../Elements/sidebar.php'; ?>
    <?php include '../../Elements/navbar.php'; ?>

    <main class="md:ml-64 mt-16 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold">Bookings</h1>
            <button onclick="openAddModal()"
                class="bg-primary text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">
                + New Booking
            </button>
        </div>

        <div class="bg-white rounded-xl shadow-md overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50 text-sm font-semibold text-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-left">Booking ID</th>
                        <th class="px-6 py-4 text-left">Guest Name</th>
                        <th class="px-6 py-4 text-left">Room</th>
                        <th class="px-6 py-4 text-left">Check-in</th>
                        <th class="px-6 py-4 text-left">Check-out</th>
                        <th class="px-6 py-4 text-left">Total Price</th>
                        <th class="px-6 py-4 text-left">Status</th>
                        <th class="px-6 py-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="bookingsTableBody" class="divide-y divide-gray-100 text-sm">

                </tbody>
            </table>
        </div>
    </main>

    <!-- Add Booking Modal -->
    <div id="addBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4">Add New Booking</h2>
            <form id="addBookingForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Name</label>
                    <select id="addUserId" name="userId"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Guest</option>
                    </select>
                    <span id="addGuestNameMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="addRoomId" name="roomId"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Room</option>
                    </select>
                    <span id="addRoomMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="addCheckIn" name="checkIn"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="addCheckInMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="addCheckOut" name="checkOut"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="addCheckOutMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Price</label>
                    <input type="number" id="addTotalPrice" name="totalPrice"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="addTotalPriceMsg" class="text-red-600 text-sm"></span>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center pt-20">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4">Edit Booking</h2>
            <form id="editBookingForm" class="space-y-4">
                <input type="hidden" id="editBookingId" name="bookingId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" id="editUserName" name="userName" readonly
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-primary focus:ring-primary">
                    <input type="hidden" id="editUserId" name="userId">
                    <span id="editGuestNameMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="editRoomId" name="roomId"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Room</option>
                    </select>
                    <span id="editRoomMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="editCheckIn" name="checkIn"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="editCheckInMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="editCheckOut" name="checkOut"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="editCheckOutMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Price</label>
                    <input type="number" id="editTotalPrice" name="totalPrice"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                    <span id="editTotalPriceMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="editStatus" name="status"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-in">Checked-in</option>
                        <option value="Checked-out">Checked-out</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                    <span id="editStatusMsg" class="text-red-600 text-sm"></span>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4">Confirm Delete</h2>
            <p class="text-gray-600 mb-6">Are you sure you want to delete this booking?</p>
            <div class="flex justify-end space-x-3">
                <button onclick="closeDeleteModal()"
                    class="px-4 py-2 text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200">Cancel</button>
                <button onclick="confirmDelete()"
                    class="px-4 py-2 bg-danger text-white rounded-lg hover:bg-red-600">Delete</button>
            </div>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h2 id="notificationTitle" class="text-2xl font-semibold"></h2>
                <button onclick="closeNotificationModal()" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <p id="notificationMessage" class="text-gray-600 mb-6"></p>
            <div class="flex justify-end">
                <button onclick="closeNotificationModal()"
                    class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-blue-600">OK</button>
            </div>
        </div>
    </div>

    <script>
        let currentBookingId = null;

        // Add validation functions
        function validateGuestName(userId) {
            if (!userId) {
                return "Guest name cannot be empty.";
            }
            return "";
        }

        function validateRoom(roomId) {
            if (!roomId) {
                return "Room cannot be empty.";
            }
            return "";
        }

        function validateDates(checkIn, checkOut) {
            if (!checkIn) {
                return "Check-in date cannot be empty.";
            }
            if (!checkOut) {
                return "Check-out date cannot be empty.";
            }
            if (new Date(checkIn) >= new Date(checkOut)) {
                return "Check-out date must be after check-in date.";
            }
            if (new Date(checkIn) < new Date().setHours(0, 0, 0, 0)) {
                return "Check-in date cannot be in the past.";
            }
            return "";
        }

        function validateTotalPrice(totalPrice) {
            if (!totalPrice) {
                return "Total price cannot be empty.";
            }
            if (isNaN(totalPrice) || totalPrice <= 0) {
                return "Total price must be a positive number.";
            }
            return "";
        }

        function validateStatus(status) {
            if (!status) {
                return "Status cannot be empty.";
            }
            return "";
        }

        function clearValidationMessages() {
            document.querySelectorAll('[id$="Msg"]').forEach(el => el.textContent = '');
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadBookings();
            loadGuestUsers();
            setupDateInputs();
        });

        function setupDateInputs() {
            const today = new Date().toISOString().split('T')[0];
            const addCheckIn = document.getElementById('addCheckIn');
            const addCheckOut = document.getElementById('addCheckOut');
            const editCheckIn = document.getElementById('editCheckIn');
            const editCheckOut = document.getElementById('editCheckOut');

            // Set minimum dates to today
            addCheckIn.min = today;
            addCheckOut.min = today;
            editCheckIn.min = today;
            editCheckOut.min = today;

            // Set initial values
            addCheckIn.value = today;
            addCheckOut.value = today;
        }

        function calculateTotalPrice(checkIn, checkOut, roomPrice) {
            if (!checkIn || !checkOut || !roomPrice) return 0;

            const start = new Date(checkIn);
            const end = new Date(checkOut);

            // Calculate number of nights
            const nights = Math.ceil((end - start) / (1000 * 60 * 60 * 24));

            // Return total price (nights * price per night)
            return nights * roomPrice;
        }

        function updateTotalPrice(checkIn, checkOut, roomId, isEdit = false) {
            const prefix = isEdit ? 'edit' : 'add';
            const roomSelect = document.getElementById(`${prefix}RoomId`);
            const totalPriceInput = document.getElementById(`${prefix}TotalPrice`);

            if (!roomId) {
                totalPriceInput.value = '';
                return;
            }

            const selectedOption = roomSelect.options[roomSelect.selectedIndex];
            const roomPrice = parseFloat(selectedOption.getAttribute('data-price'));

            if (roomPrice) {
                const totalPrice = calculateTotalPrice(checkIn, checkOut, roomPrice);
                totalPriceInput.value = totalPrice.toFixed(2);
            }
        }

        // Modify the loadAvailableRooms function to include room price
        async function loadAvailableRooms(checkIn, checkOut, currentBookingId = null) {
            if (!checkIn || !checkOut) return;

            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_available_rooms&check_in=${checkIn}&check_out=${checkOut}${currentBookingId ? `&current_booking_id=${currentBookingId}` : ''}`
                });
                const data = await response.json();

                if (data.success) {
                    const addSelect = document.getElementById('addRoomId');
                    const editSelect = document.getElementById('editRoomId');

                    const roomOptions = '<option value="">Select Room</option>' +
                        data.data.map(room =>
                            `<option value="${room.room_id}" data-price="${room.room_price}">${room.room_number} - ${room.room_type} (₱${room.room_price}/night)</option>`
                        ).join('');

                    addSelect.innerHTML = roomOptions;
                    editSelect.innerHTML = roomOptions;

                    // Update total price if dates and room are selected
                    const addCheckIn = document.getElementById('addCheckIn').value;
                    const addCheckOut = document.getElementById('addCheckOut').value;
                    const addRoomId = addSelect.value;
                    if (addCheckIn && addCheckOut && addRoomId) {
                        updateTotalPrice(addCheckIn, addCheckOut, addRoomId);
                    }

                    const editCheckIn = document.getElementById('editCheckIn').value;
                    const editCheckOut = document.getElementById('editCheckOut').value;
                    const editRoomId = editSelect.value;
                    if (editCheckIn && editCheckOut && editRoomId) {
                        updateTotalPrice(editCheckIn, editCheckOut, editRoomId, true);
                    }
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
            }
        }

        // Add event listeners for date and room changes
        document.getElementById('addCheckIn').addEventListener('change', function () {
            const checkOut = document.getElementById('addCheckOut');
            const roomId = document.getElementById('addRoomId').value;

            // Set minimum check-out date to next day after check-in
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.min = nextDay.toISOString().split('T')[0];

            // If current check-out is before or equal to check-in, update it
            if (checkOut.value <= this.value) {
                checkOut.value = nextDay.toISOString().split('T')[0];
            }

            loadAvailableRooms(this.value, checkOut.value);
            if (roomId) {
                updateTotalPrice(this.value, checkOut.value, roomId);
            }
        });

        document.getElementById('addCheckOut').addEventListener('change', function () {
            const checkIn = document.getElementById('addCheckIn').value;
            const roomId = document.getElementById('addRoomId').value;

            if (checkIn) {
                loadAvailableRooms(checkIn, this.value);
                if (roomId) {
                    updateTotalPrice(checkIn, this.value, roomId);
                }
            }
        });

        document.getElementById('addRoomId').addEventListener('change', function () {
            const checkIn = document.getElementById('addCheckIn').value;
            const checkOut = document.getElementById('addCheckOut').value;

            if (checkIn && checkOut) {
                updateTotalPrice(checkIn, checkOut, this.value);
            }
        });

        // Edit form event listeners
        document.getElementById('editCheckIn').addEventListener('change', function () {
            const checkOut = document.getElementById('editCheckOut');
            const roomId = document.getElementById('editRoomId').value;

            // Set minimum check-out date to next day after check-in
            const nextDay = new Date(this.value);
            nextDay.setDate(nextDay.getDate() + 1);
            checkOut.min = nextDay.toISOString().split('T')[0];

            // If current check-out is before or equal to check-in, update it
            if (checkOut.value <= this.value) {
                checkOut.value = nextDay.toISOString().split('T')[0];
            }

            loadAvailableRooms(this.value, checkOut.value, currentBookingId);
            if (roomId) {
                updateTotalPrice(this.value, checkOut.value, roomId, true);
            }
        });

        document.getElementById('editCheckOut').addEventListener('change', function () {
            const checkIn = document.getElementById('editCheckIn').value;
            const roomId = document.getElementById('editRoomId').value;

            if (checkIn) {
                loadAvailableRooms(checkIn, this.value, currentBookingId);
                if (roomId) {
                    updateTotalPrice(checkIn, this.value, roomId, true);
                }
            }
        });

        document.getElementById('editRoomId').addEventListener('change', function () {
            const checkIn = document.getElementById('editCheckIn').value;
            const checkOut = document.getElementById('editCheckOut').value;

            if (checkIn && checkOut) {
                updateTotalPrice(checkIn, checkOut, this.value, true);
            }
        });

        function openAddModal() {
            document.getElementById('addBookingModal').classList.remove('hidden');
            document.getElementById('addBookingModal').classList.add('flex');

            const today = new Date().toISOString().split('T')[0];
            document.getElementById('addCheckIn').value = today;
            document.getElementById('addCheckOut').value = today;

            loadAvailableRooms(today, today);
        }

        function closeAddModal() {
            document.getElementById('addBookingModal').classList.add('hidden');
            document.getElementById('addBookingModal').classList.remove('flex');
            document.getElementById('addBookingForm').reset();
        }

        function openEditModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('editBookingModal').classList.remove('hidden');
            document.getElementById('editBookingModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editBookingModal').classList.add('hidden');
            document.getElementById('editBookingModal').classList.remove('flex');
            currentBookingId = null;
        }

        async function loadGuestUsers() {
            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=get_guest_users'
                });
                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('addUserId');
                    select.innerHTML = '<option value="">Select Guest</option>';
                    data.data.forEach(user => {
                        select.innerHTML += `<option value="${user.user_id}">${user.user_name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading guest users:', error);
            }
        }

        function showNotification(title, message) {
            document.getElementById('notificationTitle').textContent = title;
            document.getElementById('notificationMessage').textContent = message;
            document.getElementById('notificationModal').classList.remove('hidden');
            document.getElementById('notificationModal').classList.add('flex');
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').classList.add('hidden');
            document.getElementById('notificationModal').classList.remove('flex');
        }

        document.getElementById('addBookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            clearValidationMessages();

            const userId = document.getElementById('addUserId').value;
            const roomId = document.getElementById('addRoomId').value;
            const checkIn = document.getElementById('addCheckIn').value;
            const checkOut = document.getElementById('addCheckOut').value;
            const totalPrice = document.getElementById('addTotalPrice').value;

            // Validate all fields
            const guestNameMsg = validateGuestName(userId);
            const roomMsg = validateRoom(roomId);
            const datesMsg = validateDates(checkIn, checkOut);
            const totalPriceMsg = validateTotalPrice(totalPrice);

            // Display validation messages
            if (guestNameMsg) document.getElementById('addGuestNameMsg').textContent = guestNameMsg;
            if (roomMsg) document.getElementById('addRoomMsg').textContent = roomMsg;
            if (datesMsg) document.getElementById('addCheckOutMsg').textContent = datesMsg;
            if (totalPriceMsg) document.getElementById('addTotalPriceMsg').textContent = totalPriceMsg;

            // If any validation failed, stop form submission
            if (guestNameMsg || roomMsg || datesMsg || totalPriceMsg) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'add_booking');
            formData.append('user_id', userId);
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('total_price', totalPrice);

            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeAddModal();
                    loadBookings();
                    showNotification('Success', 'Booking added successfully!');
                } else {
                    showNotification('Error', data.message || 'Failed to add booking');
                }
            } catch (error) {
                console.error('Error adding booking:', error);
                showNotification('Error', 'Error adding booking. Please try again.');
            }
        });

        document.getElementById('editBookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            clearValidationMessages();

            const userId = document.getElementById('editUserId').value;
            const roomId = document.getElementById('editRoomId').value;
            const checkIn = document.getElementById('editCheckIn').value;
            const checkOut = document.getElementById('editCheckOut').value;
            const totalPrice = document.getElementById('editTotalPrice').value;
            const status = document.getElementById('editStatus').value;

            // Validate all fields
            const guestNameMsg = validateGuestName(userId);
            const roomMsg = validateRoom(roomId);
            const datesMsg = validateDates(checkIn, checkOut);
            const totalPriceMsg = validateTotalPrice(totalPrice);
            const statusMsg = validateStatus(status);

            // Display validation messages
            if (guestNameMsg) document.getElementById('editGuestNameMsg').textContent = guestNameMsg;
            if (roomMsg) document.getElementById('editRoomMsg').textContent = roomMsg;
            if (datesMsg) document.getElementById('editCheckOutMsg').textContent = datesMsg;
            if (totalPriceMsg) document.getElementById('editTotalPriceMsg').textContent = totalPriceMsg;
            if (statusMsg) document.getElementById('editStatusMsg').textContent = statusMsg;

            // If any validation failed, stop form submission
            if (guestNameMsg || roomMsg || datesMsg || totalPriceMsg || statusMsg) {
                return;
            }

            const formData = new FormData();
            formData.append('action', 'update_booking');
            formData.append('booking_id', document.getElementById('editBookingId').value);
            formData.append('user_id', userId);
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('total_price', totalPrice);
            formData.append('status', status);

            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeEditModal();
                    loadBookings();
                    showNotification('Success', 'Booking updated successfully!');
                } else {
                    showNotification('Error', data.message || 'Failed to update booking');
                }
            } catch (error) {
                console.error('Error updating booking:', error);
                showNotification('Error', 'Error updating booking. Please try again.');
            }
        });

        async function editBooking(bookingId) {
            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_booking_details&booking_id=${bookingId}`
                });
                const data = await response.json();

                if (data.success) {
                    const booking = data.data;
                    currentBookingId = bookingId;

                    document.getElementById('editBookingId').value = booking.booking_id;
                    document.getElementById('editUserId').value = booking.user_id;
                    document.getElementById('editUserName').value = booking.user_name;
                    document.getElementById('editCheckIn').value = booking.booking_check_in;
                    document.getElementById('editCheckOut').value = booking.booking_check_out;
                    document.getElementById('editTotalPrice').value = booking.booking_total_price;
                    document.getElementById('editStatus').value = booking.booking_status;

                    await loadAvailableRooms(booking.booking_check_in, booking.booking_check_out, booking.booking_id);
                    document.getElementById('editRoomId').value = booking.room_id;

                    openEditModal(bookingId);
                }
            } catch (error) {
                console.error('Error loading booking details:', error);
            }
        }

        async function loadBookings() {
            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=get_bookings'
                });
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('bookingsTableBody');
                    tbody.innerHTML = '';

                    // Filter out checked-out bookings
                    const activeBookings = data.data.filter(booking => booking.booking_status !== 'Checked-out');

                    activeBookings.forEach(booking => {
                        const row = `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium">${booking.booking_id}</td>
                                <td class="px-6 py-4">${booking.user_name}</td>
                                <td class="px-6 py-4">${booking.room_number} (${booking.room_type})</td>
                                <td class="px-6 py-4">${booking.booking_check_in}</td>
                                <td class="px-6 py-4">${booking.booking_check_out}</td>
                                <td class="px-6 py-4">₱${booking.booking_total_price}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-${getStatusColor(booking.booking_status)}-100 
                                        text-${getStatusColor(booking.booking_status)}-800 
                                        px-3 py-1 rounded-full text-xs font-medium">
                                        ${booking.booking_status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 space-x-2">
                                    <button onclick="editBooking(${booking.booking_id})" 
                                        class="text-primary hover:text-blue-800">
                                        <i class="fas fa-edit text-lg"></i>
                                    </button>
                                    <button onclick="deleteBooking(${booking.booking_id})" 
                                        class="text-danger hover:text-red-800">
                                        <i class="fas fa-trash text-lg"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                }
            } catch (error) {
                console.error('Error loading bookings:', error);
            }
        }

        function deleteBooking(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        async function confirmDelete() {
            if (!currentBookingId) return;

            try {
                const response = await fetch('../back/bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete_booking&booking_id=${currentBookingId}`
                });
                const data = await response.json();

                if (data.success) {
                    closeDeleteModal();
                    loadBookings();
                    showNotification('Success', 'Booking deleted successfully!');
                } else {
                    showNotification('Error', data.message || 'Failed to delete booking');
                }
            } catch (error) {
                console.error('Error deleting booking:', error);
                showNotification('Error', 'Error deleting booking');
            }
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
            currentBookingId = null;
        }

        function getStatusColor(status) {
            switch (status.toLowerCase()) {
                case 'confirmed': return 'green';
                case 'pending': return 'yellow';
                case 'canceled': return 'red';
                case 'checked-in': return 'blue';
                case 'checked-out': return 'gray';
                default: return 'gray';
            }
        }
    </script>

</body>

</html>