<?php
session_start();
require_once '../db.php';
include('../elements/navbar.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle cancellation
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['booking_id'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify the booking belongs to the current user
    $stmt = $conn->prepare("SELECT * FROM bookings WHERE booking_id = ? AND user_id = ?");
    $stmt->execute([$booking_id, $user_id]);
    $booking = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($booking) {
        $conn->beginTransaction();
        
        try {
            // Update booking status
            $stmt = $conn->prepare("UPDATE bookings SET booking_status = 'Canceled' WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            
            // Update room status
            $stmt = $conn->prepare("UPDATE rooms SET room_status = 'available' WHERE room_id = ?");
            $stmt->execute([$booking['room_id']]);
            
            // Update payment status
            $stmt = $conn->prepare("UPDATE payments SET payment_status = 'failed' WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            
            // Update invoice status
            $stmt = $conn->prepare("UPDATE invoice SET invoice_status = 'canceled' WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            
            $conn->commit();
            $_SESSION['success_message'] = "Booking cancelled successfully. Room has been made available.";
            
        } catch (Exception $e) {
            $conn->rollBack();
            $_SESSION['error_message'] = "Failed to cancel booking: " . $e->getMessage();
        }
    } else {
        $_SESSION['error_message'] = "Invalid booking cancellation request.";
    }
    
    header("Location: cancel-booking.php");
    exit();
}

// Fetch user's bookings
$stmt = $conn->prepare("
    SELECT b.*, r.room_number, r.room_type, r.room_price 
    FROM bookings b 
    JOIN rooms r ON b.room_id = r.room_id 
    WHERE b.user_id = ? 
    ORDER BY b.booking_id DESC
");
$stmt->execute([$user_id]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Paradise Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #f3f8ff 0%, #e6f0ff 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge {
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        
        .status-confirmed { background: #d1fae5; color: #065f46; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-canceled { background: #fee2e2; color: #991b1b; }
        .status-checked-in { background: #dbeafe; color: #1e40af; }
        .status-checked-out { background: #f3e8ff; color: #7c3aed; }
        
        .booking-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .booking-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .btn-danger {
            background: linear-gradient(135deg, #ff6b6b 0%, #ee5a52 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(255, 107, 107, 0.3);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .btn-secondary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(108, 117, 125, 0.3);
        }
    </style>
</head>

<body class="min-h-screen">
    <?php include('../elements/navbar.php'); ?>

    <div class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8 mt-16">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl md:text-5xl font-bold text-blue-900 mb-4">
                <i class="fas fa-calendar-check text-green-500 mr-3"></i>
                My Bookings
            </h1>
            <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                Review and manage your reservations. You can view booking details and cancel if needed.
            </p>
        </div>

        <!-- Cancellation Policy Alert -->
        <div class="glass-card rounded-2xl p-6 mb-8 border-l-4 border-red-500">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl mr-4 mt-1"></i>
                <div>
                    <h3 class="text-lg font-bold text-red-700 mb-2">Cancellation Policy</h3>
                    <ul class="text-gray-700 space-y-1">
                        <li>• No refunds will be provided for cancelled bookings</li>
                        <li>• Cancellation fees may apply</li>
                        <li>• Once cancelled, the booking cannot be reinstated</li>
                        <li>• Room will be made available for other guests</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="bg-green-50 border-l-4 border-green-400 text-green-700 p-6 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-400 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Success!</p>
                        <p><?= $_SESSION['success_message']; unset($_SESSION['success_message']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="bg-red-50 border-l-4 border-red-400 text-red-700 p-6 rounded-lg mb-8">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle text-red-400 text-xl mr-3"></i>
                    <div>
                        <p class="font-semibold">Error!</p>
                        <p><?= $_SESSION['error_message']; unset($_SESSION['error_message']); ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Bookings List -->
        <div class="glass-card rounded-2xl p-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-calendar-check text-blue-600 mr-3"></i>
                    Booking History
                </h2>
                <a href="profile.php" class="group relative overflow-hidden bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:from-gray-700 hover:to-gray-800 hover:shadow-lg hover:shadow-gray-500/25 hover:-translate-y-1">
                    <div class="absolute inset-0 bg-gradient-to-r from-gray-400 to-gray-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                    <i class="fas fa-arrow-left mr-2"></i>
                    <span>Back to Profile</span>
                </a>
            </div>

            <?php if ($bookings): ?>
                <div class="space-y-6">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="booking-card bg-white rounded-xl p-6 shadow-lg border border-gray-100">
                            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6">
                                <div class="flex-1 space-y-4">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-xl font-bold text-gray-800">
                                            <i class="fas fa-bed text-blue-500 mr-2"></i>
                                            Room <?= $booking['room_number']; ?> - <?= $booking['room_type']; ?>
                                        </h3>
                                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $booking['booking_status'])); ?>">
                                            <?= $booking['booking_status']; ?>
                                        </span>
                                    </div>
                                    
                                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar-alt text-blue-500 mr-3 w-4"></i>
                                            <div>
                                                <p class="font-semibold">Check-in</p>
                                                <p><?= date('M j, Y', strtotime($booking['booking_check_in'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-calendar-check text-green-500 mr-3 w-4"></i>
                                            <div>
                                                <p class="font-semibold">Check-out</p>
                                                <p><?= date('M j, Y', strtotime($booking['booking_check_out'])); ?></p>
                                            </div>
                                        </div>
                                        <div class="flex items-center text-gray-600">
                                            <i class="fas fa-money-bill-wave text-purple-500 mr-3 w-4"></i>
                                            <div>
                                                <p class="font-semibold">Total Amount</p>
                                                <p class="text-lg font-bold text-green-600">₱<?= number_format($booking['booking_total_price'], 2); ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col gap-3 w-full lg:w-auto">
                                    <?php if (!in_array($booking['booking_status'], ['Canceled', 'Checked-in', 'Checked-out'])): ?>
                                        <button 
                                            onclick="confirmCancellation(<?= $booking['booking_id']; ?>, '<?= $booking['room_number']; ?>', '<?= $booking['room_type']; ?>')"
                                            class="group relative overflow-hidden bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-lg hover:shadow-red-500/25 hover:-translate-y-1"
                                        >
                                            <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                                            <i class="fas fa-times mr-2"></i>
                                            <span>Cancel Booking</span>
                                        </button>
                                    <?php else: ?>
                                        <div class="px-6 py-3 rounded-xl text-center border-2 border-gray-200 bg-gray-50">
                                            <span class="text-gray-500 italic font-medium">
                                                <?php
                                                switch($booking['booking_status']) {
                                                    case 'Canceled':
                                                        echo '<i class="fas fa-ban mr-2"></i>Already Cancelled';
                                                        break;
                                                    case 'Checked-in':
                                                        echo '<i class="fas fa-sign-in-alt mr-2"></i>Already Checked-in';
                                                        break;
                                                    case 'Checked-out':
                                                        echo '<i class="fas fa-sign-out-alt mr-2"></i>Already Checked-out';
                                                        break;
                                                    default:
                                                        echo 'Cancellation unavailable';
                                                }
                                                ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-12">
                    <div class="w-24 h-24 mx-auto bg-gray-100 rounded-full flex items-center justify-center mb-6">
                        <i class="fas fa-calendar-times text-4xl text-gray-400"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-700 mb-2">No Bookings Found</h3>
                    <p class="text-gray-500 mb-6">You haven't made any reservations yet.</p>
                    <a href="../room/front/rooms.php" class="group relative overflow-hidden bg-gradient-to-r from-green-600 to-green-700 text-white px-8 py-4 rounded-xl font-semibold inline-flex items-center transition-all duration-300 hover:from-green-700 hover:to-green-800 hover:shadow-lg hover:shadow-green-500/25 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-gradient-to-r from-green-400 to-green-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        <i class="fas fa-search mr-3 text-lg"></i>
                        <span class="text-lg">Browse Rooms</span>
                        <i class="fas fa-arrow-right ml-3 group-hover:translate-x-1 transition-transform duration-300"></i>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cancellation Confirmation Modal -->
    <div id="cancellationModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
            <div class="text-center">
                <div class="w-16 h-16 mx-auto bg-red-100 rounded-full flex items-center justify-center mb-4">
                    <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">Confirm Cancellation</h3>
                <p class="text-gray-600 mb-6" id="modalMessage"></p>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                    <h4 class="font-semibold text-red-700 mb-2">⚠️ Important Reminders:</h4>
                    <ul class="text-sm text-red-600 space-y-1">
                        <li>• No refunds will be provided</li>
                        <li>• Cancellation fees may apply</li>
                        <li>• This action cannot be undone</li>
                    </ul>
                </div>
                
                <div class="flex gap-4">
                    <button onclick="closeModal()" class="flex-1 group relative overflow-hidden bg-gradient-to-r from-gray-600 to-gray-700 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:from-gray-700 hover:to-gray-800 hover:shadow-lg hover:shadow-gray-500/25 hover:-translate-y-1">
                        <div class="absolute inset-0 bg-gradient-to-r from-gray-400 to-gray-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                        <i class="fas fa-times mr-2"></i>
                        <span>Cancel</span>
                    </button>
                    <form id="cancellationForm" method="POST" class="flex-1">
                        <input type="hidden" name="booking_id" id="modalBookingId">
                        <button type="submit" class="w-full group relative overflow-hidden bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-3 rounded-xl font-semibold transition-all duration-300 hover:from-red-600 hover:to-red-700 hover:shadow-lg hover:shadow-red-500/25 hover:-translate-y-1">
                            <div class="absolute inset-0 bg-gradient-to-r from-red-400 to-red-500 opacity-0 group-hover:opacity-20 transition-opacity duration-300"></div>
                            <i class="fas fa-check mr-2"></i>
                            <span>Confirm</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php include('../elements/footer.php'); ?>

    <script>
        function confirmCancellation(bookingId, roomNumber, roomType) {
            const modal = document.getElementById('cancellationModal');
            const modalMessage = document.getElementById('modalMessage');
            const modalBookingId = document.getElementById('modalBookingId');
            
            modalMessage.innerHTML = `Are you sure you want to cancel your booking for <strong>Room ${roomNumber} - ${roomType}</strong>?`;
            modalBookingId.value = bookingId;
            
            modal.classList.remove('hidden');
        }
        
        function closeModal() {
            const modal = document.getElementById('cancellationModal');
            modal.classList.add('hidden');
        }
        
        // Close modal when clicking outside
        document.getElementById('cancellationModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });
    </script>
</body>
</html> 