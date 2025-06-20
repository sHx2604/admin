<?php
// Authentication functions
function authenticateUser($username, $password) {
    global $pdo;

    $stmt = $pdo->prepare("SELECT id, username, password, full_name, role FROM users WHERE username = ? AND status = 'active'");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        return true;
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
        'total_users' => $users['total_users']

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
function uploadFile($file, $uploadDir = '../adminuploads') {
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
?>
