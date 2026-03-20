<?php
/**
 * MirukaStore - Home Page
 */

$pageTitle = 'Top Up Game Murah & Cepat';
$activeMenu = 'home';
$useMidtrans = false;

include __DIR__ . '/../layouts/header.php';
?>

<!-- Hero Section -->
<section class="relative overflow-hidden">
    <!-- Background -->
    <div class="absolute inset-0 bg-gradient-to-br from-primary/20 via-dark to-secondary"></div>
    <div class="absolute inset-0 bg-[url('/assets/img/pattern.png')] opacity-10"></div>
    
    <!-- Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div class="text-center md:text-left">
                <div class="inline-flex items-center bg-primary/20 border border-primary/30 rounded-full px-4 py-1.5 mb-6">
                    <span class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                    <span class="text-sm text-primary-light">24 Jam Online</span>
                </div>
                
                <h1 class="font-display text-4xl md:text-5xl lg:text-6xl font-bold text-white leading-tight mb-6">
                    Top Up Game <br>
                    <span class="bg-gradient-to-r from-primary to-primary-light bg-clip-text text-transparent">Murah & Cepat</span>
                </h1>
                
                <p class="text-gray-400 text-lg mb-8 max-w-lg mx-auto md:mx-0">
                    Platform top up game terpercaya dengan proses instant. 
                    Tersedia berbagai metode pembayaran.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center md:justify-start">
                    <a href="#games" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-semibold transition-all transform hover:scale-105 flex items-center justify-center">
                        <i class="fas fa-shopping-cart mr-2"></i>
                        Top Up Sekarang
                    </a>
                    <a href="/cek-transaksi" class="bg-dark-light hover:bg-dark border border-primary/30 text-white px-8 py-3 rounded-xl font-semibold transition-all flex items-center justify-center">
                        <i class="fas fa-search mr-2"></i>
                        Cek Transaksi
                    </a>
                </div>
                
                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 mt-10 pt-10 border-t border-gray-700">
                    <div>
                        <div class="text-2xl md:text-3xl font-bold text-white">50K+</div>
                        <div class="text-gray-400 text-sm">Transaksi</div>
                    </div>
                    <div>
                        <div class="text-2xl md:text-3xl font-bold text-white">10+</div>
                        <div class="text-gray-400 text-sm">Game</div>
                    </div>
                    <div>
                        <div class="text-2xl md:text-3xl font-bold text-white">24/7</div>
                        <div class="text-gray-400 text-sm">Support</div>
                    </div>
                </div>
            </div>
            
            <!-- Hero Image -->
            <div class="hidden md:block relative">
                <div class="absolute inset-0 bg-gradient-to-r from-primary/30 to-primary-light/30 rounded-3xl blur-3xl"></div>
                <div class="relative bg-dark-light/50 backdrop-blur-sm border border-primary/20 rounded-3xl p-6">
                    <img src="/assets/img/hero-games.png" alt="Popular Games" class="w-full rounded-2xl">
                    
                    <!-- Floating Badge -->
                    <div class="absolute -bottom-4 -left-4 bg-dark-light border border-primary/30 rounded-xl px-4 py-3 shadow-xl">
                        <div class="flex items-center space-x-2">
                            <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-bolt text-green-500"></i>
                            </div>
                            <div>
                                <div class="text-white font-semibold text-sm">Instant Process</div>
                                <div class="text-gray-400 text-xs">Kurang dari 5 menit</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Games Section -->
<section id="games" class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                Pilih Game <span class="text-primary-light">Favoritmu</span>
            </h2>
            <p class="text-gray-400 max-w-2xl mx-auto">
                Tersedia berbagai game populer dengan harga termurah dan proses instant
            </p>
        </div>
        
        <!-- Games Grid -->
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php foreach ($games as $game): ?>
            <a href="/game/<?= $game['slug'] ?>" class="group">
                <div class="bg-dark-light border border-primary/20 rounded-2xl overflow-hidden hover:border-primary/50 transition-all duration-300 hover:transform hover:scale-105 hover:shadow-xl hover:shadow-primary/20">
                    <!-- Game Image -->
                    <div class="aspect-square bg-gradient-to-br from-primary/10 to-primary-light/10 relative overflow-hidden">
                        <?php if ($game['image']): ?>
                            <img src="/assets/img/games/<?= $game['image'] ?>" alt="<?= htmlspecialchars($game['name']) ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                        <?php else: ?>
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-gamepad text-6xl text-primary/30"></i>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Overlay -->
                        <div class="absolute inset-0 bg-gradient-to-t from-dark via-transparent to-transparent opacity-60"></div>
                    </div>
                    
                    <!-- Game Info -->
                    <div class="p-4">
                        <h3 class="text-white font-semibold text-lg mb-1 group-hover:text-primary-light transition-colors">
                            <?= htmlspecialchars($game['name']) ?>
                        </h3>
                        <p class="text-gray-400 text-sm mb-3 line-clamp-2">
                            <?= htmlspecialchars($game['description'] ?: 'Top up ' . $game['name'] . ' murah dan cepat') ?>
                        </p>
                        <div class="flex items-center justify-between">
                            <span class="text-primary-light text-sm font-medium">Top Up</span>
                            <i class="fas fa-arrow-right text-primary-light opacity-0 group-hover:opacity-100 transform translate-x-[-10px] group-hover:translate-x-0 transition-all"></i>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-16 bg-secondary/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                Kenapa Memilih <span class="text-primary-light">MirukaStore?</span>
            </h2>
        </div>
        
        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-bolt text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Proses Cepat</h3>
                <p class="text-gray-400 text-sm">Transaksi diproses dalam hitungan menit</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shield-alt text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Aman & Terpercaya</h3>
                <p class="text-gray-400 text-sm">Sistem keamanan terbaik untuk data Anda</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-tags text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Harga Termurah</h3>
                <p class="text-gray-400 text-sm">Harga kompetitif dengan kualitas terbaik</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="text-center">
                <div class="w-16 h-16 bg-primary/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-headset text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold text-lg mb-2">Support 24/7</h3>
                <p class="text-gray-400 text-sm">Tim support siap membantu kapan saja</p>
            </div>
        </div>
    </div>
</section>

<!-- How to Order Section -->
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                Cara <span class="text-primary-light">Order</span>
            </h2>
            <p class="text-gray-400">4 langkah mudah untuk top up game</p>
        </div>
        
        <div class="grid md:grid-cols-4 gap-8">
            <!-- Step 1 -->
            <div class="relative">
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center relative z-10">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">1</div>
                    <h3 class="text-white font-semibold mb-2">Pilih Game</h3>
                    <p class="text-gray-400 text-sm">Pilih game favoritmu dari daftar yang tersedia</p>
                </div>
                <div class="hidden md:block absolute top-1/2 right-0 w-full h-0.5 bg-primary/30 -translate-y-1/2 translate-x-1/2 z-0"></div>
            </div>
            
            <!-- Step 2 -->
            <div class="relative">
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center relative z-10">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">2</div>
                    <h3 class="text-white font-semibold mb-2">Masukkan ID</h3>
                    <p class="text-gray-400 text-sm">Masukkan User ID dan pilih nominal</p>
                </div>
                <div class="hidden md:block absolute top-1/2 right-0 w-full h-0.5 bg-primary/30 -translate-y-1/2 translate-x-1/2 z-0"></div>
            </div>
            
            <!-- Step 3 -->
            <div class="relative">
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center relative z-10">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">3</div>
                    <h3 class="text-white font-semibold mb-2">Pilih Pembayaran</h3>
                    <p class="text-gray-400 text-sm">Pilih metode pembayaran yang tersedia</p>
                </div>
                <div class="hidden md:block absolute top-1/2 right-0 w-full h-0.5 bg-primary/30 -translate-y-1/2 translate-x-1/2 z-0"></div>
            </div>
            
            <!-- Step 4 -->
            <div class="relative">
                <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center relative z-10">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center mx-auto mb-4 text-white font-bold text-xl">4</div>
                    <h3 class="text-white font-semibold mb-2">Selesai</h3>
                    <p class="text-gray-400 text-sm">Diamond akan masuk otomatis ke akunmu</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-16 bg-secondary/30">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                Apa Kata <span class="text-primary-light">Mereka?</span>
            </h2>
        </div>
        
        <div class="grid md:grid-cols-3 gap-6">
            <!-- Testimonial 1 -->
            <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold">A</div>
                    <div class="ml-3">
                        <div class="text-white font-semibold">Ahmad Rizky</div>
                        <div class="text-gray-400 text-sm">Mobile Legends Player</div>
                    </div>
                </div>
                <div class="flex text-yellow-500 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-400 text-sm">"Top up di MirukaStore sangat cepat dan mudah. Diamond masuk dalam hitungan menit!"</p>
            </div>
            
            <!-- Testimonial 2 -->
            <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold">S</div>
                    <div class="ml-3">
                        <div class="text-white font-semibold">Siti Nurhaliza</div>
                        <div class="text-gray-400 text-sm">Free Fire Player</div>
                    </div>
                </div>
                <div class="flex text-yellow-500 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                </div>
                <p class="text-gray-400 text-sm">"Harga termurah yang pernah saya temukan. Recommended banget!"</p>
            </div>
            
            <!-- Testimonial 3 -->
            <div class="bg-dark-light border border-primary/20 rounded-2xl p-6">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-primary rounded-full flex items-center justify-center text-white font-bold">D</div>
                    <div class="ml-3">
                        <div class="text-white font-semibold">Dedi Kurniawan</div>
                        <div class="text-gray-400 text-sm">PUBG Mobile Player</div>
                    </div>
                </div>
                <div class="flex text-yellow-500 mb-3">
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star"></i>
                    <i class="fas fa-star-half-alt"></i>
                </div>
                <p class="text-gray-400 text-sm">"Pelayanan customer service sangat ramah dan responsif. Top!"</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="relative bg-gradient-to-r from-primary to-primary-light rounded-3xl p-8 md:p-12 overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 bg-[url('/assets/img/pattern.png')] opacity-10"></div>
            
            <div class="relative text-center">
                <h2 class="font-display text-3xl md:text-4xl font-bold text-white mb-4">
                    Siap Top Up Game Favoritmu?
                </h2>
                <p class="text-white/80 text-lg mb-8 max-w-2xl mx-auto">
                    Bergabung dengan ribuan pengguna yang sudah mempercayai MirukaStore
                </p>
                <a href="#games" class="inline-flex items-center bg-white text-primary px-8 py-3 rounded-xl font-semibold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-rocket mr-2"></i>
                    Mulai Top Up
                </a>
            </div>
        </div>
    </div>
</section>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
