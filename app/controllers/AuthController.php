<?php
/**
 * MirukaStore - Auth Controller
 * Controller untuk mengelola autentikasi user
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        
        // Start session jika belum
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }
    
    /**
     * Halaman login
     */
    public function login() {
        // Jika sudah login, redirect ke home
        if ($this->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        $error = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $username = $this->sanitizeInput($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    $error = 'Username dan password wajib diisi';
                } else {
                    $user = $this->userModel->login($username, $password);
                    
                    if ($user) {
                        // Set session
                        $_SESSION['user_id'] = $user['id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['role'] = $user['role'];
                        $_SESSION['logged_in'] = true;
                        
                        // Redirect ke halaman sebelumnya atau home
                        $redirect = $_SESSION['redirect_after_login'] ?? '/';
                        unset($_SESSION['redirect_after_login']);
                        
                        header('Location: ' . $redirect);
                        exit;
                    } else {
                        $error = 'Username atau password salah';
                    }
                }
            }
        }
        
        // Generate CSRF token
        $csrf_token = $this->generateCsrfToken();
        
        // Load view
        include __DIR__ . '/../../views/auth/login.php';
    }
    
    /**
     * Halaman register
     */
    public function register() {
        // Jika sudah login, redirect ke home
        if ($this->isLoggedIn()) {
            header('Location: /');
            exit;
        }
        
        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi CSRF token
            if (!$this->validateCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid security token';
            } else {
                $data = [
                    'username' => $this->sanitizeInput($_POST['username'] ?? ''),
                    'email' => $this->sanitizeInput($_POST['email'] ?? ''),
                    'password' => $_POST['password'] ?? '',
                    'full_name' => $this->sanitizeInput($_POST['full_name'] ?? ''),
                    'phone' => $this->sanitizeInput($_POST['phone'] ?? '')
                ];
                
                // Validasi input
                $validation = $this->validateRegistration($data);
                
                if ($validation !== true) {
                    $error = $validation;
                } else {
                    // Register user
                    $userId = $this->userModel->register($data);
                    
                    if ($userId) {
                        $success = 'Registrasi berhasil! Silakan login.';
                    } else {
                        $error = 'Registrasi gagal. Silakan coba lagi.';
                    }
                }
            }
        }
        
        // Generate CSRF token
        $csrf_token = $this->generateCsrfToken();
        
        // Load view
        include __DIR__ . '/../../views/auth/register.php';
    }
    
    /**
     * Logout user
     */
    public function logout() {
        // Hapus semua session
        $_SESSION = [];
        
        // Hapus cookie session
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        header('Location: /login');
        exit;
    }
    
    /**
     * Validasi registrasi
     * 
     * @param array $data Data registrasi
     * @return true|string True jika valid, pesan error jika tidak
     */
    private function validateRegistration($data) {
        // Validasi username
        if (strlen($data['username']) < 4) {
            return 'Username minimal 4 karakter';
        }
        
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $data['username'])) {
            return 'Username hanya boleh mengandung huruf, angka, dan underscore';
        }
        
        if ($this->userModel->usernameExists($data['username'])) {
            return 'Username sudah digunakan';
        }
        
        // Validasi email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return 'Email tidak valid';
        }
        
        if ($this->userModel->emailExists($data['email'])) {
            return 'Email sudah terdaftar';
        }
        
        // Validasi password
        if (strlen($data['password']) < 6) {
            return 'Password minimal 6 karakter';
        }
        
        // Validasi phone (opsional)
        if (!empty($data['phone']) && !preg_match('/^[0-9]{10,15}$/', $data['phone'])) {
            return 'Nomor telepon tidak valid';
        }
        
        return true;
    }
    
    /**
     * Cek apakah user sudah login
     * 
     * @return bool
     */
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    /**
     * Get current user data
     * 
     * @return array|null
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->getById($_SESSION['user_id']);
    }
    
    /**
     * Require login
     * Redirect ke login jika belum login
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: /login');
            exit;
        }
    }
    
    /**
     * Require admin role
     */
    public function requireAdmin() {
        $this->requireLogin();
        
        if ($_SESSION['role'] !== 'admin') {
            header('Location: /');
            exit;
        }
    }
    
    /**
     * Generate CSRF token
     * 
     * @return string
     */
    public function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Validasi CSRF token
     * 
     * @param string $token
     * @return bool
     */
    public function validateCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Sanitize input
     * 
     * @param string $input
     * @return string
     */
    private function sanitizeInput($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }
}
