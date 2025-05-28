<?php
$currentPage = basename($_SERVER['PHP_SELF']);
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

        <div class="border-t border-gray-200 my-2"></div>

        <a href="#" onclick="confirmLogout()"
            class="block px-3 py-2 rounded-lg hover:bg-red-50 font-medium text-red-600 hover:text-red-700">
            Logout
        </a>
    </nav>
</aside>

<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 hidden md:hidden"></div>

<div id="logoutModal" class="hidden fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center z-50">
    <div class="bg-white p-8 rounded-xl shadow-lg w-full max-w-md transform transition-all">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-2xl font-semibold text-gray-800 tracking-tight">Confirm Logout</h3>
            <button onclick="closeLogoutModal()"
                class="text-gray-400 hover:text-gray-600 transition-colors duration-200">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>
        <p class="text-gray-600 text-base leading-relaxed mb-8">Are you sure you want to logout?</p>
        <div class="flex justify-end gap-3">
            <button onclick="closeLogoutModal()"
                class="px-6 py-2.5 bg-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-400 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2">
                Cancel
            </button>
            <button onclick="proceedLogout()"
                class="px-6 py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Logout
            </button>
        </div>
    </div>
</div>

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

    function confirmLogout() {
        document.getElementById('logoutModal').classList.remove('hidden');
    }

    function closeLogoutModal() {
        document.getElementById('logoutModal').classList.add('hidden');
    }

    function proceedLogout() {
        window.location.href = 'actions/logout.php';
    }
</script>