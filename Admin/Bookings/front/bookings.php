<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: /Proj/Admin/Dashboard/front/index.php");
    exit;
}

// Handle search functionality
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get bookings with search filter
require_once '../../../PHP/db.php';

$query = "SELECT b.*, u.user_name, r.room_number, r.room_type 
          FROM bookings b 
          JOIN users u ON b.user_id = u.user_id 
          JOIN rooms r ON b.room_id = r.room_id 
          WHERE b.booking_status NOT IN ('Canceled', 'Checked-out')";

$params = [];

if (!empty($search)) {
    $query .= " AND (u.user_name LIKE ? OR r.room_number LIKE ? OR b.booking_id LIKE ? OR r.room_type LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$query .= " ORDER BY b.booking_check_in DESC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to get status color
function getStatusColor($status) {
    switch (strtolower($status)) {
        case 'confirmed': return 'green';
        case 'pending': return 'yellow';
        case 'canceled': return 'red';
        case 'checked-in': return 'blue';
        case 'checked-out': return 'gray';
        default: return 'gray';
    }
}

// Function to format time from 24-hour to 12-hour format
function formatTime($time) {
    if (empty($time)) return '2:00 PM';
    
    $timeObj = DateTime::createFromFormat('H:i', $time);
    if ($timeObj) {
        return $timeObj->format('g:i A');
    }
    
    return '2:00 PM'; // Default fallback
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
    <style>
        .status-badge {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <?php include '../../Elements/sidebar.php'; ?>
    <?php include '../../Elements/navbar.php'; ?>

    <main class="md:ml-64 mt-16 p-6">
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-3xl font-semibold">Bookings</h1>
            <button onclick="openAddModal()"
                class="bg-primary text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition-colors duration-200">
                <i class="fas fa-plus mr-2"></i>New Booking
            </button>
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white shadow rounded-lg p-4 mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="searchInput" placeholder="Search by guest name, email, phone, room number, or booking ID..." 
                           class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="flex gap-2">
                    <select id="statusFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Status</option>
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-in">Checked-in</option>
                        <option value="Canceled">Canceled</option>
                    </select>
                    <select id="dateFilter" class="border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">All Dates</option>
                        <option value="today">Today</option>
                        <option value="tomorrow">Tomorrow</option>
                        <option value="this_week">This Week</option>
                        <option value="this_month">This Month</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="past">Past</option>
                    </select>
                    <button onclick="clearFilters()" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 transition-colors duration-200">
                        Clear
                </button>
                </div>
            </div>
        </div>

        <div class="bg-white shadow rounded-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead class="text-gray-600 border-b bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 font-semibold w-20">Booking ID</th>
                            <th class="px-6 py-3 font-semibold w-32">Guest Name</th>
                            <th class="px-6 py-3 font-semibold w-40">Contact Info</th>
                            <th class="px-6 py-3 font-semibold w-28">Room</th>
                            <th class="px-6 py-3 font-semibold w-36">Check-in</th>
                            <th class="px-6 py-3 font-semibold w-36">Check-out</th>
                            <th class="px-6 py-3 font-semibold w-24">Total Price</th>
                            <th class="px-6 py-3 font-semibold w-32">Status</th>
                            <th class="px-6 py-3 font-semibold w-20">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="bookingsTableBody">
                        <!-- Bookings will be loaded here -->
                    </tbody>
                </table>
            </div>
            <div id="noBookingsMessage" class="hidden text-center py-8 text-gray-500">
                <i class="fas fa-calendar-times text-4xl mb-4"></i>
                <p>No bookings found matching your criteria.</p>
            </div>
        </div>
    </main>

    <!-- Add Booking Modal -->
    <div id="addBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Add New Booking</h2>
                <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="addBookingForm" class="space-y-4">
                <input type="hidden" name="csrf_token" id="csrfToken">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Name *</label>
                    <input type="text" id="addGuestName" name="guestName" placeholder="Enter guest name"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                    <span id="addGuestNameMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Phone *</label>
                    <input type="tel" id="addGuestPhone" name="guestPhone" placeholder="Enter guest phone"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                    <span id="addGuestPhoneMsg" class="text-red-600 text-sm"></span>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room *</label>
                    <select id="addRoomId" name="roomId" required
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                        <option value="">Select room</option>
                    </select>
                    <span id="addRoomIdMsg" class="text-red-600 text-sm"></span>
                    <p class="text-xs text-gray-500 mt-1">Select check-in and check-out dates to see available rooms only</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date *</label>
                        <input type="date" id="addCheckIn" name="checkIn" required
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3" min="">
                    <span id="addCheckInMsg" class="text-red-600 text-sm"></span>
                </div>
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date *</label>
                        <input type="date" id="addCheckOut" name="checkOut" required
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3" min="">
                        <span id="addCheckOutMsg" class="text-red-600 text-sm"></span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
                        <input type="time" id="addCheckinTime" name="checkinTime" value="14:00"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
                        <input type="time" id="addCheckoutTime" name="checkoutTime" value="12:00"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Price *</label>
                    <input type="number" id="addTotalPrice" name="totalPrice" placeholder="Price will be calculated automatically" required readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-100 h-12 px-3">
                    <span id="addTotalPriceMsg" class="text-red-600 text-sm"></span>
                    <p class="text-xs text-gray-500 mt-1">Price is automatically calculated based on room rate and number of nights</p>
                    <p id="nightsInfo" class="text-xs text-blue-600 mt-1 hidden"></p>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeAddModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                    <button type="submit" id="submitBtn"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <span id="submitText">Add Booking</span>
                        <i class="fas fa-spinner fa-spin hidden ml-2" id="submitSpinner"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Booking Modal -->
    <div id="editBookingModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold">Edit Booking</h2>
                <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="editBookingForm" class="space-y-4">
                <input type="hidden" name="csrf_token" id="editCSRFToken">
                <input type="hidden" id="editBookingId" name="booking_id">

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Guest Name</label>
                    <input type="text" id="editGuestName" disabled
                        class="w-full rounded-lg border-gray-300 bg-gray-100 h-12 px-3">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Room *</label>
                    <select id="editRoomId" name="room_id"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                        <option value="">Select room</option>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Date *</label>
                        <input type="date" id="editCheckIn" name="check_in"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                </div>
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Date *</label>
                        <input type="date" id="editCheckOut" name="check_out"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Check-in Time</label>
                        <input type="time" id="editCheckinTime" name="checkin_time"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Check-out Time</label>
                        <input type="time" id="editCheckoutTime" name="checkout_time"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Total Price *</label>
                    <input type="number" id="editTotalPrice" name="total_price" readonly
                        class="w-full rounded-lg border-gray-300 bg-gray-100 h-12 px-3">
                    <p class="text-xs text-gray-500 mt-1">Price is automatically calculated based on room rate and number of nights</p>
                    <p id="editNightsInfo" class="text-xs text-blue-600 mt-1 hidden"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Status *</label>
                    <select id="editStatus" name="status"
                        class="w-full rounded-lg border-gray-300 focus:ring-blue-500 focus:border-blue-500 h-12 px-3">
                        <option value="Pending">Pending</option>
                        <option value="Confirmed">Confirmed</option>
                        <option value="Checked-in">Checked-in</option>
                        <option value="Canceled">Canceled</option>
                        <option value="Checked-out">Checked-out</option>
                    </select>
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors duration-200">Cancel</button>
                    <button type="submit" id="editSubmitBtn"
                        class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-colors duration-200">
                        <span id="editSubmitText">Update Booking</span>
                        <i class="fas fa-spinner fa-spin hidden ml-2" id="editSubmitSpinner"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Notification Modal -->
    <div id="notificationModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md transform transition-all">
            <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                    <h3 id="notificationTitle" class="text-xl font-semibold text-gray-800"></h3>
                    <button onclick="closeNotificationModal()" class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
                <p id="notificationMessage" class="text-gray-600 text-base leading-relaxed mb-6"></p>
            <div class="flex justify-end">
                <button onclick="closeNotificationModal()"
                        class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        OK
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let allBookings = [];
        let csrfToken = '';
        let searchTimeout = null;

        // Load CSRF token and bookings on page load
        document.addEventListener('DOMContentLoaded', async function() {
            await loadCSRFToken();
            await loadBookings();
            setupEventListeners();
        });

        function setupEventListeners() {
            // Set date restrictions
            setDateRestrictions();
            
            // Real-time search functionality with debouncing
            document.getElementById('searchInput').addEventListener('input', function() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    performRealTimeSearch();
                }, 300); // 300ms delay to prevent too many API calls
            });
            
            document.getElementById('statusFilter').addEventListener('change', performRealTimeSearch);
            document.getElementById('dateFilter').addEventListener('change', performRealTimeSearch);
            
            // Date change listeners for room availability
            document.getElementById('addCheckIn').addEventListener('change', checkRoomAvailability);
            document.getElementById('addCheckOut').addEventListener('change', checkRoomAvailability);
            document.getElementById('editCheckIn').addEventListener('change', checkEditRoomAvailability);
            document.getElementById('editCheckOut').addEventListener('change', checkEditRoomAvailability);
            
            // Room selection listener for price calculation
            document.getElementById('addRoomId').addEventListener('change', calculateTotalPrice);
            document.getElementById('editRoomId').addEventListener('change', calculateEditTotalPrice);
        }

        async function loadCSRFToken() {
            try {
                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php?action=getCSRFToken');
                const data = await response.json();
                csrfToken = data.csrf_token;
                document.getElementById('csrfToken').value = csrfToken;
                document.getElementById('editCSRFToken').value = csrfToken;
            } catch (error) {
                console.error('Error loading CSRF token:', error);
            }
        }

        async function loadBookings() {
            await performRealTimeSearch();
        }

        async function performRealTimeSearch() {
            const searchTerm = document.getElementById('searchInput').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const dateFilter = document.getElementById('dateFilter').value;
            
            // Show loading state
            const tbody = document.getElementById('bookingsTableBody');
            const noBookingsMessage = document.getElementById('noBookingsMessage');
            
            if (searchTerm || statusFilter || dateFilter) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9" class="px-6 py-8 text-center">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-spinner fa-spin text-blue-600 text-xl mr-3"></i>
                                <span class="text-gray-600">Searching...</span>
                            </div>
                        </td>
                    </tr>
                `;
                noBookingsMessage.classList.add('hidden');
            }

            try {
                const formData = new FormData();
                formData.append('action', 'get_bookings_with_search');
                formData.append('search', searchTerm);
                formData.append('status_filter', statusFilter);
                formData.append('date_filter', dateFilter);
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                if (data.success) {
                    allBookings = data.data;
                    displayBookings(allBookings);
                } else {
                    showNotification('Error', 'Failed to load bookings');
                }
            } catch (error) {
                console.error('Error loading bookings:', error);
                showNotification('Error', 'Failed to load bookings. Please try again.');
            }
        }

        function displayBookings(bookings) {
            const tbody = document.getElementById('bookingsTableBody');
            const noBookingsMessage = document.getElementById('noBookingsMessage');
            
            if (bookings.length === 0) {
                tbody.innerHTML = '';
                noBookingsMessage.classList.remove('hidden');
                return;
            }

            noBookingsMessage.classList.add('hidden');
            tbody.innerHTML = '';

            bookings.forEach(booking => {
                const row = document.createElement('tr');
                row.className = 'border-b hover:bg-gray-50 transition-colors duration-200';
                row.innerHTML = `
                    <td class="px-6 py-4 font-medium">${booking.booking_id}</td>
                    <td class="px-6 py-4">${escapeHtml(booking.user_name)}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>${escapeHtml(booking.user_email)}</div>
                            <div class="text-gray-500">${escapeHtml(booking.user_phone)}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>${escapeHtml(booking.room_number)}</div>
                            <div class="text-gray-500">(${escapeHtml(booking.room_type)})</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>${formatDate(booking.booking_check_in)} at ${formatTime(booking.booking_checkin_time)}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm">
                            <div>${formatDate(booking.booking_check_out)} at ${formatTime(booking.booking_checkout_time)}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 font-medium">₱${parseFloat(booking.booking_total_price).toLocaleString()}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 rounded-full text-xs font-medium status-badge ${booking.status_class}">
                            ${booking.booking_status}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex space-x-2">
                            <button class="text-blue-600 hover:text-blue-800 transition-colors duration-200"
                                onclick="editBooking(${booking.booking_id})"
                                title="Edit booking">
                                <i class="fas fa-edit text-lg"></i>
                            </button>
                            <button class="text-red-600 hover:text-red-800 transition-colors duration-200" 
                                onclick="confirmDelete(${booking.booking_id})"
                                title="Delete booking">
                                <i class="fas fa-trash text-lg"></i>
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            document.getElementById('dateFilter').value = '';
            performRealTimeSearch();
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

        function formatTime(time) {
            if (!time) return '2:00 PM';
            
            const timeObj = new Date(`2000-01-01T${time}`);
            return timeObj.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }

        function showNotification(title, message) {
            document.getElementById('notificationTitle').textContent = title;
            document.getElementById('notificationMessage').textContent = message;
            document.getElementById('notificationModal').classList.remove('hidden');
        }

        function closeNotificationModal() {
            document.getElementById('notificationModal').classList.add('hidden');
        }

        // Modal functions
        function openAddModal() {
            document.getElementById('addBookingModal').classList.remove('hidden');
            document.getElementById('addBookingForm').reset();
            loadAllRooms(); // Load all rooms when modal opens
        }

        function closeAddModal() {
            document.getElementById('addBookingModal').classList.add('hidden');
        }

        function openEditModal() {
            document.getElementById('editBookingModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editBookingModal').classList.add('hidden');
        }

        // Load all rooms for initial display
        async function loadAllRooms() {
            try {
                const formData = new FormData();
                formData.append('action', 'get_all_rooms');
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    const roomSelect = document.getElementById('addRoomId');
                    roomSelect.innerHTML = '<option value="">Select room</option>';
                    
                    data.data.forEach(room => {
                        const option = document.createElement('option');
                        option.value = room.room_id;
                        option.textContent = `${room.room_number} (${room.room_type}) - ₱${parseFloat(room.room_price).toLocaleString()}/night`;
                        option.dataset.price = room.room_price;
                        roomSelect.appendChild(option);
                    });
                }
            } catch (error) {
                console.error('Error loading all rooms:', error);
            }
        }

        // Set date restrictions to prevent past dates
        function setDateRestrictions() {
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('addCheckIn').min = today;
            document.getElementById('addCheckOut').min = today;
            
            // Set minimum check-out date based on check-in date
            document.getElementById('addCheckIn').addEventListener('change', function() {
                const checkInDate = this.value;
                if (checkInDate) {
                    document.getElementById('addCheckOut').min = checkInDate;
                    // If check-out date is before check-in date, clear it
                    if (document.getElementById('addCheckOut').value && document.getElementById('addCheckOut').value < checkInDate) {
                        document.getElementById('addCheckOut').value = '';
                    }
                }
                calculateTotalPrice();
            });
            
            document.getElementById('addCheckOut').addEventListener('change', function() {
                calculateTotalPrice();
            });
        }

        // Calculate total price based on room selection and dates
        function calculateTotalPrice() {
            const roomSelect = document.getElementById('addRoomId');
            const checkIn = document.getElementById('addCheckIn').value;
            const checkOut = document.getElementById('addCheckOut').value;
            const totalPriceInput = document.getElementById('addTotalPrice');
            
            if (roomSelect.value && checkIn && checkOut) {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const roomPrice = parseFloat(selectedOption.dataset.price);
                
                // Calculate number of nights
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const totalPrice = roomPrice * nights;
                    totalPriceInput.value = totalPrice.toFixed(2);
                    document.getElementById('nightsInfo').textContent = `(${nights} nights)`;
                    document.getElementById('nightsInfo').classList.remove('hidden');
                } else {
                    totalPriceInput.value = '';
                    document.getElementById('nightsInfo').textContent = '';
                    document.getElementById('nightsInfo').classList.add('hidden');
                }
            } else {
                totalPriceInput.value = '';
                document.getElementById('nightsInfo').textContent = '';
                document.getElementById('nightsInfo').classList.add('hidden');
            }
        }

        // Room availability check
        async function checkRoomAvailability() {
            const checkIn = document.getElementById('addCheckIn').value;
            const checkOut = document.getElementById('addCheckOut').value;
            const roomSelect = document.getElementById('addRoomId');

            if (checkIn && checkOut) {
                // Show loading state
                roomSelect.innerHTML = '<option value="">Loading available rooms...</option>';
                
                try {
                    const formData = new FormData();
                    formData.append('action', 'get_available_rooms');
                    formData.append('check_in', checkIn);
                    formData.append('check_out', checkOut);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        roomSelect.innerHTML = '<option value="">Select room</option>';
                        
                        if (data.data.length === 0) {
                            roomSelect.innerHTML = '<option value="">No available rooms for selected dates</option>';
                        } else {
                            data.data.forEach(room => {
                                const option = document.createElement('option');
                                option.value = room.room_id;
                                option.textContent = `${room.room_number} (${room.room_type}) - ₱${parseFloat(room.room_price).toLocaleString()}/night`;
                                option.dataset.price = room.room_price;
                                roomSelect.appendChild(option);
                            });
                        }
                        // Calculate price after loading rooms
                        calculateTotalPrice();
                    } else {
                        roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
                    }
                } catch (error) {
                    console.error('Error checking room availability:', error);
                    roomSelect.innerHTML = '<option value="">Error loading rooms</option>';
                }
            } else {
                // If dates are not selected, load all rooms
                loadAllRooms();
            }
        }

        // Calculate total price for edit modal
        function calculateEditTotalPrice() {
            const roomSelect = document.getElementById('editRoomId');
            const checkIn = document.getElementById('editCheckIn').value;
            const checkOut = document.getElementById('editCheckOut').value;
            const totalPriceInput = document.getElementById('editTotalPrice');
            
            if (roomSelect.value && checkIn && checkOut) {
                const selectedOption = roomSelect.options[roomSelect.selectedIndex];
                const roomPrice = parseFloat(selectedOption.dataset.price);
                
                // Calculate number of nights
                const checkInDate = new Date(checkIn);
                const checkOutDate = new Date(checkOut);
                const nights = Math.ceil((checkOutDate - checkInDate) / (1000 * 60 * 60 * 24));
                
                if (nights > 0) {
                    const totalPrice = roomPrice * nights;
                    totalPriceInput.value = totalPrice.toFixed(2);
                    document.getElementById('editNightsInfo').textContent = `(${nights} nights)`;
                    document.getElementById('editNightsInfo').classList.remove('hidden');
                } else {
                    totalPriceInput.value = '';
                    document.getElementById('editNightsInfo').textContent = '';
                    document.getElementById('editNightsInfo').classList.add('hidden');
                }
            } else {
                totalPriceInput.value = '';
                document.getElementById('editNightsInfo').textContent = '';
                document.getElementById('editNightsInfo').classList.add('hidden');
            }
        }

        async function checkEditRoomAvailability() {
            const checkIn = document.getElementById('editCheckIn').value;
            const checkOut = document.getElementById('editCheckOut').value;
            const bookingId = document.getElementById('editBookingId').value;

            if (checkIn && checkOut) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'get_available_rooms');
                    formData.append('check_in', checkIn);
                    formData.append('check_out', checkOut);
                    formData.append('current_booking_id', bookingId);
                    formData.append('csrf_token', csrfToken);

                    const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                        method: 'POST',
                        body: formData
                    });

                    const data = await response.json();
                    if (data.success) {
                        const roomSelect = document.getElementById('editRoomId');
                        roomSelect.innerHTML = '<option value="">Select room</option>';
                        
                        data.data.forEach(room => {
                            const option = document.createElement('option');
                            option.value = room.room_id;
                            option.textContent = `${room.room_number} (${room.room_type}) - ₱${parseFloat(room.room_price).toLocaleString()}/night`;
                            option.dataset.price = room.room_price;
                            roomSelect.appendChild(option);
                        });
                        calculateEditTotalPrice();
                    }
                } catch (error) {
                    console.error('Error checking room availability:', error);
                }
            }
        }

        // Form submissions
        document.getElementById('addBookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const submitText = document.getElementById('submitText');
            const submitSpinner = document.getElementById('submitSpinner');

            submitBtn.disabled = true;
            submitText.textContent = 'Adding...';
            submitSpinner.classList.remove('hidden');

            try {
                const formData = new FormData(this);
            formData.append('action', 'add_booking');
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeAddModal();
                    await loadBookings();
                    showNotification('Success', 'Booking added successfully');
                } else {
                    showNotification('Error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error', 'An error occurred while adding the booking.');
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Add Booking';
                submitSpinner.classList.add('hidden');
            }
        });

        document.getElementById('editBookingForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('editSubmitBtn');
            const submitText = document.getElementById('editSubmitText');
            const submitSpinner = document.getElementById('editSubmitSpinner');

            submitBtn.disabled = true;
            submitText.textContent = 'Updating...';
            submitSpinner.classList.remove('hidden');

            try {
                const formData = new FormData(this);
            formData.append('action', 'update_booking');
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    closeEditModal();
                    await loadBookings();
                    showNotification('Success', 'Booking updated successfully');
                } else {
                    showNotification('Error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error', 'An error occurred while updating the booking.');
            } finally {
                submitBtn.disabled = false;
                submitText.textContent = 'Update Booking';
                submitSpinner.classList.add('hidden');
            }
        });

        // Edit booking function
        async function editBooking(bookingId) {
            try {
                const formData = new FormData();
                formData.append('action', 'get_booking_details');
                formData.append('booking_id', bookingId);
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    const booking = data.data;

                    // Pre-fill all form fields
                    document.getElementById('editBookingId').value = booking.booking_id;
                    document.getElementById('editGuestName').value = booking.user_name;
                    document.getElementById('editCheckIn').value = booking.booking_check_in;
                    document.getElementById('editCheckOut').value = booking.booking_check_out;
                    document.getElementById('editCheckinTime').value = booking.booking_checkin_time || '';
                    document.getElementById('editCheckoutTime').value = booking.booking_checkout_time || '';
                    document.getElementById('editTotalPrice').value = booking.booking_total_price;
                    document.getElementById('editStatus').value = booking.booking_status;

                    // Populate room information automatically with current room pre-selected
                    const roomSelect = document.getElementById('editRoomId');
                    roomSelect.innerHTML = `<option value="${booking.room_id}" selected data-price="${booking.room_price}">${booking.room_number} (${booking.room_type}) - ₱${parseFloat(booking.room_price).toLocaleString()}/night</option>`;

                    // Also load other available rooms for the same dates
                    await loadAvailableRoomsForEdit(booking.booking_check_in, booking.booking_check_out, booking.room_id);

                    // Calculate initial price
                    calculateEditTotalPrice();

                    openEditModal();
                } else {
                    showNotification('Error', 'Failed to load booking details');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('Error', 'An error occurred while loading booking details.');
            }
        }

        // Load available rooms for edit modal
        async function loadAvailableRoomsForEdit(checkIn, checkOut, currentRoomId) {
            try {
                const formData = new FormData();
                formData.append('action', 'get_available_rooms');
                formData.append('check_in', checkIn);
                formData.append('check_out', checkOut);
                formData.append('current_booking_id', document.getElementById('editBookingId').value);
                formData.append('csrf_token', csrfToken);

                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                if (data.success) {
                    const roomSelect = document.getElementById('editRoomId');
                    // Keep the current room as the first option
                    const currentOption = roomSelect.querySelector('option[selected]');
                    roomSelect.innerHTML = '';
                    
                    if (currentOption) {
                        roomSelect.appendChild(currentOption);
                    }
                    
                    // Add other available rooms
                    data.data.forEach(room => {
                        if (room.room_id != currentRoomId) {
                            const option = document.createElement('option');
                            option.value = room.room_id;
                            option.textContent = `${room.room_number} (${room.room_type}) - ₱${parseFloat(room.room_price).toLocaleString()}/night`;
                            option.dataset.price = room.room_price;
                            roomSelect.appendChild(option);
                        }
                    });
                    calculateEditTotalPrice();
                }
            } catch (error) {
                console.error('Error loading available rooms for edit:', error);
            }
        }

        // Delete booking function
        function confirmDelete(bookingId) {
            const confirmModal = document.createElement('div');
            confirmModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4';
            confirmModal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-md">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-lg font-semibold text-gray-800">Confirm Delete</h3>
                            </div>
                        </div>
                        <p class="text-gray-600 mb-6">Are you sure you want to delete this booking? This action cannot be undone.</p>
                        <div class="flex justify-end gap-3">
                            <button onclick="this.closest('.fixed').remove()" 
                                class="px-4 py-2 bg-gray-300 rounded-lg hover:bg-gray-400 transition-colors duration-200">
                                Cancel
                            </button>
                            <button onclick="deleteBooking(${bookingId}, this)" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            `;
            document.body.appendChild(confirmModal);
        }

        async function deleteBooking(bookingId, button) {
            const modal = button.closest('.fixed');
            const formData = new FormData();
            formData.append('action', 'delete_booking');
            formData.append('booking_id', bookingId);
            formData.append('csrf_token', csrfToken);

            try {
                const response = await fetch('/Proj/Admin/Bookings/back/bookings_backend.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();
                modal.remove();

                if (data.success) {
                    await loadBookings();
                    showNotification('Success', 'Booking deleted successfully');
                } else {
                    showNotification('Error', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
                modal.remove();
                showNotification('Error', 'An error occurred while deleting the booking.');
            }
        }
    </script>

</body>

</html>