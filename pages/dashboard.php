<?php
require_once '../core/functions.php';
checkAuth();

$page_title = 'Dashboard';

// Get statistics
$today_sales = getTodaySales();
$active_products = getActiveProductsCount();
$low_stock = getLowStockCount();
$active_users = getActiveUsersCount();
$weekly_sales = getWeeklySalesChart();
$top_products = getTopProducts(5);
$reservation_stats = getReservationStats();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <h1>Dashboard</h1>
        <p style="color: var(--secondary); margin-bottom: 30px;">
            Selamat datang, <?= $_SESSION['full_name'] ?>!
        </p>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">💰</div>
                <div class="stat-info">
                    <h4><?= formatCurrency($today_sales) ?></h4>
                    <p>Penjualan Hari Ini</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">📦</div>
                <div class="stat-info">
                    <h4><?= $active_products ?></h4>
                    <p>Produk Aktif</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">⚠️</div>
                <div class="stat-info">
                    <h4><?= $low_stock ?></h4>
                    <p>Stok Rendah</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">👥</div>
                <div class="stat-info">
                    <h4><?= $active_users ?></h4>
                    <p>Pengguna Aktif</p>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 20px; margin-bottom: 30px;">
            <div class="card">
                <div class="card-header">
                    <h3>Penjualan 7 Hari Terakhir</h3>
                </div>
                <div class="card-body">
                    <canvas id="salesChart" width="400" height="200"></canvas>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3>Reservasi 7 Hari Terakhir</h3>
                </div>
                <div class="card-body">
                    <canvas id="reservationChart" width="400" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Products -->
        <div class="card">
            <div class="card-header">
                <h3>Top 5 Produk Terlaris</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>Total Terjual</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($top_products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= $product['total_sold'] ?> pcs</td>
                                <td><?= formatCurrency($product['revenue']) ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($top_products)): ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data penjualan</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Draw Sales Chart
const salesData = <?= json_encode(array_column($weekly_sales, 'total')) ?>;
const salesLabels = <?= json_encode(array_column($weekly_sales, 'date')) ?>;

if (salesData && salesData.length > 0 && typeof drawBarChart === 'function') {
    drawBarChart('salesChart', salesData, salesLabels);
} else {
    console.warn('Sales chart data not available or drawBarChart function not found');
}

// Draw Reservation Chart
const reservationData = <?= json_encode(array_column($reservation_stats, 'count')) ?>;
const reservationLabels = <?= json_encode(array_column($reservation_stats, 'date')) ?>;

if (reservationData && reservationData.length > 0 && typeof drawLineChart === 'function') {
    drawLineChart('reservationChart', reservationData, reservationLabels);
} else {
    console.warn('Reservation chart data not available or drawLineChart function not found');
}
</script>

<?php include '../includes/footer.php'; ?>
