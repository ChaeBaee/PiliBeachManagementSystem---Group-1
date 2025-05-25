<?php
require_once '../PHP/db.php';
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'Admin') {
    header('Location: login.php');
    exit();
}

// Get search parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$date_filter = isset($_GET['date_filter']) ? $_GET['date_filter'] : '';

// Build the query
$query = "SELECT al.*, u.user_name as changed_by_name 
          FROM audit_logs al 
          LEFT JOIN users u ON al.changed_by = u.user_id 
          WHERE 1=1";

$params = [];

if (!empty($search)) {
    $query .= " AND (al.table_name LIKE ? OR al.action LIKE ? OR u.user_name LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
}

if (!empty($date_filter)) {
    $query .= " AND DATE(al.changed_at) = ?";
    $params[] = $date_filter;
}

$query .= " ORDER BY al.changed_at DESC";

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->execute($params);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Activity Logs - Admin Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <!-- Include Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Include Navbar -->
    <?php include 'navbar.php'; ?>

    <!-- Main Content -->
    <main class="md:ml-64 pt-16">
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
                <p class="text-gray-600">View and monitor all system activities</p>
            </div>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>"
                            placeholder="Search by table name, action, or user..."
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="w-full md:w-48">
                        <input type="date" name="date_filter" value="<?php echo htmlspecialchars($date_filter); ?>"
                            class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <button type="submit"
                        class="bg-blue-500 text-white px-6 py-2 rounded-lg hover:bg-blue-600 transition-colors">
                        Search
                    </button>
                </form>
            </div>

            <!-- Logs Table -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date & Time</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Table</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Record ID</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Changed By</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Changes</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($logs as $log): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('M d, Y H:i:s', strtotime($log['changed_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['table_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?php
                                            switch ($log['action']) {
                                                case 'INSERT':
                                                    echo 'bg-green-100 text-green-800';
                                                    break;
                                                case 'UPDATE':
                                                    echo 'bg-yellow-100 text-yellow-800';
                                                    break;
                                                case 'DELETE':
                                                    echo 'bg-red-100 text-red-800';
                                                    break;
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($log['action']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['record_id']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo htmlspecialchars($log['changed_by_name']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        <?php if ($log['action'] === 'UPDATE'): ?>
                                            <div class="space-y-1">
                                                <?php
                                                $old_data = json_decode($log['old_data'], true);
                                                $new_data = json_decode($log['new_data'], true);

                                                if ($old_data && $new_data && is_array($old_data) && is_array($new_data)) {
                                                    // Handle JSON format
                                                    foreach ($old_data as $key => $value) {
                                                        if (isset($new_data[$key]) && $old_data[$key] !== $new_data[$key]) {
                                                            echo "<div class='text-xs'>";
                                                            echo "<span class='font-medium'>" . htmlspecialchars($key) . ":</span> ";
                                                            echo "<span class='text-red-600'>" . htmlspecialchars($old_data[$key]) . "</span> → ";
                                                            echo "<span class='text-green-600'>" . htmlspecialchars($new_data[$key]) . "</span>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                } else {
                                                    // Handle string format (for bookings)
                                                    $old_parts = explode(', ', $log['old_data']);
                                                    $new_parts = explode(', ', $log['new_data']);

                                                    $old_values = [];
                                                    $new_values = [];

                                                    foreach ($old_parts as $part) {
                                                        if (strpos($part, ':') !== false) {
                                                            list($key, $value) = explode(': ', $part);
                                                            $old_values[$key] = $value;
                                                        }
                                                    }

                                                    foreach ($new_parts as $part) {
                                                        if (strpos($part, ':') !== false) {
                                                            list($key, $value) = explode(': ', $part);
                                                            $new_values[$key] = $value;
                                                        }
                                                    }

                                                    foreach ($old_values as $key => $value) {
                                                        if (isset($new_values[$key]) && $old_values[$key] !== $new_values[$key]) {
                                                            echo "<div class='text-xs'>";
                                                            echo "<span class='font-medium'>" . htmlspecialchars($key) . ":</span> ";
                                                            echo "<span class='text-red-600'>" . htmlspecialchars($old_values[$key]) . "</span> → ";
                                                            echo "<span class='text-green-600'>" . htmlspecialchars($new_values[$key]) . "</span>";
                                                            echo "</div>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php elseif ($log['action'] === 'INSERT'): ?>
                                            <div class="text-green-600 text-xs">
                                                <?php
                                                $new_data = json_decode($log['new_data'], true);
                                                if ($new_data && is_array($new_data)) {
                                                    // Handle JSON format
                                                    foreach ($new_data as $key => $value) {
                                                        echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                    }
                                                } else {
                                                    // Handle string format (for bookings)
                                                    $new_parts = explode(', ', $log['new_data']);
                                                    foreach ($new_parts as $part) {
                                                        if (strpos($part, ':') !== false) {
                                                            list($key, $value) = explode(': ', $part);
                                                            echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php elseif ($log['action'] === 'DELETE'): ?>
                                            <div class="text-red-600 text-xs">
                                                <?php
                                                $old_data = json_decode($log['old_data'], true);
                                                if ($old_data && is_array($old_data)) {
                                                    // Handle JSON format
                                                    foreach ($old_data as $key => $value) {
                                                        echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                    }
                                                } else {
                                                    // Handle string format (for bookings)
                                                    $old_parts = explode(', ', $log['old_data']);
                                                    foreach ($old_parts as $part) {
                                                        if (strpos($part, ':') !== false) {
                                                            list($key, $value) = explode(': ', $part);
                                                            echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                        }
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>
</body>

</html>