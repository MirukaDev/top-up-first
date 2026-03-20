<?php
/**
 * MirukaStore - Admin Dashboard
 */

$pageTitle = 'Dashboard Admin';
$activeMenu = 'dashboard';
$useMidtrans = false;

include __DIR__ . '/../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Dashboard</h1>
    <p class="text-gray-400">Selamat datang kembali, <?= htmlspecialchars($_SESSION['username']) ?></p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6 mb-8">
    <!-- Today's Revenue -->
    <div class="bg-dark-light border border-primary/20 rounded-xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-primary/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-money-bill-wave text-primary-light text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-medium">+12%</span>
        </div>
        <div class="text-gray-400 text-sm mb-1">Pendapatan Hari Ini</div>
        <div class="text-white text-xl md:text-2xl font-bold">Rp <?= number_format($todayStats['success_revenue'], 0, ',', '.') ?></div>
    </div>
    
    <!-- Today's Transactions -->
    <div class="bg-dark-light border border-primary/20 rounded-xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-cart text-blue-400 text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-medium">+5%</span>
        </div>
        <div class="text-gray-400 text-sm mb-1">Transaksi Hari Ini</div>
        <div class="text-white text-xl md:text-2xl font-bold"><?= $todayStats['success_transactions'] ?></div>
    </div>
    
    <!-- Total Users -->
    <div class="bg-dark-light border border-primary/20 rounded-xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-green-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-green-400 text-xl"></i>
            </div>
            <span class="text-green-400 text-sm font-medium">+8%</span>
        </div>
        <div class="text-gray-400 text-sm mb-1">Total Users</div>
        <div class="text-white text-xl md:text-2xl font-bold"><?= number_format($totalUsers, 0, ',', '.') ?></div>
    </div>
    
    <!-- Total Resellers -->
    <div class="bg-dark-light border border-primary/20 rounded-xl p-4 md:p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-yellow-500/20 rounded-xl flex items-center justify-center">
                <i class="fas fa-user-tie text-yellow-400 text-xl"></i>
            </div>
            <span class="text-gray-400 text-sm font-medium">0%</span>
        </div>
        <div class="text-gray-400 text-sm mb-1">Total Reseller</div>
        <div class="text-white text-xl md:text-2xl font-bold"><?= number_format($totalResellers, 0, ',', '.') ?></div>
    </div>
</div>

<!-- Charts & Recent Transactions -->
<div class="grid lg:grid-cols-3 gap-6 mb-8">
    <!-- Weekly Stats -->
    <div class="lg:col-span-2 bg-dark-light border border-primary/20 rounded-xl p-6">
        <h3 class="text-white font-semibold mb-4">Statistik Mingguan</h3>
        <div class="grid grid-cols-3 gap-4">
            <div class="text-center p-4 bg-dark rounded-xl">
                <div class="text-gray-400 text-sm mb-2">7 Hari Terakhir</div>
                <div class="text-white text-xl font-bold">Rp <?= number_format($weekStats['success_revenue'], 0, ',', '.') ?></div>
                <div class="text-green-400 text-sm"><?= $weekStats['success_transactions'] ?> transaksi</div>
            </div>
            <div class="text-center p-4 bg-dark rounded-xl">
                <div class="text-gray-400 text-sm mb-2">30 Hari Terakhir</div>
                <div class="text-white text-xl font-bold">Rp <?= number_format($monthStats['success_revenue'], 0, ',', '.') ?></div>
                <div class="text-green-400 text-sm"><?= $monthStats['success_transactions'] ?> transaksi</div>
            </div>
            <div class="text-center p-4 bg-dark rounded-xl">
                <div class="text-gray-400 text-sm mb-2">Pending Hari Ini</div>
                <div class="text-white text-xl font-bold"><?= $todayStats['pending_transactions'] ?></div>
                <div class="text-yellow-400 text-sm">Menunggu</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="bg-dark-light border border-primary/20 rounded-xl p-6">
        <h3 class="text-white font-semibold mb-4">Aksi Cepat</h3>
        <div class="space-y-3">
            <a href="/admin/games/add" class="flex items-center p-3 bg-dark hover:bg-primary/20 rounded-xl transition-colors">
                <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-plus text-primary-light"></i>
                </div>
                <div>
                    <div class="text-white text-sm font-medium">Tambah Game</div>
                    <div class="text-gray-400 text-xs">Tambah game baru</div>
                </div>
            </a>
            <a href="/admin/products/add" class="flex items-center p-3 bg-dark hover:bg-primary/20 rounded-xl transition-colors">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-box text-blue-400"></i>
                </div>
                <div>
                    <div class="text-white text-sm font-medium">Tambah Produk</div>
                    <div class="text-gray-400 text-xs">Tambah produk baru</div>
                </div>
            </a>
            <a href="/admin/transactions" class="flex items-center p-3 bg-dark hover:bg-primary/20 rounded-xl transition-colors">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-list text-green-400"></i>
                </div>
                <div>
                    <div class="text-white text-sm font-medium">Lihat Transaksi</div>
                    <div class="text-gray-400 text-xs">Kelola transaksi</div>
                </div>
            </a>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="bg-dark-light border border-primary/20 rounded-xl overflow-hidden">
    <div class="p-6 border-b border-primary/20 flex justify-between items-center">
        <h3 class="text-white font-semibold">Transaksi Terbaru</h3>
        <a href="/admin/transactions" class="text-primary-light hover:text-white text-sm transition-colors">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark">
                <tr>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Order ID</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">User</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Game</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Produk</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Harga</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Status</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-3 px-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                <?php foreach ($recentTransactions as $t): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="py-3 px-6">
                        <span class="text-white font-mono text-sm"><?= $t['order_id'] ?></span>
                    </td>
                    <td class="py-3 px-6">
                        <span class="text-white text-sm"><?= htmlspecialchars($t['username'] ?? 'Guest') ?></span>
                    </td>
                    <td class="py-3 px-6">
                        <span class="text-white text-sm"><?= htmlspecialchars($t['game_name']) ?></span>
                    </td>
                    <td class="py-3 px-6">
                        <span class="text-white text-sm"><?= htmlspecialchars($t['product_name']) ?></span>
                    </td>
                    <td class="py-3 px-6">
                        <span class="text-primary-light font-semibold">Rp <?= number_format($t['price'], 0, ',', '.') ?></span>
                    </td>
                    <td class="py-3 px-6">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$t['status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($t['status']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-6">
                        <a href="/admin/transactions/detail?order_id=<?= $t['order_id'] ?>" class="text-primary-light hover:text-white text-sm">
                            <i class="fas fa-eye"></i>
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../layouts/admin-footer.php'; ?>
