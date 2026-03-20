<?php
/**
 * MirukaStore - Cek Transaksi Page
 */

$pageTitle = 'Cek Transaksi';
$activeMenu = 'cek-transaksi';
$useMidtrans = false;

include __DIR__ . '/../layouts/header.php';

// Status colors
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

<section class="py-16 md:py-24">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Cek Transaksi</h1>
            <p class="text-gray-400">Masukkan Order ID untuk melihat status transaksi</p>
        </div>
        
        <!-- Search Form -->
        <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 mb-6">
            <form method="POST" action="/cek-transaksi" class="space-y-4">
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Order ID</label>
                    <div class="relative">
                        <i class="fas fa-receipt absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="text" name="order_id" required 
                            class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all uppercase"
                            placeholder="Contoh: MRK20240101123456"
                            value="<?= htmlspecialchars($_POST['order_id'] ?? '') ?>">
                    </div>
                </div>
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-xl transition-colors">
                    <i class="fas fa-search mr-2"></i>
                    Cek Status
                </button>
            </form>
        </div>
        
        <!-- Error Message -->
        <?php if ($error): ?>
        <div class="bg-red-500/20 border border-red-500/30 rounded-xl px-4 py-3 mb-6">
            <div class="flex items-center text-red-400 text-sm">
                <i class="fas fa-exclamation-circle mr-2"></i>
                <?= $error ?>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Result -->
        <?php if ($order): ?>
        <div class="bg-dark-light border border-primary/20 rounded-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary/20 to-primary-light/20 p-4 border-b border-primary/20">
                <div class="flex justify-between items-center">
                    <span class="text-gray-400 text-sm">Order ID</span>
                    <span class="text-white font-mono font-semibold"><?= $order['order_id'] ?></span>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Status -->
                <div class="text-center mb-6">
                    <div class="inline-flex flex-col items-center">
                        <?php if ($order['status'] === 'settlement' || $order['status'] === 'success'): ?>
                            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-check-circle text-3xl text-green-500"></i>
                            </div>
                        <?php elseif ($order['status'] === 'pending'): ?>
                            <div class="w-16 h-16 bg-yellow-500/20 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-clock text-3xl text-yellow-500"></i>
                            </div>
                        <?php elseif ($order['status'] === 'failed' || $order['status'] === 'expired' || $order['status'] === 'cancelled'): ?>
                            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-times-circle text-3xl text-red-500"></i>
                            </div>
                        <?php else: ?>
                            <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mb-3">
                                <i class="fas fa-spinner fa-spin text-3xl text-blue-500"></i>
                            </div>
                        <?php endif; ?>
                        
                        <span class="inline-flex px-4 py-1.5 rounded-full text-sm font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
                
                <!-- Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">Game</span>
                        <span class="text-white"><?= htmlspecialchars($order['game_name']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">Produk</span>
                        <span class="text-white"><?= htmlspecialchars($order['product_name']) ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">User ID</span>
                        <span class="text-white font-mono"><?= htmlspecialchars($order['user_game_id']) ?></span>
                    </div>
                    <?php if ($order['server_id']): ?>
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">Server</span>
                        <span class="text-white font-mono"><?= htmlspecialchars($order['server_id']) ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">Total</span>
                        <span class="text-primary-light font-semibold">Rp <?= number_format($order['price'], 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between py-2 border-b border-gray-700">
                        <span class="text-gray-400">Tanggal</span>
                        <span class="text-white"><?= date('d M Y H:i', strtotime($order['created_at'])) ?></span>
                    </div>
                </div>
                
                <!-- API Status -->
                <?php if ($order['status'] === 'settlement' || $order['status'] === 'success'): ?>
                <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 mb-6">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-400 text-sm">Status Pemrosesan</span>
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $apiStatusColors[$order['api_status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($order['api_status']) ?>
                        </span>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Actions -->
                <div class="flex space-x-3">
                    <a href="/order/invoice?order_id=<?= $order['order_id'] ?>" class="flex-1 bg-primary hover:bg-primary-dark text-white text-center py-2.5 rounded-xl transition-colors">
                        <i class="fas fa-file-invoice mr-2"></i>
                        Lihat Invoice
                    </a>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
