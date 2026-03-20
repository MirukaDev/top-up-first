<?php
/**
 * MirukaStore - User Model
 * Model untuk mengelola data pengguna
 */

require_once __DIR__ . '/../../config/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Register user baru
     * 
     * @param array $data Data user
     * @return bool|int ID user jika berhasil, false jika gagal
     */
    public function register($data) {
        try {
            // Hash password dengan bcrypt
            $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
            
            $stmt = $this->db->prepare("
                INSERT INTO users (username, email, password, full_name, phone, role) 
                VALUES (:username, :email, :password, :full_name, :phone, :role)
            ");
            
            $stmt->execute([
                ':username' => $data['username'],
                ':email' => $data['email'],
                ':password' => $hashedPassword,
                ':full_name' => $data['full_name'] ?? null,
                ':phone' => $data['phone'] ?? null,
                ':role' => $data['role'] ?? 'user'
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("User Registration Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Login user
     * 
     * @param string $username Username atau email
     * @param string $password Password
     * @return array|bool Data user jika berhasil, false jika gagal
     */
    public function login($username, $password) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM users 
                WHERE (username = :username OR email = :email) 
                AND is_active = 1
                LIMIT 1
            ");
            
            $stmt->execute([
                ':username' => $username,
                ':email' => $username
            ]);
            
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Hapus password dari array
                unset($user['password']);
                return $user;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("User Login Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by ID
     * 
     * @param int $id User ID
     * @return array|bool Data user atau false
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, role, balance, phone, full_name, 
                       is_active, created_at, updated_at 
                FROM users WHERE id = :id LIMIT 1
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get user by username
     * 
     * @param string $username Username
     * @return array|bool Data user atau false
     */
    public function getByUsername($username) {
        try {
            $stmt = $this->db->prepare("
                SELECT id, username, email, role, balance, phone, full_name, 
                       is_active, created_at, updated_at 
                FROM users WHERE username = :username LIMIT 1
            ");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get User Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update user data
     * 
     * @param int $id User ID
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update($id, $data) {
        try {
            $allowedFields = ['username', 'email', 'full_name', 'phone', 'role', 'is_active'];
            $setFields = [];
            $params = [':id' => $id];
            
            foreach ($data as $key => $value) {
                if (in_array($key, $allowedFields)) {
                    $setFields[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }
            
            if (empty($setFields)) {
                return false;
            }
            
            $sql = "UPDATE users SET " . implode(', ', $setFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update User Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update password user
     * 
     * @param int $id User ID
     * @param string $newPassword Password baru
     * @return bool
     */
    public function updatePassword($id, $newPassword) {
        try {
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            $stmt = $this->db->prepare("UPDATE users SET password = :password WHERE id = :id");
            return $stmt->execute([':password' => $hashedPassword, ':id' => $id]);
        } catch (PDOException $e) {
            error_log("Update Password Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Tambah saldo user
     * 
     * @param int $id User ID
     * @param float $amount Jumlah saldo
     * @param string $description Keterangan
     * @param string $reference_id ID referensi
     * @return bool
     */
    public function addBalance($id, $amount, $description = '', $reference_id = '') {
        try {
            $this->db->beginTransaction();
            
            // Update balance
            $stmt = $this->db->prepare("
                UPDATE users 
                SET balance = balance + :amount 
                WHERE id = :id
            ");
            $stmt->execute([':amount' => $amount, ':id' => $id]);
            
            // Log balance
            $stmt = $this->db->prepare("
                INSERT INTO balance_logs (user_id, type, amount, description, reference_id, reference_type)
                VALUES (:user_id, 'credit', :amount, :description, :reference_id, 'deposit')
            ");
            $stmt->execute([
                ':user_id' => $id,
                ':amount' => $amount,
                ':description' => $description,
                ':reference_id' => $reference_id
            ]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Add Balance Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Kurangi saldo user
     * 
     * @param int $id User ID
     * @param float $amount Jumlah saldo
     * @param string $description Keterangan
     * @param string $reference_id ID referensi
     * @return bool
     */
    public function deductBalance($id, $amount, $description = '', $reference_id = '') {
        try {
            $this->db->beginTransaction();
            
            // Cek saldo cukup
            $user = $this->getById($id);
            if (!$user || $user['balance'] < $amount) {
                return false;
            }
            
            // Update balance
            $stmt = $this->db->prepare("
                UPDATE users 
                SET balance = balance - :amount 
                WHERE id = :id
            ");
            $stmt->execute([':amount' => $amount, ':id' => $id]);
            
            // Log balance
            $stmt = $this->db->prepare("
                INSERT INTO balance_logs (user_id, type, amount, description, reference_id, reference_type)
                VALUES (:user_id, 'debit', :amount, :description, :reference_id, 'transaction')
            ");
            $stmt->execute([
                ':user_id' => $id,
                ':amount' => $amount,
                ':description' => $description,
                ':reference_id' => $reference_id
            ]);
            
            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Deduct Balance Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get balance logs user
     * 
     * @param int $user_id User ID
     * @param int $limit Limit data
     * @return array
     */
    public function getBalanceLogs($user_id, $limit = 20) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM balance_logs 
                WHERE user_id = :user_id 
                ORDER BY created_at DESC 
                LIMIT :limit
            ");
            $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Balance Logs Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cek apakah username sudah ada
     * 
     * @param string $username Username
     * @return bool
     */
    public function usernameExists($username) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE username = :username LIMIT 1");
            $stmt->execute([':username' => $username]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Check Username Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Cek apakah email sudah ada
     * 
     * @param string $email Email
     * @return bool
     */
    public function emailExists($email) {
        try {
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
            $stmt->execute([':email' => $email]);
            return $stmt->fetch() !== false;
        } catch (PDOException $e) {
            error_log("Check Email Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get semua user (untuk admin)
     * 
     * @param array $filters Filter data
     * @param int $page Halaman
     * @param int $perpage Data per halaman
     * @return array
     */
    public function getAll($filters = [], $page = 1, $perpage = 20) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filters['role'])) {
                $where[] = "role = :role";
                $params[':role'] = $filters['role'];
            }
            
            if (!empty($filters['search'])) {
                $where[] = "(username LIKE :search OR email LIKE :search OR full_name LIKE :search)";
                $params[':search'] = '%' . $filters['search'] . '%';
            }
            
            $sql = "SELECT id, username, email, role, balance, phone, full_name, is_active, created_at FROM users";
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
            
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
            error_log("Get All Users Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Count total user
     * 
     * @param array $filters Filter data
     * @return int
     */
    public function countAll($filters = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filters['role'])) {
                $where[] = "role = :role";
                $params[':role'] = $filters['role'];
            }
            
            $sql = "SELECT COUNT(*) as total FROM users";
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Count Users Error: " . $e->getMessage());
            return 0;
        }
    }
}
