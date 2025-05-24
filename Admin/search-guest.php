<?php
include_once '../PHP/db.php';

if (!isset($_GET['keyword']) || empty(trim($_GET['keyword']))) {
    exit;
}

$keyword = trim($_GET['keyword']);
$searchTerm = "%$keyword%";

$stmt = $conn->prepare("
  SELECT b.booking_id, b.user_id, b.booking_check_in, b.booking_check_out, b.booking_status, u.user_name
  FROM bookings b
  LEFT JOIN users u ON b.user_id = u.user_id
  WHERE b.booking_id LIKE :term
     OR u.user_name LIKE :term
     OR b.booking_check_in LIKE :term
     OR b.booking_check_out LIKE :term
     OR b.booking_status LIKE :term
  ORDER BY b.booking_id DESC
");

$stmt->execute([':term' => $searchTerm]);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($results) {
    foreach ($results as $res) {
        $guestName = htmlspecialchars($res['user_name'] ?? $res['user_id']);
        $status = strtolower($res['booking_status']);
        $statusColors = [
            'confirmed' => 'bg-blue-100 text-blue-800',
            'checked-in' => 'bg-green-100 text-green-800',
            'pending' => 'bg-yellow-100 text-yellow-800',
            'cancelled' => 'bg-red-100 text-red-700',
        ];
        $statusClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-800';

        echo "<tr class='hover:bg-gray-50'>";
        echo "<td class='px-6 py-4 font-medium'>#" . htmlspecialchars($res['booking_id']) . "</td>";
        echo "<td class='px-6 py-4'>{$guestName}</td>";
        echo "<td class='px-6 py-4'>" . htmlspecialchars($res['booking_check_in']) . "</td>";
        echo "<td class='px-6 py-4'>" . htmlspecialchars($res['booking_check_out']) . "</td>";
        echo "<td class='px-6 py-4'><span class='$statusClass px-3 py-1 rounded-full text-xs font-medium'>" . ucfirst($status) . "</span></td>";
        echo "<td class='px-6 py-4 space-x-2'>";
        echo "<a href='edit-booking.php?id=" . htmlspecialchars($res['booking_id']) . "' class='text-primary hover:underline'>Edit</a>";
        echo "<a href='delete-booking.php?id=" . htmlspecialchars($res['booking_id']) . "' class='text-danger hover:underline'>Delete</a>";
        echo "</td></tr>";
    }
} else {
    echo "<tr><td colspan='6' class='text-center py-4 text-gray-500'>No results found</td></tr>";
}
?>
