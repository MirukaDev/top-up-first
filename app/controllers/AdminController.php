<?php
/**
 * MirukaStore - Admin Controller
 * Controller untuk panel admin
 */

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../../config/digiflazz.php';

class AdminController {
    private $userModel;
    private $gameModel;
    private $productModel;
    private $transactionModel;
    private $auth;
    
    public function __construct() {
        $this->userModel = new User();
        $this->gameModel = new Game();
        $this->productModel = new Product();
        $this->transactionModel = new Transaction();
        $this->auth = new AuthController();
        
        // Pastikan hanya admin yang bisa akses
        $this->auth->requireAdmin();
    }
    
    /**
     * Dashboard Admin
     */
    public function index() {
        // Statistik
        $todayStats = $this->transactionModel->getStatistics('today');
        $weekStats = $this->transactionModel->getStatistics('week');
        $monthStats = $this->transactionModel->getStatistics('month');
        
        // Total users
        $totalUsers = $this->userModel->countAll();
        $totalResellers = $this->userModel->countAll(['role' => 'reseller']);
        
        // Transaksi terbaru
        $recentTransactions = $this->transactionModel->getAll([], 1, 10);
        
        include __DIR__ . '/../../views/admin/dashboard.php';
    }
    
    // ==================== MANAJEMEN GAME ====================
    
    /**
     * Daftar Game
     */
    public function games() {
        $games = $this->gameModel->getAll(false);
        include __DIR__ . '/../../views/admin/games/index.php';
    }
    
    /**
     * Tambah Game
     */
    public function addGame() {
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
                'category' => htmlspecialchars(trim($_POST['category'] ?? '')),
                'sort_order' => intval($_POST['sort_order'] ?? 0)
            ];
            
            // Handle upload gambar
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->uploadImage($_FILES['image'], 'games');
                if ($uploadResult['success']) {
                    $data['image'] = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['message'];
                }
            }
            
            if (empty($error)) {
                if ($this->gameModel->create($data)) {
                    $success = 'Game berhasil ditambahkan';
                } else {
                    $error = 'Gagal menambahkan game';
                }
            }
        }
        
        include __DIR__ . '/../../views/admin/games/add.php';
    }
    
    /**
     * Edit Game
     */
    public function editGame($id) {
        $game = $this->gameModel->getById($id);
        
        if (!$game) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
                'category' => htmlspecialchars(trim($_POST['category'] ?? '')),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'sort_order' => intval($_POST['sort_order'] ?? 0)
            ];
            
            // Handle upload gambar
            if (!empty($_FILES['image']['name'])) {
                $uploadResult = $this->uploadImage($_FILES['image'], 'games');
                if ($uploadResult['success']) {
                    $data['image'] = $uploadResult['filename'];
                } else {
                    $error = $uploadResult['message'];
                }
            }
            
            if (empty($error)) {
                if ($this->gameModel->update($id, $data)) {
                    $success = 'Game berhasil diupdate';
                    $game = $this->gameModel->getById($id); // Refresh data
                } else {
                    $error = 'Gagal mengupdate game';
                }
            }
        }
        
        include __DIR__ . '/../../views/admin/games/edit.php';
    }
    
    /**
     * Hapus Game
     */
    public function deleteGame($id) {
        if ($this->gameModel->delete($id)) {
            $_SESSION['success'] = 'Game berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus game';
        }
        
        header('Location: /admin/games');
        exit;
    }
    
    // ==================== MANAJEMEN PRODUK ====================
    
    /**
     * Daftar Produk
     */
    public function products() {
        $game_id = intval($_GET['game_id'] ?? 0);
        $products = $this->productModel->getAll($game_id, false);
        $games = $this->gameModel->getAll(false);
        include __DIR__ . '/../../views/admin/products/index.php';
    }
    
    /**
     * Tambah Produk
     */
    public function addProduct() {
        $error = '';
        $success = '';
        $games = $this->gameModel->getAll(false);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'game_id' => intval($_POST['game_id'] ?? 0),
                'product_code' => htmlspecialchars(trim($_POST['product_code'] ?? '')),
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
                'price' => floatval($_POST['price'] ?? 0),
                'reseller_price' => floatval($_POST['reseller_price'] ?? 0),
                'supplier_code' => htmlspecialchars(trim($_POST['supplier_code'] ?? '')),
                'sort_order' => intval($_POST['sort_order'] ?? 0)
            ];
            
            if ($this->productModel->create($data)) {
                $success = 'Produk berhasil ditambahkan';
            } else {
                $error = 'Gagal menambahkan produk';
            }
        }
        
        include __DIR__ . '/../../views/admin/products/add.php';
    }
    
    /**
     * Edit Produk
     */
    public function editProduct($id) {
        $product = $this->productModel->getById($id);
        
        if (!$product) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        $error = '';
        $success = '';
        $games = $this->gameModel->getAll(false);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'game_id' => intval($_POST['game_id'] ?? 0),
                'product_code' => htmlspecialchars(trim($_POST['product_code'] ?? '')),
                'name' => htmlspecialchars(trim($_POST['name'] ?? '')),
                'description' => htmlspecialchars(trim($_POST['description'] ?? '')),
                'price' => floatval($_POST['price'] ?? 0),
                'reseller_price' => floatval($_POST['reseller_price'] ?? 0),
                'supplier_code' => htmlspecialchars(trim($_POST['supplier_code'] ?? '')),
                'is_active' => isset($_POST['is_active']) ? 1 : 0,
                'sort_order' => intval($_POST['sort_order'] ?? 0)
            ];
            
            if ($this->productModel->update($id, $data)) {
                $success = 'Produk berhasil diupdate';
                $product = $this->productModel->getById($id);
            } else {
                $error = 'Gagal mengupdate produk';
            }
        }
        
        include __DIR__ . '/../../views/admin/products/edit.php';
    }
    
    /**
     * Hapus Produk
     */
    public function deleteProduct($id) {
        if ($this->productModel->delete($id)) {
            $_SESSION['success'] = 'Produk berhasil dihapus';
        } else {
            $_SESSION['error'] = 'Gagal menghapus produk';
        }
        
        header('Location: /admin/products');
        exit;
    }
    
    /**
     * Sinkronisasi Produk dari API
     */
    public function syncProducts() {
        $error = '';
        $success = '';
        $games = $this->gameModel->getAll(false);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $game_id = intval($_POST['game_id'] ?? 0);
            $brand = htmlspecialchars(trim($_POST['brand'] ?? ''));
            
            if ($game_id && $brand) {
                $digiflazz = new DigiflazzAPI();
                $apiProducts = $digiflazz->getProducts($brand);
                
                if (isset($apiProducts['data']) && is_array($apiProducts['data'])) {
                    $result = $this->productModel->syncFromAPI($apiProducts['data'], $game_id);
                    $success = "Sinkronisasi berhasil: {$result['synced']} produk disinkronkan, {$result['failed']} gagal";
                } else {
                    $error = 'Gagal mengambil data dari API';
                }
            } else {
                $error = 'Pilih game dan brand';
            }
        }
        
        include __DIR__ . '/../../views/admin/products/sync.php';
    }
    
    // ==================== MANAJEMEN TRANSAKSI ====================
    
    /**
     * Daftar Transaksi
     */
    public function transactions() {
        $filters = [
            'status' => htmlspecialchars(trim($_GET['status'] ?? '')),
            'search' => htmlspecialchars(trim($_GET['search'] ?? '')),
            'date_from' => htmlspecialchars(trim($_GET['date_from'] ?? '')),
            'date_to' => htmlspecialchars(trim($_GET['date_to'] ?? ''))
        ];
        
        $page = intval($_GET['page'] ?? 1);
        $perpage = 20;
        
        $transactions = $this->transactionModel->getAll($filters, $page, $perpage);
        $total = $this->transactionModel->countAll($filters);
        $totalPages = ceil($total / $perpage);
        
        include __DIR__ . '/../../views/admin/transactions/index.php';
    }
    
    /**
     * Detail Transaksi
     */
    public function transactionDetail($order_id) {
        $transaction = $this->transactionModel->getByOrderId($order_id);
        
        if (!$transaction) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        include __DIR__ . '/../../views/admin/transactions/detail.php';
    }
    
    /**
     * Update Status Transaksi
     */
    public function updateTransactionStatus() {
        header('Content-Type: application/json');
        
        $order_id = htmlspecialchars(trim($_POST['order_id'] ?? ''));
        $status = htmlspecialchars(trim($_POST['status'] ?? ''));
        
        if (empty($order_id) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Data tidak lengkap']);
            return;
        }
        
        if ($this->transactionModel->updateStatus($order_id, $status)) {
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengupdate status']);
        }
    }
    
    // ==================== MANAJEMEN USER ====================
    
    /**
     * Daftar User
     */
    public function users() {
        $filters = [
            'role' => htmlspecialchars(trim($_GET['role'] ?? '')),
            'search' => htmlspecialchars(trim($_GET['search'] ?? ''))
        ];
        
        $page = intval($_GET['page'] ?? 1);
        $perpage = 20;
        
        $users = $this->userModel->getAll($filters, $page, $perpage);
        $total = $this->userModel->countAll($filters);
        $totalPages = ceil($total / $perpage);
        
        include __DIR__ . '/../../views/admin/users/index.php';
    }
    
    /**
     * Edit User
     */
    public function editUser($id) {
        $user = $this->userModel->getById($id);
        
        if (!$user) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'role' => htmlspecialchars(trim($_POST['role'] ?? 'user')),
                'is_active' => isset($_POST['is_active']) ? 1 : 0
            ];
            
            if ($this->userModel->update($id, $data)) {
                $success = 'User berhasil diupdate';
                $user = $this->userModel->getById($id);
            } else {
                $error = 'Gagal mengupdate user';
            }
        }
        
        include __DIR__ . '/../../views/admin/users/edit.php';
    }
    
    /**
     * Tambah Saldo User
     */
    public function addBalance() {
        header('Content-Type: application/json');
        
        $user_id = intval($_POST['user_id'] ?? 0);
        $amount = floatval($_POST['amount'] ?? 0);
        $description = htmlspecialchars(trim($_POST['description'] ?? 'Top up saldo oleh admin'));
        
        if (!$user_id || $amount <= 0) {
            echo json_encode(['success' => false, 'message' => 'Data tidak valid']);
            return;
        }
        
        if ($this->userModel->addBalance($user_id, $amount, $description)) {
            echo json_encode(['success' => true, 'message' => 'Saldo berhasil ditambahkan']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal menambahkan saldo']);
        }
    }
    
    // ==================== HELPER ====================
    
    /**
     * Upload gambar
     */
    private function uploadImage($file, $folder) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            return ['success' => false, 'message' => 'Tipe file tidak didukung'];
        }
        
        if ($file['size'] > $maxSize) {
            return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 2MB)'];
        }
        
        $uploadDir = __DIR__ . '/../../assets/img/' . $folder . '/';
        
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $filename = uniqid() . '_' . basename($file['name']);
        $targetPath = $uploadDir . $filename;
        
        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return ['success' => true, 'filename' => $filename];
        }
        
        return ['success' => false, 'message' => 'Gagal mengupload file'];
    }
}
