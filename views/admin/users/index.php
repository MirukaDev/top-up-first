<?php
/**
 * MirukaStore - Admin Users List
 */

$pageTitle = 'Manajemen User';
$activeMenu = 'users';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Manajemen User</h1>
    <p class="text-gray-400">Kelola pengguna dan reseller</p>
</div>

<!-- Filters -->
<div class="bg-dark-light border border-primary/20 rounded-xl p-4 mb-6">
    <form method="GET" action="/admin/users" class="flex flex-wrap gap-4">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" 
                class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-all"
                placeholder="Cari username, email, atau nama...">
        </div>
        <div>
            <select name="role" class="bg-dark border border-primary/30 rounded-xl px-4 py-2 text-white focus:outline-none focus:border-primary transition-all">
                <option value="">Semua Role</option>
                <option value="user" <?= ($_GET['role'] ?? '') === 'user' ? 'selected' : '' ?>>User</option>
                <option value="reseller" <?= ($_GET['role'] ?? '') === 'reseller' ? 'selected' : '' ?>>Reseller</option>
                <option value="admin" <?= ($_GET['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
        </div>
        <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl transition-colors">
            <i class="fas fa-search mr-2"></i>
            Filter
        </button>
    </form>
</div>

<!-- Users Table -->
<div class="bg-dark-light border border-primary/20 rounded-xl overflow-hidden">
    <?php if (empty($users)): ?>
    <div class="p-12 text-center">
        <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-users text-3xl text-primary-light"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-2">Tidak Ada User</h3>
        <p class="text-gray-400">Belum ada user yang terdaftar</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark">
                <tr>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">User</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Email</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Role</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Saldo</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Status</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Bergabung</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-4">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                <?php foreach ($users as $u): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-xs"></i>
                            </div>
                            <div>
                                <div class="text-white text-sm font-medium"><?= htmlspecialchars($u['username']) ?></div>
                                <div class="text-gray-400 text-xs"><?= htmlspecialchars($u['full_name'] ?: '-') ?></div>
                            </div>
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-sm"><?= htmlspecialchars($u['email']) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium 
                            <?= $u['role'] === 'admin' ? 'bg-red-500/20 text-red-400' : ($u['role'] === 'reseller' ? 'bg-blue-500/20 text-blue-400' : 'bg-green-500/20 text-green-400') ?>">
                            <?= ucfirst($u['role']) ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-primary-light text-sm font-semibold">Rp <?= number_format($u['balance'], 0, ',', '.') ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $u['is_active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' ?>">
                            <?= $u['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="text-gray-400 text-sm"><?= date('d M Y', strtotime($u['created_at'])) ?></span>
                    </td>
                    <td class="py-3 px-4">
                        <div class="flex items-center space-x-1">
                            <a href="/admin/users/edit?id=<?= $u['id'] ?>" class="w-7 h-7 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg flex items-center justify-center transition-colors" title="Edit">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <button onclick="addBalance(<?= $u['id'] ?>, '<?= htmlspecialchars($u['username']) ?>')" class="w-7 h-7 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg flex items-center justify-center transition-colors" title="Tambah Saldo">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
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

<!-- Add Balance Modal -->
<div id="balance-modal" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 hidden items-center justify-center">
    <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 max-w-sm w-full mx-4">
        <div class="text-center mb-6">
            <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-wallet text-green-500 text-2xl"></i>
            </div>
            <h3 class="text-white font-semibold text-lg">Tambah Saldo</h3>
            <p class="text-gray-400 text-sm">User: <span id="balance-username" class="text-white"></span></p>
        </div>
        
        <form id="balance-form" class="space-y-4">
            <input type="hidden" id="balance-user-id" name="user_id">
            <div>
                <label class="block text-gray-400 text-sm mb-2">Jumlah Saldo</label>
                <input type="number" name="amount" id="balance-amount" required min="1000"
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-all"
                    placeholder="Masukkan jumlah">
            </div>
            <div>
                <label class="block text-gray-400 text-sm mb-2">Keterangan</label>
                <input type="text" name="description" 
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary transition-all"
                    placeholder="Top up saldo oleh admin">
            </div>
            <div class="flex space-x-3">
                <button type="button" onclick="closeBalanceModal()" class="flex-1 bg-dark hover:bg-dark-light border border-primary/30 text-white py-2 rounded-xl transition-colors">
                    Batal
                </button>
                <button type="submit" class="flex-1 bg-green-500 hover:bg-green-600 text-white py-2 rounded-xl transition-colors">
                    Tambah
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function addBalance(userId, username) {
    document.getElementById('balance-user-id').value = userId;
    document.getElementById('balance-username').textContent = username;
    document.getElementById('balance-modal').classList.remove('hidden');
    document.getElementById('balance-modal').classList.add('flex');
}

function closeBalanceModal() {
    document.getElementById('balance-modal').classList.add('hidden');
    document.getElementById('balance-modal').classList.remove('flex');
}

document.getElementById('balance-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/admin/users/add-balance', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Saldo berhasil ditambahkan');
            location.reload();
        } else {
            alert('Gagal: ' + data.message);
        }
    });
});
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
