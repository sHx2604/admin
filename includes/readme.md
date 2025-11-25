# 🏪 TRINITY RESTAURANT MANAGEMENT SYSTEM

## 📋 Deskripsi Sistem

Trinity Restaurant Management System adalah aplikasi berbasis web yang dibangun menggunakan PHP native dengan database MySQL. Sistem ini dirancang untuk mengelola operasional restoran secara komprehensif, mencakup Point of Sale (POS), manajemen menu, inventori, reservasi, pelaporan penjualan, dan manajemen pengguna.

---

## 🎯 Fitur Utama

### 1. **Sistem Autentikasi**
- Login multi-role (Admin, Manager, Kasir)
- Password authentication
- Session management
- Role-based access control

### 2. **Dashboard**
- Statistik penjualan hari ini
- Total produk aktif
- Monitoring stok rendah
- Total pengguna aktif
- Chart penjualan mingguan
- Chart reservasi
- Top produk terlaris

### 3. **Manajemen Menu/Produk**
- CRUD produk (Create, Read, Update, Delete)
- Kategori produk
- Upload gambar produk
- Manajemen stok
- SKU (Stock Keeping Unit)
- Harga beli dan harga jual
- Status produk (active/inactive)

### 4. **Point of Sale (POS)**
- Interface kasir yang user-friendly
- Scan produk atau pilih manual
- Shopping cart
- Multiple payment methods (Cash, Card, Transfer)
- Auto-generate invoice number
- Print receipt
- Real-time stock update

### 5. **Manajemen Transaksi**
- History transaksi lengkap
- Detail transaksi per invoice
- Filter berdasarkan tanggal
- Status transaksi (pending, completed, cancelled)
- Informasi kasir

### 6. **Manajemen Kategori**
- CRUD kategori produk
- Deskripsi kategori
- Status kategori
- Sorting order

### 7. **Sistem Reservasi**
- Input reservasi pelanggan
- Data lengkap (nama, no HP, email, jumlah tamu)
- Tanggal dan waktu pemesanan
- Status reservasi (pending, confirmed, cancelled, completed)
- Catatan khusus
- Tracking statistik reservasi

### 8. **Manajemen User**
- CRUD user
- Role management (Admin, Manager, Cashier)
- Status user (active/inactive)
- Email validation
- Password management

### 9. **Laporan Penjualan**
- Laporan harian
- Laporan mingguan
- Laporan bulanan
- Top produk terlaris
- Export PDF
- Chart visualisasi data

---

## 🏗️ Arsitektur Sistem

### **Teknologi Stack:**
```
Frontend:
- HTML5
- CSS3 (Bootstrap 5.x)
- JavaScript (Vanilla JS)
- Font Awesome Icons
- Chart.js (untuk grafik)

Backend:
- PHP 7.4+ (Native)
- PDO (PHP Data Objects)

Database:
- MySQL 5.7+ / MariaDB 10.x

Additional Libraries:
- FPDF (untuk export PDF)
- Owl Carousel
- Tempus Dominus (date picker)
```

### **Struktur Direktori:**
```
restaurant-system/
│
├── admin/                      # Folder admin (jika ada nested structure)
│   └── uploads/               # Upload files dari admin
│
├── assets/                     # Asset statis
│   ├── css/                   # Stylesheet
│   │   └── style.css
│   └── js/                    # JavaScript
│       └── main.js
│
├── config/                     # Konfigurasi sistem
│   └── database.php           # Konfigurasi database & koneksi PDO
│
├── css/                        # CSS Template
│   ├── bootstrap.min.css
│   └── style.css
│
├── img/                        # Gambar sistem
│   ├── favicon.ico
│   └── user.jpg
│
├── includes/                   # File helper/utility
│   └── functions.php          # Semua fungsi helper
│
├── js/                         # JavaScript files
│   └── main.js
│
├── lib/                        # Third-party libraries
│   ├── owlcarousel/
│   ├── tempusdominus/
│   └── fpdf/
│
├── scss/                       # SCSS source (jika ada)
│
├── uploads/                    # Upload folder untuk gambar produk
│
├── vendor/                     # Composer dependencies (jika ada)
│
├── chart_data.php             # API endpoint untuk data chart
├── check_system.php           # System check utility
├── dashboard.php              # Halaman dashboard utama
├── export_pdf.php             # Export laporan ke PDF
├── export_pdf_simple.php      # Export PDF versi simple
├── index.php                  # Landing/Login page
├── kategori.php               # Manajemen kategori
├── logout.php                 # Logout handler
├── menu.php                   # Manajemen menu/produk
├── pos.php                    # Point of Sale interface
├── reservation.php            # Manajemen reservasi
├── restaurant_db.sql          # Database schema & seed data
├── sales.php                  # Laporan penjualan
├── transaction.php            # History transaksi
├── user.php                   # Manajemen user
└── README.md                  # Dokumentasi (file ini)
```

---

## 💾 Database Schema

### **Tabel-tabel Utama:**

#### 1. `users` - Manajemen Pengguna
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- username (UNIQUE, NOT NULL)
- password (VARCHAR 255, NOT NULL)
- full_name (VARCHAR 100, NOT NULL)
- email (UNIQUE, NOT NULL)
- role (ENUM: 'admin', 'cashier', 'manager')
- status (ENUM: 'active', 'inactive')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 2. `categories` - Kategori Produk
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- name (VARCHAR 100, NOT NULL)
- description (TEXT)
- status (ENUM: 'active', 'inactive')
- sort_order (INT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 3. `products` - Produk/Menu
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- category_id (FOREIGN KEY -> categories.id)
- name (VARCHAR 100, NOT NULL)
- description (TEXT)
- price (DECIMAL 10,2, NOT NULL)
- cost_price (DECIMAL 10,2)
- stock (INT, DEFAULT 0)
- min_stock (INT, DEFAULT 0)
- sku (VARCHAR 50, UNIQUE)
- image (VARCHAR 255)
- status (ENUM: 'active', 'inactive', 'discontinued')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 4. `sales` - Transaksi Penjualan
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- user_id (FOREIGN KEY -> users.id)
- invoice_number (VARCHAR 50, UNIQUE, NOT NULL)
- total_amount (DECIMAL 10,2, NOT NULL)
- payment_method (ENUM: 'cash', 'card', 'transfer')
- customer_name (VARCHAR 100)
- customer_phone (VARCHAR 20)
- status (ENUM: 'pending', 'completed', 'cancelled')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 5. `sale_items` - Detail Item Penjualan
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- sale_id (FOREIGN KEY -> sales.id, CASCADE)
- product_id (FOREIGN KEY -> products.id)
- product_name (VARCHAR 100)
- product_sku (VARCHAR 50)
- quantity (INT, NOT NULL)
- price (DECIMAL 10,2, NOT NULL)
- total (DECIMAL 10,2, NOT NULL)
- created_at (TIMESTAMP)
```

#### 6. `reservasi` - Reservasi Pelanggan
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- nama (VARCHAR 100, NOT NULL)
- no_hp (VARCHAR 20, NOT NULL)
- email (VARCHAR 100)
- jumlah_anggota (INT, NOT NULL)
- tanggal_pemesanan (DATETIME, NOT NULL)
- status (ENUM: 'pending', 'confirmed', 'cancelled', 'completed')
- catatan (TEXT)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 7. `jumlah_reservasi` - Statistik Reservasi
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- tanggal (DATE, UNIQUE, NOT NULL)
- jumlah (INT, DEFAULT 0)
- created_at (TIMESTAMP)
```

#### 8. `posts` - CMS Posts (Optional)
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- title (VARCHAR 200, NOT NULL)
- slug (VARCHAR 200, UNIQUE, NOT NULL)
- content (TEXT)
- excerpt (TEXT)
- featured_image (VARCHAR 255)
- status (ENUM: 'draft', 'published', 'archived')
- author_id (FOREIGN KEY -> users.id)
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

#### 9. `settings` - Pengaturan Sistem
```sql
- id (PRIMARY KEY, AUTO_INCREMENT)
- setting_key (VARCHAR 100, UNIQUE, NOT NULL)
- setting_value (TEXT)
- setting_type (ENUM: 'text', 'number', 'boolean', 'json')
- created_at (TIMESTAMP)
- updated_at (TIMESTAMP)
```

### **Database Views:**
- `v_daily_sales` - View untuk laporan penjualan harian
- `v_top_products` - View produk terlaris
- `v_low_stock` - View produk dengan stok menipis

### **Stored Procedures:**
- `sp_update_stock()` - Procedure untuk update stok otomatis

### **Triggers:**
- `tr_reservasi_insert` - Auto update statistik saat insert reservasi
- `tr_reservasi_delete` - Auto update statistik saat delete reservasi

---

## 🔄 Alur Kerja Sistem

### **1. Proses Login**
```
User Input (username + password)
    ↓
index.php → authUser()
    ↓
Query ke database (tabel users)
    ↓
Validasi username & password (plain text)
    ↓
Check status = 'active'
    ↓
Set SESSION (user_id, username, full_name, role)
    ↓
Redirect ke dashboard.php
```

### **2. Proses Transaksi POS**
```
Kasir buka pos.php
    ↓
Load produk dari database (getProducts())
    ↓
User pilih produk → tambah ke cart (JavaScript)
    ↓
Input customer name & payment method
    ↓
Klik "Checkout"
    ↓
Generate invoice number (INV-YYYYMMDD-XXXX)
    ↓
BEGIN TRANSACTION
    ↓
Insert ke tabel sales
    ↓
Get last insert ID (sale_id)
    ↓
Loop setiap item cart:
    - Insert ke sale_items
    - Update stock produk (stock - quantity)
    ↓
COMMIT TRANSACTION
    ↓
Tampilkan struk/receipt
    ↓
Option: Print atau Save PDF
```

### **3. Proses Manajemen Produk**
```
Admin/Manager buka menu.php
    ↓
Pilih action: [Add | Edit | Delete]
    ↓
ADD:
    - Form input (nama, kategori, harga, stok, gambar)
    - Upload image → validateImage() → save ke /uploads/
    - INSERT INTO products
    ↓
EDIT:
    - Load data produk by ID → getProductById()
    - Form pre-filled
    - Option: ganti gambar
    - UPDATE products WHERE id = ?
    ↓
DELETE:
    - Konfirmasi (JavaScript)
    - DELETE FROM products WHERE id = ?
    ↓
Redirect kembali ke menu.php
```

### **4. Proses Laporan Penjualan**
```
User buka sales.php
    ↓
Filter: [Tanggal Mulai] [Tanggal Akhir] [Kategori]
    ↓
Query sales dengan JOIN sale_items & products
    ↓
Agregasi data:
    - Total transaksi
    - Total pendapatan
    - Rata-rata transaksi
    - Breakdown per kategori
    - Top 5 produk
    ↓
Tampilkan dalam tabel & chart
    ↓
Option Export:
    - PDF (export_pdf.php menggunakan FPDF)
    - Print langsung
```

### **5. Proses Reservasi**
```
User/Staff input data reservasi
    ↓
reservation.php → Form input
    ↓
Validasi data (nama, no HP, jumlah tamu, tanggal)
    ↓
INSERT INTO reservasi
    ↓
TRIGGER: tr_reservasi_insert
    ↓
Auto update tabel jumlah_reservasi
    (increment jumlah untuk tanggal tersebut)
    ↓
Tampilkan konfirmasi
    ↓
List semua reservasi dengan status
```

---

## 🔐 Role & Permission

### **1. Admin**
- **Akses:** FULL ACCESS (semua menu)
- **Permissions:**
  - Dashboard ✅
  - Menu Management ✅
  - Transaction History ✅
  - Kategori ✅
  - Reservasi ✅
  - User Management ✅
  - Sales Report ✅
  - POS/Kasir ✅

### **2. Manager**
- **Akses:** LIMITED (kecuali user management)
- **Permissions:**
  - Dashboard ✅
  - Menu Management ✅
  - Transaction History ✅
  - Kategori ✅
  - Reservasi ✅
  - User Management ❌
  - Sales Report ✅
  - POS/Kasir ❌

### **3. Cashier**
- **Akses:** MINIMAL (hanya operasional)
- **Permissions:**
  - Dashboard ✅ (read-only)
  - Menu Management ❌
  - Transaction History ❌
  - Kategori ❌
  - Reservasi ✅
  - User Management ❌
  - Sales Report ❌
  - POS/Kasir ✅

**Implementasi:**
```php
function checkPageAccess($page) {
    $access = [
        'admin' => 'all',
        'manager' => ['dashboard', 'sales', 'reservation', 'menu', 'transaction', 'kategori'],
        'cashier' => ['dashboard', 'pos', 'reservation']
    ];
    // Validasi akses...
}
```

---

## 📊 Fungsi-Fungsi Penting

### **Authentication & Authorization**
```php
authUser($username, $password)         // Login user
isLoggedIn()                          // Check session
requireLogin()                        // Middleware login
hasRole($role)                        // Check specific role
hasPermission($roles)                 // Check multiple roles
checkPageAccess($page)                // Check page permission
```

### **Database Operations**
```php
getUsers($limit)                      // Get all users
getProducts($search, $category, $limit) // Get products with filter
getProductById($id)                   // Get single product
getCategories()                       // Get all categories
getAllCategories($limit)              // Get categories with limit
getSales($limit)                      // Get sales transactions
getSaleItems($sale_id)                // Get items per transaction
getReservations($limit)               // Get reservations
gettotal($limit)                      // Get reservation statistics
```

### **Dashboard & Analytics**
```php
getDashboardStats()                   // Get dashboard statistics
getWeeklySalesData()                  // Weekly sales chart data
getDailySalesData()                   // Daily sales (7 days)
getWeeklyReservationData()            // Weekly reservation chart
getTopProductsData($limit)            // Top selling products
getMonthlyRevenueData($months)        // Monthly revenue (6 months)
```

### **Utility Functions**
```php
generateInvoiceNumber()               // Generate unique invoice
formatCurrency($amount)               // Format to IDR
formatDate($date)                     // Format datetime
createSlug($string)                   // Create URL-friendly slug
uploadFile($file, $uploadDir)         // Handle file upload
validateProduct($data)                // Validate product input
validateUser($data)                   // Validate user input
```

---

## 🚀 Instalasi & Setup

### **Persyaratan Sistem:**
```
- PHP 7.4 atau lebih tinggi
- MySQL 5.7+ atau MariaDB 10.x+
- Apache/Nginx Web Server
- PDO Extension enabled
- GD Library (untuk manipulasi gambar)
- mod_rewrite enabled (untuk Apache)
```

### **Langkah Instalasi:**

#### **1. Clone/Extract Project**
```bash
# Extract file admin.zip ke direktori web server
# Contoh: /var/www/html/restaurant-system/
```

#### **2. Setup Database**
```bash
# Login ke MySQL
mysql -u root -p

# Import database schema
mysql -u root -p < restaurant_db.sql

# Atau via phpMyAdmin:
# - Buat database 'restaurant_db'
# - Import file restaurant_db.sql
```

#### **3. Konfigurasi Database**
Edit file `config/database.php`:
```php
define('DB_HOST', 'localhost');      // Database host
define('DB_NAME', 'restaurant_db');  // Nama database
define('DB_USER', 'root');           // Username database
define('DB_PASS', '');               // Password database
```

#### **4. Set Permission Folder**
```bash
# Berikan permission write untuk folder uploads
chmod 777 uploads/
chmod 777 admin/uploads/

# Atau lebih aman:
chown -R www-data:www-data uploads/
chmod 755 uploads/
```

#### **5. Akses Aplikasi**
```
URL: http://localhost/restaurant-system/
atau
URL: http://your-domain.com/

Login Default:
Username: admin
Password: admin123
```

#### **6. Data Dummy (Optional)**
Database sudah include seed data:
- 3 Users (admin, kasir1, manager1)
- 4 Kategori (Makanan, Minuman, Snack, Dessert)
- 8 Produk sample
- Settings default

---

## 🔧 Konfigurasi Lanjutan

### **1. Security Enhancement**
```php
// Ganti password authentication ke hash
// Edit di includes/functions.php:
if ($user && password_verify($password, $user['password'])) {
    // Login success
}

// Untuk create user, hash password:
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
```

### **2. Upload Configuration**
```php
// Edit di includes/functions.php:
$allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
$maxFileSize = 2 * 1024 * 1024; // 2MB max
```

### **3. Invoice Format**
```php
// Edit di includes/functions.php:
function generateInvoiceNumber() {
    return 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}
// Output: INV-20251125-0001
```

### **4. Tax Configuration**
```sql
-- Update di tabel settings:
UPDATE settings SET setting_value = '11' WHERE setting_key = 'tax_percentage';
```

---

## 🐛 Troubleshooting

### **Problem 1: Database Connection Error**
```
Error: Connection failed: SQLSTATE[HY000] [1045]
```
**Solusi:**
- Cek kredensial di `config/database.php`
- Pastikan MySQL service running
- Cek user permission di MySQL

### **Problem 2: Upload Image Gagal**
```
Error: Failed to move uploaded file
```
**Solusi:**
- Cek permission folder `uploads/` (chmod 755 atau 777)
- Cek disk space
- Cek konfigurasi php.ini: `upload_max_filesize` & `post_max_size`

### **Problem 3: Session Not Working**
```
Error: Headers already sent
```
**Solusi:**
- Pastikan `session_start()` di baris pertama
- Cek tidak ada output (space/newline) sebelum `<?php`
- Cek session.save_path di php.ini

### **Problem 4: Chart Tidak Muncul**
**Solusi:**
- Cek koneksi internet (Chart.js dari CDN)
- Cek file `chart_data.php` accessible
- Cek console browser untuk error JavaScript

### **Problem 5: PDF Export Error**
```
Error: FPDF error: Unable to create output file
```
**Solusi:**
- Cek library FPDF sudah ada di folder `lib/fpdf/`
- Cek permission folder temporary
- Cek include path di export_pdf.php

---

## 📈 Pengembangan Lebih Lanjut

### **Fitur yang Bisa Ditambahkan:**

1. **Multi-branch Support**
   - Manajemen multiple outlet/cabang
   - Transfer stok antar cabang
   - Consolidate reporting

2. **Advanced Inventory**
   - Purchase order management
   - Supplier management
   - Stock opname
   - Barcode scanning

3. **Customer Management**
   - Member registration
   - Loyalty points
   - Customer history
   - Targeted promotion

4. **Kitchen Display System (KDS)**
   - Order queue untuk dapur
   - Real-time notification
   - Order status tracking

5. **Online Ordering**
   - Frontend untuk customer
   - Online payment gateway
   - Delivery tracking

6. **Mobile App**
   - iOS/Android app
   - Mobile POS
   - QR code menu

7. **Advanced Analytics**
   - Predictive analytics
   - Inventory forecasting
   - Customer behavior analysis
   - A/B testing

8. **Integration**
   - Accounting software (e.g., Accurate, Zahir)
   - Email notification (SMTP)
   - SMS notification
   - WhatsApp API

---

## 📝 API Endpoints (Internal)

### **Chart Data API**
```
Endpoint: chart_data.php?type={type}

Types:
- weekly_sales       → Data penjualan mingguan
- daily_sales        → Data penjualan 7 hari terakhir
- weekly_reservation → Data reservasi mingguan
- top_products       → Top 5 produk terlaris
- monthly_revenue    → Revenue 6 bulan terakhir

Response: JSON
{
  "labels": [...],
  "data": [...]
}
```

---

## 🔒 Security Best Practices

### **Implemented:**
✅ Prepared Statements (PDO) - mencegah SQL Injection
✅ Session Management
✅ Role-based Access Control
✅ File Upload Validation

### **Recommended Improvements:**
⚠️ **Password Hashing** - Gunakan `password_hash()` dan `password_verify()`
⚠️ **CSRF Protection** - Tambahkan CSRF token di form
⚠️ **XSS Prevention** - Escape output dengan `htmlspecialchars()`
⚠️ **HTTPS** - Gunakan SSL certificate
⚠️ **Input Validation** - Validate & sanitize semua user input
⚠️ **Error Handling** - Jangan tampilkan error detail ke user
⚠️ **File Upload Security** - Rename file, check magic bytes
⚠️ **Rate Limiting** - Cegah brute force attack

---

## 📞 Support & Contact

Untuk pertanyaan atau bantuan lebih lanjut:

- **Developer:** trinity007
- **Email:** shxzx6690@gmail.com
- **Website:** -
- **Version:** 1.0.0
- **Last Updated:** November 2025

---

## 📄 License

Proprietary Software - All Rights Reserved

---

## 🙏 Credits

- **Bootstrap 5** - https://getbootstrap.com/
- **Font Awesome** - https://fontawesome.com/
- **Chart.js** - https://www.chartjs.org/
- **FPDF** - http://www.fpdf.org/
- **Owl Carousel** - https://owlcarousel2.github.io/OwlCarousel2/

---

## 📌 Catatan Penting

1. **Password Default** untuk semua user demo adalah **plain text** - segera ganti dengan hash untuk production!
2. **Folder uploads/** harus writable (chmod 755/777)
3. **Backup database** secara berkala
4. **Update PHP** dan **MySQL** ke versi terbaru untuk security
5. Sistem ini **belum production-ready** - perlu security hardening

---

**Terakhir diupdate:** 25 November 2025
**Versi Dokumentasi:** 1.0
**Status:** ✅ Completed & Verified
