<?php
/**
 * MirukaStore - Invoice Page
 */

$pageTitle = 'Invoice #' . $order['order_id'];
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
?>

<section class="py-8">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Back Button -->
        <a href="/order/history" class="inline-flex items-center text-gray-400 hover:text-white mb-6 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Riwayat
        </a>
        
        <!-- Invoice Card -->
        <div class="bg-dark-light border border-primary/20 rounded-2xl overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-primary/20 to-primary-light/20 p-6 border-b border-primary/20">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h1 class="text-2xl font-bold text-white mb-1">INVOICE</h1>
                        <p class="text-gray-400 text-sm">Order ID: <span class="text-white font-mono"><?= $order['order_id'] ?></span></p>
                    </div>
                    <div class="mt-4 md:mt-0">
                        <span class="inline-flex px-4 py-2 rounded-full text-sm font-medium <?= $statusColors[$order['status']] ?? 'bg-gray-500/20 text-gray-400' ?>">
                            <?= ucfirst($order['status']) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Content -->
            <div class="p-6">
                <!-- Info Grid -->
                <div class="grid md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <h3 class="text-gray-400 text-sm mb-2">Informasi Pelanggan</h3>
                        <div class="bg-dark border border-primary/10 rounded-xl p-4">
                            <p class="text-white font-medium"><?= htmlspecialchars($order['full_name'] ?? $order['username'] ?? 'Guest') ?></p>
                            <p class="text-gray-400 text-sm"><?= htmlspecialchars($order['email'] ?? '-') ?></p>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-gray-400 text-sm mb-2">Informasi Transaksi</h3>
                        <div class="bg-dark border border-primary/10 rounded-xl p-4">
                            <p class="text-white text-sm">
                                <span class="text-gray-400">Tanggal:</span> 
                                <?= date('d M Y H:i', strtotime($order['created_at'])) ?>
                            </p>
                            <p class="text-white text-sm mt-1">
                                <span class="text-gray-400">Metode:</span> 
                                <?= ucfirst($order['payment_method'] ?? '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Order Details -->
                <h3 class="text-gray-400 text-sm mb-3">Detail Pesanan</h3>
                <div class="bg-dark border border-primary/10 rounded-xl overflow-hidden mb-6">
                    <table class="w-full">
                        <thead class="bg-primary/10">
                            <tr>
                                <th class="text-left text-gray-400 text-sm py-3 px-4">Item</th>
                                <th class="text-left text-gray-400 text-sm py-3 px-4">User ID</th>
                                <th class="text-right text-gray-400 text-sm py-3 px-4">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-primary/10">
                                <td class="py-4 px-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-gamepad text-primary-light"></i>
                                        </div>
                                        <div>
                                            <p class="text-white font-medium"><?= htmlspecialchars($order['product_name']) ?></p>
                                            <p class="text-gray-400 text-sm"><?= htmlspecialchars($order['game_name']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4">
                                    <span class="text-white font-mono"><?= htmlspecialchars($order['user_game_id']) ?></span>
                                    <?php if ($order['server_id']): ?>
                                        <span class="text-gray-400 text-sm"> (<?= htmlspecialchars($order['server_id']) ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td class="py-4 px-4 text-right">
                                    <span class="text-white">Rp <?= number_format($order['price'], 0, ',', '.') ?></span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot class="bg-dark">
                            <tr>
                                <td colspan="2" class="py-3 px-4 text-right text-gray-400">Subtotal</td>
                                <td class="py-3 px-4 text-right text-white">Rp <?= number_format($order['price'], 0, ',', '.') ?></td>
                            </tr>
                            <?php if ($order['balance_used'] > 0): ?>
                            <tr>
                                <td colspan="2" class="py-3 px-4 text-right text-gray-400">Potongan Saldo</td>
                                <td class="py-3 px-4 text-right text-green-400">-Rp <?= number_format($order['balance_used'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endif; ?>
                            <tr class="border-t border-primary/20">
                                <td colspan="2" class="py-4 px-4 text-right text-white font-semibold">Total</td>
                                <td class="py-4 px-4 text-right text-primary-light text-xl font-bold">
                                    Rp <?= number_format($order['price'] - $order['balance_used'], 0, ',', '.') ?>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- API Status -->
                <?php if ($order['status'] === 'settlement' || $order['status'] === 'success'): ?>
                <div class="bg-primary/10 border border-primary/20 rounded-xl p-4 mb-6">
                    <h3 class="text-white font-medium mb-2">Status Pemrosesan</h3>
                    <div class="flex items-center space-x-3">
                        <?php if ($order['api_status'] === 'success'): ?>
                            <i class="fas fa-check-circle text-green-500 text-xl"></i>
                            <div>
                                <p class="text-white">Pesanan berhasil diproses</p>
                                <p class="text-gray-400 text-sm">Diamond telah dikirim ke akun game Anda</p>
                            </div>
                        <?php elseif ($order['api_status'] === 'failed'): ?>
                            <i class="fas fa-times-circle text-red-500 text-xl"></i>
                            <div>
                                <p class="text-white">Pemrosesan gagal</p>
                                <p class="text-gray-400 text-sm">Silakan hubungi support untuk bantuan</p>
                            </div>
                        <?php else: ?>
                            <i class="fas fa-spinner fa-spin text-primary-light text-xl"></i>
                            <div>
                                <p class="text-white">Sedang diproses</p>
                                <p class="text-gray-400 text-sm">Pesanan sedang dikirim ke server game</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <button onclick="window.print()" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>
                        Cetak Invoice
                    </button>
                    <a href="/cek-transaksi" class="bg-dark hover:bg-dark-light border border-primary/30 text-white px-6 py-3 rounded-xl font-semibold transition-colors flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Cek Status
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Help -->
        <div class="mt-6 text-center">
            <p class="text-gray-400 text-sm">
                Butuh bantuan? Hubungi kami di 
                <a href="https://wa.me/081219748457" target="_blank" class="text-primary-light hover:underline">WhatsApp</a>
            </p>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
