<?php
/**
 * MirukaStore - Register Page
 */

$pageTitle = 'Register';
$activeMenu = '';
$useMidtrans = false;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MirukaStore</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
        body {
            background: linear-gradient(135deg, #0f0a1e 0%, #1e1b4b 50%, #0f0a1e 100%);
            min-height: 100vh;
        }
        .auth-card {
            background: rgba(26, 20, 41, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(124, 58, 237, 0.2);
        }
    </style>
</head>
<body class="flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <a href="/" class="inline-flex items-center space-x-2">
                <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-light rounded-xl flex items-center justify-center">
                    <i class="fas fa-gamepad text-white text-2xl"></i>
                </div>
                <span class="font-display font-bold text-2xl text-white">Miruka<span class="text-primary-light">Store</span></span>
            </a>
        </div>
        
        <!-- Auth Card -->
        <div class="auth-card rounded-2xl p-8">
            <h1 class="text-2xl font-bold text-white text-center mb-2">Buat Akun Baru</h1>
            <p class="text-gray-400 text-center mb-6">Daftar gratis dan nikmati kemudahan top up</p>
            
            <?php if ($error): ?>
            <div class="bg-red-500/20 border border-red-500/30 rounded-xl px-4 py-3 mb-4">
                <div class="flex items-center text-red-400 text-sm">
                    <i class="fas fa-exclamation-circle mr-2"></i>
                    <?= $error ?>
                </div>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="bg-green-500/20 border border-green-500/30 rounded-xl px-4 py-3 mb-4">
                <div class="flex items-center text-green-400 text-sm">
                    <i class="fas fa-check-circle mr-2"></i>
                    <?= $success ?>
                </div>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="/register" class="space-y-4">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Username</label>
                        <div class="relative">
                            <i class="fas fa-user absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input type="text" name="username" required 
                                class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                placeholder="Username">
                        </div>
                    </div>
                    <div>
                        <label class="block text-gray-400 text-sm mb-2">Nama Lengkap</label>
                        <div class="relative">
                            <i class="fas fa-id-card absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                            <input type="text" name="full_name" 
                                class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                                placeholder="Nama lengkap">
                        </div>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Email</label>
                    <div class="relative">
                        <i class="fas fa-envelope absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="email" name="email" required 
                            class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="email@example.com">
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Nomor Telepon</label>
                    <div class="relative">
                        <i class="fas fa-phone absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="tel" name="phone" 
                            class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-4 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="08123456789">
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="password" name="password" id="password" required 
                            class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-11 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Minimal 6 karakter">
                        <button type="button" onclick="togglePassword('password', 'toggle-icon1')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-eye" id="toggle-icon1"></i>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="block text-gray-400 text-sm mb-2">Konfirmasi Password</label>
                    <div class="relative">
                        <i class="fas fa-lock absolute left-4 top-1/2 -translate-y-1/2 text-gray-500"></i>
                        <input type="password" name="password_confirm" id="password_confirm" required 
                            class="w-full bg-dark border border-primary/30 rounded-xl pl-11 pr-11 py-3 text-white placeholder-gray-500 focus:outline-none focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all"
                            placeholder="Ulangi password">
                        <button type="button" onclick="togglePassword('password_confirm', 'toggle-icon2')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-500 hover:text-white transition-colors">
                            <i class="fas fa-eye" id="toggle-icon2"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-start">
                    <input type="checkbox" id="terms" required class="w-4 h-4 mt-1 rounded border-gray-600 text-primary focus:ring-primary bg-dark">
                    <label for="terms" class="ml-2 text-gray-400 text-sm">
                        Saya menyetujui <a href="#" class="text-primary-light hover:underline">Syarat & Ketentuan</a> dan <a href="#" class="text-primary-light hover:underline">Kebijakan Privasi</a>
                    </label>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-primary-dark text-white font-semibold py-3 rounded-xl transition-colors">
                    Daftar
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-400 text-sm">
                    Sudah punya akun? 
                    <a href="/login" class="text-primary-light hover:underline font-medium">Masuk</a>
                </p>
            </div>
        </div>
        
        <!-- Back to Home -->
        <div class="text-center mt-6">
            <a href="/" class="text-gray-400 hover:text-white text-sm transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke beranda
            </a>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const toggleIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>
