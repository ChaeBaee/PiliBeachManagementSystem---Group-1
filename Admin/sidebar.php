<?php
$currentPage = basename($_SERVER['PHP_SELF']); // gets the current file name, like 'bookings.php'
?>

<aside id="sidebar"
    class="fixed top-0 left-0 w-64 h-full bg-white border-r border-gray-200 shadow-lg z-50 transform transition-transform duration-300 -translate-x-full md:translate-x-0">
    <div class="p-4 border-b flex justify-between items-center">
        <h2 class="text-2xl font-bold text-blue-600">Pili Admin</h2>
        <button id="sidebar-close" class="md:hidden text-gray-500 hover:text-gray-700 text-xl">✕</button>
    </div>
    <nav class="mt-6 px-4 space-y-2">
        <a href="index.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'index.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Dashboard
        </a>

        <a href="bookings.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'bookings.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Bookings
        </a>

        <a href="rooms.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'rooms.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Rooms
        </a>

        <a href="payments.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'payments.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Payments
        </a>

        <a href="invoice.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'invoice.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Invoices
        </a>

        <a href="users.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'users.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Users
        </a>

        <a href="activity-logs.php" class="block px-3 py-2 rounded-lg hover:bg-blue-50 font-medium
        <?= $currentPage == 'activity-logs.php' ? 'bg-blue-100 text-blue-700 font-semibold' : 'text-gray-700' ?>">
            Activity Logs
        </a>
    </nav>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<!-- JS for sidebar only -->
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        const menuToggle = document.getElementById('menu-toggle');
        const sidebarClose = document.getElementById('sidebar-close');

        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            });
        }

        if (sidebarClose) {
            sidebarClose.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }

        if (overlay) {
            overlay.addEventListener('click', () => {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            });
        }
    });
</script>