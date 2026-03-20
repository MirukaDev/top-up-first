<?php
/**
 * MirukaStore - Game Order Page
 */

$pageTitle = 'Top Up ' . $game['name'];
$activeMenu = 'games';
$useMidtrans = true;

include __DIR__ . '/../layouts/header.php';
?>

<!-- Breadcrumb -->
<section class="bg-secondary/50 py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center text-sm">
            <a href="/" class="text-gray-400 hover:text-white transition-colors">Beranda</a>
            <i class="fas fa-chevron-right text-gray-600 mx-2 text-xs"></i>
            <span class="text-primary-light"><?= htmlspecialchars($game['name']) ?></span>
        </nav>
    </div>
</section>

<!-- Game Header -->
<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center space-x-4">
            <div class="w-20 h-20 bg-gradient-to-br from-primary/20 to-primary-light/20 rounded-2xl flex items-center justify-center border border-primary/20">
                <?php if ($game['image']): ?>
                    <img src="/assets/img/games/<?= $game['image'] ?>" alt="<?= htmlspecialchars($game['name']) ?>" class="w-16 h-16 object-cover rounded-xl">
                <?php else: ?>
                    <i class="fas fa-gamepad text-3xl text-primary-light"></i>
                <?php endif; ?>
            </div>
            <div>
                <h1 class="font-display text-2xl md:text-3xl font-bold text-white"><?= htmlspecialchars($game['name']) ?></h1>
                <p class="text-gray-400"><?= htmlspecialchars($game['description'] ?: 'Top up ' . $game['name']) ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Order Form -->
<section class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Form Section -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Step 1: Input ID -->
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <h2 class="text-white font-semibold text-lg">Masukkan User ID</h2>
                    </div>
                    
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">User ID</label>
                            <input type="text" id="user_id" class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Masukkan User ID">
                        </div>
                        <div>
                            <label class="block text-gray-400 text-sm mb-2">Server ID (Opsional)</label>
                            <input type="text" id="server_id" class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all" placeholder="Contoh: 1234">
                        </div>
                    </div>
                    
                    <div class="mt-4 p-4 bg-primary/10 border border-primary/20 rounded-xl">
                        <div class="flex items-start space-x-3">
                            <i class="fas fa-info-circle text-primary-light mt-0.5"></i>
                            <div class="text-sm text-gray-300">
                                <p class="font-medium text-white mb-1">Cara menemukan User ID:</p>
                                <p>Buka profil game Anda, User ID biasanya terlihat di bagian atas profil.</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Step 2: Select Nominal -->
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                        <h2 class="text-white font-semibold text-lg">Pilih Nominal</h2>
                    </div>
                    
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4" id="products-grid">
                        <?php foreach ($products as $product): ?>
                        <div class="product-card cursor-pointer" data-product-id="<?= $product['id'] ?>" data-price="<?= $product['price'] ?>">
                            <div class="bg-dark border border-primary/30 rounded-xl p-4 hover:border-primary hover:bg-primary/5 transition-all">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-white font-semibold"><?= htmlspecialchars($product['name']) ?></span>
                                    <?php if ($product['icon']): ?>
                                        <img src="/assets/img/icons/<?= $product['icon'] ?>" alt="" class="w-6 h-6">
                                    <?php else: ?>
                                        <i class="fas fa-gem text-primary-light"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="text-primary-light font-bold">
                                    Rp <?= number_format($product['price'], 0, ',', '.') ?>
                                </div>
                                <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'reseller'): ?>
                                    <div class="text-gray-500 text-xs mt-1">
                                        Reseller: Rp <?= number_format($product['reseller_price'], 0, ',', '.') ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <input type="hidden" id="selected_product_id" value="">
                </div>
                
                <!-- Step 3: Payment Method -->
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                    <div class="flex items-center space-x-3 mb-6">
                        <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                        <h2 class="text-white font-semibold text-lg">Pilih Pembayaran</h2>
                    </div>
                    
                    <div class="space-y-3" id="payment-methods">
                        <!-- QRIS -->
                        <label class="payment-method flex items-center p-4 bg-dark border border-primary/30 rounded-xl cursor-pointer hover:border-primary transition-all">
                            <input type="radio" name="payment_method" value="qris" class="w-4 h-4 text-primary border-gray-600 focus:ring-primary">
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                    <img src="/assets/img/payment/qris.png" alt="QRIS" class="h-8">
                                </div>
                                <div class="ml-4">
                                    <div class="text-white font-medium">QRIS</div>
                                    <div class="text-gray-400 text-sm">Scan dengan aplikasi e-wallet</div>
                                </div>
                            </div>
                            <div class="text-primary-light text-sm">Cek Harga</div>
                        </label>
                        
                        <!-- DANA -->
                        <label class="payment-method flex items-center p-4 bg-dark border border-primary/30 rounded-xl cursor-pointer hover:border-primary transition-all">
                            <input type="radio" name="payment_method" value="dana" class="w-4 h-4 text-primary border-gray-600 focus:ring-primary">
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                    <img src="/assets/img/payment/dana.png" alt="DANA" class="h-8">
                                </div>
                                <div class="ml-4">
                                    <div class="text-white font-medium">DANA</div>
                                    <div class="text-gray-400 text-sm">Pembayaran via DANA</div>
                                </div>
                            </div>
                            <div class="text-primary-light text-sm">Cek Harga</div>
                        </label>
                        
                        <!-- OVO -->
                        <label class="payment-method flex items-center p-4 bg-dark border border-primary/30 rounded-xl cursor-pointer hover:border-primary transition-all">
                            <input type="radio" name="payment_method" value="ovo" class="w-4 h-4 text-primary border-gray-600 focus:ring-primary">
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                    <img src="/assets/img/payment/ovo.png" alt="OVO" class="h-8">
                                </div>
                                <div class="ml-4">
                                    <div class="text-white font-medium">OVO</div>
                                    <div class="text-gray-400 text-sm">Pembayaran via OVO</div>
                                </div>
                            </div>
                            <div class="text-primary-light text-sm">Cek Harga</div>
                        </label>
                        
                        <!-- GoPay -->
                        <label class="payment-method flex items-center p-4 bg-dark border border-primary/30 rounded-xl cursor-pointer hover:border-primary transition-all">
                            <input type="radio" name="payment_method" value="gopay" class="w-4 h-4 text-primary border-gray-600 focus:ring-primary">
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                    <img src="/assets/img/payment/gopay.png" alt="GoPay" class="h-8">
                                </div>
                                <div class="ml-4">
                                    <div class="text-white font-medium">GoPay</div>
                                    <div class="text-gray-400 text-sm">Pembayaran via GoPay</div>
                                </div>
                            </div>
                            <div class="text-primary-light text-sm">Cek Harga</div>
                        </label>
                        
                        <!-- Bank Transfer -->
                        <label class="payment-method flex items-center p-4 bg-dark border border-primary/30 rounded-xl cursor-pointer hover:border-primary transition-all">
                            <input type="radio" name="payment_method" value="bank_transfer" class="w-4 h-4 text-primary border-gray-600 focus:ring-primary">
                            <div class="ml-4 flex items-center flex-1">
                                <div class="w-12 h-12 bg-white rounded-lg flex items-center justify-center">
                                    <i class="fas fa-university text-gray-600 text-2xl"></i>
                                </div>
                                <div class="ml-4">
                                    <div class="text-white font-medium">Virtual Account</div>
                                    <div class="text-gray-400 text-sm">BCA, BNI, BRI, Mandiri</div>
                                </div>
                            </div>
                            <div class="text-primary-light text-sm">Cek Harga</div>
                        </label>
                    </div>
                </div>
                
                <!-- Use Balance (if logged in) -->
                <?php if ($currentUser && $currentUser['balance'] > 0): ?>
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-wallet text-primary-light text-xl"></i>
                            <div>
                                <div class="text-white font-medium">Gunakan Saldo</div>
                                <div class="text-gray-400 text-sm">Saldo Anda: Rp <?= number_format($currentUser['balance'], 0, ',', '.') ?></div>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" id="use_balance" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-700 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/20 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                        </label>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Summary Section -->
            <div class="lg:col-span-1">
                <div class="sticky top-24">
                    <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                        <h3 class="text-white font-semibold text-lg mb-4">Ringkasan Pesanan</h3>
                        
                        <div class="space-y-3 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Game</span>
                                <span class="text-white"><?= htmlspecialchars($game['name']) ?></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Produk</span>
                                <span class="text-white" id="summary-product">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">User ID</span>
                                <span class="text-white" id="summary-user-id">-</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Server</span>
                                <span class="text-white" id="summary-server">-</span>
                            </div>
                        </div>
                        
                        <hr class="border-gray-700 mb-4">
                        
                        <div class="space-y-2 mb-6">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-400">Harga</span>
                                <span class="text-white" id="summary-price">Rp 0</span>
                            </div>
                            <div class="flex justify-between text-sm" id="balance-discount-row" style="display: none;">
                                <span class="text-gray-400">Potongan Saldo</span>
                                <span class="text-green-400" id="summary-balance-discount">-Rp 0</span>
                            </div>
                        </div>
                        
                        <hr class="border-gray-700 mb-4">
                        
                        <div class="flex justify-between items-center mb-6">
                            <span class="text-white font-semibold">Total Bayar</span>
                            <span class="text-primary-light text-2xl font-bold" id="summary-total">Rp 0</span>
                        </div>
                        
                        <button type="button" id="btn-order" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-xl transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                            <i class="fas fa-shopping-cart mr-2"></i>
                            Bayar Sekarang
                        </button>
                        
                        <div class="mt-4 text-center">
                            <p class="text-gray-500 text-xs">
                                Dengan melakukan pembayaran, Anda menyetujui <a href="#" class="text-primary-light hover:underline">Syarat & Ketentuan</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Loading Modal -->
<div id="loading-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-dark-light border border-primary/20 rounded-2xl p-8 text-center">
        <div class="w-16 h-16 border-4 border-primary border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <h3 class="text-white font-semibold text-lg">Memproses Pesanan</h3>
        <p class="text-gray-400 text-sm mt-2">Mohon tunggu sebentar...</p>
    </div>
</div>

<script>
// Game ID untuk digunakan di JavaScript
const GAME_ID = <?= $game['id'] ?>;
const USER_BALANCE = <?= $currentUser ? $currentUser['balance'] : 0 ?>;
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
