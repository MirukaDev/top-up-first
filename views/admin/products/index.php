<?php
/**
 * MirukaStore - Admin Products List
 */

$pageTitle = 'Manajemen Produk';
$activeMenu = 'products';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Manajemen Produk</h1>
        <p class="text-gray-400">Kelola daftar produk/diamond</p>
    </div>
    <div class="mt-4 md:mt-0 flex space-x-3">
        <a href="/admin/products/sync" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-xl transition-colors inline-flex items-center">
            <i class="fas fa-sync mr-2"></i>
            Sync API
        </a>
        <a href="/admin/products/add" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl transition-colors inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
        </a>
    </div>
</div>

<!-- Filter -->
<div class="bg-dark-light border border-primary/20 rounded-xl p-4 mb-6">
    <form method="GET" action="/admin/products" class="flex flex-wrap gap-4">
        <div>
            <select name="game_id" onchange="this.form.submit()" class="bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary transition-all">
                <option value="">Semua Game</option>
                <?php foreach ($games as $g): ?>
                <option value="<?= $g['id'] ?>" <?= ($_GET['game_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($g['name']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </form>
</div>

<!-- Products Table -->
<div class="bg-dark-light border border-primary/20 rounded-xl overflow-hidden">
    <?php if (empty($products)): ?>
    <div class="p-12 text-center">
        <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-box text-3xl text-primary-light"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-2">Belum Ada Produk</h3>
        <p class="text-gray-400 mb-4">Tambahkan produk pertama Anda</p>
        <a href="/admin/products/add" class="inline-flex items-center bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-xl transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
        </a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark">
                <tr>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Produk</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Game</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Kode</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Harga User</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Harga Reseller</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Status</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                <?php foreach ($products as $p): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <?php if ($p['icon']): ?>
                                <img src="/assets/img/icons/<?= $p['icon'] ?>" alt="" class="w-8 h-8 rounded-lg">
                            <?php else: ?>
                                <div class="w-8 h-8 bg-primary/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gem text-primary-light text-sm"></i>
                                </div>
                            <?php endif; ?>
                            <span class="text-white text-sm"><?= htmlspecialchars($p['name']) ?></span>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-sm"><?= htmlspecialchars($p['game_name']) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-xs font-mono"><?= $p['product_code'] ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-white text-sm">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-primary-light text-sm">Rp <?= number_format($p['reseller_price'], 0, ',', '.') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $p['is_active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' ?>">
                            <?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-2">
                            <a href="/admin/products/edit?id=<?= $p['id'] ?>" class="w-8 h-8 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg flex items-center justify-center transition-colors" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <button onclick="confirmDelete(<?= $p['id'] ?>, '<?= htmlspecialchars($p['name']) ?>')" class="w-8 h-8 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg flex items-center justify-center transition-colors" title="Hapus">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<!-- Delete Confirmation Modal -->
<div id="delete-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 max-w-sm w-full mx-4">
        <div class="text-center">
            <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg mb-2">Hapus Produk?</h3>
            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus produk <span id="delete-product-name" class="text-white"></span>?</p>
            <div class="flex space-x-3">
                <button onclick="closeDeleteModal()" class="flex-1 bg-dark hover:bg-dark-light border border-primary/30 text-white py-2 rounded-xl transition-colors">
                    Batal
                </button>
                <a id="delete-link" href="#" class="flex-1 bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl transition-colors text-center">
                    Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(id, name) {
    document.getElementById('delete-product-name').textContent = name;
    document.getElementById('delete-link').href = '/admin/products/delete?id=' + id;
    document.getElementById('delete-modal').classList.remove('hidden');
    document.getElementById('delete-modal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.getElementById('delete-modal').classList.remove('flex');
}
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
