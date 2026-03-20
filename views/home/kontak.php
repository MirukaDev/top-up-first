<?php
/**
 * MirukaStore - Kontak Page
 */

$pageTitle = 'Kontak Kami';
$activeMenu = 'kontak';
$useMidtrans = false;

include __DIR__ . '/../layouts/header.php';
?>

<section class="py-16 md:py-24">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-white mb-4">Hubungi Kami</h1>
            <p class="text-gray-400">Kami siap membantu Anda 24 jam sehari</p>
        </div>
        
        <!-- Contact Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-12">
            <!-- WhatsApp -->
            <a href="https://wa.me/081219748457" target="_blank" class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center hover:border-primary/50 transition-all group">
                <div class="w-16 h-16 bg-green-500/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-green-500/30 transition-colors">
                    <i class="fab fa-whatsapp text-3xl text-green-500"></i>
                </div>
                <h3 class="text-white font-semibold mb-2">WhatsApp</h3>
                <p class="text-gray-400 text-sm">0812-1974-8457</p>
            </a>
            
            <!-- Email -->
            <a href="mailto:support@mirukastore.com" class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center hover:border-primary/50 transition-all group">
                <div class="w-16 h-16 bg-blue-500/20 rounded-full flex items-center justify-center mx-auto mb-4 group-hover:bg-blue-500/30 transition-colors">
                    <i class="fas fa-envelope text-3xl text-blue-500"></i>
                </div>
                <h3 class="text-white font-semibold mb-2">Email</h3>
                <p class="text-gray-400 text-sm">support@mirukastore.com</p>
            </a>
            
            <!-- 24 Jam -->
            <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 text-center">
                <div class="w-16 h-16 bg-primary/20 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-clock text-3xl text-primary-light"></i>
                </div>
                <h3 class="text-white font-semibold mb-2">Jam Operasional</h3>
                <p class="text-gray-400 text-sm">24 Jam Online</p>
            </div>
        </div>
        
        <!-- FAQ Section -->
        <div class="bg-dark-light border border-primary/20 rounded-2xl p-6 md:p-8">
            <h2 class="text-xl font-bold text-white mb-6 text-center">Pertanyaan yang Sering Diajukan</h2>
            
            <div class="space-y-4">
                <!-- FAQ 1 -->
                <div class="border border-primary/20 rounded-xl overflow-hidden">
                    <button class="w-full flex items-center justify-between p-4 text-left hover:bg-primary/5 transition-colors" onclick="toggleFaq(this)">
                        <span class="text-white font-medium">Berapa lama proses top up?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden px-4 pb-4">
                        <p class="text-gray-400 text-sm">Proses top up biasanya memakan waktu 1-5 menit setelah pembayaran berhasil. Dalam kondisi tertentu bisa memakan waktu lebih lama.</p>
                    </div>
                </div>
                
                <!-- FAQ 2 -->
                <div class="border border-primary/20 rounded-xl overflow-hidden">
                    <button class="w-full flex items-center justify-between p-4 text-left hover:bg-primary/5 transition-colors" onclick="toggleFaq(this)">
                        <span class="text-white font-medium">Metode pembayaran apa saja yang tersedia?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden px-4 pb-4">
                        <p class="text-gray-400 text-sm">Kami menerima pembayaran via QRIS, DANA, OVO, GoPay, dan Virtual Account (BCA, BNI, BRI, Mandiri).</p>
                    </div>
                </div>
                
                <!-- FAQ 3 -->
                <div class="border border-primary/20 rounded-xl overflow-hidden">
                    <button class="w-full flex items-center justify-between p-4 text-left hover:bg-primary/5 transition-colors" onclick="toggleFaq(this)">
                        <span class="text-white font-medium">Apakah transaksi saya aman?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden px-4 pb-4">
                        <p class="text-gray-400 text-sm">Ya, semua transaksi di MirukaStore menggunakan sistem keamanan terbaik dan terintegrasi dengan payment gateway terpercaya.</p>
                    </div>
                </div>
                
                <!-- FAQ 4 -->
                <div class="border border-primary/20 rounded-xl overflow-hidden">
                    <button class="w-full flex items-center justify-between p-4 text-left hover:bg-primary/5 transition-colors" onclick="toggleFaq(this)">
                        <span class="text-white font-medium">Bagaimana cara menjadi reseller?</span>
                        <i class="fas fa-chevron-down text-gray-400 transition-transform"></i>
                    </button>
                    <div class="hidden px-4 pb-4">
                        <p class="text-gray-400 text-sm">Anda dapat menghubungi kami via WhatsApp untuk informasi lebih lanjut tentang program reseller.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
function toggleFaq(button) {
    const content = button.nextElementSibling;
    const icon = button.querySelector('i');
    
    content.classList.toggle('hidden');
    icon.classList.toggle('rotate-180');
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
