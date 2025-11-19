<?php
session_start();
require_once '../../../PHP/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: /Proj/Admin/Dashboard/front/index.php");
    exit;
}


$stmt = $conn->query("
    SELECT i.*, u.user_name, b.booking_id, b.booking_total_price, b.booking_check_in, b.booking_check_out, 
           r.room_number, r.room_price, p.payment_status, p.payment_date,
           DATEDIFF(b.booking_check_out, b.booking_check_in) as nights
    FROM invoice i 
    JOIN payments p ON i.payment_id = p.payment_id 
    JOIN bookings b ON p.booking_id = b.booking_id 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id
    ORDER BY i.invoice_date_created DESC
");
$invoices = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Management - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
    <div class="flex h-screen">
        <?php include '../../Elements/sidebar.php'; ?>
        <div class="flex-1 flex flex-col overflow-hidden">

            <div class="md:ml-0 w-full">
                <?php include '../../Elements/navbar.php'; ?>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 pt-20 px-6 md:ml-64">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">Invoice Management</h1>
                        <button onclick="openAddInvoiceModal()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i> Create Invoice Tab
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Invoice ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Customer</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Booking ID</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Amount</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Payment Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Invoice Status</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date Created</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php foreach ($invoices as $invoice): ?>
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $invoice['invoice_id']; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $invoice['payment_id']; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo htmlspecialchars($invoice['user_name']); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo $invoice['booking_id']; ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                ₱<?php echo number_format($invoice['invoice_total_amount'], 2); ?></td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $invoice['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' :
                                                    ($invoice['payment_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                        'bg-red-100 text-red-800'); ?>">
                                                    <?php echo ucfirst($invoice['payment_status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                <?php echo $invoice['invoice_status'] === 'paid' ? 'bg-green-100 text-green-800' :
                                                    ($invoice['invoice_status'] === 'unpaid' ? 'bg-yellow-100 text-yellow-800' :
                                                        'bg-red-100 text-red-800'); ?>">
                                                    <?php echo ucfirst($invoice['invoice_status']); ?>
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                <?php echo date('M d, Y', strtotime($invoice['invoice_date_created'])); ?>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <button
                                                    onclick="openEditModal(<?php echo htmlspecialchars(json_encode($invoice)); ?>)"
                                                    class="text-indigo-600 hover:text-indigo-900 mr-3">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="deleteInvoice(<?php echo $invoice['invoice_id']; ?>)"
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
        </div>
    </div>

    <div id="addInvoiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Create Invoice Tab</h3>
                <p class="text-sm text-gray-600 mb-4">Select a booking to automatically generate an invoice with all details.</p>
                <form id="addInvoiceForm" onsubmit="handleAddInvoice(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="booking_id">Select Room/Booking</label>
                        <select name="booking_id" id="booking_id" required onchange="showBookingDetails(this.value)"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a room/booking</option>
                            <?php
                            $stmt = $conn->query("
                                SELECT b.booking_id, b.booking_total_price, b.booking_check_in, b.booking_check_out,
                                       u.user_name, r.room_number, r.room_price,
                                       DATEDIFF(b.booking_check_out, b.booking_check_in) as nights
                                FROM bookings b 
                                JOIN users u ON b.user_id = u.user_id 
                                JOIN rooms r ON b.room_id = r.room_id
                                WHERE b.booking_status = 'Confirmed'
                                AND b.booking_id NOT IN (
                                    SELECT p.booking_id 
                                    FROM payments p 
                                    JOIN invoice i ON p.payment_id = i.payment_id
                                )
                                ORDER BY b.booking_check_in DESC
                            ");
                            while ($booking = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $checkInDate = date('M d, Y', strtotime($booking['booking_check_in']));
                                echo "<option value='{$booking['booking_id']}' 
                                      data-amount='{$booking['booking_total_price']}'
                                      data-customer='" . htmlspecialchars($booking['user_name']) . "'
                                      data-room='{$booking['room_number']}'
                                      data-checkin='{$booking['booking_check_in']}'
                                      data-checkout='{$booking['booking_check_out']}'
                                      data-nights='{$booking['nights']}'
                                      data-room-price='{$booking['room_price']}'>
                                      Room {$booking['room_number']} - {$booking['user_name']} ({$checkInDate})
                                </option>";
                            }
                            ?>
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

                    <div class="flex justify-end">
                        <button type="button" onclick="closeAddInvoiceModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">Cancel</button>
                        <button type="submit" id="submitBtn" disabled
                            class="bg-blue-500 text-white px-4 py-2 rounded-lg disabled:bg-gray-400 disabled:cursor-not-allowed">
                            Create Invoice
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editInvoiceModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full flex items-center justify-center z-50">
        <div class="relative mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-2">Update Invoice Status</h3>
                <p class="text-sm text-gray-600 mb-4">Review invoice details and update the status.</p>
                <form id="editInvoiceForm" onsubmit="handleEditInvoice(event)">
                    <input type="hidden" name="invoice_id" id="edit_invoice_id">

                    <!-- Invoice Details Display -->
                    <div id="editInvoiceDetails" class="mb-4 p-3 bg-gray-50 rounded-lg">
                        <h4 class="font-semibold text-gray-800 mb-2">Invoice Details</h4>
                        <div class="space-y-1 text-sm">
                            <div><strong>Invoice ID:</strong> <span id="editInvoiceId" class="text-gray-700"></span></div>
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
                            <div class="border-t pt-2 mt-2">
                                <strong>Invoice Date:</strong> 
                                <span id="editInvoiceDate" class="text-gray-700"></span>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="invoice_status">Invoice Status</label>
                        <div class="mb-2">
                            <span class="text-xs text-gray-500">Current Status: </span>
                            <span id="currentInvoiceStatus" class="px-2 py-1 text-xs font-semibold rounded-full"></span>
                        </div>
                        <select name="invoice_status" id="edit_invoice_status" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="paid">Paid</option>
                            <option value="unpaid">Unpaid</option>
                            <option value="canceled">Canceled</option>
                        </select>
                    </div>

                    <div class="flex justify-end">
                        <button type="button" onclick="closeEditModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Update Status</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Modal functions
        function openAddInvoiceModal() {
            document.getElementById('addInvoiceModal').classList.remove('hidden');
            // Reset form and hide booking details
            document.getElementById('booking_id').value = '';
            document.getElementById('bookingDetails').classList.add('hidden');
            document.getElementById('submitBtn').disabled = true;
        }

        function closeAddInvoiceModal() {
            document.getElementById('addInvoiceModal').classList.add('hidden');
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

        function openEditModal(invoice) {
            document.getElementById('edit_invoice_id').value = invoice.invoice_id;
            document.getElementById('edit_invoice_status').value = invoice.invoice_status;
            document.getElementById('editInvoiceModal').classList.remove('hidden');

            // Populate edit invoice details
            document.getElementById('editInvoiceId').textContent = invoice.invoice_id;
            document.getElementById('editPaymentId').textContent = invoice.payment_id;
            document.getElementById('editCustomerName').textContent = invoice.user_name || 'N/A';
            document.getElementById('editRoomNumber').textContent = invoice.room_number || 'N/A';
            
            // Format dates for display
            let checkInDate = '';
            let checkOutDate = '';
            let paymentDate = '';
            let invoiceDate = '';
            
            try {
                if (invoice.booking_check_in) {
                    checkInDate = new Date(invoice.booking_check_in).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
                if (invoice.booking_check_out) {
                    checkOutDate = new Date(invoice.booking_check_out).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
                if (invoice.payment_date) {
                    paymentDate = new Date(invoice.payment_date).toLocaleDateString('en-US', { 
                        year: 'numeric', 
                        month: 'short', 
                        day: 'numeric' 
                    });
                }
                if (invoice.invoice_date_created) {
                    invoiceDate = new Date(invoice.invoice_date_created).toLocaleDateString('en-US', { 
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
            document.getElementById('editInvoiceDate').textContent = invoiceDate || 'N/A';
            
            // Use nights from backend data
            document.getElementById('editNights').textContent = invoice.nights ? `${invoice.nights} night(s)` : 'N/A';
            
            // Use room price from backend data
            document.getElementById('editRoomPrice').textContent = invoice.room_price ? 
                `₱${parseFloat(invoice.room_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})} per night` : 'N/A';
            
            document.getElementById('editTotalAmount').textContent = invoice.invoice_total_amount ? 
                `₱${parseFloat(invoice.invoice_total_amount).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}` : '₱0.00';

            // Set current status
            const currentStatus = document.getElementById('currentInvoiceStatus');
            const statusText = invoice.invoice_status.charAt(0).toUpperCase() + invoice.invoice_status.slice(1);
            currentStatus.textContent = statusText;
            currentStatus.className = `px-2 py-1 text-xs font-semibold rounded-full ${
                invoice.invoice_status === 'paid' ? 'bg-green-100 text-green-800' :
                (invoice.invoice_status === 'unpaid' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')
            }`;
        }

        function closeEditModal() {
            document.getElementById('editInvoiceModal').classList.add('hidden');
            // Reset form
            document.getElementById('edit_invoice_id').value = '';
            document.getElementById('edit_invoice_status').value = 'unpaid';
        }

        function handleAddInvoice(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('../back/invoice_add.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeAddInvoiceModal();
                        location.reload();
                    } else {
                        alert('Error creating invoice: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while creating the invoice');
                });
        }

        function handleEditInvoice(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('../back/invoice_edit.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        closeEditModal();
                        location.reload();
                    } else {
                        alert('Error updating invoice: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while updating the invoice');
                });
        }

        function deleteInvoice(invoiceId) {
            if (confirm('Are you sure you want to delete this invoice?')) {
                fetch('../back/invoice_delete.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'invoice_id=' + invoiceId
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error deleting invoice: ' + data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while deleting the invoice');
                    });
            }
        }
    </script>
</body>

</html>