<?php
/**
 * MirukaStore - Payment Controller
 * Controller untuk mengelola callback dan notifikasi pembayaran
 */

require_once __DIR__ . '/../models/Transaction.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../../config/midtrans.php';
require_once __DIR__ . '/../../config/digiflazz.php';

class PaymentController {
    private $transactionModel;
    private $userModel;
    
    public function __construct() {
        $this->transactionModel = new Transaction();
        $this->userModel = new User();
    }
    
    /**
     * Midtrans Notification Handler (Webhook)
     * Endpoint untuk menerima callback dari Midtrans
     */
    public function midtransCallback() {
        // Log raw input untuk debugging
        $raw_input = file_get_contents('php://input');
        error_log("Midtrans Callback: " . $raw_input);
        
        $notification = json_decode($raw_input, true);
        
        if (!$notification) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid notification']);
            return;
        }
        
        // Verifikasi signature key
        $order_id = $notification['order_id'] ?? '';
        $status_code = $notification['status_code'] ?? '';
        $gross_amount = $notification['gross_amount'] ?? '';
        $signature_key = $notification['signature_key'] ?? '';
        
        if (!MidtransConfig::verifySignature($order_id, $status_code, $gross_amount, $signature_key)) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => 'Invalid signature']);
            return;
        }
        
        // Get transaction status dari Midtrans
        $transaction_status = $notification['transaction_status'] ?? '';
        $payment_type = $notification['payment_type'] ?? '';
        
        // Map status Midtrans ke status internal
        $statusMap = [
            'capture' => 'settlement',
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired',
            'failure' => 'failed',
            'refund' => 'refund'
        ];
        
        $internalStatus = $statusMap[$transaction_status] ?? 'pending';
        
        // Update transaksi
        $this->transactionModel->updateStatus($order_id, $internalStatus, [
            'payment_method' => $payment_type
        ]);
        
        // Jika pembayaran sukses, proses ke supplier
        if ($internalStatus === 'settlement') {
            $this->processToSupplier($order_id);
        }
        
        // Jika pembayaran gagal/expired, refund saldo user jika menggunakan saldo
        if (in_array($internalStatus, ['failed', 'expired', 'cancelled'])) {
            $this->refundBalance($order_id);
        }
        
        http_response_code(200);
        echo json_encode(['status' => 'success']);
    }
    
    /**
     * Midtrans Finish Redirect
     * Redirect setelah pembayaran selesai
     */
    public function midtransFinish() {
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        $status_code = htmlspecialchars(trim($_GET['status_code'] ?? ''));
        $transaction_status = htmlspecialchars(trim($_GET['transaction_status'] ?? ''));
        
        if (empty($order_id)) {
            header('Location: /');
            exit;
        }
        
        // Update status transaksi
        $statusMap = [
            'capture' => 'settlement',
            'settlement' => 'settlement',
            'pending' => 'pending',
            'deny' => 'failed',
            'cancel' => 'cancelled',
            'expire' => 'expired'
        ];
        
        $internalStatus = $statusMap[$transaction_status] ?? 'pending';
        
        $this->transactionModel->updateStatus($order_id, $internalStatus);
        
        // Redirect ke halaman sukses atau status
        if ($internalStatus === 'settlement') {
            header('Location: /order/success?order_id=' . $order_id);
        } else {
            header('Location: /order/status?order_id=' . $order_id . '&status=' . $internalStatus);
        }
        exit;
    }
    
    /**
     * Midtrans Error Redirect
     */
    public function midtransError() {
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        
        if (!empty($order_id)) {
            $this->transactionModel->updateStatus($order_id, 'failed');
            $this->refundBalance($order_id);
        }
        
        header('Location: /order/status?order_id=' . $order_id . '&status=error');
        exit;
    }
    
    /**
     * Proses order ke supplier setelah pembayaran sukses
     */
    private function processToSupplier($order_id) {
        // Get transaction data
        $transaction = $this->transactionModel->getByOrderId($order_id);
        
        if (!$transaction) {
            error_log("Process to Supplier: Transaction not found - " . $order_id);
            return false;
        }
        
        // Cek apakah sudah diproses
        if ($transaction['api_status'] !== 'pending') {
            return true;
        }
        
        // Get product data
        require_once __DIR__ . '/../models/Product.php';
        $productModel = new Product();
        $product = $productModel->getById($transaction['product_id']);
        
        if (!$product) {
            error_log("Process to Supplier: Product not found - " . $transaction['product_id']);
            return false;
        }
        
        // Kirim ke Digiflazz
        $digiflazz = new DigiflazzAPI();
        
        $response = $digiflazz->createTransaction(
            $order_id,
            $product['supplier_code'],
            $transaction['user_game_id'],
            $transaction['server_id']
        );
        
        // Update status API
        $apiStatus = 'processing';
        if (isset($response['data']['status'])) {
            if ($response['data']['status'] === 'Sukses') {
                $apiStatus = 'success';
            } elseif ($response['data']['status'] === 'Gagal') {
                $apiStatus = 'failed';
            }
        }
        
        $this->transactionModel->updateApiStatus($order_id, $apiStatus, json_encode($response));
        
        return $response;
    }
    
    /**
     * Refund saldo user jika transaksi gagal
     */
    private function refundBalance($order_id) {
        $transaction = $this->transactionModel->getByOrderId($order_id);
        
        if (!$transaction || $transaction['balance_used'] <= 0) {
            return false;
        }
        
        // Refund saldo
        $this->userModel->addBalance(
            $transaction['user_id'],
            $transaction['balance_used'],
            'Refund transaksi gagal - ' . $order_id,
            $order_id
        );
        
        return true;
    }
    
    /**
     * API Cek status pembayaran Midtrans
     */
    public function apiCheckPaymentStatus() {
        header('Content-Type: application/json');
        
        $order_id = htmlspecialchars(trim($_GET['order_id'] ?? ''));
        
        if (empty($order_id)) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            return;
        }
        
        $transaction = $this->transactionModel->getByOrderId($order_id);
        
        if (!$transaction) {
            echo json_encode(['success' => false, 'message' => 'Transaksi tidak ditemukan']);
            return;
        }
        
        echo json_encode([
            'success' => true,
            'order_id' => $order_id,
            'status' => $transaction['status'],
            'api_status' => $transaction['api_status'],
            'payment_method' => $transaction['payment_method']
        ]);
    }
    
    /**
     * Manual retry ke supplier (untuk admin)
     */
    public function retrySupplier() {
        // Hanya admin yang bisa akses
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $order_id = htmlspecialchars(trim($_POST['order_id'] ?? ''));
        
        if (empty($order_id)) {
            echo json_encode(['success' => false, 'message' => 'Order ID required']);
            return;
        }
        
        $result = $this->processToSupplier($order_id);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Order retried successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to retry order']);
        }
    }
}
