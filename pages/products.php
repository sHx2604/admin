<?php
require_once '../core/functions.php';
checkAuth();
requireRole(['admin', 'manager']);

$page_title = 'Manajemen Produk';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $data = [
            'category_id' => $_POST['category_id'],
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => $_POST['price'],
            'cost_price' => $_POST['cost_price'],
            'stock' => $_POST['stock'],
            'min_stock' => $_POST['min_stock'],
            'sku' => sanitizeInput($_POST['sku']),
            'image' => null,
            'status' => $_POST['status']
        ];

        if (createProduct($data)) {
            $success = 'Produk berhasil ditambahkan!';
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'category_id' => $_POST['category_id'],
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'price' => $_POST['price'],
            'cost_price' => $_POST['cost_price'],
            'stock' => $_POST['stock'],
            'min_stock' => $_POST['min_stock'],
            'sku' => sanitizeInput($_POST['sku']),
            'image' => $_POST['current_image'],
            'status' => $_POST['status']
        ];

        if (updateProduct($id, $data)) {
            $success = 'Produk berhasil diupdate!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        if (deleteProduct($id)) {
            $success = 'Produk berhasil dihapus!';
        }
    }
}

$products = getProducts();
$categories = getCategories(true);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Daftar Produk</h3>
                <button class="btn btn-primary" onclick="openModal('addModal')">
                    + Tambah Produk
                </button>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <div class="card-body">
                <input type="text" id="searchTable" class="form-control" placeholder="Cari produk..." style="margin-bottom: 20px;">

                <div class="table-container">
                    <table id="productsTable">
                        <thead>
                            <tr>
                                <th>SKU</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Harga Beli</th>
                                <th>Stok</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['sku']) ?></td>
                                <td><?= htmlspecialchars($product['name']) ?></td>
                                <td><?= htmlspecialchars($product['category_name'] ?? '-') ?></td>
                                <td><?= formatCurrency($product['price']) ?></td>
                                <td><?= formatCurrency($product['cost_price']) ?></td>
                                <td>
                                    <?= $product['stock'] ?>
                                    <?php if ($product['stock'] <= $product['min_stock']): ?>
                                        <span class="badge badge-warning">Low</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $product['status'] == 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($product['status']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick="editProduct(<?= $product['id'] ?>)">
                                        Edit
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $product['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Tambah Produk</h3>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="create">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" class="form-control" required>
                            <option value="">Pilih Kategori</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" class="form-control" placeholder="Kosongkan untuk auto-generate">
                        <small class="form-text">Biarkan kosong untuk generate otomatis</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="price" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="cost_price" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="min_stock" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('addModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Produk</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST" id="editForm">
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">
                <input type="hidden" name="current_image" id="edit_current_image">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" id="edit_category_id" class="form-control" required>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" id="edit_sku" class="form-control" placeholder="Kosongkan untuk auto-generate">
                        <small class="form-text">Biarkan kosong untuk generate otomatis</small>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Harga Jual</label>
                        <input type="number" name="price" id="edit_price" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Harga Beli</label>
                        <input type="number" name="cost_price" id="edit_cost_price" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Stok</label>
                        <input type="number" name="stock" id="edit_stock" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Stok Minimum</label>
                        <input type="number" name="min_stock" id="edit_min_stock" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
searchTable('searchTable', 'productsTable');

async function editProduct(id) {
    try {
        const response = await fetch(`../api/get-product.php?id=${id}`);
        const data = await response.json();

        if (data.success) {
            const product = data.product;

            $('#edit_id').value = product.id;
            $('#edit_category_id').value = product.category_id;
            $('#edit_name').value = product.name;
            $('#edit_description').value = product.description;
            $('#edit_price').value = product.price;
            $('#edit_cost_price').value = product.cost_price;
            $('#edit_stock').value = product.stock;
            $('#edit_min_stock').value = product.min_stock;
            $('#edit_sku').value = product.sku;
            $('#edit_current_image').value = product.image || '';
            $('#edit_status').value = product.status;

            openModal('editModal');
        }
    } catch (error) {
        showAlert('Gagal memuat data produk', 'danger');
    }
}
</script>

<?php include '../includes/footer.php'; ?>
