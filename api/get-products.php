<?php
/**
 * MirukaStore - Get Products API
 * Endpoint untuk mengambil daftar produk berdasarkan game
 */

require_once __DIR__ . '/../app/controllers/HomeController.php';

$homeController = new HomeController();
$homeController->apiGetProducts();
