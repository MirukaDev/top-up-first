<?php
/**
 * MirukaStore - Retry Supplier API
 * Endpoint untuk retry order ke supplier (admin only)
 */

session_start();

// Cek admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../app/controllers/PaymentController.php';

$paymentController = new PaymentController();
$paymentController->retrySupplier();
