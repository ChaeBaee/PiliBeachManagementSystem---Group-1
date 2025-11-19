<?php
include_once '../back/activity-logs-back.php'
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
    <?php include '../../Elements/sidebar.php'; ?>

    <?php include '../../Elements/navbar.php'; ?>

    <main class="md:ml-64 pt-16">
        <div class="p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Activity Logs</h1>
                <p class="text-gray-600">View and monitor all system activities</p>
            </div>

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

                                                    foreach ($new_data as $key => $value) {
                                                        echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                    }
                                                } else {

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

                                                    foreach ($old_data as $key => $value) {
                                                        echo "<div><span class='font-medium'>" . htmlspecialchars($key) . ":</span> " . htmlspecialchars($value) . "</div>";
                                                    }
                                                } else {

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
                
                <!-- Pagination Controls -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                        <div class="flex-1 flex justify-between sm:hidden">
                            <?php if ($current_page > 1): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Previous
                                </a>
                            <?php endif; ?>
                            <?php if ($current_page < $total_pages): ?>
                                <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" 
                                   class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                    Next
                                </a>
                            <?php endif; ?>
                        </div>
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Showing 
                                    <span class="font-medium"><?php echo ($offset + 1); ?></span>
                                    to 
                                    <span class="font-medium"><?php echo min($offset + $records_per_page, $total_records); ?></span>
                                    of 
                                    <span class="font-medium"><?php echo $total_records; ?></span>
                                    results
                                </p>
                            </div>
                            <div>
                                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                                    <?php if ($current_page > 1): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>" 
                                           class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Previous</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php
                                    $start_page = max(1, $current_page - 2);
                                    $end_page = min($total_pages, $current_page + 2);
                                    
                                    if ($start_page > 1): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => 1])); ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            1
                                        </a>
                                        <?php if ($start_page > 2): ?>
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $i])); ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium 
                                                  <?php echo $i === $current_page 
                                                        ? 'z-10 bg-blue-50 border-blue-500 text-blue-600' 
                                                        : 'border-gray-300 bg-white text-gray-500 hover:bg-gray-50'; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                    
                                    <?php if ($end_page < $total_pages): ?>
                                        <?php if ($end_page < $total_pages - 1): ?>
                                            <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">
                                                ...
                                            </span>
                                        <?php endif; ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $total_pages])); ?>" 
                                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50">
                                            <?php echo $total_pages; ?>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if ($current_page < $total_pages): ?>
                                        <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>" 
                                           class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                            <span class="sr-only">Next</span>
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                            </svg>
                                        </a>
                                    <?php endif; ?>
                                </nav>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>

</html>