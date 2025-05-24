<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
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
        <a href="add-booking.php"
           class="bg-primary text-white px-4 py-2 rounded-lg shadow hover:bg-blue-600 transition">
            + New Booking
        </a>
    </div>

    <div class="mb-4">
        <input
                type="text"
                id="searchInput"
                placeholder="Search bookings..."
                class="w-full md:w-1/3 px-4 py-2 border rounded-lg shadow-sm"
                autocomplete="off"
        />
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50 text-sm font-semibold text-gray-600">
            <tr>
                <th class="px-6 py-4 text-left">Booking ID</th>
                <th class="px-6 py-4 text-left">Guest Name</th>
                <th class="px-6 py-4 text-left">Check-in</th>
                <th class="px-6 py-4 text-left">Check-out</th>
                <th class="px-6 py-4 text-left">Status</th>
                <th class="px-6 py-4 text-left">Actions</th>
            </tr>
            </thead>
            <tbody id="search-body" class="divide-y divide-gray-100 text-sm">
            <?php
            include_once '../PHP/db.php';

            $stmt = $conn->query("
            SELECT b.booking_id, b.user_id, b.booking_check_in, b.booking_check_out, b.booking_status, u.user_name
            FROM bookings b
            LEFT JOIN users u ON b.user_id = u.user_id
            ORDER BY b.booking_id ASC
          ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
            ?>
            </tbody>
        </table>
    </div>
</main>

<script>
    const searchInput = document.getElementById('searchInput');
    const searchBody = document.getElementById('search-body');

    searchInput.addEventListener('input', function () {
        const keyword = this.value.trim();

        if (keyword.length === 0) {
            location.reload(); // reload original data
            return;
        }

        fetch(`search-guest.php?keyword=${encodeURIComponent(keyword)}`)
            .then(response => response.text())
            .then(data => {
                searchBody.innerHTML = data;
            })
            .catch(() => {
                searchBody.innerHTML = `<tr><td colspan="6" class="text-center py-4 text-red-500">Error fetching results.</td></tr>`;
            });
    });
</script>
</body>

</html>
