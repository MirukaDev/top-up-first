<?php
/**
 * MirukaStore - Header Layout
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../app/models/User.php';
    $userModel = new User();
    $currentUser = $userModel->getById($_SESSION['user_id']);
}

$cartCount = 0; // Bisa ditambahkan fitur cart nanti
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>MirukaStore - Top Up Game Cepat & Terpercaya</title>
    <meta name="description" content="MirukaStore - Platform top up game cepat, murah, dan terpercaya. Tersedia Mobile Legends, Free Fire, PUBG Mobile, dan banyak lagi.">
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/img/favicon.png">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Midtrans Snap -->
    <?php if (isset($useMidtrans) && $useMidtrans): ?>
    <script type="text/javascript" src="<?= MIDTRANS_SNAP_URL ?>" data-client-key="<?= MIDTRANS_CLIENT_KEY ?>"></script>
    <?php endif; ?>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7c3aed',
                        'primary-dark': '#6d28d9',
                        'primary-light': '#8b5cf6',
                        secondary: '#1e1b4b',
                        dark: '#0f0a1e',
                        'dark-light': '#1a1429',
                        accent: '#f59e0b',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-gray-100 font-sans min-h-screen">
    <!-- Navbar -->
    <nav class="bg-secondary/95 backdrop-blur-md sticky top-0 z-50 border-b border-primary/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-light rounded-lg flex items-center justify-center">
                        <i class="fas fa-gamepad text-white text-xl"></i>
                    </div>
                    <span class="font-display font-bold text-xl text-white">Miruka<span class="text-primary-light">Store</span></span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center space-x-8">
                    <a href="/" class="text-gray-300 hover:text-white transition-colors <?= $activeMenu == 'home' ? 'text-primary-light' : '' ?>">Beranda</a>
                    <a href="/cek-transaksi" class="text-gray-300 hover:text-white transition-colors <?= $activeMenu == 'cek-transaksi' ? 'text-primary-light' : '' ?>">Cek Transaksi</a>
                    <a href="/kontak" class="text-gray-300 hover:text-white transition-colors <?= $activeMenu == 'kontak' ? 'text-primary-light' : '' ?>">Kontak</a>
                </div>
                
                <!-- User Menu -->
                <div class="flex items-center space-x-4">
                    <?php if ($currentUser): ?>
                        <!-- Balance -->
                        <div class="hidden sm:flex items-center bg-dark-light px-3 py-1.5 rounded-lg border border-primary/30">
                            <i class="fas fa-wallet text-primary-light mr-2"></i>
                            <span class="text-sm font-medium">Rp <?= number_format($currentUser['balance'], 0, ',', '.') ?></span>
                        </div>
                        
                        <!-- User Dropdown -->
                        <div class="relative group">
                            <button class="flex items-center space-x-2 hover:bg-dark-light px-3 py-2 rounded-lg transition-colors">
                                <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="hidden sm:block text-sm font-medium"><?= htmlspecialchars($currentUser['username']) ?></span>
                                <i class="fas fa-chevron-down text-xs text-gray-400"></i>
                            </button>
                            
                            <div class="absolute right-0 mt-2 w-48 bg-dark-light border border-primary/20 rounded-lg shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                                <?php if ($currentUser['role'] === 'admin'): ?>
                                    <a href="/admin" class="block px-4 py-2 text-sm text-gray-300 hover:bg-primary/20 hover:text-white rounded-t-lg">
                                        <i class="fas fa-cog mr-2"></i>Admin Panel
                                    </a>
                                <?php endif; ?>
                                <a href="/order/history" class="block px-4 py-2 text-sm text-gray-300 hover:bg-primary/20 hover:text-white <?= $currentUser['role'] !== 'admin' ? 'rounded-t-lg' : '' ?>">
                                    <i class="fas fa-history mr-2"></i>Riwayat Transaksi
                                </a>
                                <a href="/profile" class="block px-4 py-2 text-sm text-gray-300 hover:bg-primary/20 hover:text-white">
                                    <i class="fas fa-user-circle mr-2"></i>Profil
                                </a>
                                <hr class="border-gray-700">
                                <a href="/logout" class="block px-4 py-2 text-sm text-red-400 hover:bg-red-500/20 rounded-b-lg">
                                    <i class="fas fa-sign-out-alt mr-2"></i>Logout
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="/login" class="hidden sm:block text-gray-300 hover:text-white transition-colors">Login</a>
                        <a href="/register" class="bg-primary hover:bg-primary-dark text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                            Daftar
                        </a>
                    <?php endif; ?>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-btn" class="md:hidden text-gray-300 hover:text-white">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="hidden md:hidden bg-dark-light border-t border-primary/20">
            <div class="px-4 py-3 space-y-2">
                <a href="/" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-primary/20 hover:text-white">Beranda</a>
                <a href="/cek-transaksi" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-primary/20 hover:text-white">Cek Transaksi</a>
                <a href="/kontak" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-primary/20 hover:text-white">Kontak</a>
                <?php if (!$currentUser): ?>
                    <hr class="border-gray-700 my-2">
                    <a href="/login" class="block px-3 py-2 rounded-lg text-gray-300 hover:bg-primary/20 hover:text-white">Login</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>
    
    <!-- Main Content -->
    <main>
