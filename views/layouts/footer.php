<?php
/**
 * MirukaStore - Footer Layout
 */
?>
    </main>
    
    <!-- Footer -->
    <footer class="bg-secondary border-t border-primary/20 mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Brand -->
                <div class="col-span-1 md:col-span-1">
                    <a href="/" class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-light rounded-lg flex items-center justify-center">
                            <i class="fas fa-gamepad text-white text-xl"></i>
                        </div>
                        <span class="font-display font-bold text-xl text-white">Miruka<span class="text-primary-light">Store</span></span>
                    </a>
                    <p class="text-gray-400 text-sm mb-4">
                        Platform top up game terpercaya dengan harga termurah dan proses instant.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-8 h-8 bg-dark-light rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-dark-light rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-8 h-8 bg-dark-light rounded-lg flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                            <i class="fab fa-telegram"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Menu Cepat</h3>
                    <ul class="space-y-2">
                        <li><a href="/" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Beranda</a></li>
                        <li><a href="/cek-transaksi" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Cek Transaksi</a></li>
                        <li><a href="/kontak" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Kontak Kami</a></li>
                        <li><a href="/tentang" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Tentang Kami</a></li>
                    </ul>
                </div>
                
                <!-- Games -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Game Populer</h3>
                    <ul class="space-y-2">
                        <li><a href="/game/mobile-legends" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Mobile Legends</a></li>
                        <li><a href="/game/free-fire" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Free Fire</a></li>
                        <li><a href="/game/pubg-mobile" class="text-gray-400 hover:text-primary-light text-sm transition-colors">PUBG Mobile</a></li>
                        <li><a href="/game/genshin-impact" class="text-gray-400 hover:text-primary-light text-sm transition-colors">Genshin Impact</a></li>
                    </ul>
                </div>
                
                <!-- Contact -->
                <div>
                    <h3 class="text-white font-semibold mb-4">Hubungi Kami</h3>
                    <ul class="space-y-3">
                        <li class="flex items-start space-x-3">
                            <i class="fab fa-whatsapp text-primary-light mt-1"></i>
                            <span class="text-gray-400 text-sm">0812-1974-8457</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-envelope text-primary-light mt-1"></i>
                            <span class="text-gray-400 text-sm">support@mirukastore.com</span>
                        </li>
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-clock text-primary-light mt-1"></i>
                            <span class="text-gray-400 text-sm">24 Jam Online</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <!-- Payment Methods -->
            <div class="border-t border-gray-700 mt-8 pt-8">
                <div class="flex flex-wrap justify-center items-center gap-4">
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/qris.png" alt="QRIS" class="h-6">
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/dana.png" alt="DANA" class="h-6">
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/ovo.png" alt="OVO" class="h-6">
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/gopay.png" alt="GoPay" class="h-6">
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/bca.png" alt="BCA" class="h-6">
                    </div>
                    <div class="bg-white rounded-lg px-4 py-2 flex items-center">
                        <img src="/assets/img/payment/bni.png" alt="BNI" class="h-6">
                    </div>
                </div>
            </div>
            
            <!-- Copyright -->
            <div class="border-t border-gray-700 mt-8 pt-8 text-center">
                <p class="text-gray-500 text-sm">
                    &copy; <?= date('Y') ?> MirukaStore. All rights reserved.
                </p>
            </div>
        </div>
    </footer>
    
    <!-- JavaScript -->
    <script src="/assets/js/main.js"></script>
    
    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-4 right-4 z-50 transform translate-y-full opacity-0 transition-all duration-300">
        <div class="bg-dark-light border border-primary/30 rounded-lg shadow-xl px-4 py-3 flex items-center space-x-3">
            <i id="toast-icon" class="fas fa-check-circle text-green-500"></i>
            <span id="toast-message" class="text-sm">Notification</span>
        </div>
    </div>
</body>
</html>
