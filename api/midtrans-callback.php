<?php
/**
 * MirukaStore - Midtrans Callback API
 * Endpoint untuk menerima notifikasi pembayaran dari Midtrans
 */

require_once __DIR__ . '/../app/controllers/PaymentController.php';

$paymentController = new PaymentController();
$paymentController->midtransCallback();
