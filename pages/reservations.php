<?php
require_once '../core/functions.php';
checkAuth();

$page_title = 'Manajemen Reservasi';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'create') {
        // Convert datetime-local format (YYYY-MM-DDTHH:mm) to MySQL DATETIME (YYYY-MM-DD HH:mm:ss)
        $tanggal = $_POST['tanggal_pemesanan'] ?? '';
        
        // Validate datetime format
        if (empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $tanggal)) {
            $success = 'Tanggal pemesanan tidak valid!';
        } else {
            $tanggal = str_replace('T', ' ', $tanggal) . ':00';
            
            $data = [
                'nama' => sanitizeInput($_POST['nama']),
                'no_hp' => sanitizeInput($_POST['no_hp']),
                'email' => sanitizeInput($_POST['email']),
                'jumlah_anggota' => (int)$_POST['jumlah_anggota'],
                'tanggal_pemesanan' => $tanggal,
                'catatan' => sanitizeInput($_POST['catatan'])
            ];

            if (createReservation($data)) {
                $success = 'Reservasi berhasil ditambahkan!';
            }
        }
    } elseif ($action === 'update') {
        $id = $_POST['id'];
        
        // Convert datetime-local format (YYYY-MM-DDTHH:mm) to MySQL DATETIME (YYYY-MM-DD HH:mm:ss)
        $tanggal = $_POST['tanggal_pemesanan'] ?? '';
        
        // Validate datetime format
        if (empty($tanggal) || !preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}/', $tanggal)) {
            $success = 'Tanggal pemesanan tidak valid!';
        } else {
            $tanggal = str_replace('T', ' ', $tanggal) . ':00';
            
            $data = [
                'nama' => sanitizeInput($_POST['nama']),
                'no_hp' => sanitizeInput($_POST['no_hp']),
                'email' => sanitizeInput($_POST['email']),
                'jumlah_anggota' => (int)$_POST['jumlah_anggota'],
                'tanggal_pemesanan' => $tanggal,
                'status' => $_POST['status'],
                'catatan' => sanitizeInput($_POST['catatan'])
            ];

            if (updateReservation($id, $data)) {
                $success = 'Reservasi berhasil diupdate!';
            }
        }
    } elseif ($action === 'delete') {
        $id = $_POST['id'];
        if (deleteReservation($id)) {
            $success = 'Reservasi berhasil dihapus!';
        }
    }
}

$reservations = getReservations();

include '../includes/header.php';
include '../includes/navbar.php';
?>

<div class="main-content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3>Daftar Reservasi</h3>
                <button class="btn btn-primary" onclick="openModal('addModal')">
                    + Tambah Reservasi
                </button>
            </div>

            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>

            <div class="card-body">
                <div class="table-container">
                    <table id="reservationsTable">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>No HP</th>
                                <th>Email</th>
                                <th>Jumlah Tamu</th>
                                <th>Tanggal Reservasi</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservations as $reservation): ?>
                            <tr>
                                <td><?= htmlspecialchars($reservation['nama']) ?></td>
                                <td><?= htmlspecialchars($reservation['no_hp']) ?></td>
                                <td><?= htmlspecialchars($reservation['email']) ?></td>
                                <td><?= $reservation['jumlah_anggota'] ?> orang</td>
                                <td><?= date('d/m/Y H:i', strtotime($reservation['tanggal_pemesanan'])) ?></td>
                                <td>
                                    <?php
                                    $badge_class = 'info';
                                    if ($reservation['status'] == 'confirmed') $badge_class = 'success';
                                    elseif ($reservation['status'] == 'cancelled') $badge_class = 'danger';
                                    elseif ($reservation['status'] == 'completed') $badge_class = 'success';
                                    ?>
                                    <span class="badge badge-<?= $badge_class ?>">
                                        <?= ucfirst($reservation['status']) ?>
                                    </span>
                                </td>
                                <td class="table-actions">
                                    <button class="btn btn-sm btn-primary" onclick="editReservation(<?= htmlspecialchars(json_encode($reservation), ENT_QUOTES, 'UTF-8') ?>)">
                                        Edit
                                    </button>
                                    <form method="POST" style="display: inline;" onsubmit="return confirmDelete();">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $reservation['id'] ?>">
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
            <h3>Tambah Reservasi</h3>
            <button class="modal-close" onclick="closeModal('addModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="create">

                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jumlah Tamu</label>
                        <input type="number" name="jumlah_anggota" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal & Waktu</label>
                        <input type="datetime-local" name="tanggal_pemesanan" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"></textarea>
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
            <h3>Edit Reservasi</h3>
            <button class="modal-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form method="POST">
            <div class="modal-body">
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="edit_id">

                <div class="form-group">
                    <label class="form-label">Nama</label>
                    <input type="text" name="nama" id="edit_nama" class="form-control" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">No HP</label>
                        <input type="text" name="no_hp" id="edit_no_hp" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" id="edit_email" class="form-control" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Jumlah Tamu</label>
                        <input type="number" name="jumlah_anggota" id="edit_jumlah_anggota" class="form-control" min="1" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Tanggal & Waktu</label>
                        <input type="datetime-local" name="tanggal_pemesanan" id="edit_tanggal_pemesanan" class="form-control" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-control" required>
                        <option value="pending">Pending</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" id="edit_catatan" class="form-control" rows="3"></textarea>
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
function editReservation(reservation) {
    $('#edit_id').value = reservation.id;
    $('#edit_nama').value = reservation.nama;
    $('#edit_no_hp').value = reservation.no_hp;
    $('#edit_email').value = reservation.email;
    $('#edit_jumlah_anggota').value = reservation.jumlah_anggota;

    // Format datetime from database (YYYY-MM-DD HH:mm:ss) to datetime-local (YYYY-MM-DDTHH:mm)
    if (reservation.tanggal_pemesanan) {
        // Input datetime-local expects format YYYY-MM-DDTHH:mm
        const datetime = reservation.tanggal_pemesanan.substring(0, 16).replace(' ', 'T');
        $('#edit_tanggal_pemesanan').value = datetime;
    }

    $('#edit_status').value = reservation.status;
    $('#edit_catatan').value = reservation.catatan || '';

    openModal('editModal');
}
</script>

<?php include '../includes/footer.php'; ?>
