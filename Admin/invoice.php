<?php
session_start();
require_once '../PHP/db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

$stmt = $conn->query("
    SELECT i.*, u.user_name, b.booking_id, b.booking_total_price, p.payment_status 
    FROM invoice i 
    JOIN payments p ON i.payment_id = p.payment_id 
    JOIN bookings b ON p.booking_id = b.booking_id 
    JOIN users u ON b.user_id = u.user_id 
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
        <?php require_once 'sidebar.php'; ?>

        <div class="flex-1 flex flex-col overflow-hidden">

            <div class="md:ml-0 w-full">
                <?php require_once 'navbar.php'; ?>
            </div>

            <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-100 pt-20 px-6 md:ml-64">
                <div class="container mx-auto">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-semibold text-gray-800">Invoice Management</h1>
                        <button onclick="openAddInvoiceModal()"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex items-center">
                            <i class="fas fa-plus mr-2"></i> Add Invoice
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

    <div id="addInvoiceModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Invoice</h3>
                <form id="addInvoiceForm" onsubmit="handleAddInvoice(event)">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="booking_id">Select
                            Booking</label>
                        <select name="booking_id" id="booking_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a booking</option>
                            <?php
                            $stmt = $conn->query("
                                SELECT b.booking_id, u.user_name, b.booking_total_price 
                                FROM bookings b 
                                JOIN users u ON b.user_id = u.user_id 
                                WHERE b.booking_status = 'Confirmed'
                                AND b.booking_id NOT IN (
                                    SELECT p.booking_id 
                                    FROM payments p 
                                    JOIN invoice i ON p.payment_id = i.payment_id
                                )
                            ");
                            while ($booking = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<option value='{$booking['booking_id']}'>Booking #{$booking['booking_id']} - {$booking['user_name']} (₱{$booking['booking_total_price']})</option>";
                            }
                            ?>
                        </select>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="closeAddInvoiceModal()"
                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg mr-2">Cancel</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded-lg">Add Invoice</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="editInvoiceModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Invoice Status</h3>
                <form id="editInvoiceForm" onsubmit="handleEditInvoice(event)">
                    <input type="hidden" name="invoice_id" id="edit_invoice_id">
                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="invoice_status">Status</label>
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
        }

        function closeAddInvoiceModal() {
            document.getElementById('addInvoiceModal').classList.add('hidden');
        }

        function openEditModal(invoice) {
            document.getElementById('edit_invoice_id').value = invoice.invoice_id;
            document.getElementById('edit_invoice_status').value = invoice.invoice_status;
            document.getElementById('editInvoiceModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editInvoiceModal').classList.add('hidden');
        }

        function handleAddInvoice(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);

            fetch('backend/invoice_add.php', {
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

            fetch('backend/invoice_edit.php', {
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
                fetch('backend/invoice_delete.php', {
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