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
            <?php echo $_SESSION['user_name']; ?>
        </div>

        <!-- Date and time -->
        <div id="datetime" class="hidden sm:block text-gray-600 text-sm font-medium"></div>
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