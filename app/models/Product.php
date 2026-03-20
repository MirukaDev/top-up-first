<?php
/**
 * MirukaStore - Product Model
 * Model untuk mengelola data produk/diamond
 */

require_once __DIR__ . '/../../config/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get semua produk
     * 
     * @param int|null $game_id Filter by game ID
     * @param bool $onlyActive Hanya produk aktif
     * @return array
     */
    public function getAll($game_id = null, $onlyActive = true) {
        try {
            $sql = "SELECT p.*, g.name as game_name, g.slug as game_slug 
                    FROM products p 
                    JOIN games g ON p.game_id = g.id";
            
            $where = [];
            $params = [];
            
            if ($game_id) {
                $where[] = "p.game_id = :game_id";
                $params[':game_id'] = $game_id;
            }
            
            if ($onlyActive) {
                $where[] = "p.is_active = 1 AND g.is_active = 1";
            }
            
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY p.sort_order ASC, p.price ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Products Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get produk by ID
     * 
     * @param int $id Product ID
     * @return array|bool
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, g.name as game_name, g.slug as game_slug 
                FROM products p 
                JOIN games g ON p.game_id = g.id 
                WHERE p.id = :id 
                LIMIT 1
            ");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Product Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get produk by game ID
     * 
     * @param int $game_id Game ID
     * @param string $role Role user (untuk harga reseller)
     * @return array
     */
    public function getByGameId($game_id, $role = 'user') {
        try {
            $priceColumn = ($role === 'reseller') ? 'reseller_price' : 'price';
            
            $stmt = $this->db->prepare("
                SELECT p.id, p.game_id, p.product_code, p.name, p.description, 
                       p.{$priceColumn} as price, p.icon, p.supplier_code
                FROM products p 
                JOIN games g ON p.game_id = g.id 
                WHERE p.game_id = :game_id 
                AND p.is_active = 1 AND g.is_active = 1
                ORDER BY p.sort_order ASC, p.price ASC
            ");
            $stmt->execute([':game_id' => $game_id]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Products By Game Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create produk baru
     * 
     * @param array $data Data produk
     * @return bool|int
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO products (game_id, product_code, name, description, price, reseller_price, supplier_code, icon, sort_order)
                VALUES (:game_id, :product_code, :name, :description, :price, :reseller_price, :supplier_code, :icon, :sort_order)
            ");
            
            $stmt->execute([
                ':game_id' => $data['game_id'],
                ':product_code' => $data['product_code'],
                ':name' => $data['name'],
                ':description' => $data['description'] ?? '',
                ':price' => $data['price'],
                ':reseller_price' => $data['reseller_price'] ?? $data['price'],
                ':supplier_code' => $data['supplier_code'] ?? '',
                ':icon' => $data['icon'] ?? '',
                ':sort_order' => $data['sort_order'] ?? 0
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Product Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update produk
     * 
     * @param int $id Product ID
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update($id, $data) {
        try {
            $allowedFields = ['game_id', 'product_code', 'name', 'description', 'price', 'reseller_price', 'supplier_code', 'icon', 'is_active', 'sort_order'];
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
            
            $sql = "UPDATE products SET " . implode(', ', $setFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Product Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete produk
     * 
     * @param int $id Product ID
     * @return bool
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete Product Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get harga produk berdasarkan role
     * 
     * @param int $product_id Product ID
     * @param string $role Role user
     * @return float|false
     */
    public function getPrice($product_id, $role = 'user') {
        try {
            $priceColumn = ($role === 'reseller') ? 'reseller_price' : 'price';
            
            $stmt = $this->db->prepare("
                SELECT {$priceColumn} as price 
                FROM products 
                WHERE id = :id AND is_active = 1 
                LIMIT 1
            ");
            $stmt->execute([':id' => $product_id]);
            $result = $stmt->fetch();
            
            return $result ? $result['price'] : false;
        } catch (PDOException $e) {
            error_log("Get Price Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Sinkronisasi produk dari API supplier
     * 
     * @param array $apiProducts Data produk dari API
     * @param int $game_id Game ID
     * @return array Hasil sinkronisasi
     */
    public function syncFromAPI($apiProducts, $game_id) {
        $synced = 0;
        $failed = 0;
        
        foreach ($apiProducts as $apiProduct) {
            // Cek apakah produk sudah ada
            $stmt = $this->db->prepare("
                SELECT id FROM products 
                WHERE supplier_code = :supplier_code AND game_id = :game_id 
                LIMIT 1
            ");
            $stmt->execute([
                ':supplier_code' => $apiProduct['code'],
                ':game_id' => $game_id
            ]);
            $existing = $stmt->fetch();
            
            $data = [
                'game_id' => $game_id,
                'product_code' => $apiProduct['code'],
                'name' => $apiProduct['name'],
                'price' => $apiProduct['price'] * 1.1, // Tambah 10% margin
                'reseller_price' => $apiProduct['price'] * 1.05, // Tambah 5% untuk reseller
                'supplier_code' => $apiProduct['code'],
                'is_active' => 1
            ];
            
            if ($existing) {
                // Update produk yang sudah ada
                if ($this->update($existing['id'], $data)) {
                    $synced++;
                } else {
                    $failed++;
                }
            } else {
                // Buat produk baru
                if ($this->create($data)) {
                    $synced++;
                } else {
                    $failed++;
                }
            }
        }
        
        return ['synced' => $synced, 'failed' => $failed];
    }
    
    /**
     * Search produk
     * 
     * @param string $keyword Keyword pencarian
     * @return array
     */
    public function search($keyword) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, g.name as game_name, g.slug as game_slug 
                FROM products p 
                JOIN games g ON p.game_id = g.id 
                WHERE (p.name LIKE :keyword OR p.description LIKE :keyword OR g.name LIKE :keyword)
                AND p.is_active = 1 AND g.is_active = 1
                ORDER BY p.name ASC
            ");
            $stmt->execute([':keyword' => '%' . $keyword . '%']);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Search Products Error: " . $e->getMessage());
            return [];
        }
    }
}
