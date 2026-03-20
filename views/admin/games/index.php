<?php
/**
 * MirukaStore - Admin Games List
 */

$pageTitle = 'Manajemen Game';
$activeMenu = 'games';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="flex flex-col md:flex-row md:items-center md:justify-between mb-8">
    <div>
        <h1 class="text-2xl md:text-3xl font-bold text-white mb-2">Manajemen Game</h1>
        <p class="text-gray-400">Kelola daftar game yang tersedia</p>
    </div>
    <div class="mt-4 md:mt-0">
        <a href="/admin/games/add" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-xl transition-colors inline-flex items-center">
            <i class="fas fa-plus mr-2"></i>
            Tambah Game
        </a>
    </div>
</div>

<!-- Games Grid -->
<div class="bg-dark-light border border-primary/20 rounded-xl overflow-hidden">
    <?php if (empty($games)): ?>
    <div class="p-12 text-center">
        <div class="w-20 h-20 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-gamepad text-3xl text-primary-light"></i>
        </div>
        <h3 class="text-white font-semibold text-lg mb-2">Belum Ada Game</h3>
        <p class="text-gray-400 mb-4">Tambahkan game pertama Anda</p>
        <a href="/admin/games/add" class="inline-flex items-center bg-primary hover:bg-primary-dark text-white px-6 py-2 rounded-xl transition-colors">
            <i class="fas fa-plus mr-2"></i>
            Tambah Game
        </a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-dark">
                <tr>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Game</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Slug</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Kategori</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Status</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Urutan</th>
                    <th class="text-left text-gray-400 font-medium text-sm py-4 px-6">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/10">
                <?php foreach ($games as $game): ?>
                <tr class="hover:bg-primary/5 transition-colors">
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-3">
                            <?php if ($game['image']): ?>
                                <img src="/assets/img/games/<?= $game['image'] ?>" alt="" class="w-10 h-10 rounded-lg object-cover">
                            <?php else: ?>
                                <div class="w-10 h-10 bg-primary/20 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-gamepad text-primary-light"></i>
                                </div>
                            <?php endif; ?>
                            <div>
                                <div class="text-white font-medium"><?= htmlspecialchars($game['name']) ?></div>
                                <div class="text-gray-400 text-sm"><?= htmlspecialchars(substr($game['description'], 0, 50)) ?>...</div>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-gray-400 text-sm font-mono"><?= $game['slug'] ?></span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-white text-sm"><?= htmlspecialchars($game['category'] ?: '-') ?></span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="inline-flex px-2 py-1 rounded-full text-xs font-medium <?= $game['is_active'] ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' ?>">
                            <?= $game['is_active'] ? 'Aktif' : 'Nonaktif' ?>
                        </span>
                    </td>
                    <td class="py-4 px-6">
                        <span class="text-white text-sm"><?= $game['sort_order'] ?></span>
                    </td>
                    <td class="py-4 px-6">
                        <div class="flex items-center space-x-2">
                            <a href="/admin/games/edit?id=<?= $game['id'] ?>" class="w-8 h-8 bg-blue-500/20 hover:bg-blue-500/30 text-blue-400 rounded-lg flex items-center justify-center transition-colors" title="Edit">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <a href="/game/<?= $game['slug'] ?>" target="_blank" class="w-8 h-8 bg-green-500/20 hover:bg-green-500/30 text-green-400 rounded-lg flex items-center justify-center transition-colors" title="Lihat">
                                <i class="fas fa-eye text-sm"></i>
                            </a>
                            <button onclick="confirmDelete(<?= $game['id'] ?>, '<?= htmlspecialchars($game['name']) ?>')" class="w-8 h-8 bg-red-500/20 hover:bg-red-500/30 text-red-400 rounded-lg flex items-center justify-center transition-colors" title="Hapus">
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
            <h3 class="text-white font-semibold text-lg mb-2">Hapus Game?</h3>
            <p class="text-gray-400 text-sm mb-6">Apakah Anda yakin ingin menghapus game <span id="delete-game-name" class="text-white"></span>?</p>
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
    document.getElementById('delete-game-name').textContent = name;
    document.getElementById('delete-link').href = '/admin/games/delete?id=' + id;
    document.getElementById('delete-modal').classList.remove('hidden');
    document.getElementById('delete-modal').classList.add('flex');
}

function closeDeleteModal() {
    document.getElementById('delete-modal').classList.add('hidden');
    document.getElementById('delete-modal').classList.remove('flex');
}
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
