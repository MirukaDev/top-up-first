# MirukaStore - Platform Top Up Game

MirukaStore adalah platform top up game otomatis yang terintegrasi dengan payment gateway (Midtrans) dan API supplier (Digiflazz/VIP Reseller). Dibangun dengan PHP native menggunakan struktur MVC.

## Fitur Utama

### 👤 User
- ✅ Register & Login dengan password hashing (bcrypt)
- ✅ Session management
- ✅ Halaman utama dengan daftar game populer
- ✅ Halaman top up dengan input User ID & Server
- ✅ Pilihan nominal dari database
- ✅ Integrasi Midtrans (QRIS, DANA, OVO, GoPay, Bank Transfer)
- ✅ Riwayat transaksi realtime
- ✅ Sistem saldo & deposit

### 💳 Payment Gateway (Midtrans)
- ✅ Snap API integration
- ✅ Webhook callback untuk update status
- ✅ Multiple payment methods
- ✅ Auto retry mechanism

### 🔌 Auto Top Up (API Supplier)
- ✅ Digiflazz API integration
- ✅ Cek saldo API
- ✅ Ambil daftar produk otomatis
- ✅ Kirim transaksi top up otomatis setelah pembayaran sukses
- ✅ Logging API response

### 💰 Sistem Saldo & Reseller
- ✅ Role: User, Reseller, Admin
- ✅ Top up saldo via Midtrans
- ✅ Harga khusus reseller
- ✅ Balance logs

### 🛠️ Admin Panel
- ✅ Dashboard dengan statistik
- ✅ Kelola game (CRUD)
- ✅ Kelola produk (CRUD + Sync API)
- ✅ Manajemen transaksi
- ✅ Manajemen user & reseller
- ✅ Update status transaksi manual

## Teknologi

- **Backend**: PHP 7.4+ (Native, MVC Pattern)
- **Database**: MySQL dengan PDO
- **Frontend**: Tailwind CSS, Font Awesome
- **Payment**: Midtrans Snap API
- **Supplier**: Digiflazz API / VIP Reseller API

## Instalasi

### 1. Clone/Download Repository
```bash
cd /path/to/htdocs
git clone https://github.com/username/mirukastore.git
```

### 2. Import Database
```bash
mysql -u root -p
CREATE DATABASE mirukastore;
USE mirukastore;
SOURCE database/schema.sql;
```

### 3. Konfigurasi Database
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'mirukastore');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### 4. Konfigurasi Midtrans
Edit file `config/midtrans.php`:
```php
define('MIDTRANS_SERVER_KEY', 'YOUR_SERVER_KEY');
define('MIDTRANS_CLIENT_KEY', 'YOUR_CLIENT_KEY');
define('MIDTRANS_IS_PRODUCTION', false); // true untuk production
```

### 5. Konfigurasi Digiflazz
Edit file `config/digiflazz.php`:
```php
define('DIGIFLAZZ_USERNAME', 'your_username');
define('DIGIFLAZZ_API_KEY', 'your_api_key');
```

### 6. Konfigurasi Web Server (Apache)
Pastikan mod_rewrite aktif dan pointing ke folder project.

### 7. Akses Website
```
http://localhost/MirukaStore/
```

Default admin:
- Username: `admin`
- Password: `admin123`

## Struktur Folder

```
MirukaStore/
├── config/                 # Konfigurasi
│   ├── database.php       # Database config
│   ├── midtrans.php       # Midtrans config
│   └── digiflazz.php      # Digiflazz config
├── app/
│   ├── controllers/       # Controllers
│   ├── models/           # Models
│   └── middleware/       # Middleware
├── views/                # Views
│   ├── layouts/         # Layouts
│   ├── auth/           # Auth pages
│   ├── home/          # Home pages
│   ├── order/         # Order pages
│   └── admin/         # Admin pages
├── api/                 # API endpoints
├── assets/             # CSS, JS, Images
├── database/          # Database schema
├── index.php         # Main router
└── .htaccess        # Apache config
```

## API Endpoints

### Midtrans Callback
```
POST /api/midtrans-callback.php
```

### Check Status
```
GET /api/check-status.php?order_id=MRK20240101123456
```

### Get Products
```
GET /api/get-products.php?game_id=1
```

### Create Order
```
POST /api/create-order.php
```

## Keamanan

- ✅ Password hashing dengan bcrypt
- ✅ Prepared statements (PDO)
- ✅ CSRF protection
- ✅ XSS protection dengan htmlspecialchars
- ✅ Input validation
- ✅ Signature verification untuk Midtrans callback

## Troubleshooting

### 404 Not Found
- Pastikan mod_rewrite aktif
- Cek konfigurasi .htaccess

### Database Connection Error
- Cek konfigurasi database.php
- Pastikan MySQL running

### Midtrans Error
- Cek Server Key dan Client Key
- Pastikan menggunakan key yang benar (sandbox/production)

### API Supplier Error
- Cek username dan API key
- Pastikan saldo mencukupi

## Lisensi

MIT License - Bebas digunakan untuk personal dan komersial.

## Kontak

- WhatsApp: 0812-1974-8457
- Email: support@mirukastore.com

---

Dibuat dengan ❤️ oleh MirukaStore Team
