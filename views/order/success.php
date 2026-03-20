<?php
/**
 * MirukaStore - Order Success Page
 */

$pageTitle = 'Pesanan Berhasil';
$activeMenu = '';
$useMidtrans = false;

include __DIR__ . '/../layouts/header.php';
?>

<section class="py-16 md:py-24">
    <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-dark-light border border-primary/20 rounded-2xl p-8 text-center">
            <!-- Success Icon -->
            <div class="w-24 h-24 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-5xl text-green-500"></i>
            </div>
            
            <h1 class="text-2xl md:text-3xl font-bold text-white mb-4">Pesanan Berhasil!</h1>
            <p class="text-gray-400 mb-6">Terima kasih telah melakukan pembelian. Pesanan Anda sedang diproses.</p>
            
            <!-- Order Details -->
            <div class="bg-dark border border-primary/20 rounded-xl p-6 mb-6 text-left">
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">Order ID</span>
                    <span class="text-white font-mono font-semibold"><?= $order['order_id'] ?></span>
                </div>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">Game</span>
                    <span class="text-white"><?= htmlspecialchars($order['game_name']) ?></span>
                </div>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">Produk</span>
                    <span class="text-white"><?= htmlspecialchars($order['product_name']) ?></span>
                </div>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">User ID</span>
                    <span class="text-white font-mono"><?= htmlspecialchars($order['user_game_id']) ?></span>
                </div>
                <?php if ($order['server_id']): ?>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">Server</span>
                    <span class="text-white font-mono"><?= htmlspecialchars($order['server_id']) ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center mb-4 pb-4 border-b border-gray-700">
                    <span class="text-gray-400">Status</span>
                    <span class="px-3 py-1 rounded-full text-sm font-medium <?= $order['status'] === 'settlement' || $order['status'] === 'success' ? 'bg-green-500/20 text-green-400' : 'bg-yellow-500/20 text-yellow-400' ?>">
                        <?= ucfirst($order['status']) ?>
                    </span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-400">Total</span>
                    <span class="text-primary-light text-xl font-bold">Rp <?= number_format($order['price'], 0, ',', '.') ?></span>
                </div>
            </div>
            
            <!-- Status Info -->
            <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <i class="fas fa-info-circle text-primary-light mt-0.5"></i>
                    <div class="text-left text-sm text-gray-300">
                        <p class="font-medium text-white mb-1">Status Pemrosesan</p>
                        <p id="api-status-text">
                            <?php if ($order['api_status'] === 'success'): ?>
                                Pesanan telah berhasil diproses. Silakan cek game Anda.
                            <?php elseif ($order['api_status'] === 'failed'): ?>
                                Pemrosesan mengalami kendala. Silakan hubungi support.
                            <?php else: ?>
                                Pesanan sedang diproses ke server game. Mohon tunggu...
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/order/invoice?order_id=<?= $order['order_id'] ?>" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center">
                    <i class="fas fa-file-invoice mr-2"></i>
                    Lihat Invoice
                </a>
                <a href="/" class="bg-dark hover:bg-dark-light border border-primary/30 text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center">
                    <i class="fas fa-home mr-2"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Auto refresh untuk cek status -->
<?php if ($order['api_status'] === 'pending' || $order['api_status'] === 'processing'): ?>
<script>
setInterval(() => {
    fetch('/api/check-status.php?order_id=<?= $order['order_id'] ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.api_status !== 'pending' && data.api_status !== 'processing') {
                location.reload();
            }
        });
}, 10000); // Cek setiap 10 detik
</script>
<?php endif; ?>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
