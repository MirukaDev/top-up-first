<?php
/**
 * MirukaStore - Midtrans Payment Gateway Configuration
 * Konfigurasi untuk integrasi Midtrans Snap API
 */

// Midtrans Sandbox Credentials (untuk development)
define('MIDTRANS_SERVER_KEY', 'SB-Mid-server-XXXXXXXXXXXXXXXXXXXX');
define('MIDTRANS_CLIENT_KEY', 'SB-Mid-client-XXXXXXXXXXXXXXXXXXXX');
define('MIDTRANS_IS_PRODUCTION', false);
define('MIDTRANS_IS_SANITIZED', true);
define('MIDTRANS_IS_3DS', true);

// URL Midtrans
if (MIDTRANS_IS_PRODUCTION) {
    define('MIDTRANS_SNAP_URL', 'https://app.midtrans.com/snap/snap.js');
    define('MIDTRANS_API_URL', 'https://app.midtrans.com/snap/v1/transactions');
} else {
    define('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js');
    define('MIDTRANS_API_URL', 'https://app.sandbox.midtrans.com/snap/v1/transactions');
}

/**
 * Class MidtransConfig
 * Mengelola konfigurasi Midtrans
 */
class MidtransConfig {
    
    /**
     * Generate Snap Token untuk pembayaran
     * 
     * @param string $order_id ID order unik
     * @param float $amount Jumlah pembayaran
     * @param array $customer_data Data customer
     * @return string Snap token
     */
    public static function getSnapToken($order_id, $amount, $customer_data = []) {
        $server_key = MIDTRANS_SERVER_KEY;
        
        $params = [
            'transaction_details' => [
                'order_id' => $order_id,
                'gross_amount' => (int) $amount,
            ],
            'customer_details' => [
                'first_name' => $customer_data['name'] ?? 'Customer',
                'email' => $customer_data['email'] ?? 'customer@example.com',
                'phone' => $customer_data['phone'] ?? '08123456789',
            ],
            'item_details' => [
                [
                    'id' => $customer_data['product_code'] ?? 'ITEM001',
                    'price' => (int) $amount,
                    'quantity' => 1,
                    'name' => $customer_data['product_name'] ?? 'Top Up Game',
                ]
            ]
        ];
        
        $headers = [
            'Accept: application/json',
            'Content-Type: application/json',
            'Authorization: Basic ' . base64_encode($server_key . ':')
        ];
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, MIDTRANS_API_URL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code == 201) {
            $result = json_decode($response, true);
            return $result['token'] ?? null;
        }
        
        error_log("Midtrans Error: " . $response);
        return null;
    }
    
    /**
     * Verifikasi signature key dari callback Midtrans
     * 
     * @param string $order_id Order ID
     * @param string $status_code Status code
     * @param string $gross_amount Gross amount
     * @param string $signature_key Signature key dari Midtrans
     * @return bool Valid atau tidak
     */
    public static function verifySignature($order_id, $status_code, $gross_amount, $signature_key) {
        $hash = hash('sha512', $order_id . $status_code . $gross_amount . MIDTRANS_SERVER_KEY);
        return $hash === $signature_key;
    }
}
