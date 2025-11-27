# Trinity Restaurant POS System

Sistem Point of Sale (POS) untuk restoran dengan fitur lengkap, dibangun menggunakan PHP murni (tanpa framework), HTML, CSS, dan JavaScript.

## Fitur Utama

### 1. Sistem Autentikasi
- Login multi-role (Admin, Manager, Kasir)
- Password authentication
- Session management
- Role-based access control

### 2. Dashboard
- Statistik penjualan hari ini
- Total produk aktif
- Monitoring stok rendah
- Total pengguna aktif
- Chart penjualan mingguan
- Chart reservasi
- Top 5 produk terlaris

### 3. Manajemen Menu/Produk
- CRUD produk (Create, Read, Update, Delete)
- Kategori produk
- Upload gambar produk
- Manajemen stok
- SKU (Stock Keeping Unit)
- Harga beli dan harga jual
- Status produk (active/inactive)
- Alert stok rendah

### 4. Point of Sale (POS)
- Interface kasir yang user-friendly
- Scan produk atau pilih manual
- Shopping cart
- Multiple payment methods (Cash, Card, Transfer)
- Auto-generate invoice number
- Print receipt
- Real-time stock update

### 5. Manajemen Transaksi
- History transaksi lengkap
- Detail transaksi per invoice
- Filter berdasarkan tanggal
- Status transaksi (pending, completed, cancelled)
- Informasi kasir
- View detail transaksi
- Print ulang struk

### 6. Manajemen Kategori
- CRUD kategori produk
- Deskripsi kategori
- Status kategori
- Sorting order

### 7. Sistem Reservasi
- Input reservasi pelanggan
- Data lengkap (nama, no HP, email, jumlah tamu)
- Tanggal dan waktu pemesanan
- Status reservasi (pending, confirmed, cancelled, completed)
- Catatan khusus
- Tracking statistik reservasi

### 8. Manajemen User
- CRUD user
- Role management (Admin, Manager, Cashier)
- Status user (active/inactive)
- Email validation
- Password management

### 9. Laporan Penjualan
- Laporan harian
- Laporan mingguan
- Laporan bulanan
- Laporan custom (pilih tanggal)
- Top produk terlaris
- Export PDF (print)
- Chart visualisasi data

### 10. UI/UX
- Desain minimalis dan modern
- Responsive design
- Mudah dipahami dan digunakan
- Clean interface
- Fast loading

## Teknologi

- **Backend**: PHP 7.4+
- **Database**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **No Framework**: Fully custom-built

## Instalasi

### Requirements
- PHP 7.4 atau lebih tinggi
- MySQL 8.0 atau lebih tinggi
- Apache/Nginx Web Server
- Web browser modern

### Langkah Instalasi

1. **Clone atau Download project**
   ```bash
   cd htdocs # untuk XAMPP
   # atau
   cd www # untuk WAMP
   ```

2. **Import Database**
   - Buka phpMyAdmin
   - Buat database baru dengan nama `restaurant_db`
   - Import file SQL yang Anda berikan

3. **Konfigurasi Database**
   - Buka file `config/database.php`
   - Sesuaikan konfigurasi database:
   ```php
   define('DB_HOST', '127.0.0.1');
   define('DB_USER', 'root');
   define('DB_PASS', ''); // sesuaikan dengan password MySQL Anda
   define('DB_NAME', 'restaurant_db');
   ```

4. **Set Permissions**
   - Pastikan folder `assets/uploads/` memiliki permission write (777)

5. **Akses Aplikasi**
   - Buka browser dan akses: `http://localhost/restaurant-pos/`

## Default Accounts

### Admin
- Username: `admin`
- Password: `admin123`
- Akses: Full access ke semua fitur

### Manager
- Username: `manager1`
- Password: `manager123`
- Akses: Dashboard, POS, Transaksi, Produk, Kategori, Reservasi, Laporan

### Kasir
- Username: `kasir1`
- Password: `kasir123`
- Akses: Dashboard, POS, Transaksi, Reservasi

## Struktur Folder

```
restaurant-pos/
├── assets/
│   ├── css/
│   │   └── style.css          # Global styling
│   ├── js/
│   │   └── main.js            # JavaScript utilities
│   └── uploads/               # Upload directory
├── config/
│   └── database.php           # Database configuration
├── includes/
│   ├── header.php             # Common header
│   ├── footer.php             # Common footer
│   └── navbar.php             # Sidebar navigation
├── core/
│   └── functions.php          # All business logic & algorithms
├── pages/
│   ├── dashboard.php          # Dashboard page
│   ├── products.php           # Product management
│   ├── categories.php         # Category management
│   ├── pos.php                # Point of Sale
│   ├── transactions.php       # Transaction history
│   ├── reservations.php       # Reservation management
│   ├── users.php              # User management
│   ├── reports.php            # Sales reports
│   └── print-receipt.php      # Receipt printing
├── auth/
│   ├── login.php              # Login page
│   └── logout.php             # Logout handler
├── api/
│   ├── process-sale.php       # API for processing sales
│   ├── get-sale-detail.php    # API for sale details
│   └── get-product.php        # API for product details
├── index.php                  # Landing page
└── README.md                  # Documentation
```

## Optimalisasi

### Performance
1. **Single Algorithm File**: Semua algoritma dan logika bisnis dikumpulkan dalam satu file (`core/functions.php`) untuk efisiensi loading
2. **Prepared Statements**: Menggunakan prepared statements untuk keamanan dan performa
3. **Minimal Dependencies**: Tidak ada framework atau library eksternal
4. **Efficient Queries**: Query database yang dioptimalkan
5. **Static Connection**: Database connection menggunakan static variable untuk reusability

### Security
1. **Input Sanitization**: Semua input di-sanitize sebelum diproses
2. **SQL Injection Prevention**: Menggunakan prepared statements
3. **XSS Protection**: Output encoding untuk mencegah XSS
4. **Session Management**: Secure session handling
5. **Role-based Access Control**: Pembatasan akses berdasarkan role

## Penggunaan

### Login
1. Akses `http://localhost/restaurant-pos/`
2. Masukkan username dan password
3. Sistem akan redirect ke dashboard sesuai role

### POS (Point of Sale)
1. Login sebagai kasir/admin/manager
2. Pilih menu "Point of Sale"
3. Klik produk untuk menambahkan ke keranjang
4. Atur jumlah dengan tombol +/-
5. Pilih metode pembayaran
6. Klik "Bayar" untuk menyelesaikan transaksi
7. Cetak struk jika diperlukan

### Manajemen Produk
1. Login sebagai admin/manager
2. Pilih menu "Produk"
3. Klik "+ Tambah Produk" untuk menambah produk baru
4. Isi form dengan data produk
5. Klik "Edit" untuk mengubah data produk
6. Klik "Hapus" untuk menghapus produk

### Laporan
1. Login sebagai admin/manager
2. Pilih menu "Laporan"
3. Pilih periode atau custom date range
4. Klik "Tampilkan"
5. Klik "Cetak Laporan" untuk print

## Troubleshooting

### Database Connection Error
- Pastikan MySQL service berjalan
- Cek konfigurasi di `config/database.php`
- Pastikan database `restaurant_db` sudah dibuat

### Permission Denied
- Set permission folder `assets/uploads/` ke 777
- Atau sesuaikan dengan permission web server Anda

### Session Not Working
- Pastikan session.save_path di php.ini sudah benar
- Cek permission folder session

## Support

Jika ada pertanyaan atau menemukan bug, silakan hubungi tim developer.

## License

© 2025 Trinity Restaurant. All Rights Reserved.
