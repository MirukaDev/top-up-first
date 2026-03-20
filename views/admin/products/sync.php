<?php
/**
 * MirukaStore - Admin Sync Products
 */

$pageTitle = 'Sinkronisasi Produk';
$activeMenu = 'products';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <a href="/admin/products" class="text-gray-400 hover:text-white mb-2 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>
    <h1 class="text-2xl md:text-3xl font-bold text-white">Sinkronisasi Produk</h1>
    <p class="text-gray-400">Sinkronisasi produk dari API Digiflazz</p>
</div>

<!-- Sync Form -->
<div class="bg-dark-light border border-primary/20 rounded-xl p-6">
    <?php if ($error): ?>
    <div class="bg-red-500/20 border border-red-500/30 rounded-xl px-4 py-3 mb-6">
        <div class="flex items-center text-red-400 text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>
            <?= $error ?>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($success): ?>
    <div class="bg-green-500/20 border border-green-500/30 rounded-xl px-4 py-3 mb-6">
        <div class="flex items-center text-green-400 text-sm">
            <i class="fas fa-check-circle mr-2"></i>
            <?= $success ?>
        </div>
    </div>
    <?php endif; ?>
    
    <form method="POST" action="/admin/products/sync" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Game -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Pilih Game <span class="text-red-400">*</span></label>
                <select name="game_id" required
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
                    <option value="">-- Pilih Game --</option>
                    <?php foreach ($games as $g): ?>
                    <option value="<?= $g['id'] ?>"><?= htmlspecialchars($g['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Brand/Code -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Brand/Kode <span class="text-red-400">*</span></label>
                <input type="text" name="brand" required 
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                    placeholder="Contoh: MOBILELEGENDS, FF, PUBG">
                <p class="text-gray-500 text-xs mt-1">Kode brand dari Digiflazz</p>
            </div>
        </div>
        
        <!-- Info -->
        <div class="bg-primary/10 border border-primary/20 rounded-xl p-4">
            <div class="flex items-start space-x-3">
                <i class="fas fa-info-circle text-primary-light mt-0.5"></i>
                <div class="text-sm text-gray-300">
                    <p class="font-medium text-white mb-1">Informasi Sinkronisasi</p>
                    <ul class="list-disc list-inside space-y-1 text-gray-400">
                        <li>Produk yang sudah ada akan diupdate harganya</li>
                        <li>Produk baru akan ditambahkan otomatis</li>
                        <li>Harga user = Harga API + 10% margin</li>
                        <li>Harga reseller = Harga API + 5% margin</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Buttons -->
        <div class="flex space-x-4">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                <i class="fas fa-sync mr-2"></i>
                Sinkronkan
            </button>
            <a href="/admin/products" class="bg-dark hover:bg-dark-light border border-primary/30 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<!-- Check Balance Card -->
<div class="mt-6 bg-dark-light border border-primary/20 rounded-xl p-6">
    <h3 class="text-white font-semibold mb-4">Cek Saldo Digiflazz</h3>
    <button onclick="checkBalance()" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl transition-colors">
        <i class="fas fa-wallet mr-2"></i>
        Cek Saldo
    </button>
    <div id="balance-result" class="mt-4 hidden">
        <div class="bg-dark border border-primary/20 rounded-xl p-4">
            <span class="text-gray-400">Saldo:</span>
            <span id="balance-amount" class="text-white font-bold text-xl ml-2"></span>
        </div>
    </div>
</div>

<script>
function checkBalance() {
    const btn = document.querySelector('button[onclick="checkBalance()"]');
    const result = document.getElementById('balance-result');
    const amount = document.getElementById('balance-amount');
    
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Loading...';
    btn.disabled = true;
    
    fetch('/api/digiflazz-balance.php')
        .then(r => r.json())
        .then(data => {
            if (data.data && data.data.deposit) {
                amount.textContent = 'Rp ' + parseInt(data.data.deposit).toLocaleString('id-ID');
                result.classList.remove('hidden');
            } else {
                alert('Gagal mengambil data saldo');
            }
        })
        .catch(() => {
            alert('Terjadi kesalahan');
        })
        .finally(() => {
            btn.innerHTML = '<i class="fas fa-wallet mr-2"></i> Cek Saldo';
            btn.disabled = false;
        });
}
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
