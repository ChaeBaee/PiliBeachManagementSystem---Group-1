<?php
require_once '../back/payments_backend.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: /Proj/Admin/Dashboard/front/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments Management - Pili Beach Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
<?php include '../../Elements/navbar.php'; ?>
<?php include '../../Elements/sidebar.php'; ?>
    <main class="md:ml-64 p-4 pt-20">
        <div class="max-w-7xl mx-auto">

            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Payments Management</h1>
                <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Create Payment Tab
                </button>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4"
                    role="alert">
                    <span class="block sm:inline"><?php echo $_SESSION['success'];
                    unset($_SESSION['success']); ?></span>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline"><?php echo $_SESSION['error'];
                    unset($_SESSION['error']); ?></span>
                </div>
            <?php endif; ?>

            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payment ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Booking Details</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Amount</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Status</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($payments as $payment): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $payment['payment_id']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div>Room: <?php echo $payment['room_number']; ?></div>
                                        <div>Guest: <?php echo $payment['user_name']; ?></div>
                                        <div class="text-xs text-gray-500">
                                            <?php echo date('M d, Y', strtotime($payment['booking_check_in'])); ?> -
                                            <?php echo date('M d, Y', strtotime($payment['booking_check_out'])); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        ₱<?php echo number_format($payment['payment_amount'], 2); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo date('M d, Y', strtotime($payment['payment_date'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php echo $payment['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' :
                                                ($payment['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'); ?>">
                                            <?php echo ucfirst($payment['payment_status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button
                                            onclick="openEditModal(<?php echo htmlspecialchars(json_encode($payment)); ?>)"
                                            class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="openDeleteModal(<?php echo $payment['payment_id']; ?>)"
                                            class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Create Payment Tab</h3>
                <p class="text-sm text-gray-600 mb-4">Select a room to automatically calculate the total cost for the customer's stay.</p>
                <form action="payments.php" method="POST">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="booking_id">Select Room/Booking</label>
                        <select name="booking_id" id="booking_id" required onchange="showBookingDetails(this.value)"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a room/booking</option>
                            <?php foreach ($availableBookings as $booking): ?>
                                <option value="<?php echo $booking['booking_id']; ?>"
                                    data-amount="<?php echo $booking['total_amount']; ?>"
                                    data-customer="<?php echo htmlspecialchars($booking['user_name']); ?>"
                                    data-room="<?php echo $booking['room_number']; ?>"
                                    data-checkin="<?php echo $booking['booking_check_in']; ?>"
                                    data-checkout="<?php echo $booking['booking_check_out']; ?>"
                                    data-nights="<?php echo $booking['nights']; ?>"
                                    data-room-price="<?php echo $booking['room_price']; ?>">
                                    Room <?php echo $booking['room_number']; ?> - <?php echo $booking['user_name']; ?> 
                                    (<?php echo date('M d, Y', strtotime($booking['booking_check_in'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">Select a room to view booking details and total cost</p>
                    </div>

                    <!-- Booking Details Display -->
                    <div id="bookingDetails" class="hidden mb-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">Booking Details</h4>
                        <div class="space-y-1 text-sm">
                            <div><strong>Customer:</strong> <span id="customerName" class="text-gray-700"></span></div>
                            <div><strong>Room:</strong> <span id="roomNumber" class="text-gray-700"></span></div>
                            <div><strong>Check-in:</strong> <span id="checkInDate" class="text-gray-700"></span></div>
                            <div><strong>Check-out:</strong> <span id="checkOutDate" class="text-gray-700"></span></div>
                            <div><strong>Nights:</strong> <span id="nights" class="text-gray-700"></span></div>
                            <div><strong>Room Price:</strong> <span id="roomPrice" class="text-gray-700"></span></div>
                            <div class="border-t pt-2 mt-2">
                                <strong>Total Amount:</strong> 
                                <span id="totalAmount" class="text-lg font-bold text-green-600">₱0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_status">Payment Status</label>
                        <select name="payment_status" id="payment_status" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeAddModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" id="submitBtn" disabled
                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 disabled:bg-gray-400 disabled:cursor-not-allowed">
                            Create Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Update Payment Status</h3>
                <p class="text-sm text-gray-600 mb-4">Review payment details and update the status.</p>
                <form action="payments.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="payment_id" id="edit_payment_id">

                    <!-- Payment Details Display -->
                    <div id="editPaymentDetails" class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">Payment Details</h4>
                        <div class="space-y-1 text-sm">
                            <div><strong>Payment ID:</strong> <span id="editPaymentId" class="text-gray-700"></span></div>
                            <div><strong>Customer:</strong> <span id="editCustomerName" class="text-gray-700"></span></div>
                            <div><strong>Room:</strong> <span id="editRoomNumber" class="text-gray-700"></span></div>
                            <div><strong>Check-in:</strong> <span id="editCheckInDate" class="text-gray-700"></span></div>
                            <div><strong>Check-out:</strong> <span id="editCheckOutDate" class="text-gray-700"></span></div>
                            <div><strong>Nights:</strong> <span id="editNights" class="text-gray-700"></span></div>
                            <div><strong>Room Price:</strong> <span id="editRoomPrice" class="text-gray-700"></span></div>
                            <div class="border-t pt-2 mt-2">
                                <strong>Total Amount:</strong> 
                                <span id="editTotalAmount" class="text-lg font-bold text-green-600">₱0.00</span>
                            </div>
                            <div class="border-t pt-2 mt-2">
                                <strong>Payment Date:</strong> 
                                <span id="editPaymentDate" class="text-gray-700"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2"
                            for="edit_payment_status">Payment Status</label>
                        <div class="mb-2">
                            <span class="text-xs text-gray-500">Current Status: </span>
                            <span id="currentStatus" class="px-2 py-1 text-xs font-semibold rounded-full"></span>
                        </div>
                        <select name="payment_status" id="edit_payment_status" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="pending">Pending</option>
                            <option value="paid">Paid</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Update Status
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Delete Payment</h3>
                <p class="text-gray-600 mb-4">Are you sure you want to delete this payment? This action cannot be
                    undone.</p>
                <form action="payments.php" method="POST">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="payment_id" id="delete_payment_id">

                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeDeleteModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400">
                            Cancel
                        </button>
                        <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700">
                            Delete Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            // Reset form and hide booking details
            document.getElementById('booking_id').value = '';
            document.getElementById('bookingDetails').classList.add('hidden');
            document.getElementById('submitBtn').disabled = true;
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('booking_id').value = '';
            document.getElementById('bookingDetails').classList.add('hidden');
            document.getElementById('submitBtn').disabled = true;
        }

        function showBookingDetails(bookingId) {
            const select = document.getElementById('booking_id');
            const selectedOption = select.options[select.selectedIndex];
            const submitBtn = document.getElementById('submitBtn');
            
            if (bookingId && selectedOption && bookingId !== '') {
                const amount = selectedOption.getAttribute('data-amount');
                const customer = selectedOption.getAttribute('data-customer');
                const room = selectedOption.getAttribute('data-room');
                const checkIn = selectedOption.getAttribute('data-checkin');
                const checkOut = selectedOption.getAttribute('data-checkout');
                const nights = selectedOption.getAttribute('data-nights');
                const roomPrice = selectedOption.getAttribute('data-room-price');

                // Format dates for display
                let checkInDate = '';
                let checkOutDate = '';
                
                try {
                    if (checkIn) {
                        checkInDate = new Date(checkIn).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    }
                    if (checkOut) {
                        checkOutDate = new Date(checkOut).toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'short', 
                            day: 'numeric' 
                        });
                    }
                } catch (error) {
                    console.error('Error formatting dates:', error);
                }

                document.getElementById('customerName').textContent = customer || 'N/A';
                document.getElementById('roomNumber').textContent = room || 'N/A';
                document.getElementById('checkInDate').textContent = checkInDate || 'N/A';
                document.getElementById('checkOutDate').textContent = checkOutDate || 'N/A';
                document.getElementById('nights').textContent = nights ? `${nights} night(s)` : 'N/A';
                document.getElementById('roomPrice').textContent = roomPrice ? `₱${parseFloat(roomPrice).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per night` : 'N/A';
                document.getElementById('totalAmount').textContent = amount ? `₱${parseFloat(amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱0.00';

                document.getElementById('bookingDetails').classList.remove('hidden');
                submitBtn.disabled = false;
            } else {
                document.getElementById('bookingDetails').classList.add('hidden');
                submitBtn.disabled = true;
            }
        }

        function openEditModal(payment) {
            document.getElementById('edit_payment_id').value = payment.payment_id;
            document.getElementById('edit_payment_status').value = payment.payment_status;
            document.getElementById('editModal').classList.remove('hidden');

            // Populate edit payment details
            document.getElementById('editPaymentId').textContent = payment.payment_id;
            document.getElementById('editCustomerName').textContent = payment.user_name || 'N/A';
            document.getElementById('editRoomNumber').textContent = payment.room_number || 'N/A';
            
            // Format dates for display
            let checkInDate = '';
            let checkOutDate = '';
            let paymentDate = '';
            
            try {
                if (payment.booking_check_in) {
                    checkInDate = new Date(payment.booking_check_in).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
                if (payment.booking_check_out) {
                    checkOutDate = new Date(payment.booking_check_out).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
                if (payment.payment_date) {
                    paymentDate = new Date(payment.payment_date).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
            } catch (error) {
                console.error('Error formatting dates:', error);
            }

            document.getElementById('editCheckInDate').textContent = checkInDate || 'N/A';
            document.getElementById('editCheckOutDate').textContent = checkOutDate || 'N/A';
            document.getElementById('editPaymentDate').textContent = paymentDate || 'N/A';
            
            // Use nights from backend data
            document.getElementById('editNights').textContent = payment.nights ? `${payment.nights} night(s)` : 'N/A';
            
            // Use room price from backend data
            document.getElementById('editRoomPrice').textContent = payment.room_price ? 
                `₱${parseFloat(payment.room_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per night` : 'N/A';
            
            document.getElementById('editTotalAmount').textContent = payment.payment_amount ? 
                `₱${parseFloat(payment.payment_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱0.00';

            // Set current status
            const currentStatus = document.getElementById('currentStatus');
            const statusText = payment.payment_status.charAt(0).toUpperCase() + payment.payment_status.slice(1);
            currentStatus.textContent = statusText;
            currentStatus.className = `px-2 py-1 text-xs font-semibold rounded-full ${
                payment.payment_status === 'paid' ? 'bg-green-100 text-green-800' :
                (payment.payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
            }`;
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            // Reset form
            document.getElementById('edit_payment_id').value = '';
            document.getElementById('edit_payment_status').value = 'pending';
        }

        function openDeleteModal(paymentId) {
            document.getElementById('delete_payment_id').value = paymentId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        window.onclick = function (event) {
            if (event.target == document.getElementById('addModal')) {
                closeAddModal();
            }
            if (event.target == document.getElementById('editModal')) {
                closeEditModal();
            }
            if (event.target == document.getElementById('deleteModal')) {
                closeDeleteModal();
            }
        }
    </script>
</body>

</html>