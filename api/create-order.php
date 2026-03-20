<?php
/**
 * MirukaStore - Create Order API
 * Endpoint untuk membuat order baru
 */

require_once __DIR__ . '/../app/controllers/OrderController.php';

$orderController = new OrderController();
$orderController->create();
