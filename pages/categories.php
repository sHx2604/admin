<?php
require_once '../core/functions.php';
checkAuth();
requireRole(['admin', 'manager']);

$page_title = 'Manajemen Kategori';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'status' => $_POST['status'],
            'sort_order' => $_POST['sort_order']
        ];

        if (createCategory($data)) {
            $success = 'Kategori berhasil ditambahkan!';
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'name' => sanitizeInput($_POST['name']),
            'description' => sanitizeInput($_POST['description']),
            'status' => $_POST['status'],
            'sort_order' => $_POST['sort_order']
        ];

        if (updateCategory($id, $data)) {
            $success = 'Kategori berhasil diupdate!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        if (deleteCategory($id)) {
            $success = 'Kategori berhasil dihapus!';
        }
    }
}

$categories = getCategories();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Daftar Kategori</h3>
                <button class="btn btn-primary" onclick="openModal('addModal')">
                    + Tambah Kategori
                </button>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?= htmlspecialchars($category['name']) ?></td>
                                <td><?= htmlspecialchars($category['description']) ?></td>
                                <td><?= $category['sort_order'] ?></td>
                                <td>
                                    <span class="badge badge-<?= $category['status'] == 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($category['status']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick='editCategory(<?= json_encode($category) ?>)'>
                                        Edit
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $category['id'] ?>">
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
            <h3>Tambah Kategori</h3>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" class="form-control" value="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
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
            <h3>Edit Kategori</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-group">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="description" id="edit_description" class="form-control"></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <select name="status" id="edit_status" class="form-control" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
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
function editCategory(category) {
    $('#edit_id').value = category.id;
    $('#edit_name').value = category.name;
    $('#edit_description').value = category.description;
    $('#edit_sort_order').value = category.sort_order;
    $('#edit_status').value = category.status;
    openModal('editModal');
}
</script>

<?php include '../includes/footer.php'; ?>
