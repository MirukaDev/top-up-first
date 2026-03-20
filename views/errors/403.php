<?php
/**
 * MirukaStore - 403 Error Page
 */

http_response_code(403);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
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
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-dark text-gray-100 min-h-screen flex items-center justify-center">
    <div class="text-center px-4">
        <div class="mb-8">
            <div class="w-32 h-32 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-lock text-6xl text-red-400"></i>
            </div>
            <h1 class="text-6xl md:text-8xl font-bold text-white mb-4">403</h1>
            <h2 class="text-2xl md:text-3xl font-semibold text-white mb-4">Akses Ditolak</h2>
            <p class="text-gray-400 max-w-md mx-auto mb-8">
                Maaf, Anda tidak memiliki izin untuk mengakses halaman ini.
            </p>
        </div>
        
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="/" class="bg-primary hover:bg-primary-dark text-white px-8 py-3 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                <i class="fas fa-home mr-2"></i>
                Kembali ke Beranda
            </a>
            <?php if (!isset($_SESSION['logged_in'])): ?>
            <a href="/login" class="bg-dark-light hover:bg-dark border border-primary/30 text-white px-8 py-3 rounded-xl font-semibold transition-colors inline-flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Login
            </a>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
