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
                    <select id="addUserId" name="userId" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Guest</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="addRoomId" name="roomId" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Room</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="addCheckIn" name="checkIn" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="addCheckOut" name="checkOut" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Price</label>
                    <input type="number" id="addTotalPrice" name="totalPrice" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
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
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 class="text-2xl font-semibold mb-4">Edit Booking</h2>
            <form id="editBookingForm" class="space-y-4">
                <input type="hidden" id="editBookingId" name="bookingId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Name</label>
                    <input type="text" id="editUserName" name="userName" readonly
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-primary focus:ring-primary">
                    <input type="hidden" id="editUserId" name="userId">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="editRoomId" name="roomId" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="">Select Room</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="editCheckIn" name="checkIn" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="editCheckOut" name="checkOut" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Price</label>
                    <input type="number" id="editTotalPrice" name="totalPrice" required
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="editStatus" name="status"
                        class="mt-1 block w-full h-12 pl-3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-in">Checked-in</option>
                        <option value="Checked-out">Checked-out</option>
                        <option value="Canceled">Canceled</option>
                    </select>
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

    <script>
        let currentBookingId = null;

        document.addEventListener('DOMContentLoaded', () => {
            loadBookings();
            loadGuestUsers();
            setupDateInputs();
        });

        function setupDateInputs() {
            const today = new Date().toISOString().split('T')[0];
            const addCheckIn = document.getElementById('addCheckIn');
            const addCheckOut = document.getElementById('addCheckOut');

            addCheckIn.min = today;
            addCheckOut.min = today;

            addCheckIn.value = today;
            addCheckOut.value = today;
        }

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
                const response = await fetch('bookings_backend.php', {
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

        async function loadAvailableRooms(checkIn, checkOut, currentBookingId = null) {
            if (!checkIn || !checkOut) return;

            try {
                const response = await fetch('bookings_backend.php', {
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
                            `<option value="${room.room_id}">${room.room_number} - ${room.room_type} (₱${room.room_price}/night)</option>`
                        ).join('');

                    addSelect.innerHTML = roomOptions;
                    editSelect.innerHTML = roomOptions;
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
            }
        }

        document.getElementById('addBookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('action', 'add_booking');

            const userId = document.getElementById('addUserId').value;
            const roomId = document.getElementById('addRoomId').value;
            const checkIn = document.getElementById('addCheckIn').value;
            const checkOut = document.getElementById('addCheckOut').value;
            const totalPrice = document.getElementById('addTotalPrice').value;

            // Validate dates
            if (new Date(checkIn) >= new Date(checkOut)) {
                alert('Check-out date must be after check-in date');
                return;
            }

            if (!userId || !roomId || !checkIn || !checkOut || !totalPrice) {
                alert('Please fill in all required fields');
                return;
            }

            formData.append('user_id', userId);
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('total_price', totalPrice);

            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeAddModal();
                    loadBookings();
                    alert('Booking added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to add booking'));
                }
            } catch (error) {
                console.error('Error adding booking:', error);
                alert('Error adding booking. Please try again.');
            }
        });

        document.getElementById('editBookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData();
            formData.append('action', 'update_booking');

            const userId = document.getElementById('editUserId').value;
            const roomId = document.getElementById('editRoomId').value;
            const checkIn = document.getElementById('editCheckIn').value;
            const checkOut = document.getElementById('editCheckOut').value;
            const totalPrice = document.getElementById('editTotalPrice').value;
            const status = document.getElementById('editStatus').value;

            // Validate dates
            if (new Date(checkIn) >= new Date(checkOut)) {
                alert('Check-out date must be after check-in date');
                return;
            }

            if (!userId || !roomId || !checkIn || !checkOut || !totalPrice || !status) {
                alert('Please fill in all required fields');
                return;
            }

            formData.append('booking_id', document.getElementById('editBookingId').value);
            formData.append('user_id', userId);
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('total_price', totalPrice);
            formData.append('status', status);

            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeEditModal();
                    loadBookings();
                    alert('Booking updated successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to update booking'));
                }
            } catch (error) {
                console.error('Error updating booking:', error);
                alert('Error updating booking. Please try again.');
            }
        });

        async function editBooking(bookingId) {
            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_booking_details&booking_id=${bookingId}`
                });
                const data = await response.json();

                if (data.success) {
                    const booking = data.data;

                    document.getElementById('editBookingId').value = booking.booking_id;
                    document.getElementById('editUserId').value = booking.user_id;
                    document.getElementById('editUserName').value = booking.user_name;
                    document.getElementById('editCheckIn').value = booking.booking_check_in;
                    document.getElementById('editCheckOut').value = booking.booking_check_out;
                    document.getElementById('editTotalPrice').value = booking.booking_total_price;
                    document.getElementById('editStatus').value = booking.booking_status;

                    // Load available rooms for the selected dates
                    await loadAvailableRooms(booking.booking_check_in, booking.booking_check_out, booking.booking_id);

                    // Set the current room after loading available rooms
                    document.getElementById('editRoomId').value = booking.room_id;

                    openEditModal(bookingId);
                }
            } catch (error) {
                console.error('Error loading booking details:', error);
            }
        }

        // Date change event listeners
        document.getElementById('addCheckIn').addEventListener('change', function () {
            const checkOut = document.getElementById('addCheckOut');
            checkOut.min = this.value;
            if (checkOut.value && checkOut.value < this.value) {
                checkOut.value = this.value;
            }
            loadAvailableRooms(this.value, checkOut.value);
        });

        document.getElementById('addCheckOut').addEventListener('change', function () {
            const checkIn = document.getElementById('addCheckIn');
            if (checkIn.value) {
                loadAvailableRooms(checkIn.value, this.value);
            }
        });

        document.getElementById('editCheckIn').addEventListener('change', function () {
            const checkOut = document.getElementById('editCheckOut');
            checkOut.min = this.value;
            if (checkOut.value && checkOut.value < this.value) {
                checkOut.value = this.value;
            }
            loadAvailableRooms(this.value, checkOut.value, currentBookingId);
        });

        document.getElementById('editCheckOut').addEventListener('change', function () {
            const checkIn = document.getElementById('editCheckIn');
            if (checkIn.value) {
                loadAvailableRooms(checkIn.value, this.value, currentBookingId);
            }
        });

        async function loadBookings() {
            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=get_bookings'
                });
                const data = await response.json();

                if (data.success) {
                    const tbody = document.getElementById('bookingsTableBody');
                    tbody.innerHTML = '';

                    data.data.forEach(booking => {
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
                                        class="text-primary hover:underline">Edit</button>
                                    <button onclick="deleteBooking(${booking.booking_id})" 
                                        class="text-danger hover:underline">Delete</button>
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
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=delete_booking&booking_id=${currentBookingId}`
                });
                const data = await response.json();

                if (data.success) {
                    closeDeleteModal();
                    loadBookings();
                } else {
                    alert('Error: ' + (data.message || 'Failed to delete booking'));
                }
            } catch (error) {
                console.error('Error deleting booking:', error);
                alert('Error deleting booking');
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