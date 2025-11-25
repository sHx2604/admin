<?php

function authUser($username, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([trim($username)]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Perbandingan password plain text
    if ($user && trim($password) === $user['password']) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        return true;
    }
    return false;
}

function checkPageAccess($page) {
    $role = isset($_SESSION['role']) ? $_SESSION['role'] : null;
    $page = strtolower($page);
    // Daftar akses per role
    $access = [
        'admin' => 'all',
        'cashier' => ['dashboard', 'pos', 'reservation'],
        'manager' => ['dashboard', 'sales', 'reservation', 'menu', 'transaction', 'kategori']
    ];
    if ($role === 'admin') {
        return true; // Admin bisa akses semua
    }
    if (isset($access[$role])) {
        if ($access[$role] === 'all') return true;
        return in_array($page, $access[$role]);
    }
    return false;
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: index.php');
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

function hasPermission($roles) {
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}

// Utility functions
function generateInvoiceNumber() {
    return 'INV-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
}

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function formatDate($date) {
    return date('d/m/Y H:i', strtotime($date));
}

function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9]+/', '-', $string);
    return trim($string, '-');
}

// Ambil semua user
function getUsers($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM users ORDER BY role ASC";
    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Ambil semua kategori (tanpa filter status)
function getAllCategories($limit = null) {
    global $pdo;
    $sql = "SELECT * FROM categories ORDER BY id ASC";
    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Database helper functions
function getProducts($search = '', $category = '', $limit = null) {
    global $pdo;

    $sql = "SELECT p.*, c.name as category_name FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            ";
    $params = [];

    if ($search) {
        $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }

    if ($category) {
        $sql .= " AND p.category_id = ?";
        $params[] = $category;
    }

    $sql .= " ORDER BY p.name";

    if ($limit) {
        $sql .= " LIMIT $limit";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getProductById($id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p
                          LEFT JOIN categories c ON p.category_id = c.id
                          WHERE p.id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getCategories() {
    global $pdo;

    $stmt = $pdo->prepare("SELECT * FROM categories  ORDER BY name");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getSales($limit = 50) {
    global $pdo;

    // Pastikan limit adalah integer untuk mencegah SQL injection
    $limit = is_numeric($limit) ? (int)$limit : 50;

    // Gabungkan langsung ke query karena tidak bisa bind LIMIT sebagai parameter
    $sql = "SELECT s.*, u.full_name as cashier_name
            FROM sales s
            LEFT JOIN users u ON s.user_id = u.id
            ORDER BY s.created_at DESC
            LIMIT $limit";

    $stmt = $pdo->query($sql);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function getSaleItems($sale_id) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT si.*, p.name as product_name
                          FROM sale_items si
                          LEFT JOIN products p ON si.product_id = p.id
                          WHERE si.sale_id = ?");
    $stmt->execute([$sale_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getDashboardStats() {
    global $pdo;

    // Today's sales
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_sales, COALESCE(SUM(total_amount), 0) as total_revenue
                          FROM sales WHERE DATE(created_at) = CURDATE() AND status = 'completed'");
    $stmt->execute();
    $today = $stmt->fetch(PDO::FETCH_ASSOC);

    // Total products
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_products FROM products WHERE status = 'active'");
    $stmt->execute();
    $products = $stmt->fetch(PDO::FETCH_ASSOC);

    // Low stock products
    $stmt = $pdo->prepare("SELECT COUNT(*) as low_stock FROM products WHERE stock <= 10 AND status = 'active'");
    $stmt->execute();
    $lowStock = $stmt->fetch(PDO::FETCH_ASSOC);

    // Total users
    $stmt = $pdo->prepare("SELECT COUNT(*) as total_users FROM users WHERE status = 'active'");
    $stmt->execute();
    $users = $stmt->fetch(PDO::FETCH_ASSOC);

    //Total reservation

     $stmt = $pdo->prepare("SELECT COUNT(*) as total_reservation FROM reservasi");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'today_sales' => $today['total_sales'],
        'today_revenue' => $today['total_revenue'],
        'total_products' => $products['total_products'],
        'low_stock' => $lowStock['low_stock'],
        'total_users' => $users['total_users'],
        'total_reservation' => isset($result['total_reservation']) ? (int)$result['total_reservation'] : 0

    ];
}
// Total Reservation

function gettotal($limit = null) {
    global $pdo;

    $sql = "SELECT * FROM jumlah_reservasi ORDER BY tanggal DESC";

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


//Call Reservation Table

function getReservations($limit = null) {
    global $pdo;

    $sql = "SELECT * FROM reservasi ORDER BY tanggal_pemesanan DESC";

    if ($limit !== null && is_numeric($limit)) {
        $sql .= " LIMIT " . (int)$limit;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// File upload function
function uploadFile($file, $uploadDir = 'uploads/') {
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowedTypes)) {
        return false;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}

// Validation functions
function validateProduct($data) {
    $errors = [];

    if (empty($data['name'])) {
        $errors[] = 'Nama produk harus diisi';
    }

    if (empty($data['price']) || $data['price'] <= 0) {
        $errors[] = 'Harga harus diisi dan lebih dari 0';
    }

    if (isset($data['stock']) && $data['stock'] < 0) {
        $errors[] = 'Stok tidak boleh negatif';
    }

    return $errors;
}

function validateUser($data) {
    $errors = [];

    if (empty($data['username'])) {
        $errors[] = 'Username harus diisi';
    }

    if (empty($data['full_name'])) {
        $errors[] = 'Nama lengkap harus diisi';
    }

    if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email tidak valid';
    }

    if (empty($data['password']) && !isset($data['id'])) {
        $errors[] = 'Password harus diisi';
    }

    return $errors;
}

// chart handle

function getWeeklySalesData() {
    global $pdo;

    $stmt = $pdo->prepare("
        SELECT DAYNAME(created_at) as hari, SUM(total_amount) as total
        FROM sales
        WHERE WEEK(created_at) = WEEK(CURDATE()) AND status = 'completed'
        GROUP BY DAYNAME(created_at)
        ORDER BY FIELD(DAYNAME(created_at), 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')
    ");
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $hariMap = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu',
    ];

    $labels = [];
    $values = [];

    foreach ($hariMap as $eng => $ind) {
        $found = false;
        foreach ($data as $row) {
            if ($row['hari'] === $eng) {
                $labels[] = $ind;
                $values[] = (int) $row['total'];
                $found = true;
                break;
            }
        }
        if (!$found) {
            $labels[] = $ind;
            $values[] = 0;
        }
    }

    return [
        'labels' => $labels,
        'data' => $values
    ];
}


// Data penjualan harian (7 hari terakhir)
function getDailySalesData() {
    global $pdo;

    $stmt = $pdo->prepare("SELECT DATE(created_at) as tanggal, COALESCE(SUM(total_amount),0) as total_penjualan FROM sales WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) AND status = 'completed' GROUP BY DATE(created_at) ORDER BY DATE(created_at) ASC");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $values = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $labels[] = date('d/m', strtotime($date));
        $found = false;
        foreach ($rows as $r) {
            if ($r['tanggal'] === $date) {
                $values[] = (float)$r['total_penjualan'];
                $found = true;
                break;
            }
        }
        if (!$found) $values[] = 0;
    }

    return ['labels' => $labels, 'data' => $values];
}

// Data reservasi mingguan (hari Senin - Minggu)
function getWeeklyReservationData() {
    global $pdo;

    $stmt = $pdo->prepare("SELECT DAYNAME(tanggal) as hari, COUNT(*) as jumlah FROM jumlah_reservasi WHERE WEEK(tanggal_pemesanan) = WEEK(CURDATE()) AND YEAR(tanggal_pemesanan) = YEAR(CURDATE()) GROUP BY DAYNAME(tanggal_pemesanan)");
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $dayOrder = ['Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu','Sunday'=>'Minggu'];
    $labels = [];
    $values = [];
    foreach ($dayOrder as $eng => $ind) {
        $labels[] = $ind;
        $found = false;
        foreach ($rows as $r) {
            if ($r['hari'] === $eng) {
                $values[] = (int)$r['jumlah'];
                $found = true; break;
            }
        }
        if (!$found) $values[] = 0;
    }

    return ['labels' => $labels, 'data' => $values];
}

// Top products (last 30 days)
function getTopProductsData($limit = 5) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT p.name, COALESCE(SUM(si.quantity),0) as total_terjual FROM sale_items si JOIN products p ON si.product_id = p.id JOIN sales s ON si.sale_id = s.id WHERE s.status = 'completed' AND s.created_at >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) GROUP BY p.id, p.name ORDER BY total_terjual DESC LIMIT ?");
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $values = [];
    foreach ($rows as $r) {
        $labels[] = $r['name'];
        $values[] = (int)$r['total_terjual'];
    }

    return ['labels' => $labels, 'data' => $values];
}

// Monthly revenue (last 6 months)
function getMonthlyRevenueData($months = 6) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT YEAR(created_at) as tahun, MONTH(created_at) as bulan, COALESCE(SUM(total_amount),0) as total_revenue FROM sales WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL ? MONTH) AND status = 'completed' GROUP BY YEAR(created_at), MONTH(created_at) ORDER BY YEAR(created_at) ASC, MONTH(created_at) ASC");
    $stmt->bindValue(1, (int)$months, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $labels = [];
    $values = [];

    for ($i = $months-1; $i >= 0; $i--) {
        $date = date('Y-m', strtotime("-{$i} months"));
        $labels[] = date('M Y', strtotime($date));
        $year = date('Y', strtotime($date));
        $month = (int)date('n', strtotime($date));

        $found = false;
        foreach ($rows as $r) {
            if ((int)$r['tahun'] == (int)$year && (int)$r['bulan'] == $month) {
                $values[] = (float)$r['total_revenue'];
                $found = true; break;
            }
        }
        if (!$found) $values[] = 0;
    }

    return ['labels' => $labels, 'data' => $values];
}

// ============ PDF REPORT DATA FUNCTIONS ============

// Get Indonesian day name
function getIndonesianDay($englishDay) {
    $days = [
        'Monday' => 'Senin',
        'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis',
        'Friday' => 'Jumat',
        'Saturday' => 'Sabtu',
        'Sunday' => 'Minggu'
    ];
    return isset($days[$englishDay]) ? $days[$englishDay] : $englishDay;
}

// Get daily report data
function getDailyReportData($date) {
    global $pdo;

    if (empty($date)) {
        $date = date('Y-m-d');
    }

    // Get sales summary
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_transaksi,
            COALESCE(SUM(total_amount), 0) as total_pendapatan,
            COALESCE(AVG(total_amount), 0) as rata_rata_transaksi
        FROM sales
        WHERE DATE(created_at) = ? AND status = 'completed'
    ");
    $stmt->execute([$date]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get top products
    $stmt = $pdo->prepare("
        SELECT
            p.name,
            SUM(si.quantity) as qty_terjual,
            SUM(si.total) as total_revenue
        FROM sale_items si
        JOIN products p ON si.product_id = p.id
        JOIN sales s ON si.sale_id = s.id
        WHERE DATE(s.created_at) = ? AND s.status = 'completed'
        GROUP BY p.id, p.name
        ORDER BY qty_terjual DESC
        LIMIT 10
    ");
    $stmt->execute([$date]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get reservations
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as jumlah_reservasi,
            COALESCE(SUM(jumlah_anggota), 0) as total_tamu
        FROM reservasi
        WHERE DATE(tanggal_pemesanan) = ?
    ");
    $stmt->execute([$date]);
    $reservations = $stmt->fetch(PDO::FETCH_ASSOC);

    return [
        'date' => $date,
        'summary' => $summary,
        'top_products' => $top_products,
        'reservations' => $reservations
    ];
}

// Get weekly report data
function getWeeklyReportData($week_start) {
    global $pdo;

    if (empty($week_start)) {
        $week_start = date('Y-m-d', strtotime('monday this week'));
    }

    $week_end = date('Y-m-d', strtotime($week_start . ' +6 days'));

    // Get sales summary
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_transaksi,
            COALESCE(SUM(total_amount), 0) as total_pendapatan,
            COALESCE(AVG(total_amount), 0) as rata_rata_transaksi
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
    ");
    $stmt->execute([$week_start, $week_end]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get daily sales breakdown
    $stmt = $pdo->prepare("
        SELECT
            DATE(created_at) as tanggal,
            DAYNAME(created_at) as hari,
            COUNT(*) as jumlah_transaksi,
            COALESCE(SUM(total_amount), 0) as total_penjualan
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
        GROUP BY DATE(created_at), DAYNAME(created_at)
        ORDER BY DATE(created_at)
    ");
    $stmt->execute([$week_start, $week_end]);
    $daily_sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get top products
    $stmt = $pdo->prepare("
        SELECT
            p.name,
            SUM(si.quantity) as qty_terjual,
            SUM(si.total) as total_revenue
        FROM sale_items si
        JOIN products p ON si.product_id = p.id
        JOIN sales s ON si.sale_id = s.id
        WHERE DATE(s.created_at) BETWEEN ? AND ? AND s.status = 'completed'
        GROUP BY p.id, p.name
        ORDER BY qty_terjual DESC
        LIMIT 10
    ");
    $stmt->execute([$week_start, $week_end]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'week_start' => $week_start,
        'week_end' => $week_end,
        'summary' => $summary,
        'daily_sales' => $daily_sales,
        'top_products' => $top_products
    ];
}

// Get monthly report data
function getMonthlyReportData($month, $year) {
    global $pdo;

    if (empty($month) || empty($year)) {
        $month = date('m');
        $year = date('Y');
    }

    // Get date range
    $start_date = "$year-$month-01";
    $end_date = date('Y-m-t', strtotime($start_date));

    // Get sales summary
    $stmt = $pdo->prepare("
        SELECT
            COUNT(*) as total_transaksi,
            COALESCE(SUM(total_amount), 0) as total_pendapatan,
            COALESCE(AVG(total_amount), 0) as rata_rata_transaksi,
            MIN(total_amount) as transaksi_terkecil,
            MAX(total_amount) as transaksi_terbesar
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
    ");
    $stmt->execute([$start_date, $end_date]);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);

    // Get top products
    $stmt = $pdo->prepare("
        SELECT
            p.name,
            p.sku,
            c.name as category,
            SUM(si.quantity) as qty_terjual,
            SUM(si.total) as total_revenue,
            AVG(si.price) as avg_price
        FROM sale_items si
        JOIN products p ON si.product_id = p.id
        LEFT JOIN categories c ON p.category_id = c.id
        JOIN sales s ON si.sale_id = s.id
        WHERE DATE(s.created_at) BETWEEN ? AND ? AND s.status = 'completed'
        GROUP BY p.id, p.name, p.sku, c.name
        ORDER BY total_revenue DESC
        LIMIT 15
    ");
    $stmt->execute([$start_date, $end_date]);
    $top_products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get payment method breakdown
    $stmt = $pdo->prepare("
        SELECT
            payment_method,
            COUNT(*) as jumlah_transaksi,
            COALESCE(SUM(total_amount), 0) as total_amount
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
        GROUP BY payment_method
    ");
    $stmt->execute([$start_date, $end_date]);
    $payment_methods = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get daily trend
    $stmt = $pdo->prepare("
        SELECT
            DATE(created_at) as tanggal,
            COUNT(*) as jumlah_transaksi,
            COALESCE(SUM(total_amount), 0) as total_penjualan
        FROM sales
        WHERE DATE(created_at) BETWEEN ? AND ? AND status = 'completed'
        GROUP BY DATE(created_at)
        ORDER BY DATE(created_at)
    ");
    $stmt->execute([$start_date, $end_date]);
    $daily_trend = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'month' => $month,
        'year' => $year,
        'month_name' => date('F', strtotime($start_date)),
        'start_date' => $start_date,
        'end_date' => $end_date,
        'summary' => $summary,
        'top_products' => $top_products,
        'payment_methods' => $payment_methods,
        'daily_trend' => $daily_trend
    ];
}
?>
