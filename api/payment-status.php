<?php
/**
 * MirukaStore - Payment Status API
 * Endpoint untuk cek status pembayaran
 */

require_once __DIR__ . '/../app/controllers/PaymentController.php';

$paymentController = new PaymentController();
$paymentController->apiCheckPaymentStatus();
