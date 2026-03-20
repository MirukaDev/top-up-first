<?php
/**
 * MirukaStore - Order Controller
 * Controller untuk mengelola order dan transaksi
 */

require_once __DIR__ . '/../models/Game.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/AuthController.php';
require_once __DIR__ . '/../../config/midtrans.php';

class OrderController {
    private $gameModel;
    private $productModel;
    private $transactionModel;
    private $userModel;
    private $auth;
    
    public function __construct() {
        $this->gameModel = new Game();
        $this->productModel = new Product();
        $this->transactionModel = new Transaction();
        $this->userModel = new User();
        $this->auth = new AuthController();
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Proses order baru
     */
    public function create() {
        header('Content-Type: application/json');
        
        // Validasi CSRF
        if (!$this->auth->validateCsrfToken($_POST['csrf_token'] ?? '')) {
            echo json_encode(['success' => false, 'message' => 'Invalid security token']);
            return;
        }
        
        // Get input
        $game_id = intval($_POST['game_id'] ?? 0);
        $product_id = intval($_POST['product_id'] ?? 0);
        $user_id = htmlspecialchars(trim($_POST['user_id'] ?? ''));
        $server_id = htmlspecialchars(trim($_POST['server_id'] ?? ''));
        $payment_method = htmlspecialchars(trim($_POST['payment_method'] ?? ''));
        $use_balance = isset($_POST['use_balance']) ? 1 : 0;
        
        // Validasi input
        if (!$game_id || !$product_id || empty($user_id) || empty($payment_method)) {
            echo json_encode(['success' => false, 'message' => 'Semua field wajib diisi']);
            return;
        }
        
        // Get product data
        $product = $this->productModel->getById($product_id);
        if (!$product) {
            echo json_encode(['success' => false, 'message' => 'Produk tidak ditemukan']);
            return;
        }
        
        // Get harga berdasarkan role
        $role = $_SESSION['role'] ?? 'user';
        $price = ($role === 'reseller') ? $product['reseller_price'] : $product['price'];
        
        // Cek jika user ingin menggunakan saldo
        $balance_used = 0;
        $user = null;
        
        if ($use_balance && $this->auth->isLoggedIn()) {
            $user = $this->userModel->getById($_SESSION['user_id']);
            if ($user && $user['balance'] > 0) {
                $balance_used = min($user['balance'], $price);
                $price = $price - $balance_used;
            }
        }
        
        // Jika harga setelah potongan saldo = 0, langsung proses
        if ($price <= 0) {
            $this->processFreeOrder($product, $user_id, $server_id, $balance_used);
            return;
        }
        
        // Create transaksi
        $transactionData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'game_id' => $game_id,
            'product_id' => $product_id,
            'user_game_id' => $user_id,
            'server_id' => $server_id,
            'product_name' => $product['name'],
            'price' => $price + $balance_used, // Harga asli
            'payment_method' => $payment_method,
            'status' => 'pending',
            'use_balance' => $use_balance,
            'balance_used' => $balance_used
        ];
        
        $order_id = $this->transactionModel->create($transactionData);
        
        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat transaksi']);
            return;
        }
        
        // Generate Midtrans Snap Token
        $customerData = [
            'name' => $user['full_name'] ?? $_SESSION['username'] ?? 'Customer',
            'email' => $user['email'] ?? 'customer@example.com',
            'phone' => $user['phone'] ?? '08123456789',
            'product_code' => $product['product_code'],
            'product_name' => $product['name']
        ];
        
        $snapToken = MidtransConfig::getSnapToken($order_id, $price, $customerData);
        
        if (!$snapToken) {
            // Update status transaksi ke failed
            $this->transactionModel->updateStatus($order_id, 'failed');
            echo json_encode(['success' => false, 'message' => 'Gagal membuat token pembayaran']);
            return;
        }
        
        // Update payment token
        $this->transactionModel->updateStatus($order_id, 'pending', [
            'payment_token' => $snapToken
        ]);
        
        // Kurangi saldo user jika menggunakan saldo
        if ($balance_used > 0 && $user) {
            $this->userModel->deductBalance($user['id'], $balance_used, 'Pembayaran order ' . $order_id, $order_id);
        }
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'snap_token' => $snapToken,
            'client_key' => MIDTRANS_CLIENT_KEY,
            'message' => 'Transaksi berhasil dibuat'
        ]);
    }
    
    /**
     * Proses order gratis (full saldo)
     */
    private function processFreeOrder($product, $user_id, $server_id, $balance_used) {
        $transactionData = [
            'user_id' => $_SESSION['user_id'] ?? null,
            'game_id' => $product['game_id'],
            'product_id' => $product['id'],
            'user_game_id' => $user_id,
            'server_id' => $server_id,
            'product_name' => $product['name'],
            'price' => $balance_used,
            'payment_method' => 'saldo',
            'status' => 'settlement',
            'use_balance' => 1,
            'balance_used' => $balance_used
        ];
        
        $order_id = $this->transactionModel->create($transactionData);
        
        if (!$order_id) {
            echo json_encode(['success' => false, 'message' => 'Gagal membuat transaksi']);
            return;
        }
        
        // Kurangi saldo user
        if ($this->auth->isLoggedIn()) {
            $this->userModel->deductBalance($_SESSION['user_id'], $balance_used, 'Pembayaran order ' . $order_id, $order_id);
        }
        
        // Proses ke API supplier
        $this->processSupplierOrder($order_id, $product, $user_id, $server_id);
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'redirect' => '/order/success?order_id=' . $order_id,
            'message' => 'Transaksi berhasil'
        ]);
    }
    
    /**
     * Proses order ke supplier
     */
    private function processSupplierOrder($order_id, $product, $user_id, $server_id) {
        require_once __DIR__ . '/../../config/digiflazz.php';
        
        $digiflazz = new DigiflazzAPI();
        
        $response = $digiflazz->createTransaction(
            $order_id,
            $product['supplier_code'],
            $user_id,
            $server_id
        );
        
        // Update status API
        if (isset($response['data']['status']) && $response['data']['status'] === 'Sukses') {
            $this->transactionModel->updateApiStatus($order_id, 'success', json_encode($response));
        } else {
            $this->transactionModel->updateApiStatus($order_id, 'failed', json_encode($response));
        }
        
        return $response;
    }
    
    /**
     * Halaman sukses order
     */
    public function success() {
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        
        if (empty($order_id)) {
            header('Location: /');
            exit;
        }
        
        $order = $this->transactionModel->getByOrderId($order_id);
        
        if (!$order) {
            http_response_code(404);
            include __DIR__ . '/../../views/errors/404.php';
            return;
        }
        
        include __DIR__ . '/../../views/order/success.php';
    }
    
    /**
     * Halaman invoice
     */
    public function invoice() {
        $this->auth->requireLogin();
        
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        
        if (empty($order_id)) {
            header('Location: /');
            exit;
        }
        
        $order = $this->transactionModel->getByOrderId($order_id);
        
        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            http_response_code(403);
            include __DIR__ . '/../../views/errors/403.php';
            return;
        }
        
        include __DIR__ . '/../../views/order/invoice.php';
    }
    
    /**
     * Halaman riwayat transaksi
     */
    public function history() {
        $this->auth->requireLogin();
        
        $transactions = $this->transactionModel->getByUserId($_SESSION['user_id'], 50);
        
        include __DIR__ . '/../../views/order/history.php';
    }
    
    /**
     * API Cek status transaksi
     */
    public function apiCheckStatus() {
        header('Content-Type: application/json');
        
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        
        if (empty($order_id)) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            return;
        }
        
        $status = $this->transactionModel->checkStatus($order_id);
        
        if (!$status) {
            echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'status' => $status
        ]);
    }
}
