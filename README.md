# Trinity Restaurant POS System

Sistem Point of Sale (POS) komprehensif untuk restoran dengan dashboard admin lengkap dan antarmuka pelanggan, dibangun menggunakan PHP murni (tanpa framework), MySQL, HTML5, CSS3, dan JavaScript vanilla.

## Deskripsi Sistem

Aplikasi ini terdiri dari dua bagian utama:
1. **Dashboard Admin** - Manajemen operasional lengkap untuk staff restoran
2. **Customer Interface** - Halaman landing dan menu untuk pelanggan

## Fitur Utama

### Dashboard Admin & Manajemen

#### 1. Sistem Autentikasi & Otorisasi
- Login multi-role dengan session management
- Roles: Admin, Manager, Kasir (Cashier)
- Password authentication dengan plaintext
- Role-based access control (RBAC)
- Secure session handling

#### 2. Dashboard
- Statistik penjualan real-time hari ini
- Total produk aktif monitoring
- Alert stok rendah products
- Pengguna aktif tracking
- Chart penjualan 7 hari terakhir (Chart.js)
- Chart reservasi 7 hari terakhir
- Top 5 produk terlaris dengan revenue

#### 3. Manajemen Produk
- CRUD lengkap (Create, Read, Update, Delete)
- Kategori produk terstruktur
- SKU (Stock Keeping Unit) unique tracking
- Dual pricing: harga beli (cost price) dan harga jual
- Manajemen stok dengan minimum stock alerts
- Status produk (active/inactive)
- Upload & manajemen gambar produk
- Deskripsi detail produk

#### 4. Manajemen Kategori
- CRUD kategori produk
- Deskripsi & sorting order
- Status kategori (active/inactive)
- Link ke produk terkait

#### 5. Point of Sale (POS)
- Interface kasir intuitif dan cepat
- Pencarian & filter produk by kategori
- Shopping cart dengan update real-time
- Adjustment qty dengan +/- buttons
- Multiple payment methods: Cash, Card, Transfer
- Auto-generate invoice number
- Input nama pelanggan (opsional)
- Validasi stok real-time
- Print receipt functionality
- Automatic stock deduction

#### 6. Manajemen Transaksi
- History lengkap semua penjualan
- Filter by tanggal range
- Detail transaksi per invoice (line items, subtotal, payment method)
- Status tracking (pending, completed)
- Kasir information tracking
- View & reprint receipt functionality

#### 7. Sistem Reservasi
- Input & manajemen reservasi pelanggan
- Data: nama, nomor HP, email, jumlah tamu
- Tanggal & waktu reservasi scheduling
- Status tracking (pending, confirmed, cancelled, completed)
- Catatan khusus per reservasi
- Statistik reservasi 7 hari terakhir
- Double input source: public + admin dashboard

#### 8. Manajemen User
- CRUD user account
- Role assignment (Admin, Manager, Kasir)
- Status user (active/inactive)
- Email validation
- Password management
- Prevent self-deletion safeguard

#### 9. Laporan Penjualan
- Filter options: Harian, Mingguan, Bulanan, Custom range
- Summary statistics (total revenue, transaction count)
- Product sales breakdown dengan revenue
- Chart visualisasi penjualan
- Print/export functionality
- Period-based auto-date calculation

### Customer Interface

#### 1. Landing Page (index.php)
- Reservation form terintegrasi
- Customer profile collection
- Direct database insertion
- Success notification

#### 2. Menu Page (menu.php)
- Public menu display
- Semua produk aktif
- Product details & images
- Read-only (non-logged-in access)

## Teknologi Stack

- **Backend**: PHP 7.4+ (Pure/Vanilla - no framework)
- **Database**: MySQL 8.0+ (MySQLi driver)
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Charts**: Chart.js library
- **Architecture**: Monolithic with single-file core functions

## Instalasi & Setup

### Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Apache/Nginx Web Server
- Web browser modern (Chrome, Firefox, Safari, Edge)

### Langkah Instalasi

1. **Clone atau Download Project**
   ```bash
   # Untuk XAMPP
   cd htdocs
   
   # Atau untuk WAMP
   cd www
   ```

2. **Import Database**
   - Buka phpMyAdmin (http://localhost/phpmyadmin)
   - Buat database baru: `restaurant_db`
   - Pilih tab "Import" dan pilih file `restaurant_db.sql`
   - Klik "Go" untuk import

3. **Konfigurasi Database**
   - Buka file `config/database.php`
   - Sesuaikan konstanta database jika diperlukan:
   ```php
   define('DB_HOST', '127.0.0.1');    // Host MySQL
   define('DB_USER', 'root');         // User MySQL
   define('DB_PASS', '');             // Password MySQL (default kosong)
   define('DB_NAME', 'restaurant_db'); // Nama database
   ```

4. **Konfigurasi File Uploads**
   - Pastikan folder `assets/uploads/` ada dan writable
   - Set permission: `chmod 777 assets/uploads/` (Linux/Mac)
   - Untuk Windows, pastikan user memiliki write access

5. **Akses Aplikasi**
   ```
   Admin Dashboard: http://localhost/admin/auth/login.php
   Customer Interface: http://localhost/admin/
   Menu Page: http://localhost/admin/menu.php
   ```

## Default Login Accounts

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Akses**: Full access semua fitur (Dashboard, Products, Categories, POS, Transactions, Reservations, Users, Reports)

### Manager Account
- **Username**: `manager1`
- **Password**: `manager123`
- **Akses**: Dashboard, POS, Transactions, Products, Categories, Reservations, Reports (tanpa User Management)

### Cashier Account
- **Username**: `kasir1`
- **Password**: `kasir123`
- **Akses**: Dashboard, POS, Transactions, Reservations (operasional penjualan saja)

## Struktur File & Folder

```
admin/
├── index.php                          # Landing page + public reservation form
├── index1.php                         # Auth redirect router
├── menu.php                           # Public menu display page
├── README.md                          # Dokumentasi ini
├── restaurant_db.sql                  # Database schema & sample data
│
├── auth/                              # Authentication pages
│   ├── login.php                      # Login form & handler
│   └── logout.php                     # Session logout handler
│
├── config/                            # Konfigurasi aplikasi
│   └── database.php                   # MySQL connection & constants
│
├── core/                              # Business logic & algorithms
│   └── functions.php                  # Semua fungsi utama (774 lines)
│                                      # - Auth & session management
│                                      # - Dashboard statistics
│                                      # - Product CRUD & queries
│                                      # - Category CRUD & queries
│                                      # - Sales/transaction operations
│                                      # - Reservation management
│                                      # - User management
│                                      # - Report generation
│                                      # - Utility helpers
│
├── includes/                          # Reusable HTML components
│   ├── header.php                     # HTML head & meta tags
│   ├── navbar.php                     # Sidebar navigation menu
│   └── footer.php                     # Common footer
│
├── pages/                             # Admin dashboard pages
│   ├── dashboard.php                  # Main dashboard + statistics
│   ├── products.php                   # Product CRUD interface
│   ├── categories.php                 # Category CRUD interface
│   ├── pos.php                        # Point of Sale interface
│   ├── transactions.php               # Transaction history & filtering
│   ├── reservations.php               # Reservation management
│   ├── users.php                      # User account management
│   ├── reports.php                    # Sales reports & analytics
│   └── print-receipt.php              # Receipt printing template
│
├── api/                               # AJAX/JSON endpoints
│   ├── process-sale.php               # Process checkout & create sale
│   ├── get-product.php                # Fetch product details (JSON)
│   └── get-sale-detail.php            # Fetch transaction details (JSON)
│
├── assets/                            # Admin static resources
│   ├── css/
│   │   └── style.css                  # Admin styling
│   ├── js/
│   │   └── main.js                    # Admin utilities & scripts
│   └── uploads/                       # Product images directory
│
└── assets front/                      # Customer-facing resources
    ├── css/
    │   └── style.css                  # Customer page styling
    ├── images/                        # Customer page images
    └── js/
        └── script.js                  # Customer page scripts
```

## Arsitektur & Optimalisasi

### Design Patterns

**Monolithic Single-File Core Architecture**
- Semua algoritma & business logic terpusat di `core/functions.php`
- Memudahkan pemeliharaan & pencarian fungsi
- Single source of truth untuk operasi database
- Total 774 lines dalam satu file terstruktur dengan baik

**Database Abstraction**
```php
function getDBConnection() {
    static $conn = null;  // Singleton pattern
    if ($conn === null) {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    }
    return $conn;
}
```

### Performance Optimizations

1. **Efficient Database Connection**
   - Static variable caching untuk reusable connection
   - Prevent multiple connection instantiation
   - Charset utf8mb4 support

2. **Prepared Statements**
   - Semua query menggunakan parameterized queries
   - Binding parameter dengan type hints
   - SQL injection prevention

3. **Query Optimization**
   - JOINs untuk menghindari N+1 queries
   - Efficient WHERE clauses dengan index-friendly filters
   - LIMIT untuk pagination & large dataset handling

4. **Minimal Dependencies**
   - Pure PHP/MySQL tanpa framework
   - Chart.js hanya untuk visualisasi (loaded conditionally)
   - No database ORM overhead

5. **Static Resource Optimization**
   - CSS & JS terpisah untuk caching
   - Inline SVG untuk icons (no requests)
   - Product images dengan lazy loading consideration

### Security Measures

1. **Input Validation & Sanitization**
   ```php
   function sanitizeInput($input) {
       return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
   }
   ```

2. **SQL Injection Prevention**
   - 100% prepared statements usage
   - Type-safe parameter binding
   - No raw SQL concatenation

3. **XSS Protection**
   - Output encoding dengan htmlspecialchars()
   - JSON_encode untuk API responses
   - Content-Type headers proper setting

4. **Session Security**
   - Session-based authentication
   - Role-based access control checks
   - Automatic redirect untuk unauthenticated users

5. **Access Control**
   - checkAuth() for all admin pages
   - requireRole() for role-specific pages
   - User self-deletion prevention

### Code Organization

**Functional Grouping dalam functions.php**
```php
// ============================================================
// AUTHENTICATION & SESSION MANAGEMENT
// ============================================================
// - startSession()
// - login()
// - logout()
// - isLoggedIn()
// - checkAuth()
// - hasRole()
// - requireRole()

// ============================================================
// DASHBOARD STATISTICS
// ============================================================
// - getTodaySales()
// - getActiveProductsCount()
// - getLowStockCount()
// - getActiveUsersCount()
// - getWeeklySalesChart()
// - getTopProducts()
// - getReservationStats()

// ============================================================
// PRODUCT MANAGEMENT
// ============================================================
// - getProducts()
// - getProductById()
// - createProduct()
// - updateProduct()
// - deleteProduct()

// [Similar structure for Categories, Sales, Reservations, Users, Reports]

// ============================================================
// UTILITY HELPERS
// ============================================================
// - formatCurrency()
// - jsonResponse()
// - sanitizeInput()
```

## Panduan Penggunaan

### Alur Login & Navigation

1. **Akses Landing Page**
   ```
   http://localhost/admin/
   ```
   - Menampilkan public reservation form
   - Link ke menu.php untuk melihat menu

2. **Login ke Dashboard**
   ```
   http://localhost/admin/auth/login.php
   ```
   - Masukkan username & password
   - Sistem auto-redirect ke dashboard sesuai role

3. **Navigasi Menu**
   - Sidebar navigation otomatis sesuai role
   - Admin: lihat semua menu
   - Manager: tanpa User Management
   - Kasir: hanya operational menus

### POS (Point of Sale) - Kasir

**Alur Transaksi:**
1. Login → Dashboard → Klik "Point of Sale"
2. Interface terbagi 2 kolom:
   - **Kiri**: Daftar produk dengan kategori filter & search
   - **Kanan**: Shopping cart real-time

3. **Tambah Produk ke Keranjang**
   - Cari produk via search box
   - Filter by kategori (optional)
   - Klik produk untuk tambah (qty auto +1 jika duplicate)

4. **Adjust Quantity**
   - Gunakan +/- buttons di cart
   - Harga otomatis ter-update

5. **Checkout**
   - Masukkan nama pelanggan (optional)
   - Pilih metode pembayaran: Tunai/Kartu/Transfer
   - Klik "Bayar" untuk proses transaksi
   - System auto-generate invoice number
   - Stok otomatis ter-update

6. **Print Receipt**
   - Modal receipt muncul post-transaksi
   - Klik "Cetak" untuk print

### Manajemen Produk - Admin/Manager

**Tambah Produk:**
1. Menu → Produk → "+ Tambah Produk"
2. Isi form:
   - Kategori
   - Nama produk
   - Deskripsi (optional)
   - Harga jual
   - Harga beli (cost price)
   - Stok awal
   - Minimum stok (untuk alert)
   - SKU (unique identifier)
   - Status (active/inactive)
3. Klik "Simpan"

**Edit Produk:**
1. Menu → Produk → Cari produk
2. Klik tombol "Edit" pada baris produk
3. Ubah data sesuai kebutuhan
4. Klik "Perbarui"

**Hapus Produk:**
1. Menu → Produk → Cari produk
2. Klik tombol "Hapus"
3. Confirm deletion

**Search & Filter:**
- Search box mencari by nama atau SKU
- Filter by kategori via dropdown
- Real-time filtering

### Laporan Penjualan - Admin/Manager

**Akses Laporan:**
1. Menu → Laporan Penjualan

**Filter Options:**
- **Harian**: Penjualan hari ini
- **Mingguan**: 7 hari terakhir
- **Bulanan**: Bulan berjalan
- **Custom**: Pilih tanggal dari-sampai

**Konten Laporan:**
- Summary: Total revenue, jumlah transaksi
- Product breakdown: Top produk dengan qty & revenue
- Chart visualisasi penjualan
- Printable format

**Export/Print:**
- Klik "Cetak Laporan" untuk print-friendly view

### Manajemen Transaksi - All Staff

**View History:**
1. Menu → Transaksi
2. Filter by tanggal range
3. Tabel menampilkan: invoice, tanggal, kasir, pelanggan, total, payment method, status

**View Detail:**
- Klik tombol "Detail" untuk melihat line items
- Modal menampilkan: item list, qty, price, subtotal

### Manajemen Kategori - Admin/Manager

**Tambah Kategori:**
1. Menu → Kategori → "+ Tambah Kategori"
2. Isi: Nama, Deskripsi, Sort Order, Status
3. Klik "Simpan"

**Edit/Hapus:**
- Tombol Edit & Hapus di setiap baris

### Manajemen Reservasi - All Staff

**View Reservasi:**
1. Menu → Reservasi
2. Tabel menampilkan: nama, no HP, email, jumlah tamu, tanggal, status

**Tambah/Edit Reservasi:**
1. Klik "+ Tambah Reservasi" atau tombol "Edit"
2. Isi form dengan data customer
3. Pilih status
4. Klik "Simpan"

**Input via Public Form:**
- Customer bisa submit via landing page (index.php)
- Data langsung masuk ke database
- Staff bisa manage dari dashboard

### Manajemen User - Admin Only

**Tambah User:**
1. Menu → Pengguna → "+ Tambah Pengguna"
2. Isi: Username, Password, Nama, Email, Role
3. Role: Admin / Manager / Kasir
4. Klik "Simpan"

**Edit User:**
1. Klik tombol "Edit"
2. Ubah data & status
3. Klik "Perbarui"

**Hapus User:**
1. Klik tombol "Hapus"
2. Confirm deletion
3. ⚠️ Tidak bisa hapus diri sendiri

## Troubleshooting & FAQ

### Database & Connection Issues

**Error: "Connection failed: Access denied"**
- Cek MySQL service berjalan
- Verify username & password di `config/database.php`
- Pastikan localhost bisa diakses

**Error: "Unknown database 'restaurant_db'"**
- Buka phpMyAdmin
- Buat database manual: CREATE DATABASE restaurant_db;
- Import file restaurant_db.sql

**Error: "SQLSTATE[HY000]: General error"**
- Jalankan query di phpMyAdmin untuk restore
- Cek file permission pada database folder

### File & Permission Issues

**Error: "Permission denied" pada upload**
- Set folder permission: `chmod 777 assets/uploads/`
- Atau gunakan FTP client untuk set write permission

**Images tidak tampil**
- Cek apakah file ada di `assets/uploads/`
- Verify path di database (image column)
- Clear browser cache

### Session & Authentication Issues

**Redirect loop saat login**
- Clear browser cookies & session
- Cek PHP session.save_path di php.ini
- Verify $_SESSION working (buat test.php dengan phpinfo())

**"Access denied" error padahal sudah login**
- Cek role user di users table
- Verify checkAuth() & requireRole() logic
- Clear session cache browser

**Logout tidak berfungsi**
- Cek file permissions pada session folder
- Restart MySQL & Apache
- Check php.ini session settings

### Data & Calculation Issues

**Stok tidak berkurang setelah transaksi**
- Cek sale_items table ada isinya
- Verify function createSale() berjalan complete
- Cek transaction lock pada database

**Chart tidak tampil di dashboard**
- Verify Chart.js loaded (check network tab)
- Cek data dari getWeeklySalesChart() via browser console
- Ensure canvas element ada di HTML

**Laporan data salah**
- Verify date filter working (check URL parameter)
- Cek timezone setting di PHP (date_default_timezone_set)
- Check database datetime format

### Performance Issues

**Halaman loading lambat**
- Check database query performance
- Use browser dev tools Network tab
- Verify server resources (CPU, RAM)
- Consider database indexing untuk table besar

**POS cart slow**
- Reduce product list jika >1000 items
- Enable browser caching
- Check JavaScript console untuk errors

## FAQ - Frequently Asked Questions

**Q: Bagaimana menambah kategori produk baru?**
A: Admin/Manager → Menu Kategori → "+ Tambah Kategori" → Isi form → Simpan

**Q: Bisa ganti password user?**
A: Di halaman User Management (Admin only), edit user & isi password field. Kosongkan untuk keep password sama.

**Q: Laporan bisa di-export ke Excel?**
A: Saat ini hanya print/PDF via browser print. Bisa copy-paste tabel ke Excel.

**Q: Berapa maksimal produk yang bisa di-manage?**
A: Unlimited. Performa tergantung MySQL indexing & server resources.

**Q: Apa bedanya role Admin, Manager, Kasir?**
A: 
- **Admin**: Akses semua fitur termasuk user management
- **Manager**: Semua admin fitur kecuali user management
- **Kasir**: Hanya POS, transaksi, & reservasi (operasional)

**Q: Bisa pakai password hash lebih aman?**
A: Ya. Modifikasi login() di functions.php ganti dengan password_hash/password_verify.

**Q: Database bisa di-backup dari mana?**
A: phpMyAdmin → Select database → Export → Download SQL file

**Q: Bisa pakai database selain MySQL?**
A: Saat ini hanya MySQLi. Butuh refactor database layer untuk PostgreSQL/SQLite.
## Database Schema

### Core Tables

**users**
- id: Primary Key
- username: Unique login identifier
- password: User password (plaintext)
- full_name: Display name
- email: User email
- role: ENUM (admin, manager, cashier)
- status: ENUM (active, inactive)
- created_at: Timestamp

**products**
- id: Primary Key
- category_id: Foreign Key  categories
- name: Product name
- description: Product details
- sku: Unique product code
- price: Selling price
- cost_price: Purchase/cost price
- stock: Current quantity
- min_stock: Reorder threshold
- image: Product image path
- status: ENUM (active, inactive)
- created_at: Timestamp

**categories**
- id: Primary Key
- name: Category name
- description: Category description
- sort_order: Display order
- status: ENUM (active, inactive)
- created_at: Timestamp

**sales**
- id: Primary Key
- user_id: Foreign Key  users (cashier)
- invoice_number: Unique invoice identifier
- customer_name: Customer name
- customer_phone: Customer phone (optional)
- payment_method: ENUM (cash, card, transfer)
- total_amount: Transaction total
- status: ENUM (pending, completed, cancelled)
- created_at: Sale timestamp

**sale_items**
- id: Primary Key
- sale_id: Foreign Key  sales
- product_id: Foreign Key  products
- quantity: Item quantity
- price: Unit price at sale time
- total: Line item total

**reservasi** (Reservations)
- id: Primary Key
- nama: Customer name
- no_hp: Phone number
- email: Email address
- jumlah_anggota: Number of guests
- tanggal_pemesanan: Reservation datetime
- status: ENUM (pending, confirmed, cancelled, completed)
- catatan: Special notes/requests
- created_at: Reservation timestamp

## Entity Relationship Diagram (ERD)

### ERD - Struktur Relasi Database

```
┌─────────────────────────────────────────────────────────────────────────────┐
│                          RESTAURANT POS DATABASE                            │
└─────────────────────────────────────────────────────────────────────────────┘

                                    USERS
                           ┌────────────────────┐
                           │ id (PK)            │
                           │ username (UNIQUE)  │
                           │ password           │
                           │ full_name          │
                           │ email (UNIQUE)     │
                           │ role (enum)        │ ◄──┐
                           │ status (enum)      │    │
                           │ created_at         │    │
                           │ updated_at         │    │
                           └────────────────────┘    │
                                  ▲                  │
                                  │ 1                │
                                  │                  │
                            ┌──────┴──────┐          │
                            │ (user_id)   │          │
                            │ 1:N         │          │
                            │             │          │
                 ┌──────────┘             └────────────────────┐
                 │                                             │
                 │                                             │
              SALES                                        POSTS
        ┌──────────────────┐                     ┌───────────────────┐
        │ id (PK)          │                     │ id (PK)           │
        │ user_id (FK)     │                     │ title             │
        │ invoice_number   │                     │ slug (UNIQUE)     │
        │ total_amount     │                     │ content           │
        │ payment_method   │                     │ excerpt           │
        │ customer_name    │                     │ featured_image    │
        │ customer_phone   │                     │ status (enum)     │
        │ status (enum)    │                     │ author_id (FK)    │
        │ created_at       │                     │ created_at        │
        │ updated_at       │                     │ updated_at        │
        └──────────────────┘                     └───────────────────┘
              │
              │ 1
              │
              │ (sale_id)
              │ 1:N
              │
         SALE_ITEMS
       ┌─────────────────────┐
       │ id (PK)             │
       │ sale_id (FK)        │ ────────────────┐
       │ product_id (FK)     │                 │
       │ product_name        │                 │
       │ product_sku         │                 │
       │ quantity            │                 │
       │ price               │                 │
       │ total               │                 │
       │ created_at          │                 │
       └─────────────────────┘                 │
              │                                │
              │ 1                             │
              │                                │
              │ (product_id)                  │
              │ N:1                           │
              │                                │
         PRODUCTS                             │
       ┌──────────────────┐                   │
       │ id (PK)          │ ◄──────────────────
       │ category_id (FK) │ ──┐
       │ name             │   │
       │ description      │   │
       │ price            │   │
       │ cost_price       │   │ 1
       │ stock            │   │
       │ min_stock        │   │ (category_id)
       │ sku (UNIQUE)     │   │ N:1
       │ image            │   │
       │ status (enum)    │   │
       │ created_at       │   │
       │ updated_at       │   │
       └──────────────────┘   │
              ▲                │
              │                │
              └────────────────┤
                               │
                          CATEGORIES
                       ┌─────────────────┐
                       │ id (PK)         │
                       │ name            │
                       │ description     │
                       │ status (enum)   │
                       │ sort_order      │
                       │ created_at      │
                       │ updated_at      │
                       └─────────────────┘


        RESERVASI (Standalone Table)
       ┌──────────────────────┐
       │ id (PK)              │
       │ nama                 │
       │ no_hp                │
       │ email                │
       │ jumlah_anggota       │
       │ tanggal_pemesanan    │
       │ status (enum)        │
       │ catatan              │
       │ created_at           │
       │ updated_at           │
       └──────────────────────┘


    JUMLAH_RESERVASI (Statistic Table)
       ┌──────────────────────┐
       │ id (PK)              │
       │ tanggal (UNIQUE)     │
       │ jumlah               │
       │ created_at           │
       └──────────────────────┘


         SETTINGS (Configuration)
       ┌──────────────────────┐
       │ id (PK)              │
       │ setting_key (UNIQUE) │
       │ setting_value        │
       │ setting_type (enum)  │
       │ created_at           │
       │ updated_at           │
       └──────────────────────┘
```

### Relasi Detail

**1. USERS ↔ SALES (1:N)**
- Satu user (kasir) dapat membuat banyak sales
- `sales.user_id` → `users.id`
- ON DELETE SET NULL: Jika kasir dihapus, sales tetap ada dengan user_id = NULL

**2. USERS ↔ POSTS (1:N)**
- Satu user (author) dapat membuat banyak posts
- `posts.author_id` → `users.id`
- ON DELETE SET NULL: Post akan tetap ada tanpa author

**3. CATEGORIES ↔ PRODUCTS (1:N)**
- Satu kategori dapat memiliki banyak produk
- `products.category_id` → `categories.id`
- ON DELETE SET NULL: Jika kategori dihapus, produk kategorinya menjadi NULL

**4. PRODUCTS ↔ SALE_ITEMS (1:N)**
- Satu produk dapat terjual dalam banyak transaksi
- `sale_items.product_id` → `products.id`
- ON DELETE SET NULL: Jika produk dihapus, detail penjualan tetap ada (product_id = NULL)
- Menyimpan product_name & sku untuk history yang akurat

**5. SALES ↔ SALE_ITEMS (1:N)**
- Satu penjualan dapat memiliki banyak item
- `sale_items.sale_id` → `sales.id`
- ON DELETE CASCADE: Jika penjualan dihapus, semua item juga terhapus

**6. RESERVASI (Standalone)**
- Tabel mandiri tanpa relasi FK
- Data reservasi dari pelanggan tidak perlu link ke user
- Statistik disimpan di `jumlah_reservasi`

**7. SETTINGS & JUMLAH_RESERVASI**
- Tabel konfigurasi & statistik
- Tidak memiliki relasi FK dengan tabel lain

### Kardinalitas

| Relasi | Tipe | Keterangan |
|--------|------|-----------|
| USERS ↔ SALES | 1:N | 1 user membuat N sales |
| USERS ↔ POSTS | 1:N | 1 author membuat N posts |
| CATEGORIES ↔ PRODUCTS | 1:N | 1 kategori memiliki N produk |
| PRODUCTS ↔ SALE_ITEMS | 1:N | 1 produk muncul di N transaksi |
| SALES ↔ SALE_ITEMS | 1:N | 1 penjualan memiliki N item |

### Cascade Rules

```
╔══════════════════════════════════════════════════════════════════╗
║ FOREIGN KEY CONSTRAINT BEHAVIOR                                 ║
╠══════════════════════════════════════════════════════════════════╣
║ Table        │ FK Column      │ Parent Table │ Action            ║
╠══════════════════════════════════════════════════════════════════╣
║ SALES        │ user_id        │ USERS        │ SET NULL          ║
║ POSTS        │ author_id      │ USERS        │ SET NULL          ║
║ PRODUCTS     │ category_id    │ CATEGORIES   │ SET NULL          ║
║ SALE_ITEMS   │ sale_id        │ SALES        │ CASCADE DELETE    ║
║ SALE_ITEMS   │ product_id     │ PRODUCTS     │ SET NULL          ║
╚══════════════════════════════════════════════════════════════════╝
```

**Penjelasan:**
- **SET NULL**: Menghapus parent record akan set FK menjadi NULL (data child tetap)
- **CASCADE DELETE**: Menghapus parent record akan menghapus semua child records

### Indexing Strategy

```
UNIQUE INDEXES (untuk identifikasi unik):
├── users.username
├── users.email
├── categories.slug (jika ada)
├── products.sku
├── sales.invoice_number
└── posts.slug

SEARCH INDEXES (untuk performa query):
├── products.name
├── products.status
├── sales.created_at
├── sales.user_id
├── reservasi.tanggal_pemesanan
└── posts.status

FOREIGN KEY INDEXES (untuk join performa):
├── products.category_id
├── sales.user_id
├── sale_items.sale_id
├── sale_items.product_id
└── posts.author_id
```

### Database Views

Aplikasi menggunakan 3 materialized views untuk laporan:

```sql
-- View 1: Laporan Penjualan Harian
v_daily_sales (tanggal, jumlah_transaksi, total_penjualan, avg_transaksi, payment_method, kasir)

-- View 2: Produk Terlaris
v_top_products (id, name, sku, category, total_terjual, total_revenue)

-- View 3: Stok Menipis
v_low_stock (id, name, sku, category, stock, min_stock, kekurangan)
```

### Diagram Alur Data

```
┌──────────────────────────────────────────────────────────────────┐
│                         FLOW DATA SISTEM                         │
└──────────────────────────────────────────────────────────────────┘

1. USER MANAGEMENT
   ┌──────────┐
   │ User     │ (Admin/Manager/Cashier)
   │ Register │ ──────> INSERT INTO users
   └──────────┘ (username, password, full_name, email, role)


2. PRODUCT SETUP
   ┌──────────┐      ┌──────────┐
   │Category  │ ──-> │ Product  │ ──────> INSERT INTO products
   │Management│      │Management│ (category_id FK, sku, price, stock)
   └──────────┘      └──────────┘


3. POINT OF SALE TRANSACTION
   ┌────────────────────────────────────────────────────────┐
   │                    POS CHECKOUT FLOW                   │
   └────────────────────────────────────────────────────────┘

   a) Kasir login (users auth)
   b) Add products to cart (from PRODUCTS)
   c) Click checkout
   d) SELECT from PRODUCTS WHERE id IN (...)
   e) INSERT INTO sales (user_id, invoice_number, total_amount, payment_method)
   f) INSERT INTO sale_items (sale_id, product_id, quantity, price, total)
   g) UPDATE products SET stock = stock - quantity
   h) Return invoice_number ke customer
   

4. RESERVATION SYSTEM
   ┌──────────────────┐
   │ Customer Reserve │ ──────> INSERT INTO reservasi
   │ (Public Form)    │ (nama, no_hp, email, jumlah_anggota, tanggal)
   └──────────────────┘
                         ──────> UPDATE jumlah_reservasi (trigger)


5. REPORTING SYSTEM
   ┌──────────────────┐
   │ Generate Report  │ ──────> SELECT FROM v_daily_sales
   │ (Filter Date)    │ ──────> SELECT FROM v_top_products
   └──────────────────┘ ──────> SELECT FROM v_low_stock
```

### Contoh Query Relational

**Query 1: Laporan Penjualan dengan Detail Kasir**
```sql
SELECT 
    s.invoice_number,
    s.created_at,
    u.full_name as kasir_name,
    s.customer_name,
    s.total_amount,
    s.payment_method,
    COUNT(si.id) as item_count
FROM sales s
LEFT JOIN users u ON s.user_id = u.id
LEFT JOIN sale_items si ON s.id = si.sale_id
WHERE DATE(s.created_at) = CURDATE()
GROUP BY s.id;
-- Menggunakan relasi: SALES.user_id → USERS.id, SALES.id → SALE_ITEMS.sale_id
```

**Query 2: Produk Terlaris Per Kategori**
```sql
SELECT 
    c.name as kategori,
    p.name as produk,
    p.sku,
    SUM(si.quantity) as terjual,
    SUM(si.total) as revenue,
    (SUM(si.total) - (p.cost_price * SUM(si.quantity))) as profit
FROM products p
JOIN categories c ON p.category_id = c.id
LEFT JOIN sale_items si ON p.id = si.product_id
GROUP BY p.id
ORDER BY revenue DESC;
-- Menggunakan relasi: PRODUCTS.category_id → CATEGORIES.id, PRODUCTS.id → SALE_ITEMS.product_id
```

**Query 3: Monitoring Stok Rendah**
```sql
SELECT 
    c.name as kategori,
    p.name as produk,
    p.sku,
    p.stock,
    p.min_stock,
    (p.min_stock - p.stock) as kekurangan_stok,
    (p.min_stock - p.stock) * p.cost_price as nilai_kekurangan
FROM products p
JOIN categories c ON p.category_id = c.id
WHERE p.stock <= p.min_stock AND p.status = 'active'
ORDER BY kekurangan_stok DESC;
-- Menggunakan relasi: PRODUCTS.category_id → CATEGORIES.id
```

**Query 4: Statistic Kasir Performance**
```sql
SELECT 
    u.full_name as kasir,
    u.role,
    COUNT(DISTINCT s.id) as total_transaksi,
    SUM(s.total_amount) as total_penjualan,
    AVG(s.total_amount) as avg_transaksi,
    MIN(s.created_at) as first_sale,
    MAX(s.created_at) as last_sale
FROM users u
LEFT JOIN sales s ON u.id = s.user_id AND s.status = 'completed'
WHERE u.role IN ('cashier', 'manager')
GROUP BY u.id;
-- Menggunakan relasi: USERS.id ← SALES.user_id
```

### ERD Summary - Ringkasan Struktur Database

| Aspek | Detail |
|-------|--------|
| **Total Tables** | 9 tables (Core + Support) |
| **Core Tables** | users, categories, products, sales, sale_items, reservasi |
| **Support Tables** | jumlah_reservasi, posts, settings |
| **Total FK Relations** | 8 foreign key relationships |
| **Total Unique Constraints** | 7 UNIQUE keys |
| **Database Engine** | InnoDB (for transactions & FK support) |
| **Charset** | utf8mb4 (Unicode support) |


**Performance Optimizations:**
- 15+ indexes untuk fast queries
- Proper normalization untuk reduce redundancy
- Denormalization pada sale_items (product_name, product_sku) untuk history accuracy
- Cascade delete pada sale_items untuk data consistency

## Development & Extension

To add new features, follow this pattern:
1. Create database table in restaurant_db.sql
2. Add CRUD functions in core/functions.php
3. Create management page in pages/
4. Add navigation link in includes/navbar.php
5. Add access control checks (checkAuth, requireRole)

## Support & License

 2025 Trinity Restaurant. All Rights Reserved.

**Stack**: Pure PHP + MySQL 8.0+
**License**: Proprietary - Trinity Restaurant
**Last Updated**: December 8, 2025
