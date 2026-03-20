<?php
/**
 * MirukaStore - Transaction Model
 * Model untuk mengelola data transaksi
 */

require_once __DIR__ . '/../../config/database.php';

class Transaction {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Create transaksi baru
     * 
     * @param array $data Data transaksi
     * @return bool|int ID transaksi jika berhasil
     */
    public function create($data) {
        try {
            $order_id = $this->generateOrderId();
            
            $stmt = $this->db->prepare("
                INSERT INTO transactions (
                    order_id, user_id, game_id, product_id, user_game_id, server_id,
                    product_name, price, payment_method, payment_token, status, use_balance, balance_used
                ) VALUES (
                    :order_id, :user_id, :game_id, :product_id, :user_game_id, :server_id,
                    :product_name, :price, :payment_method, :payment_token, :status, :use_balance, :balance_used
                )
            ");
            
            $stmt->execute([
                ':order_id' => $order_id,
                ':user_id' => $data['user_id'] ?? null,
                ':game_id' => $data['game_id'],
                ':product_id' => $data['product_id'],
                ':user_game_id' => $data['user_game_id'],
                ':server_id' => $data['server_id'] ?? '',
                ':product_name' => $data['product_name'],
                ':price' => $data['price'],
                ':payment_method' => $data['payment_method'] ?? '',
                ':payment_token' => $data['payment_token'] ?? '',
                ':status' => $data['status'] ?? 'pending',
                ':use_balance' => $data['use_balance'] ?? 0,
                ':balance_used' => $data['balance_used'] ?? 0
            ]);
            
            return $order_id;
        } catch (PDOException $e) {
            error_log("Create Transaction Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get transaksi by order_id
     * 
     * @param string $order_id Order ID
     * @return array|bool
     */
    public function getByOrderId($order_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, 
                       g.name as game_name, g.slug as game_slug,
                       u.username, u.email, u.full_name
                FROM transactions t
                JOIN games g ON t.game_id = g.id
                LEFT JOIN users u ON t.user_id = u.id
                WHERE t.order_id = :order_id
                LIMIT 1
            ");
            $stmt->execute([':order_id' => $order_id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Transaction Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get transaksi by user ID
     * 
     * @param int $user_id User ID
     * @param int $limit Limit data
     * @return array
     */
    public function getByUserId($user_id, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT t.*, g.name as game_name, g.image as game_image
                FROM transactions t
                JOIN games g ON t.game_id = g.id
                WHERE t.user_id = :user_id
                ORDER BY t.created_at DESC
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get User Transactions Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Update status transaksi
     * 
     * @param string $order_id Order ID
     * @param string $status Status baru
     * @param array $additionalData Data tambahan
     * @return bool
     */
    public function updateStatus($order_id, $status, $additionalData = []) {
        try {
            $allowedStatus = ['pending', 'processing', 'settlement', 'success', 'failed', 'expired', 'cancelled'];
            
            if (!in_array($status, $allowedStatus)) {
                return false;
            }
            
            $setFields = ['status = :status'];
            $params = [':order_id' => $order_id, ':status' => $status];
            
            // Jika status settlement, update paid_at
            if ($status === 'settlement' || $status === 'success') {
                $setFields[] = "paid_at = NOW()";
            }
            
            // Tambahkan field tambahan jika ada
            if (!empty($additionalData['payment_method'])) {
                $setFields[] = "payment_method = :payment_method";
                $params[':payment_method'] = $additionalData['payment_method'];
            }
            
            if (!empty($additionalData['api_status'])) {
                $setFields[] = "api_status = :api_status";
                $params[':api_status'] = $additionalData['api_status'];
            }
            
            if (!empty($additionalData['api_response'])) {
                $setFields[] = "api_response = :api_response";
                $params[':api_response'] = $additionalData['api_response'];
            }
            
            if (!empty($additionalData['api_ref_id'])) {
                $setFields[] = "api_ref_id = :api_ref_id";
                $params[':api_ref_id'] = $additionalData['api_ref_id'];
            }
            
            $sql = "UPDATE transactions SET " . implode(', ', $setFields) . " WHERE order_id = :order_id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Transaction Status Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update API status
     * 
     * @param string $order_id Order ID
     * @param string $api_status Status API
     * @param string $api_response Response API
     * @return bool
     */
    public function updateApiStatus($order_id, $api_status, $api_response = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE transactions 
                SET api_status = :api_status, api_response = :api_response 
                WHERE order_id = :order_id
            ");
            return $stmt->execute([
                ':order_id' => $order_id,
                ':api_status' => $api_status,
                ':api_response' => $api_response
            ]);
        } catch (PDOException $e) {
            error_log("Update API Status Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get semua transaksi (untuk admin)
     * 
     * @param array $filters Filter
     * @param int $page Halaman
     * @param int $perpage Data per halaman
     * @return array
     */
    public function getAll($filters = [], $page = 1, $perpage = 20) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filters['status'])) {
                $where[] = "t.status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['user_id'])) {
                $where[] = "t.user_id = :user_id";
                $params[':user_id'] = $filters['user_id'];
            }
            
            if (!empty($filters['game_id'])) {
                $where[] = "t.game_id = :game_id";
                $params[':game_id'] = $filters['game_id'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(t.order_id LIKE :search OR t.user_game_id LIKE :search OR u.username LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            if (!empty($filters['date_from'])) {
                $where[] = "DATE(t.created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            if (!empty($filters['date_to'])) {
                $where[] = "DATE(t.created_at) <= :date_to";
                $params[':date_to'] = $filters['date_to'];
            }
            
            $sql = "SELECT t.*, g.name as game_name, u.username, u.full_name 
                    FROM transactions t
                    JOIN games g ON t.game_id = g.id
                    LEFT JOIN users u ON t.user_id = u.id";
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY t.created_at DESC LIMIT :limit OFFSET :offset";
            
            $offset = ($page - 1) * $perpage;
            
            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            $stmt->bindValue(':limit', $perpage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Transactions Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total transaksi
     * 
     * @param array $filters Filter
     * @return int
     */
    public function countAll($filters = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filters['status'])) {
                $where[] = "status = :status";
                $params[':status'] = $filters['status'];
            }
            
            if (!empty($filters['date_from'])) {
                $where[] = "DATE(created_at) >= :date_from";
                $params[':date_from'] = $filters['date_from'];
            }
            
            $sql = "SELECT COUNT(*) as total FROM transactions";
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Count Transactions Error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * Get statistik transaksi
     * 
     * @param string $period Period (today, week, month, all)
     * @return array
     */
    public function getStatistics($period = 'today') {
        try {
            $dateFilter = '';
            
            switch ($period) {
                case 'today':
                    $dateFilter = "DATE(created_at) = CURDATE()";
                    break;
                case 'week':
                    $dateFilter = "created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    break;
                case 'month':
                    $dateFilter = "created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
                    break;
                default:
                    $dateFilter = "1=1";
            }
            
            // Total transaksi
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total, SUM(price) as revenue 
                FROM transactions 
                WHERE {$dateFilter}
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            // Transaksi sukses
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total, SUM(price) as revenue 
                FROM transactions 
                WHERE {$dateFilter} AND status IN ('settlement', 'success')
            ");
            $stmt->execute();
            $success = $stmt->fetch();
            
            // Transaksi pending
            $stmt = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM transactions 
                WHERE {$dateFilter} AND status = 'pending'
            ");
            $stmt->execute();
            $pending = $stmt->fetch();
            
            return [
                'total_transactions' => $result['total'] ?? 0,
                'total_revenue' => $result['revenue'] ?? 0,
                'success_transactions' => $success['total'] ?? 0,
                'success_revenue' => $success['revenue'] ?? 0,
                'pending_transactions' => $pending['total'] ?? 0
            ];
        } catch (PDOException $e) {
            error_log("Get Statistics Error: " . $e->getMessage());
            return [
                'total_transactions' => 0,
                'total_revenue' => 0,
                'success_transactions' => 0,
                'success_revenue' => 0,
                'pending_transactions' => 0
            ];
        }
    }
    
    /**
     * Generate order ID unik
     * 
     * @return string
     */
    private function generateOrderId() {
        $prefix = 'MRK';
        $date = date('Ymd');
        $random = strtoupper(substr(uniqid(), -6));
        return $prefix . $date . $random;
    }
    
    /**
     * Cek status transaksi
     * 
     * @param string $order_id Order ID
     * @return string|false
     */
    public function checkStatus($order_id) {
        try {
            $stmt = $this->db->prepare("
                SELECT status FROM transactions WHERE order_id = :order_id LIMIT 1
            ");
            $stmt->execute([':order_id' => $order_id]);
            $result = $stmt->fetch();
            
            return $result ? $result['status'] : false;
        } catch (PDOException $e) {
            error_log("Check Status Error: " . $e->getMessage());
            return false;
        }
    }
}
