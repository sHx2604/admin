<?php
require_once '../core/functions.php';
checkAuth();
requireRole(['admin']);

$page_title = 'Manajemen Pengguna';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        $data = [
            'username' => sanitizeInput($_POST['username']),
            'password' => $_POST['password'],
            'full_name' => sanitizeInput($_POST['full_name']),
            'email' => sanitizeInput($_POST['email']),
            'role' => $_POST['role']
        ];

        if (createUser($data)) {
            $success = 'Pengguna berhasil ditambahkan!';
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        $data = [
            'username' => sanitizeInput($_POST['username']),
            'password' => $_POST['password'],
            'full_name' => sanitizeInput($_POST['full_name']),
            'email' => sanitizeInput($_POST['email']),
            'role' => $_POST['role'],
            'status' => $_POST['status']
        ];

        if (updateUser($id, $data)) {
            $success = 'Pengguna berhasil diupdate!';
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        if ($id != $_SESSION['user_id']) {
            if (deleteUser($id)) {
                $success = 'Pengguna berhasil dihapus!';
            }
        } else {
            $error = 'Tidak dapat menghapus akun sendiri!';
        }
    }
}

$users = getUsers();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Daftar Pengguna</h3>
                <button class="btn btn-primary" onclick="openModal('addModal')">
                    + Tambah Pengguna
                </button>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="card-body">
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Username</th>
                                <th>Nama Lengkap</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td>
                                    <?php
                                    $role_badge = 'info';
                                    if ($user['role'] == 'admin') $role_badge = 'danger';
                                    elseif ($user['role'] == 'manager') $role_badge = 'warning';
                                    ?>
                                    <span class="badge badge-<?= $role_badge ?>">
                                        <?= ucfirst($user['role']) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-<?= $user['status'] == 'active' ? 'success' : 'danger' ?>">
                                        <?= ucfirst($user['status']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick='editUser(<?= json_encode($user) ?>)'>
                                        Edit
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $user['id'] ?>">
                                        <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                    <?php endif; ?>
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
            <h3>Tambah Pengguna</h3>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Role</label>
                    <select name="role" class="form-control" required>
                        <option value="cashier">Cashier</option>
                        <option value="manager">Manager</option>
                        <option value="admin">Admin</option>
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
            <h3>Edit Pengguna</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" id="edit_username" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Password (Kosongkan jika tidak diubah)</label>
                    <input type="password" name="password" id="edit_password" class="form-control">
                </div>

                <div class="form-group">
                    <label class="form-label">Nama Lengkap</label>
                    <input type="text" name="full_name" id="edit_full_name" class="form-control" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" id="edit_email" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Role</label>
                        <select name="role" id="edit_role" class="form-control" required>
                            <option value="cashier">Cashier</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
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
function editUser(user) {
    $('#edit_id').value = user.id;
    $('#edit_username').value = user.username;
    $('#edit_full_name').value = user.full_name;
    $('#edit_email').value = user.email;
    $('#edit_role').value = user.role;
    $('#edit_status').value = user.status;
    $('#edit_password').value = '';

    openModal('editModal');
}
</script>

<?php include '../includes/footer.php'; ?>
