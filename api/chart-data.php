<?php
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

header('Content-Type: application/json');

// Penjualan hari ini & jumlah reservasi
$stats = getDashboardStats();

// Transaksi produk mingguan
global $pdo;
$startOfWeek = date('Y-m-d', strtotime('monday this week'));
$endOfWeek = date('Y-m-d', strtotime('sunday this week'));
$stmt = $pdo->prepare("
    SELECT si.product_id, SUM(si.qty) as total_qty, p.name
    FROM sale_items si
    JOIN sales s ON si.sale_id = s.id
    JOIN products p ON si.product_id = p.id
    WHERE DATE(s.created_at) BETWEEN ? AND ?
    GROUP BY si.product_id
");
$stmt->execute([$startOfWeek, $endOfWeek]);
$productTransactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    'today_sales' => $stats['today_sales'],
    'total_reservation' => $stats['total_reservation'] ?? 0,
    'product_transactions' => $productTransactions
]);