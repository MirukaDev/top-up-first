--
-- MirukaStore Database Schema
-- Database untuk platform top up game otomatis
--

-- Buat database
CREATE DATABASE IF NOT EXISTS mirukastore CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mirukastore;

-- Tabel Users
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('user', 'reseller', 'admin') DEFAULT 'user',
    balance DECIMAL(15, 2) DEFAULT 0.00,
    phone VARCHAR(20),
    full_name VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Games
CREATE TABLE games (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    image VARCHAR(255),
    banner VARCHAR(255),
    category VARCHAR(50),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active),
    INDEX idx_sort (sort_order)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Products
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    game_id INT NOT NULL,
    product_code VARCHAR(50) NOT NULL,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(15, 2) NOT NULL,
    reseller_price DECIMAL(15, 2) NOT NULL,
    supplier_code VARCHAR(50),
    icon VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    INDEX idx_game (game_id),
    INDEX idx_code (product_code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Transactions
CREATE TABLE transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id VARCHAR(50) NOT NULL UNIQUE,
    user_id INT,
    game_id INT NOT NULL,
    product_id INT NOT NULL,
    user_game_id VARCHAR(100) NOT NULL,
    server_id VARCHAR(50),
    product_name VARCHAR(100),
    price DECIMAL(15, 2) NOT NULL,
    payment_method VARCHAR(50),
    payment_token VARCHAR(255),
    status ENUM('pending', 'processing', 'settlement', 'success', 'failed', 'expired', 'cancelled') DEFAULT 'pending',
    paid_at TIMESTAMP NULL,
    api_status ENUM('pending', 'success', 'failed', 'processing') DEFAULT 'pending',
    api_response TEXT,
    api_ref_id VARCHAR(100),
    use_balance TINYINT(1) DEFAULT 0,
    balance_used DECIMAL(15, 2) DEFAULT 0.00,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (game_id) REFERENCES games(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_order_id (order_id),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Balance Logs
CREATE TABLE balance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type ENUM('credit', 'debit') NOT NULL,
    amount DECIMAL(15, 2) NOT NULL,
    description VARCHAR(255),
    reference_id VARCHAR(100),
    reference_type VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_type (type),
    INDEX idx_created (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Payment Methods
CREATE TABLE payment_methods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(100) NOT NULL,
    type ENUM('e-wallet', 'bank_transfer', 'qris', 'retail') NOT NULL,
    fee_fixed DECIMAL(15, 2) DEFAULT 0.00,
    fee_percent DECIMAL(5, 2) DEFAULT 0.00,
    icon VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Vouchers
CREATE TABLE vouchers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    type ENUM('percentage', 'fixed') NOT NULL,
    value DECIMAL(15, 2) NOT NULL,
    min_order DECIMAL(15, 2) DEFAULT 0.00,
    max_discount DECIMAL(15, 2) DEFAULT 0.00,
    usage_limit INT DEFAULT NULL,
    usage_count INT DEFAULT 0,
    valid_from TIMESTAMP,
    valid_until TIMESTAMP,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_code (code),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Settings
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL UNIQUE,
    `value` TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data default

-- Games default
INSERT INTO games (name, slug, description, image, banner, category, sort_order) VALUES
('Mobile Legends', 'mobile-legends', 'Top up Diamond Mobile Legends murah dan cepat', 'mlbb.jpg', 'mlbb-banner.jpg', 'MOBA', 1),
('Free Fire', 'free-fire', 'Top up Diamond Free Fire termurah', 'ff.jpg', 'ff-banner.jpg', 'Battle Royale', 2),
('PUBG Mobile', 'pubg-mobile', 'Top up UC PUBG Mobile instant', 'pubg.jpg', 'pubg-banner.jpg', 'Battle Royale', 3),
('Genshin Impact', 'genshin-impact', 'Top up Genesis Crystal Genshin Impact', 'genshin.jpg', 'genshin-banner.jpg', 'RPG', 4),
('Valorant', 'valorant', 'Top up VP Valorant', 'valorant.jpg', 'valorant-banner.jpg', 'FPS', 5);

-- Payment Methods default
INSERT INTO payment_methods (code, name, type, fee_fixed, fee_percent, icon, sort_order) VALUES
('qris', 'QRIS', 'qris', 0, 0.7, 'qris.png', 1),
('dana', 'DANA', 'e-wallet', 0, 1.5, 'dana.png', 2),
('ovo', 'OVO', 'e-wallet', 0, 1.5, 'ovo.png', 3),
('gopay', 'GoPay', 'e-wallet', 0, 1.5, 'gopay.png', 4),
('bca', 'BCA Virtual Account', 'bank_transfer', 4000, 0, 'bca.png', 5),
('bni', 'BNI Virtual Account', 'bank_transfer', 4000, 0, 'bni.png', 6),
('bri', 'BRI Virtual Account', 'bank_transfer', 4000, 0, 'bri.png', 7),
('mandiri', 'Mandiri Virtual Account', 'bank_transfer', 4000, 0, 'mandiri.png', 8);

-- Settings default
INSERT INTO settings (`key`, `value`) VALUES
('site_name', 'MirukaStore'),
('site_description', 'Platform top up game cepat, murah, dan terpercaya'),
('site_logo', 'logo.png'),
('contact_whatsapp', '081219748457'),
('contact_email', 'support@mirukastore.com'),
('min_deposit', '10000'),
('maintenance_mode', '0'),
('midtrans_status', 'sandbox');

-- Admin user default (password: admin123)
INSERT INTO users (username, email, password, role, full_name) VALUES
('admin', 'admin@mirukastore.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 'Administrator');
