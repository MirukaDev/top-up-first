<?php
/**
 * MirukaStore - Admin Transactions List
 */

$pageTitle = 'Manajemen Transaksi';
$activeMenu = 'transactions';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Manajemen Transaksi</h1>
    <p class="text-gray-400">Kelola semua transaksi yang masuk</p>
</div>

<!-- Filters -->
<div class="bg-dark-light border border-primary/20 rounded-xl p-4 mb-6">
    <form method="GET" action="/admin/transactions" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-all"
                placeholder="Cari Order ID, User ID, atau Username...">
        </div>
        <div>
            <select name="status" class="bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary transition-all">
                <option value="">Semua Status</option>
                <option value="pending" <?= ($_GET['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                <option value="processing" <?= ($_GET['status'] ?? '') === 'processing' ? 'selected' : '' ?>>Processing</option>
                <option value="settlement" <?= ($_GET['status'] ?? '') === 'settlement' ? 'selected' : '' ?>>Settlement</option>
                <option value="success" <?= ($_GET['status'] ?? '') === 'success' ? 'selected' : '' ?>>Success</option>
                <option value="failed" <?= ($_GET['status'] ?? '') === 'failed' ? 'selected' : '' ?>>Failed</option>
            </select>
        </div>
        <div>
            <input type="date" name="date_from" value="<?= htmlspecialchars($_GET['date_from'] ?? '') ?>"
                class="bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary transition-all">
        </div>
        <div>
            <input type="date" name="date_to" value="<?= htmlspecialchars($_GET['date_to'] ?? '') ?>"
                class="bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary transition-all">
        </div>
        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl transition-colors">
            <i class="fas fa-search mr-2"></i>
            Filter
        </button>
    </form>
</div>

<!-- Transactions Table -->
<div class="bg-dark-light border border-primary/20 rounded-xl overflow-hidden">
    <?php if (empty($transactions)): ?>
    <div class="p-12 text-center">
        <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-shopping-cart text-3xl text-primary-light"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-2">Tidak Ada Transaksi</h3>
        <p class="text-gray-400">Belum ada transaksi yang sesuai dengan filter</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark">
                <tr>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Order ID</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">User</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Game</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Produk</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">User ID</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Harga</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Status</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">API</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Tanggal</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                <?php foreach ($transactions as $t): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="py-3 px-4">
                        <span class="text-white font-mono text-xs"><?= $t['order_id'] ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-white text-xs"><?= htmlspecialchars($t['username'] ?? 'Guest') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-white text-xs"><?= htmlspecialchars($t['game_name']) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-white text-xs"><?= htmlspecialchars($t['product_name']) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-white font-mono text-xs"><?= htmlspecialchars($t['user_game_id']) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-primary-light font-semibold text-xs">Rp <?= number_format($t['price'], 0, ',', '.') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$t['status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($t['status']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $apiStatusColors[$t['api_status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($t['api_status']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-xs"><?= date('d/m H:i', strtotime($t['created_at'])) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-1">
                            <a href="/admin/transactions/detail?order_id=<?= $t['order_id'] ?>" class="w-7 h-7 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg flex items-center justify-center transition-colors" title="Detail">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            <?php if ($t['status'] === 'settlement' && $t['api_status'] === 'pending'): ?>
                            <button onclick="retrySupplier('<?= $t['order_id'] ?>')" class="w-7 h-7 bg-yellow-500/20 hover:bg-yellow-500/30 text-yellow-400 rounded-lg flex items-center justify-center transition-colors" title="Retry">
                                <i class="fas fa-redo text-xs"></i>
                            </button>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="p-4 border-t border-primary/20 flex justify-between items-center">
        <div class="text-gray-400 text-sm">
            Halaman <?= $page ?> dari <?= $totalPages ?>
        </div>
        <div class="flex space-x-2">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>&<?= http_build_query(array_diff_key($_GET, ['page' => 1])) ?>" class="px-3 py-1 bg-dark border border-primary/30 rounded-lg text-white hover:bg-primary/20 transition-colors">
                <i class="fas fa-chevron-left"></i>
            </a>
            <?php endif; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>&<?= http_build_query(array_diff_key($_GET, ['page' => 1])) ?>" class="px-3 py-1 bg-dark border border-primary/30 rounded-lg text-white hover:bg-primary/20 transition-colors">
                <i class="fas fa-chevron-right"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>

<script>
function retrySupplier(orderId) {
    if (!confirm('Retry order ke supplier?')) return;
    
    fetch('/api/retry-supplier.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'order_id=' + encodeURIComponent(orderId)
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Order retried successfully');
            location.reload();
        } else {
            alert('Failed: ' + data.message);
        }
    });
}
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
