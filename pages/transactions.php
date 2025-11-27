<?php
require_once '../core/functions.php';
checkAuth();

$page_title = 'Transaksi';

// Get filter parameters
$date_from = $_GET['date_from'] ?? date('Y-m-d');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

$filters = [
    'date_from' => $date_from,
    'date_to' => $date_to
];

$sales = getSales($filters);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Daftar Transaksi</h3>
            </div>

            <div class="card-body">
                <!-- Filter -->
                <form method="GET" class="form-row" style="margin-bottom: 20px;">
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
                            Filter
                        </button>
                    </div>
                </form>

                <!-- Table -->
                <div class="table-container">
                    <table id="transactionsTable">
                        <thead>
                            <tr>
                                <th>No Invoice</th>
                                <th>Tanggal</th>
                                <th>Kasir</th>
                                <th>Pelanggan</th>
                                <th>Total</th>
                                <th>Pembayaran</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($sales as $sale): ?>
                            <tr>
                                <td><?= htmlspecialchars($sale['invoice_number']) ?></td>
                                <td><?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></td>
                                <td><?= htmlspecialchars($sale['cashier_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($sale['customer_name'] ?: '-') ?></td>
                                <td><?= formatCurrency($sale['total_amount']) ?></td>
                                <td><?= ucfirst($sale['payment_method']) ?></td>
                                <td>
                                    <span class="badge badge-<?= $sale['status'] == 'completed' ? 'success' : 'warning' ?>">
                                        <?= ucfirst($sale['status']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick="viewDetail(<?= $sale['id'] ?>)">
                                        Detail
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>

                            <?php if (empty($sales)): ?>
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada transaksi</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Detail Modal -->
<div id="detailModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Detail Transaksi</h3>
            <button class="modal-close" onclick="closeModal('detailModal')">&times;</button>
        </div>
        <div class="modal-body" id="detailContent">
            Loading...
        </div>
    </div>
</div>

<script>
async function viewDetail(saleId) {
    openModal('detailModal');

    try {
        const response = await fetch(`../api/get-sale-detail.php?id=${saleId}`);
        const data = await response.json();

        if (data.success) {
            const sale = data.sale;
            const items = data.items;

            let itemsHtml = '';
            items.forEach(item => {
                itemsHtml += `
                    <tr>
                        <td>${item.product_name}</td>
                        <td>${formatCurrency(item.price)}</td>
                        <td>${item.quantity}</td>
                        <td>${formatCurrency(item.total)}</td>
                    </tr>
                `;
            });

            const html = `
                <div style="margin-bottom: 20px;">
                    <h4>Invoice: ${sale.invoice_number}</h4>
                    <p>Tanggal: ${sale.created_at}</p>
                    <p>Kasir: ${sale.cashier_name || '-'}</p>
                    <p>Pelanggan: ${sale.customer_name || '-'}</p>
                    <p>Pembayaran: ${sale.payment_method}</p>
                </div>

                <table style="width: 100%;">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${itemsHtml}
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong>${formatCurrency(sale.total_amount)}</strong></td>
                        </tr>
                    </tfoot>
                </table>
            `;

            $('#detailContent').innerHTML = html;
        } else {
            $('#detailContent').innerHTML = '<p class="text-center text-danger">Gagal memuat detail</p>';
        }
    } catch (error) {
        $('#detailContent').innerHTML = '<p class="text-center text-danger">Terjadi kesalahan</p>';
    }
}
</script>

<?php include '../includes/footer.php'; ?>
