<?php
/**
 * MirukaStore - Digiflazz API Configuration
 * Konfigurasi untuk integrasi API supplier Digiflazz
 */

// Digiflazz API Credentials
define('DIGIFLAZZ_USERNAME', 'your_digiflazz_username');
define('DIGIFLAZZ_API_KEY', 'your_digiflazz_api_key');
define('DIGIFLAZZ_API_URL', 'https://api.digiflazz.com/v1');

/**
 * Class DigiflazzAPI
 * Mengelola komunikasi dengan API Digiflazz
 */
class DigiflazzAPI {
    
    private $username;
    private $api_key;
    private $api_url;
    
    public function __construct() {
        $this->username = DIGIFLAZZ_USERNAME;
        $this->api_key = DIGIFLAZZ_API_KEY;
        $this->api_url = DIGIFLAZZ_API_URL;
    }
    
    /**
     * Generate signature untuk request Digiflazz
     * 
     * @param string $ref_id Reference ID unik
     * @return string Signature MD5
     */
    private function generateSignature($ref_id = '') {
        if ($ref_id) {
            return md5($this->username . $this->api_key . $ref_id);
        }
        return md5($this->username . $this->api_key);
    }
    
    /**
     * Cek saldo Digiflazz
     * 
     * @return array Response dari API
     */
    public function checkBalance() {
        $payload = [
            'cmd' => 'deposit',
            'username' => $this->username,
            'sign' => $this->generateSignature()
        ];
        
        return $this->sendRequest('/cek-saldo', $payload);
    }
    
    /**
     * Ambil daftar produk dari Digiflazz
     * 
     * @param string $brand Filter berdasarkan brand (opsional)
     * @return array Response dari API
     */
    public function getProducts($brand = '') {
        $payload = [
            'cmd' => 'prepaid',
            'username' => $this->username,
            'sign' => $this->generateSignature()
        ];
        
        if ($brand) {
            $payload['code'] = $brand;
        }
        
        return $this->sendRequest('/price-list', $payload);
    }
    
    /**
     * Kirim transaksi top up ke Digiflazz
     * 
     * @param string $ref_id Reference ID unik (order_id)
     * @param string $product_code Kode produk
     * @param string $customer_no Nomor customer (User ID)
     * @param string $server_id Server ID (opsional)
     * @return array Response dari API
     */
    public function createTransaction($ref_id, $product_code, $customer_no, $server_id = '') {
        $buyer_sku_code = $product_code;
        $customer_no = $server_id ? $customer_no . '-' . $server_id : $customer_no;
        
        $payload = [
            'username' => $this->username,
            'buyer_sku_code' => $buyer_sku_code,
            'customer_no' => $customer_no,
            'ref_id' => $ref_id,
            'sign' => $this->generateSignature($ref_id)
        ];
        
        return $this->sendRequest('/transaction', $payload);
    }
    
    /**
     * Cek status transaksi
     * 
     * @param string $ref_id Reference ID
     * @return array Response dari API
     */
    public function checkTransactionStatus($ref_id) {
        $payload = [
            'username' => $this->username,
            'ref_id' => $ref_id,
            'sign' => $this->generateSignature($ref_id)
        ];
        
        return $this->sendRequest('/transaction', $payload);
    }
    
    /**
     * Send HTTP request ke Digiflazz API
     * 
     * @param string $endpoint API endpoint
     * @param array $payload Data payload
     * @return array Response
     */
    private function sendRequest($endpoint, $payload) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("Digiflazz API Error: " . $error);
            return ['status' => 'error', 'message' => $error];
        }
        
        return json_decode($response, true);
    }
}

/**
 * VIP Reseller API Alternative
 * Jika menggunakan VIP Reseller sebagai supplier
 */
class VIPResellerAPI {
    
    private $api_key;
    private $api_url = 'https://vip-reseller.co.id/api';
    
    public function __construct() {
        $this->api_key = 'your_vip_reseller_api_key';
    }
    
    /**
     * Cek saldo VIP Reseller
     */
    public function checkBalance() {
        return $this->sendRequest('/profile', ['api_key' => $this->api_key]);
    }
    
    /**
     * Ambil daftar layanan
     */
    public function getServices($type = 'games') {
        return $this->sendRequest('/services', [
            'api_key' => $this->api_key,
            'type' => $type
        ]);
    }
    
    /**
     * Buat order baru
     */
    public function createOrder($service_id, $target, $additional_data = '') {
        return $this->sendRequest('/order', [
            'api_key' => $this->api_key,
            'service_id' => $service_id,
            'target' => $target,
            'additional_data' => $additional_data
        ]);
    }
    
    /**
     * Cek status order
     */
    public function checkOrder($order_id) {
        return $this->sendRequest('/status', [
            'api_key' => $this->api_key,
            'order_id' => $order_id
        ]);
    }
    
    private function sendRequest($endpoint, $params) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url . $endpoint);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        curl_close($ch);
        
        return json_decode($response, true);
    }
}
