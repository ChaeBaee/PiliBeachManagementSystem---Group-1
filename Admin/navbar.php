<nav class="fixed top-0 z-40 flex items-center justify-between h-16 bg-white border-b border-gray-200 shadow-sm px-4
           md:ml-64 md:w-[calc(100%-16rem)] w-full">
    <!-- Menu toggle for small screens -->
    <button id="menu-toggle" class="text-gray-600 md:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <!-- Title and other info -->
    <div class="flex items-center ml-auto space-x-4">
        <!-- Page title -->
        <div class="hidden sm:block text-gray-700 text-lg font-semibold">
            PILI
        </div>

        <!-- Date and time -->
        <div id="datetime" class="hidden sm:block text-gray-600 text-sm font-medium"></div>

        <!-- Notification bell -->
        <button class="relative text-gray-600 hover:text-gray-800 focus:outline-none">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-500 rounded-full"></span>
        </button>

        <!-- Account dropdown -->
        <div class="relative">
            <button id="account-btn" class="text-gray-600 hover:text-gray-800 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M5.121 17.804A9 9 0 1119.88 6.196a9 9 0 01-14.758 11.608z" />
                </svg>
            </button>
            <div id="account-menu" class="hidden absolute right-0 mt-2 w-40 bg-white border rounded-lg shadow-lg z-50">
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Profile</a>
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Settings</a>
                <a href="/Proj/PiliBeachManagementSystem---Group-1/Admin/actions/logout.php"
                   class="block px-4 py-2 text-sm hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>
</nav>

<script>
    // Toggle account dropdown menu
    document.addEventListener('DOMContentLoaded', () => {
        const accountBtn = document.getElementById('account-btn');
        const accountMenu = document.getElementById('account-menu');

        if (accountBtn) {
            accountBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                accountMenu.classList.toggle('hidden');
            });
        }

        document.addEventListener('click', (e) => {
            if (!accountMenu.contains(e.target) && !accountBtn.contains(e.target)) {
                accountMenu.classList.add('hidden');
            }
        });

        // Date and time updater
        function updateDateTime() {
            const now = new Date();
            const options = {
                weekday: 'short',
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            const datetimeEl = document.getElementById('datetime');
            if (datetimeEl) {
                datetimeEl.textContent = now.toLocaleDateString('en-US', options);
            }
        }

        setInterval(updateDateTime, 1000);
        updateDateTime();
    });
</script>
