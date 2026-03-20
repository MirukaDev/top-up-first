<?php
/**
 * MirukaStore - Transaction History Page
 */

$pageTitle = 'Riwayat Transaksi';
$activeMenu = '';
$useMidtrans = false;

include __DIR__ . '/../layouts/header.php';

// Status badge colors
$statusColors = [
    'pending' => 'bg-yellow-500/20 text-yellow-400',
    'processing' => 'bg-blue-500/20 text-blue-400',
    'settlement' => 'bg-green-500/20 text-green-400',
    'success' => 'bg-green-500/20 text-green-400',
    'failed' => 'bg-red-500/20 text-red-400',
    'expired' => 'bg-gray-500/20 text-gray-400',
    'cancelled' => 'bg-gray-500/20 text-gray-400'
];

$apiStatusColors = [
    'pending' => 'bg-yellow-500/20 text-yellow-400',
    'processing' => 'bg-blue-500/20 text-blue-400',
    'success' => 'bg-green-500/20 text-green-400',
    'failed' => 'bg-red-500/20 text-red-400'
];
?>

<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Riwayat Transaksi</h1>
            <p class="text-gray-400">Lihat semua transaksi yang pernah Anda lakukan</p>
        </div>
        
        <!-- Transactions List -->
        <div class="bg-dark-light border border-primary/20 rounded-2xl overflow-hidden">
            <?php if (empty($transactions)): ?>
            <div class="p-12 text-center">
                <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Belum Ada Transaksi</h3>
                <p class="text-gray-400 mb-4">Anda belum melakukan transaksi apapun</p>
                <a href="/" class="inline-flex items-center bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-xl transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>
                    Mulai Belanja
                </a>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-dark border-b border-primary/20">
                        <tr>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Order ID</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Game</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Produk</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">User ID</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Harga</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Status</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Tanggal</th>
                            <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/10">
                        <?php foreach ($transactions as $transaction): ?>
                        <tr class="hover:bg-primary/5 transition-colors">
                            <td class="py-4 px-6">
                                <span class="text-white font-mono text-sm"><?= $transaction['order_id'] ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center space-x-3">
                                    <?php if ($transaction['game_image']): ?>
                                        <img src="/assets/img/games/<?= $transaction['game_image'] ?>" alt="" class="w-8 h-8 rounded-lg object-cover">
                                    <?php else: ?>
                                        <div class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-gamepad text-primary-light text-xs"></i>
                                        </div>
                                    <?php endif; ?>
                                    <span class="text-white text-sm"><?= htmlspecialchars($transaction['game_name']) ?></span>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-white text-sm"><?= htmlspecialchars($transaction['product_name']) ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-white font-mono text-sm"><?= htmlspecialchars($transaction['user_game_id']) ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-primary-light font-semibold">Rp <?= number_format($transaction['price'], 0, ',', '.') ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex flex-col space-y-1">
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $statusColors[$transaction['status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                                        <?= ucfirst($transaction['status']) ?>
                                    </span>
                                    <?php if ($transaction['status'] === 'settlement' || $transaction['status'] === 'success'): ?>
                                    <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $apiStatusColors[$transaction['api_status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                                        API: <?= ucfirst($transaction['api_status']) ?>
                                    </span>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-gray-400 text-sm"><?= date('d M Y H:i', strtotime($transaction['created_at'])) ?></span>
                            </td>
                            <td class="py-4 px-6">
                                <a href="/order/invoice?order_id=<?= $transaction['order_id'] ?>" class="text-primary-light hover:text-white text-sm transition-colors">
                                    <i class="fas fa-eye mr-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
