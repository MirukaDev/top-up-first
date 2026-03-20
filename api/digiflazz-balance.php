<?php
/**
 * MirukaStore - Digiflazz Balance API
 * Endpoint untuk cek saldo Digiflazz (admin only)
 */

session_start();

// Cek admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/../config/digiflazz.php';

header('Content-Type: application/json');

$digiflazz = new DigiflazzAPI();
$balance = $digiflazz->checkBalance();

echo json_encode($balance);
