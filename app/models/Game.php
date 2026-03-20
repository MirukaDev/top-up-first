<?php
/**
 * MirukaStore - Game Model
 * Model untuk mengelola data game
 */

require_once __DIR__ . '/../../config/database.php';

class Game {
    private $db;
    
    public function __construct() {
        $this->db = getDB();
    }
    
    /**
     * Get semua game yang aktif
     * 
     * @param bool $onlyActive Hanya game aktif
     * @return array
     */
    public function getAll($onlyActive = true) {
        try {
            $sql = "SELECT * FROM games";
            if ($onlyActive) {
                $sql .= " WHERE is_active = 1";
            }
            $sql .= " ORDER BY sort_order ASC, name ASC";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get All Games Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get game by ID
     * 
     * @param int $id Game ID
     * @return array|bool
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM games WHERE id = :id LIMIT 1");
            $stmt->execute([':id' => $id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Game Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get game by slug
     * 
     * @param string $slug Game slug
     * @return array|bool
     */
    public function getBySlug($slug) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM games 
                WHERE slug = :slug AND is_active = 1 
                LIMIT 1
            ");
            $stmt->execute([':slug' => $slug]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Get Game By Slug Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Create game baru
     * 
     * @param array $data Data game
     * @return bool|int
     */
    public function create($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO games (name, slug, description, image, banner, category, sort_order)
                VALUES (:name, :slug, :description, :image, :banner, :category, :sort_order)
            ");
            
            $stmt->execute([
                ':name' => $data['name'],
                ':slug' => $this->createSlug($data['name']),
                ':description' => $data['description'] ?? '',
                ':image' => $data['image'] ?? '',
                ':banner' => $data['banner'] ?? '',
                ':category' => $data['category'] ?? '',
                ':sort_order' => $data['sort_order'] ?? 0
            ]);
            
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create Game Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Update game
     * 
     * @param int $id Game ID
     * @param array $data Data yang akan diupdate
     * @return bool
     */
    public function update($id, $data) {
        try {
            $allowedFields = ['name', 'slug', 'description', 'image', 'banner', 'category', 'is_active', 'sort_order'];
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
            
            $sql = "UPDATE games SET " . implode(', ', $setFields) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update Game Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Delete game
     * 
     * @param int $id Game ID
     * @return bool
     */
    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM games WHERE id = :id");
            return $stmt->execute([':id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete Game Error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get game populer (berdasarkan jumlah transaksi)
     * 
     * @param int $limit Jumlah game
     * @return array
     */
    public function getPopular($limit = 5) {
        try {
            $stmt = $this->db->prepare("
                SELECT g.*, COUNT(t.id) as transaction_count 
                FROM games g
                LEFT JOIN transactions t ON g.id = t.game_id
                WHERE g.is_active = 1
                GROUP BY g.id
                ORDER BY transaction_count DESC, g.sort_order ASC
                LIMIT :limit
            ");
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Get Popular Games Error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Create slug dari nama game
     * 
     * @param string $name Nama game
     * @return string
     */
    private function createSlug($name) {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        $slug = trim($slug, '-');
        
        // Cek apakah slug sudah ada
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Cek apakah slug sudah ada
     * 
     * @param string $slug
     * @return bool
     */
    private function slugExists($slug) {
        $stmt = $this->db->prepare("SELECT id FROM games WHERE slug = :slug LIMIT 1");
        $stmt->execute([':slug' => $slug]);
        return $stmt->fetch() !== false;
    }
}
