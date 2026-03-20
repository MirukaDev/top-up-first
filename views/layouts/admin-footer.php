<?php
/**
 * MirukaStore - Admin Footer Layout
 */
?>
            </main>
        </div>
    </div>
    
    <!-- Mobile Sidebar -->
    <div id="mobile-sidebar" class="fixed inset-0 z-50 hidden">
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" onclick="toggleSidebar()"></div>
        <aside class="absolute left-0 top-0 bottom-0 w-64 bg-dark-light border-r border-primary/20 flex flex-col">
            <div class="p-6 border-b border-primary/20 flex justify-between items-center">
                <span class="font-display font-bold text-lg text-white">Admin Panel</span>
                <button onclick="toggleSidebar()" class="text-gray-400 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="/admin" class="sidebar-link">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                <a href="/admin/games" class="sidebar-link">
                    <i class="fas fa-gamepad w-5"></i>
                    <span class="ml-3">Game</span>
                </a>
                <a href="/admin/products" class="sidebar-link">
                    <i class="fas fa-box w-5"></i>
                    <span class="ml-3">Produk</span>
                </a>
                <a href="/admin/transactions" class="sidebar-link">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Transaksi</span>
                </a>
                <a href="/admin/users" class="sidebar-link">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Users</span>
                </a>
            </nav>
        </aside>
    </div>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('mobile-sidebar');
            sidebar.classList.toggle('hidden');
        }
        
        document.getElementById('sidebar-toggle')?.addEventListener('click', toggleSidebar);
    </script>
</body>
</html>
