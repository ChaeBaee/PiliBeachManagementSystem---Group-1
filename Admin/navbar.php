<nav class="fixed top-0 z-40 flex items-center justify-between h-16 bg-white border-b border-gray-200 shadow-sm px-4
           md:ml-64 md:w-[calc(100%-16rem)] w-full">
    <button id="menu-toggle" class="text-gray-600 md:hidden">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
        </svg>
    </button>

    <div class="flex items-center ml-auto space-x-4">
        <div class="hidden sm:block w-40 md:w-56">
            <input type="text" placeholder="Search..." class="w-full px-3 py-2 border rounded-lg shadow-sm" />
        </div>
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
                <a href="#" class="block px-4 py-2 text-sm hover:bg-gray-100">Logout</a>
            </div>
        </div>
    </div>
</nav>

<!-- JS for navbar only -->
<script>
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
    });
</script>