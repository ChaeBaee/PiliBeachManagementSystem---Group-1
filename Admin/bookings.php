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


    <div id="bookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h2 id="modalTitle" class="text-2xl font-semibold mb-4">Add New Booking</h2>
            <form id="bookingForm" class="space-y-4">
                <input type="hidden" id="bookingId" name="bookingId">

                <div>
                    <label class="block text-sm font-medium text-gray-700">Guest Name</label>
                    <select id="userId" name="userId" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">

                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Room</label>
                    <select id="roomId" name="roomId" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">

                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-in Date</label>
                    <input type="date" id="checkIn" name="checkIn" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Check-out Date</label>
                    <input type="date" id="checkOut" name="checkOut" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Price</label>
                    <input type="number" id="totalPrice" name="totalPrice" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                </div>

                <div id="statusField" class="hidden">
                    <label class="block text-sm font-medium text-gray-700">Status</label>
                    <select id="status" name="status"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary">
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="canceled">Canceled</option>
                    </select>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button type="button" onclick="closeModal()"
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
        let isEditMode = false;


        document.addEventListener('DOMContentLoaded', () => {
            loadBookings();
            loadGuestUsers();
            setupDateInputs();
        });


        function setupDateInputs() {
            const today = new Date().toISOString().split('T')[0];
            const checkIn = document.getElementById('checkIn');
            const checkOut = document.getElementById('checkOut');


            checkIn.min = today;
            checkOut.min = today;


            checkIn.value = today;
            checkOut.value = today;
        }


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
                                <td class="px-6 py-4 font-medium">#${booking.booking_id}</td>
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


        async function loadGuestUsers() {
            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=get_guest_users'
                });
                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('userId');
                    select.innerHTML = '<option value="">Select Guest</option>';
                    data.data.forEach(user => {
                        select.innerHTML += `<option value="${user.user_id}">${user.user_name}</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading guest users:', error);
            }
        }


        async function loadAvailableRooms(checkIn, checkOut) {
            if (!checkIn || !checkOut) return;

            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `action=get_available_rooms&check_in=${checkIn}&check_out=${checkOut}`
                });
                const data = await response.json();

                if (data.success) {
                    const select = document.getElementById('roomId');
                    select.innerHTML = '<option value="">Select Room</option>';
                    data.data.forEach(room => {
                        select.innerHTML += `<option value="${room.room_id}">${room.room_number} - ${room.room_type} (₱${room.room_price}/night)</option>`;
                    });
                }
            } catch (error) {
                console.error('Error loading rooms:', error);
            }
        }


        function openAddModal() {
            isEditMode = false;
            document.getElementById('modalTitle').textContent = 'Add New Booking';
            document.getElementById('statusField').classList.add('hidden');
            document.getElementById('bookingForm').reset();


            const today = new Date().toISOString().split('T')[0];
            document.getElementById('checkIn').value = today;
            document.getElementById('checkOut').value = today;


            loadAvailableRooms(today, today);

            document.getElementById('bookingModal').classList.remove('hidden');
            document.getElementById('bookingModal').classList.add('flex');
        }

        function closeModal() {
            document.getElementById('bookingModal').classList.add('hidden');
            document.getElementById('bookingModal').classList.remove('flex');
        }

        function openDeleteModal(bookingId) {
            currentBookingId = bookingId;
            document.getElementById('deleteModal').classList.remove('hidden');
            document.getElementById('deleteModal').classList.add('flex');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
            document.getElementById('deleteModal').classList.remove('flex');
            currentBookingId = null;
        }


        document.getElementById('bookingForm').addEventListener('submit', async (e) => {
            e.preventDefault();


            const formData = new FormData();
            formData.append('action', isEditMode ? 'update_booking' : 'add_booking');


            const userId = document.getElementById('userId').value;
            const roomId = document.getElementById('roomId').value;
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            const totalPrice = document.getElementById('totalPrice').value;


            if (!userId || !roomId || !checkIn || !checkOut || !totalPrice) {
                alert('Please fill in all required fields');
                return;
            }


            formData.append('user_id', userId);
            formData.append('room_id', roomId);
            formData.append('check_in', checkIn);
            formData.append('check_out', checkOut);
            formData.append('total_price', totalPrice);


            if (isEditMode) {
                formData.append('booking_id', document.getElementById('bookingId').value);
                formData.append('status', document.getElementById('status').value);
            }

            try {
                const response = await fetch('bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeModal();
                    loadBookings();
                    alert(isEditMode ? 'Booking updated successfully!' : 'Booking added successfully!');
                } else {
                    alert('Error: ' + (data.message || 'Failed to save booking'));
                }
            } catch (error) {
                console.error('Error saving booking:', error);
                alert('Error saving booking. Please try again.');
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
                    isEditMode = true;
                    currentBookingId = bookingId;

                    document.getElementById('modalTitle').textContent = 'Edit Booking';
                    document.getElementById('statusField').classList.remove('hidden');

                    const booking = data.data;
                    document.getElementById('bookingId').value = booking.booking_id;
                    document.getElementById('userId').value = booking.user_id;
                    document.getElementById('roomId').value = booking.room_id;
                    document.getElementById('checkIn').value = booking.booking_check_in;
                    document.getElementById('checkOut').value = booking.booking_check_out;
                    document.getElementById('totalPrice').value = booking.booking_total_price;
                    document.getElementById('status').value = booking.booking_status;

                    document.getElementById('bookingModal').classList.remove('hidden');
                    document.getElementById('bookingModal').classList.add('flex');
                }
            } catch (error) {
                console.error('Error loading booking details:', error);
            }
        }


        function deleteBooking(bookingId) {
            openDeleteModal(bookingId);
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


        function getStatusColor(status) {
            switch (status) {
                case 'confirmed': return 'green';
                case 'pending': return 'yellow';
                case 'canceled': return 'red';
                default: return 'gray';
            }
        }


        document.getElementById('checkIn').addEventListener('change', function () {
            const checkOut = document.getElementById('checkOut');
            checkOut.min = this.value;
            if (checkOut.value && checkOut.value < this.value) {
                checkOut.value = this.value;
            }
            loadAvailableRooms(this.value, checkOut.value);
        });

        document.getElementById('checkOut').addEventListener('change', function () {
            const checkIn = document.getElementById('checkIn');
            if (checkIn.value) {
                loadAvailableRooms(checkIn.value, this.value);
            }
        });
    </script>

</body>

</html>