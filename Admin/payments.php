<?php
session_start();
require_once '../PHP/db.php';

// Set current user ID for audit logging
$currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
$conn->exec("SET @current_user_id = $currentUserId");

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        // Set current user ID for audit logging
        $currentUserId = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
        $conn->exec("SET @current_user_id = $currentUserId");

        switch ($_POST['action']) {
            case 'add':
                try {
                    // Get booking details for audit log
                    $stmt = $conn->prepare("SELECT b.*, u.user_name, r.room_number FROM bookings b 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE b.booking_id = ?");
                    $stmt->execute([$_POST['booking_id']]);
                    $booking = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before insert
                    $conn->exec("SET @current_user_id = $currentUserId");

                    $stmt = $conn->prepare("INSERT INTO payments (booking_id, payment_amount, payment_date, payment_status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([
                        $_POST['booking_id'],
                        $_POST['payment_amount'],
                        $_POST['payment_date'],
                        $_POST['payment_status']
                    ]);
                    $_SESSION['success'] = "Payment added successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error adding payment: " . $e->getMessage();
                }
                break;

            case 'edit':
                try {
                    // Get current payment data for audit log
                    $stmt = $conn->prepare("SELECT * FROM payments WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $currentPayment = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before update
                    $conn->exec("SET @current_user_id = $currentUserId");

                    $stmt = $conn->prepare("UPDATE payments SET payment_status = ? WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_status'], $_POST['payment_id']]);
                    $_SESSION['success'] = "Payment updated successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error updating payment: " . $e->getMessage();
                }
                break;

            case 'delete':
                try {
                    // Get payment data for audit log before deleting
                    $stmt = $conn->prepare("SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
                                          FROM payments p 
                                          JOIN bookings b ON p.booking_id = b.booking_id 
                                          JOIN users u ON b.user_id = u.user_id 
                                          JOIN rooms r ON b.room_id = r.room_id 
                                          WHERE p.payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $paymentToDelete = $stmt->fetch(PDO::FETCH_ASSOC);

                    // Set current user ID again before delete
                    $conn->exec("SET @current_user_id = $currentUserId");

                    $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ?");
                    $stmt->execute([$_POST['payment_id']]);
                    $_SESSION['success'] = "Payment deleted successfully!";
                } catch (PDOException $e) {
                    $_SESSION['error'] = "Error deleting payment: " . $e->getMessage();
                }
                break;
        }
        header("Location: payments.php");
        exit();
    }
}

// Fetch all payments with booking details
$stmt = $conn->query("
    SELECT p.*, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
    FROM payments p 
    JOIN bookings b ON p.booking_id = b.booking_id 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    ORDER BY p.payment_date DESC
");
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch available bookings for new payment
$stmt = $conn->query("
    SELECT b.booking_id, b.booking_check_in, b.booking_check_out, u.user_name, r.room_number 
    FROM bookings b 
    JOIN users u ON b.user_id = u.user_id 
    JOIN rooms r ON b.room_id = r.room_id 
    WHERE b.booking_id NOT IN (SELECT booking_id FROM payments)
");
$availableBookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
    <?php include 'navbar.php'; ?>
    <?php include 'sidebar.php'; ?>

    <main class="md:ml-64 p-4 pt-20">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-semibold text-gray-800">Payments Management</h1>
                <button onclick="openAddModal()" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                    <i class="fas fa-plus mr-2"></i>Add New Payment
                </button>
            </div>

            <!-- Alert Messages -->
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

            <!-- Payments Table -->
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

    <!-- Add Payment Modal -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Payment</h3>
                <form action="payments.php" method="POST">
                    <input type="hidden" name="action" value="add">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="booking_id">Booking</label>
                        <select name="booking_id" id="booking_id" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                            <option value="">Select a booking</option>
                            <?php foreach ($availableBookings as $booking): ?>
                                <option value="<?php echo $booking['booking_id']; ?>">
                                    Room <?php echo $booking['room_number']; ?> - <?php echo $booking['user_name']; ?>
                                    (<?php echo date('M d, Y', strtotime($booking['booking_check_in'])); ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_amount">Amount</label>
                        <input type="number" name="payment_amount" id="payment_amount" required step="0.01" min="0"
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_date">Date</label>
                        <input type="date" name="payment_date" id="payment_date" required
                            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                    </div>

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2" for="payment_status">Status</label>
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
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Add Payment
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Payment Modal -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Edit Payment Status</h3>
                <form action="payments.php" method="POST">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="payment_id" id="edit_payment_id">

                    <div class="mb-4">
                        <label class="block text-gray-700 text-sm font-bold mb-2"
                            for="edit_payment_status">Status</label>
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

    <!-- Delete Payment Modal -->
    <div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
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
        // Modal functions
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function openEditModal(payment) {
            document.getElementById('edit_payment_id').value = payment.payment_id;
            document.getElementById('edit_payment_status').value = payment.payment_status;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openDeleteModal(paymentId) {
            document.getElementById('delete_payment_id').value = paymentId;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').classList.add('hidden');
        }

        // Close modals when clicking outside
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