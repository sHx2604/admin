<?php
// Core Functions - All Algorithms in One File
require_once __DIR__ . '/../config/database.php';

// ============================================================
// AUTHENTICATION & SESSION MANAGEMENT
// ============================================================

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function login($username, $password) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE username = ? AND status = 'active'");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // Simple password check (in production, use password_hash/verify)
        if ($password === $user['password']) {
            startSession();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            return true;
        }
    }
    return false;
}

function logout() {
    startSession();
    session_unset();
    session_destroy();
}

function isLoggedIn() {
    startSession();
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function checkAuth() {
    if (!isLoggedIn()) {
        header('Location: /restaurant-pos/auth/login.php');
        exit;
    }
}

function hasRole($roles) {
    startSession();
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    return isset($_SESSION['role']) && in_array($_SESSION['role'], $roles);
}

function requireRole($roles) {
    checkAuth();
    if (!hasRole($roles)) {
        die('Access denied');
    }
}

// ============================================================
// DASHBOARD STATISTICS
// ============================================================

function getTodaySales() {
    $db = getDBConnection();
    $today = date('Y-m-d');
    $query = "SELECT COALESCE(SUM(total_amount), 0) as total
              FROM sales
              WHERE DATE(created_at) = ? AND status = 'completed'";
    $stmt = $db->prepare($query);
    $stmt->bind_param("s", $today);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc()['total'];
}

function getActiveProductsCount() {
    $db = getDBConnection();
    $query = "SELECT COUNT(*) as count FROM products WHERE status = 'active'";
    $result = $db->query($query);
    return $result->fetch_assoc()['count'];
}

function getLowStockCount() {
    $db = getDBConnection();
    $query = "SELECT COUNT(*) as count FROM products WHERE stock <= min_stock AND status = 'active'";
    $result = $db->query($query);
    return $result->fetch_assoc()['count'];
}

function getActiveUsersCount() {
    $db = getDBConnection();
    $query = "SELECT COUNT(*) as count FROM users WHERE status = 'active'";
    $result = $db->query($query);
    return $result->fetch_assoc()['count'];
}

function getWeeklySalesChart() {
    $db = getDBConnection();
    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $query = "SELECT COALESCE(SUM(total_amount), 0) as total
                  FROM sales
                  WHERE DATE(created_at) = ? AND status = 'completed'";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $total = $result->fetch_assoc()['total'];

        $data[] = [
            'date' => date('D', strtotime($date)),
            'total' => $total
        ];
    }

    return $data;
}

function getTopProducts($limit = 5) {
    $db = getDBConnection();
    $query = "SELECT p.name, SUM(si.quantity) as total_sold, SUM(si.total) as revenue
              FROM sale_items si
              JOIN products p ON si.product_id = p.id
              GROUP BY si.product_id, p.name
              ORDER BY total_sold DESC
              LIMIT ?";
    $stmt = $db->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getReservationStats() {
    $db = getDBConnection();
    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $query = "SELECT COUNT(*) as count
                  FROM reservasi
                  WHERE DATE(created_at) = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("s", $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $count = $result->fetch_assoc()['count'];

        $data[] = [
            'date' => date('D', strtotime($date)),
            'count' => $count
        ];
    }

    return $data;
}

// ============================================================
// PRODUCT MANAGEMENT
// ============================================================

function getProducts($filters = []) {
    $db = getDBConnection();
    $query = "SELECT p.*, c.name as category_name
              FROM products p
              LEFT JOIN categories c ON p.category_id = c.id
              WHERE 1=1";

    $params = [];
    $types = "";

    if (isset($filters['category_id'])) {
        $query .= " AND p.category_id = ?";
        $params[] = $filters['category_id'];
        $types .= "i";
    }

    if (isset($filters['status'])) {
        $query .= " AND p.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }

    if (isset($filters['search'])) {
        $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
        $search = "%{$filters['search']}%";
        $params[] = $search;
        $params[] = $search;
        $types .= "ss";
    }

    $query .= " ORDER BY p.created_at DESC";

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($query);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getProductById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createProduct($data) {
    $db = getDBConnection();
    $query = "INSERT INTO products (category_id, name, description, price, cost_price, stock, min_stock, sku, image, status, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";

    $stmt = $db->prepare($query);
    $stmt->bind_param("issddiiiss",
        $data['category_id'],
        $data['name'],
        $data['description'],
        $data['price'],
        $data['cost_price'],
        $data['stock'],
        $data['min_stock'],
        $data['sku'],
        $data['image'],
        $data['status']
    );

    return $stmt->execute();
}

function updateProduct($id, $data) {
    $db = getDBConnection();
    $query = "UPDATE products SET
              category_id = ?, name = ?, description = ?, price = ?, cost_price = ?,
              stock = ?, min_stock = ?, sku = ?, image = ?, status = ?, updated_at = NOW()
              WHERE id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("issddiiissi",
        $data['category_id'],
        $data['name'],
        $data['description'],
        $data['price'],
        $data['cost_price'],
        $data['stock'],
        $data['min_stock'],
        $data['sku'],
        $data['image'],
        $data['status'],
        $id
    );

    return $stmt->execute();
}

function deleteProduct($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function updateStock($product_id, $quantity) {
    $db = getDBConnection();
    $stmt = $db->prepare("UPDATE products SET stock = stock - ?, updated_at = NOW() WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $product_id);
    return $stmt->execute();
}

// ============================================================
// CATEGORY MANAGEMENT
// ============================================================

function getCategories($active_only = false) {
    $db = getDBConnection();
    $query = "SELECT * FROM categories";
    if ($active_only) {
        $query .= " WHERE status = 'active'";
    }
    $query .= " ORDER BY sort_order ASC";
    return $db->query($query)->fetch_all(MYSQLI_ASSOC);
}

function getCategoryById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createCategory($data) {
    $db = getDBConnection();
    $query = "INSERT INTO categories (name, description, status, sort_order, created_at, updated_at)
              VALUES (?, ?, ?, ?, NOW(), NOW())";

    $stmt = $db->prepare($query);
    $stmt->bind_param("sssi",
        $data['name'],
        $data['description'],
        $data['status'],
        $data['sort_order']
    );

    return $stmt->execute();
}

function updateCategory($id, $data) {
    $db = getDBConnection();
    $query = "UPDATE categories SET name = ?, description = ?, status = ?, sort_order = ?, updated_at = NOW() WHERE id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("sssii",
        $data['name'],
        $data['description'],
        $data['status'],
        $data['sort_order'],
        $id
    );

    return $stmt->execute();
}

function deleteCategory($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// ============================================================
// SALES / POS MANAGEMENT
// ============================================================

function generateInvoiceNumber() {
    $date = date('Ymd');
    $random = rand(1000, 9999);
    return "INV-{$date}-{$random}";
}

function createSale($data, $items) {
    $db = getDBConnection();

    // Start transaction
    $db->begin_transaction();

    try {
        // Insert sale
        $invoice = generateInvoiceNumber();
        $query = "INSERT INTO sales (user_id, invoice_number, total_amount, payment_method, customer_name, customer_phone, status, created_at, updated_at)
                  VALUES (?, ?, ?, ?, ?, ?, 'completed', NOW(), NOW())";

        $stmt = $db->prepare($query);
        $stmt->bind_param("isdsss",
            $data['user_id'],
            $invoice,
            $data['total_amount'],
            $data['payment_method'],
            $data['customer_name'],
            $data['customer_phone']
        );
        $stmt->execute();
        $sale_id = $db->insert_id;

        // Insert sale items and update stock
        $item_query = "INSERT INTO sale_items (sale_id, product_id, product_name, product_sku, quantity, price, total, created_at)
                       VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        $item_stmt = $db->prepare($item_query);

        foreach ($items as $item) {
            $item_stmt->bind_param("iissidd",
                $sale_id,
                $item['product_id'],
                $item['product_name'],
                $item['product_sku'],
                $item['quantity'],
                $item['price'],
                $item['total']
            );
            $item_stmt->execute();

            // Update stock
            updateStock($item['product_id'], $item['quantity']);
        }

        $db->commit();
        return ['success' => true, 'invoice' => $invoice, 'sale_id' => $sale_id];

    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

function getSales($filters = []) {
    $db = getDBConnection();
    $query = "SELECT s.*, u.full_name as cashier_name
              FROM sales s
              LEFT JOIN users u ON s.user_id = u.id
              WHERE 1=1";

    $params = [];
    $types = "";

    if (isset($filters['date_from'])) {
        $query .= " AND DATE(s.created_at) >= ?";
        $params[] = $filters['date_from'];
        $types .= "s";
    }

    if (isset($filters['date_to'])) {
        $query .= " AND DATE(s.created_at) <= ?";
        $params[] = $filters['date_to'];
        $types .= "s";
    }

    if (isset($filters['status'])) {
        $query .= " AND s.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }

    $query .= " ORDER BY s.created_at DESC";

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($query);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getSaleById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT s.*, u.full_name as cashier_name
                          FROM sales s
                          LEFT JOIN users u ON s.user_id = u.id
                          WHERE s.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function getSaleItems($sale_id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM sale_items WHERE sale_id = ?");
    $stmt->bind_param("i", $sale_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============================================================
// RESERVATION MANAGEMENT
// ============================================================

function getReservations($filters = []) {
    $db = getDBConnection();
    $query = "SELECT * FROM reservasi WHERE 1=1";

    $params = [];
    $types = "";

    if (isset($filters['status'])) {
        $query .= " AND status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }

    if (isset($filters['date'])) {
        $query .= " AND DATE(tanggal_pemesanan) = ?";
        $params[] = $filters['date'];
        $types .= "s";
    }

    $query .= " ORDER BY tanggal_pemesanan DESC";

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($query);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getReservationById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM reservasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createReservation($data) {
    $db = getDBConnection();
    $query = "INSERT INTO reservasi (nama, no_hp, email, jumlah_anggota, tanggal_pemesanan, status, catatan, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, 'pending', ?, NOW(), NOW())";

    $stmt = $db->prepare($query);
    $stmt->bind_param("sssiss",
        $data['nama'],
        $data['no_hp'],
        $data['email'],
        $data['jumlah_anggota'],
        $data['tanggal_pemesanan'],
        $data['catatan']
    );

    return $stmt->execute();
}

function updateReservation($id, $data) {
    $db = getDBConnection();
    $query = "UPDATE reservasi SET
              nama = ?, no_hp = ?, email = ?, jumlah_anggota = ?,
              tanggal_pemesanan = ?, status = ?, catatan = ?, updated_at = NOW()
              WHERE id = ?";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ssssissi",
        $data['nama'],
        $data['no_hp'],
        $data['email'],
        $data['jumlah_anggota'],
        $data['tanggal_pemesanan'],
        $data['status'],
        $data['catatan'],
        $id
    );

    return $stmt->execute();
}

function deleteReservation($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM reservasi WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// ============================================================
// USER MANAGEMENT
// ============================================================

function getUsers($filters = []) {
    $db = getDBConnection();
    $query = "SELECT * FROM users WHERE 1=1";

    $params = [];
    $types = "";

    if (isset($filters['role'])) {
        $query .= " AND role = ?";
        $params[] = $filters['role'];
        $types .= "s";
    }

    if (isset($filters['status'])) {
        $query .= " AND status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }

    $query .= " ORDER BY created_at DESC";

    if (!empty($params)) {
        $stmt = $db->prepare($query);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $db->query($query);
    }

    return $result->fetch_all(MYSQLI_ASSOC);
}

function getUserById($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function createUser($data) {
    $db = getDBConnection();
    $query = "INSERT INTO users (username, password, full_name, email, role, status, created_at, updated_at)
              VALUES (?, ?, ?, ?, ?, 'active', NOW(), NOW())";

    $stmt = $db->prepare($query);
    $stmt->bind_param("sssss",
        $data['username'],
        $data['password'],
        $data['full_name'],
        $data['email'],
        $data['role']
    );

    return $stmt->execute();
}

function updateUser($id, $data) {
    $db = getDBConnection();

    if (!empty($data['password'])) {
        $query = "UPDATE users SET username = ?, password = ?, full_name = ?, email = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("ssssssi",
            $data['username'],
            $data['password'],
            $data['full_name'],
            $data['email'],
            $data['role'],
            $data['status'],
            $id
        );
    } else {
        $query = "UPDATE users SET username = ?, full_name = ?, email = ?, role = ?, status = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->bind_param("sssssi",
            $data['username'],
            $data['full_name'],
            $data['email'],
            $data['role'],
            $data['status'],
            $id
        );
    }

    return $stmt->execute();
}

function deleteUser($id) {
    $db = getDBConnection();
    $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

// ============================================================
// REPORTS
// ============================================================

function getSalesReport($period = 'daily', $date_from = null, $date_to = null) {
    $db = getDBConnection();

    if ($date_from === null) {
        $date_from = date('Y-m-d');
    }
    if ($date_to === null) {
        $date_to = date('Y-m-d');
    }

    $query = "SELECT
                COUNT(*) as total_transactions,
                SUM(total_amount) as total_revenue,
                AVG(total_amount) as avg_transaction
              FROM sales
              WHERE DATE(created_at) BETWEEN ? AND ?
              AND status = 'completed'";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $summary = $stmt->get_result()->fetch_assoc();

    // Get detailed sales
    $detail_query = "SELECT * FROM sales
                     WHERE DATE(created_at) BETWEEN ? AND ?
                     AND status = 'completed'
                     ORDER BY created_at DESC";

    $stmt = $db->prepare($detail_query);
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    $details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

    return [
        'summary' => $summary,
        'details' => $details
    ];
}

function getProductSalesReport($date_from, $date_to) {
    $db = getDBConnection();
    $query = "SELECT
                p.name,
                p.sku,
                SUM(si.quantity) as total_sold,
                SUM(si.total) as total_revenue,
                COUNT(DISTINCT si.sale_id) as transaction_count
              FROM sale_items si
              JOIN products p ON si.product_id = p.id
              JOIN sales s ON si.sale_id = s.id
              WHERE DATE(s.created_at) BETWEEN ? AND ?
              AND s.status = 'completed'
              GROUP BY si.product_id, p.name, p.sku
              ORDER BY total_sold DESC";

    $stmt = $db->prepare($query);
    $stmt->bind_param("ss", $date_from, $date_to);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// ============================================================
// UTILITY FUNCTIONS
// ============================================================

function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function uploadImage($file, $upload_dir = '../assets/uploads/') {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        return null;
    }

    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return null;
}

function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}
