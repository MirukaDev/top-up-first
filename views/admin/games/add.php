<?php
/**
 * MirukaStore - Admin Add Game
 */

$pageTitle = 'Tambah Game';
$activeMenu = 'games';
$useMidtrans = false;

include __DIR__ . '/../../layouts/admin-header.php';
?>

<!-- Page Header -->
<div class="mb-8">
    <a href="/admin/games" class="text-gray-400 hover:text-white mb-2 inline-flex items-center">
        <i class="fas fa-arrow-left mr-2"></i>
        Kembali
    </a>
    <h1 class="text-2xl md:text-3xl font-bold text-white">Tambah Game Baru</h1>
</div>

<!-- Form -->
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
    
    <form method="POST" action="/admin/games/add" enctype="multipart/form-data" class="space-y-6">
        <div class="grid md:grid-cols-2 gap-6">
            <!-- Name -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Nama Game <span class="text-red-400">*</span></label>
                <input type="text" name="name" required 
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                    placeholder="Contoh: Mobile Legends">
            </div>
            
            <!-- Category -->
            <div>
                <label class="block text-gray-400 text-sm mb-2">Kategori</label>
                <input type="text" name="category" 
                    class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                    placeholder="Contoh: MOBA, Battle Royale">
            </div>
        </div>
        
        <!-- Description -->
        <div>
            <label class="block text-gray-400 text-sm mb-2">Deskripsi</label>
            <textarea name="description" rows="3"
                class="w-full bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                placeholder="Deskripsi singkat tentang game"></textarea>
        </div>
        
        <!-- Image -->
        <div>
            <label class="block text-gray-400 text-sm mb-2">Gambar Game</label>
            <div class="border-2 border-dashed border-primary/30 rounded-xl p-6 text-center hover:border-primary/50 transition-colors">
                <input type="file" name="image" id="image" accept="image/*" class="hidden" onchange="previewImage(this)">
                <label for="image" class="cursor-pointer">
                    <div id="image-preview" class="hidden mb-4">
                        <img src="" alt="Preview" class="max-h-40 mx-auto rounded-lg">
                    </div>
                    <div id="upload-placeholder">
                        <i class="fas fa-cloud-upload-alt text-4xl text-primary-light mb-2"></i>
                        <p class="text-gray-400 text-sm">Klik untuk upload gambar</p>
                        <p class="text-gray-500 text-xs mt-1">Format: JPG, PNG, GIF (Max 2MB)</p>
                    </div>
                </label>
            </div>
        </div>
        
        <!-- Sort Order -->
        <div>
            <label class="block text-gray-400 text-sm mb-2">Urutan</label>
            <input type="number" name="sort_order" value="0" min="0"
                class="w-full md:w-32 bg-dark border border-primary/30 rounded-xl px-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all">
            <p class="text-gray-500 text-xs mt-1">Semakin kecil angka, semakin awal ditampilkan</p>
        </div>
        
        <!-- Buttons -->
        <div class="flex space-x-4">
            <button type="submit" class="bg-primary hover:bg-primary-dark text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                <i class="fas fa-save mr-2"></i>
                Simpan
            </button>
            <a href="/admin/games" class="bg-dark hover:bg-dark-light border border-primary/30 text-white px-6 py-3 rounded-xl font-semibold transition-colors">
                Batal
            </a>
        </div>
    </form>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    const placeholder = document.getElementById('upload-placeholder');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.querySelector('img').src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php include __DIR__ . '/../../layouts/admin-footer.php'; ?>
