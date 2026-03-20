<?php
/**
 * MirukaStore - Admin Header Layout
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: /');
    exit;
}

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    require_once __DIR__ . '/../../app/models/User.php';
    $userModel = new User();
    $currentUser = $userModel->getById($_SESSION['user_id']);
}

// Status colors untuk digunakan di view
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
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $pageTitle . ' - ' : '' ?>Admin - MirukaStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        display: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .sidebar-link {
            @apply flex items-center px-4 py-3 text-gray-400 hover:text-white hover:bg-primary/20 rounded-xl transition-all;
        }
        .sidebar-link.active {
            @apply text-white bg-primary/20 border border-primary/30;
        }
    </style>
</head>
<body class="bg-dark text-gray-100 font-sans">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-dark-light border-r border-primary/20 flex-shrink-0 hidden md:flex flex-col">
            <!-- Logo -->
            <div class="p-6 border-b border-primary/20">
                <a href="/admin" class="flex items-center space-x-2">
                    <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-light rounded-lg flex items-center justify-center">
                        <i class="fas fa-gamepad text-white text-xl"></i>
                    </div>
                    <div>
                        <span class="font-display font-bold text-lg text-white">Miruka<span class="text-primary-light">Store</span></span>
                        <div class="text-xs text-gray-400">Admin Panel</div>
                    </div>
                </a>
            </div>
            
            <!-- Menu -->
            <nav class="flex-1 overflow-y-auto p-4 space-y-1">
                <a href="/admin" class="sidebar-link <?= $activeMenu == 'dashboard' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt w-5"></i>
                    <span class="ml-3">Dashboard</span>
                </a>
                
                <div class="pt-4 pb-2">
                    <div class="text-xs text-gray-500 uppercase font-semibold px-4">Manajemen</div>
                </div>
                
                <a href="/admin/games" class="sidebar-link <?= $activeMenu == 'games' ? 'active' : '' ?>">
                    <i class="fas fa-gamepad w-5"></i>
                    <span class="ml-3">Game</span>
                </a>
                <a href="/admin/products" class="sidebar-link <?= $activeMenu == 'products' ? 'active' : '' ?>">
                    <i class="fas fa-box w-5"></i>
                    <span class="ml-3">Produk</span>
                </a>
                <a href="/admin/transactions" class="sidebar-link <?= $activeMenu == 'transactions' ? 'active' : '' ?>">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="ml-3">Transaksi</span>
                </a>
                <a href="/admin/users" class="sidebar-link <?= $activeMenu == 'users' ? 'active' : '' ?>">
                    <i class="fas fa-users w-5"></i>
                    <span class="ml-3">Users</span>
                </a>
                
                <div class="pt-4 pb-2">
                    <div class="text-xs text-gray-500 uppercase font-semibold px-4">Lainnya</div>
                </div>
                
                <a href="/" target="_blank" class="sidebar-link">
                    <i class="fas fa-external-link-alt w-5"></i>
                    <span class="ml-3">Lihat Website</span>
                </a>
                <a href="/logout" class="sidebar-link text-red-400 hover:text-red-300 hover:bg-red-500/10">
                    <i class="fas fa-sign-out-alt w-5"></i>
                    <span class="ml-3">Logout</span>
                </a>
            </nav>
            
            <!-- User -->
            <div class="p-4 border-t border-primary/20">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-white"></i>
                    </div>
                    <div>
                        <div class="text-white text-sm font-medium"><?= htmlspecialchars($currentUser['username'] ?? 'Admin') ?></div>
                        <div class="text-gray-400 text-xs">Administrator</div>
                    </div>
                </div>
            </div>
        </aside>
        
        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navbar -->
            <header class="bg-dark-light border-b border-primary/20 h-16 flex items-center justify-between px-4 md:px-8">
                <button id="sidebar-toggle" class="md:hidden text-gray-400 hover:text-white">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                <div class="flex items-center space-x-4">
                    <a href="/api/digiflazz-balance.php" target="_blank" class="hidden sm:flex items-center bg-dark px-3 py-1.5 rounded-lg border border-primary/30 text-sm">
                        <i class="fas fa-wallet text-primary-light mr-2"></i>
                        <span class="text-gray-400 mr-2">Digiflazz:</span>
                        <span class="text-white">Cek Saldo</span>
                    </a>
                    
                    <div class="relative">
                        <button class="flex items-center space-x-2 hover:bg-dark px-3 py-2 rounded-lg transition-colors">
                            <div class="w-8 h-8 bg-primary rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-white text-sm"></i>
                            </div>
                        </button>
                    </div>
                </div>
            </header>
            
            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
