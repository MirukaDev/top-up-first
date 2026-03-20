<?php
/**
 * MirukaStore - Main Router
 * Entry point untuk aplikasi
 */

// Error reporting (matikan di production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get request URI
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$request_uri = trim($request_uri, '/');

// Remove query string
$request_uri = explode('?', $request_uri)[0];

// Define routes
$routes = [
    // Home
    '' => ['HomeController', 'index'],
    
    // Auth
    'login' => ['AuthController', 'login'],
    'register' => ['AuthController', 'register'],
    'logout' => ['AuthController', 'logout'],
    
    // Game
    'game/([a-z0-9-]+)' => ['HomeController', 'game'],
    
    // Order
    'order/create' => ['OrderController', 'create'],
    'order/success' => ['OrderController', 'success'],
    'order/invoice' => ['OrderController', 'invoice'],
    'order/history' => ['OrderController', 'history'],
    
    // Cek Transaksi
    'cek-transaksi' => ['HomeController', 'cekTransaksi'],
    
    // Kontak & Tentang
    'kontak' => ['HomeController', 'kontak'],
    'tentang' => ['HomeController', 'tentang'],
    
    // Search
    'search' => ['HomeController', 'search'],
    
    // Admin Dashboard
    'admin' => ['AdminController', 'index'],
    
    // Admin Games
    'admin/games' => ['AdminController', 'games'],
    'admin/games/add' => ['AdminController', 'addGame'],
    'admin/games/edit' => ['AdminController', 'editGame'],
    'admin/games/delete' => ['AdminController', 'deleteGame'],
    
    // Admin Products
    'admin/products' => ['AdminController', 'products'],
    'admin/products/add' => ['AdminController', 'addProduct'],
    'admin/products/edit' => ['AdminController', 'editProduct'],
    'admin/products/delete' => ['AdminController', 'deleteProduct'],
    'admin/products/sync' => ['AdminController', 'syncProducts'],
    
    // Admin Transactions
    'admin/transactions' => ['AdminController', 'transactions'],
    'admin/transactions/detail' => ['AdminController', 'transactionDetail'],
    'admin/transactions/update-status' => ['AdminController', 'updateTransactionStatus'],
    
    // Admin Users
    'admin/users' => ['AdminController', 'users'],
    'admin/users/edit' => ['AdminController', 'editUser'],
    'admin/users/add-balance' => ['AdminController', 'addBalance'],
    
    // Payment Callbacks
    'payment/midtrans-callback' => ['PaymentController', 'midtransCallback'],
    'payment/finish' => ['PaymentController', 'midtransFinish'],
    'payment/error' => ['PaymentController', 'midtransError'],
];

// Match route
$matched = false;
$params = [];

foreach ($routes as $pattern => $handler) {
    $pattern = '#^' . $pattern . '$#';
    if (preg_match($pattern, $request_uri, $matches)) {
        $matched = true;
        $controllerName = $handler[0];
        $methodName = $handler[1];
        
        // Extract parameters
        if (count($matches) > 1) {
            $params = array_slice($matches, 1);
        }
        
        break;
    }
}

// If no route matched, show 404
if (!$matched) {
    http_response_code(404);
    include __DIR__ . '/views/errors/404.php';
    exit;
}

// Load controller
try {
    $controllerFile = __DIR__ . '/app/controllers/' . $controllerName . '.php';
    
    if (!file_exists($controllerFile)) {
        throw new Exception("Controller not found: " . $controllerName);
    }
    
    require_once $controllerFile;
    
    if (!class_exists($controllerName)) {
        throw new Exception("Controller class not found: " . $controllerName);
    }
    
    $controller = new $controllerName();
    
    if (!method_exists($controller, $methodName)) {
        throw new Exception("Method not found: " . $methodName);
    }
    
    // Call method with parameters
    call_user_func_array([$controller, $methodName], $params);
    
} catch (Exception $e) {
    error_log("Router Error: " . $e->getMessage());
    http_response_code(500);
    
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Internal server error']);
    } else {
        echo "<h1>500 Internal Server Error</h1>";
        echo "<p>" . htmlspecialchars($e->getMessage()) . "</p>";
    }
}
