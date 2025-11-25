-- =============================================
-- Database: restaurant_db
-- Restaurant Management System
-- =============================================

-- Create Database
CREATE DATABASE IF NOT EXISTS restaurant_db;
USE restaurant_db;

-- =============================================
-- 1. TABEL USERS
-- =============================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'cashier', 'manager') DEFAULT 'cashier',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 2. TABEL CATEGORIES
-- =============================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 3. TABEL PRODUCTS
-- =============================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    cost_price DECIMAL(10,2) DEFAULT 0,
    stock INT DEFAULT 0,
    min_stock INT DEFAULT 0,
    sku VARCHAR(50) UNIQUE,
    image VARCHAR(255),
    status ENUM('active', 'inactive', 'discontinued') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 4. TABEL SALES
-- =============================================
CREATE TABLE IF NOT EXISTS sales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'card', 'transfer') DEFAULT 'cash',
    customer_name VARCHAR(100),
    customer_phone VARCHAR(20),
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'completed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 5. TABEL SALE_ITEMS
-- =============================================
CREATE TABLE IF NOT EXISTS sale_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sale_id INT NOT NULL,
    product_id INT,
    product_name VARCHAR(100),
    product_sku VARCHAR(50),
    quantity INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sale_id) REFERENCES sales(id) ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 6. TABEL RESERVASI
-- =============================================
CREATE TABLE IF NOT EXISTS reservasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    no_hp VARCHAR(20) NOT NULL,
    email VARCHAR(100),
    jumlah_anggota INT NOT NULL,
    tanggal_pemesanan DATETIME NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 7. TABEL JUMLAH_RESERVASI (untuk statistik)
-- =============================================
CREATE TABLE IF NOT EXISTS jumlah_reservasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE NOT NULL,
    jumlah INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 8. TABEL POSTS (untuk CMS)
-- =============================================
CREATE TABLE IF NOT EXISTS posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    slug VARCHAR(200) UNIQUE NOT NULL,
    content TEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    author_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- 9. TABEL SETTINGS
-- =============================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'number', 'boolean', 'json') DEFAULT 'text',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- DATA AWAL (SEED DATA)
-- =============================================

-- Insert default admin user
-- Password: admin123 (plain text sesuai kode)
INSERT INTO users (username, password, full_name, email, role, status)
VALUES
('admin', 'admin123', 'Administrator', 'admin@trinity.com', 'admin', 'active'),
('kasir1', 'kasir123', 'Kasir Satu', 'kasir1@trinity.com', 'cashier', 'active'),
('manager1', 'manager123', 'Manager Satu', 'manager1@trinity.com', 'manager', 'active');

-- Insert sample categories
INSERT INTO categories (name, description, status, sort_order)
VALUES
('Makanan', 'Menu makanan utama', 'active', 1),
('Minuman', 'Menu minuman segar', 'active', 2),
('Snack', 'Menu camilan ringan', 'active', 3),
('Dessert', 'Menu penutup', 'active', 4);

-- Insert sample products
INSERT INTO products (category_id, name, description, price, cost_price, stock, min_stock, sku, status)
VALUES
(1, 'Nasi Goreng', 'Nasi goreng spesial dengan telur', 25000, 15000, 50, 10, 'FOOD-001', 'active'),
(1, 'Mie Goreng', 'Mie goreng pedas nikmat', 22000, 13000, 45, 10, 'FOOD-002', 'active'),
(1, 'Ayam Bakar', 'Ayam bakar bumbu kecap', 35000, 20000, 30, 5, 'FOOD-003', 'active'),
(2, 'Es Teh Manis', 'Es teh manis segar', 5000, 2000, 100, 20, 'DRINK-001', 'active'),
(2, 'Jus Jeruk', 'Jus jeruk segar tanpa gula', 12000, 6000, 50, 10, 'DRINK-002', 'active'),
(2, 'Kopi Hitam', 'Kopi hitam original', 10000, 4000, 60, 15, 'DRINK-003', 'active'),
(3, 'French Fries', 'Kentang goreng crispy', 15000, 7000, 40, 10, 'SNACK-001', 'active'),
(4, 'Es Krim Vanilla', 'Es krim vanilla premium', 18000, 9000, 35, 5, 'DESSERT-001', 'active');

-- Insert sample settings
INSERT INTO settings (setting_key, setting_value, setting_type)
VALUES
('restaurant_name', 'Trinity Restaurant', 'text'),
('currency', 'IDR', 'text'),
('tax_percentage', '10', 'number'),
('open_hour', '08:00', 'text'),
('close_hour', '22:00', 'text');

-- =============================================
-- CREATE INDEXES untuk performa lebih baik
-- =============================================

-- Index untuk pencarian
CREATE INDEX idx_products_name ON products(name);
CREATE INDEX idx_products_sku ON products(sku);
CREATE INDEX idx_sales_invoice ON sales(invoice_number);
CREATE INDEX idx_sales_date ON sales(created_at);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_reservasi_tanggal ON reservasi(tanggal_pemesanan);

-- Index untuk join
CREATE INDEX idx_products_category ON products(category_id);
CREATE INDEX idx_sales_user ON sales(user_id);
CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);
CREATE INDEX idx_sale_items_product ON sale_items(product_id);

-- =============================================
-- CREATE VIEWS untuk laporan
-- =============================================

-- View untuk laporan penjualan harian
CREATE OR REPLACE VIEW v_daily_sales AS
SELECT
    DATE(s.created_at) as tanggal,
    COUNT(s.id) as jumlah_transaksi,
    SUM(s.total_amount) as total_penjualan,
    AVG(s.total_amount) as rata_rata_transaksi,
    s.payment_method,
    u.full_name as kasir
FROM sales s
LEFT JOIN users u ON s.user_id = u.id
WHERE s.status = 'completed'
GROUP BY DATE(s.created_at), s.payment_method, u.full_name;

-- View untuk produk terlaris
CREATE OR REPLACE VIEW v_top_products AS
SELECT
    p.id,
    p.name,
    p.sku,
    c.name as category_name,
    SUM(si.quantity) as total_terjual,
    SUM(si.total) as total_revenue
FROM products p
LEFT JOIN sale_items si ON p.id = si.product_id
LEFT JOIN categories c ON p.category_id = c.id
GROUP BY p.id, p.name, p.sku, c.name
ORDER BY total_terjual DESC;

-- View untuk stok menipis
CREATE OR REPLACE VIEW v_low_stock AS
SELECT
    p.id,
    p.name,
    p.sku,
    c.name as category_name,
    p.stock,
    p.min_stock,
    (p.min_stock - p.stock) as kekurangan
FROM products p
LEFT JOIN categories c ON p.category_id = c.id
WHERE p.stock <= p.min_stock AND p.status = 'active'
ORDER BY kekurangan DESC;

-- =============================================
-- STORED PROCEDURES
-- =============================================

-- Procedure untuk update stok otomatis
DELIMITER //
CREATE PROCEDURE sp_update_stock(
    IN p_product_id INT,
    IN p_quantity INT,
    IN p_type ENUM('in', 'out')
)
BEGIN
    IF p_type = 'in' THEN
        UPDATE products
        SET stock = stock + p_quantity
        WHERE id = p_product_id;
    ELSE
        UPDATE products
        SET stock = stock - p_quantity
        WHERE id = p_product_id;
    END IF;
END //
DELIMITER ;

-- =============================================
-- TRIGGERS
-- =============================================

-- Trigger untuk auto update jumlah_reservasi
DELIMITER //
CREATE TRIGGER tr_reservasi_insert
AFTER INSERT ON reservasi
FOR EACH ROW
BEGIN
    INSERT INTO jumlah_reservasi (tanggal, jumlah)
    VALUES (DATE(NEW.tanggal_pemesanan), 1)
    ON DUPLICATE KEY UPDATE jumlah = jumlah + 1;
END //
DELIMITER ;

-- Trigger untuk update jumlah_reservasi saat delete
DELIMITER //
CREATE TRIGGER tr_reservasi_delete
AFTER DELETE ON reservasi
FOR EACH ROW
BEGIN
    UPDATE jumlah_reservasi
    SET jumlah = jumlah - 1
    WHERE tanggal = DATE(OLD.tanggal_pemesanan);
END //
DELIMITER ;

-- =============================================
-- SELESAI
-- =============================================
