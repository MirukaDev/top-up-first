<?php
/**
 * MirukaStore - Check Status API
 * Endpoint untuk cek status transaksi
 */

require_once __DIR__ . '/../app/controllers/OrderController.php';

$orderController = new OrderController();
$orderController->apiCheckStatus();
