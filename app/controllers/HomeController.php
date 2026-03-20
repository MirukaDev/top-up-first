<?php
/**
 * MirukaStore - Home Controller
 * Controller untuk halaman utama
 */

require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Product.php';

class HomeController {
    private $gameModel;
    private $productModel;
    
    public function __construct() {
        $this->gameModel = new Game();
        $this->productModel = new Product();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Halaman utama
     */
    public function index() {
        // Get data games
        $games = $this->gameModel->getAll(true);
        $popularGames = $this->gameModel->getPopular(6);
        
        // Load view
        include __DIR__ . '/../../views/home/index.php';
    }
    
    /**
     * Halaman detail game
     */
    public function game($slug) {
        // Get game by slug
        $game = $this->gameModel->getBySlug($slug);
        
        if (!$game) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        // Get products untuk game ini
        $role = $_SESSION['role'] ?? 'user';
        $products = $this->productModel->getByGameId($game['id'], $role);
        
        // Load view
        include __DIR__ . '/../../views/home/game.php';
    }
    
    /**
     * Halaman cek transaksi
     */
    public function cekTransaksi() {
        $order = null;
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $order_id = htmlspecialchars(trim($_POST['order_id'] ?? ''));
            
            if (empty($order_id)) {
                $error = 'Masukkan Order ID';
            } else {
                require_once __DIR__ . '/../models/Transaction.php';
                $transactionModel = new Transaction();
                $order = $transactionModel->getByOrderId($order_id);
                
                if (!$order) {
                    $error = 'Transaksi tidak ditemukan';
                }
            }
        }
        
        include __DIR__ . '/../../views/home/cek-transaksi.php';
    }
    
    /**
     * Halaman kontak
     */
    public function kontak() {
        include __DIR__ . '/../../views/home/kontak.php';
    }
    
    /**
     * Halaman tentang
     */
    public function tentang() {
        include __DIR__ . '/../../views/home/tentang.php';
    }
    
    /**
     * Search produk
     */
    public function search() {
        $keyword = htmlspecialchars(trim($_GET['q'] ?? ''));
        $results = [];
        
        if (!empty($keyword)) {
            $results = $this->productModel->search($keyword);
        }
        
        include __DIR__ . '/../../views/home/search.php';
    }
    
    /**
     * API Get Products (AJAX)
     */
    public function apiGetProducts() {
        header('Content-Type: application/json');
        
        $game_id = intval($_GET['game_id'] ?? 0);
        
        if (!$game_id) {
            echo json_encode(['success' => false, 'message' => 'Game ID required']);
            return;
        }
        
        $role = $_SESSION['role'] ?? 'user';
        $products = $this->productModel->getByGameId($game_id, $role);
        
        echo json_encode([
            'success' => true,
            'data' => $products
        ]);
    }
}
