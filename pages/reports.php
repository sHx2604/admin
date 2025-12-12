<?php
require_once '../core/functions.php';
checkAuth();
requireRole(['admin', 'manager']);

$page_title = 'Laporan Penjualan';

// Get filter parameters - use custom date range
$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

$report = getSalesReport('custom', $date_from, $date_to);
$product_report = getProductSalesReport($date_from, $date_to);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card no-print">
            <div class="card-header">
                <h3>Filter Laporan</h3>
                <button class="btn btn-primary" onclick="window.print()">
                    🖨️ Cetak Laporan
                </button>
            </div>
            <div class="card-body">
                <form method="GET" class="form-row">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Dari Tanggal</label>
                        <input type="date" name="date_from" class="form-control" value="<?= $date_from ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Sampai Tanggal</label>
                        <input type="date" name="date_to" class="form-control" value="<?= $date_to ?>">
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary" style="display: block;">
                            Tampilkan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Report Header -->
        <div style="text-align: center; margin: 30px 0;">
            <h1>Trinity Restaurant</h1>
            <h3>Laporan Penjualan</h3>
            <p>Periode: <?= date('d/m/Y', strtotime($date_from)) ?> - <?= date('d/m/Y', strtotime($date_to)) ?></p>
            <p class="no-print">Dicetak pada: <?= date('d/m/Y H:i') ?></p>
        </div>

        <!-- Summary Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">💰</div>
                <div class="stat-info">
                    <h4><?= formatCurrency($report['summary']['total_revenue'] ?? 0) ?></h4>
                    <p>Total Pendapatan</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon green">📊</div>
                <div class="stat-info">
                    <h4><?= $report['summary']['total_transactions'] ?? 0 ?></h4>
                    <p>Total Transaksi</p>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">📈</div>
                <div class="stat-info">
                    <h4><?= formatCurrency($report['summary']['avg_transaction'] ?? 0) ?></h4>
                    <p>Rata-rata Transaksi</p>
                </div>
            </div>
        </div>

        <!-- Product Sales Report -->
        <div class="card">
            <div class="card-header">
                <h3>Laporan Penjualan Produk</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Terjual</th>
                                <th>Transaksi</th>
                                <th>Total Pendapatan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($product_report as $item): ?>
                            <tr>
                                <td><?= htmlspecialchars($item['name']) ?></td>
                                <td><?= htmlspecialchars($item['sku']) ?></td>
                                <td><?= $item['total_sold'] ?> pcs</td>
                                <td><?= $item['transaction_count'] ?></td>
                                <td><?= formatCurrency($item['total_revenue']) ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($product_report)): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Detailed Sales -->
        <div class="card">
            <div class="card-header">
                <h3>Detail Transaksi</h3>
            </div>
            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Invoice</th>
                                <th>Tanggal</th>
                                <th>Pelanggan</th>
                                <th>Pembayaran</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($report['details'] as $sale): ?>
                            <tr>
                                <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
                                <td><?= htmlspecialchars($sale['customer_name'] ?: '-') ?></td>
                                <td><?= ucfirst($sale['payment_method']) ?></td>
                                <td><?= formatCurrency($sale['total_amount']) ?></td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($report['details'])): ?>
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada transaksi</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                        <tfoot>
                            <tr style="font-weight: bold; background: var(--light);">
                                <td colspan="4">TOTAL</td>
                                <td><?= formatCurrency($report['summary']['total_revenue'] ?? 0) ?></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin: 40px 0; padding: 20px; border-top: 1px solid var(--border);">
            <p style="color: var(--secondary);">
                Trinity Restaurant POS System<br>
                © <?= date('Y') ?> All Rights Reserved
            </p>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
