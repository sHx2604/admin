<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

requireLogin();
$kategori = getAllCategories();

// --- Tambah Kategori Baru ---
if (isset($_POST['submit_baru'])) {
    $nama = $_POST['name'];
    $deskripsi = $_POST['description'];
    $status = $_POST['status'];
    // sort_order bisa diatur otomatis atau dari input, di sini default 0
    $sort_order = 0;
    $stmt = $pdo->prepare("INSERT INTO categories (name, description, status, sort_order) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nama, $deskripsi, $status, $sort_order]);
    header("Location: kategori.php");
    exit;
}

// --- Update Kategori ---
if (isset($_POST['submit_edit'])) {
    $id = $_POST['id'];
    $nama = $_POST['name'];
    $deskripsi = $_POST['description'];
    $status = $_POST['status'];
    $stmt = $pdo->prepare("UPDATE categories SET name=?, description=?, status=? WHERE id=?");
    $stmt->execute([$nama, $deskripsi, $status, $id]);
    header("Location: kategori.php");
    exit;
}

// --- Hapus Kategori ---
if (isset($_GET['hapus'])) {
    $id = $_GET['hapus'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
    $stmt->execute([$id]);
    header("Location: kategori.php");
    exit;
}

// --- Ambil data user untuk form edit jika ada parameter edit ---
$edit_mode = false;
$edit_user = null;
if (isset($_GET['edit'])) {
    $edit_mode = true;
    $id = $_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE id=?");
    $stmt->execute([$id]);
    $edit_user = $stmt->fetch(PDO::FETCH_ASSOC);

}
?>


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>TRINITY SYSTEM</title>
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta content="" name="keywords">
    <meta content="" name="description">

    <!-- Favicon -->
    <link href="img/favicon.ico" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icon Font Stylesheet -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">
    <link href="lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css" rel="stylesheet" />

    <!-- Customized Bootstrap Stylesheet -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Template Stylesheet -->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->


        <!-- Sidebar Start -->
        <div class="sidebar pe-4 pb-3">
            <nav class="navbar bg-light navbar-light">
                <a href="index.html" class="navbar-brand mx-4 mb-3">
                    <h3 class="text-primary"><i class="fa fa-store me-2"></i> 
  TRINITY</h3>
                </a>
                <div class="d-flex align-items-center ms-4 mb-4">
                    <div class="position-relative">
                        <img class="rounded-circle" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                        <div class="bg-success rounded-circle border border-2 border-white position-absolute end-0 bottom-0 p-1"></div>
                    </div>
                    <div class="ms-3">
                        <h6 class="mb-0"><?php echo $_SESSION['full_name']; ?></h6>
                        <span><?php echo $_SESSION['role']; ?></span>
                    </div>
                </div>
                <div class="navbar-nav w-100">
                    
                   <a href="../admin/dashboard.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Dashboard</a>
                    <a href="../admin/menu.php" class="nav-item nav-link"><i class="fa fa-box me-2"></i>Menu</a>
                    <a href="../admin/transaction.php" class="nav-item nav-link"><i class="fa fa-receipt me-2"></i>Transaksi</a>
                    <a href="../admin/kategori.php" class="nav-item nav-link active"><i class="fa fa-check-square me-2"></i>Kategori</a>
                    <a href="../admin/reservation.php" class="nav-item nav-link"><i class="fa fa-briefcase me-2"></i>Reservasi</a>
                    <a href="../admin/user.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>User</a>
                    <a href="../admin/sales.php" class="nav-item nav-link"><i class="fa fa-chart-line me-2"></i>Laporan</a>
                    <a href="../admin/pos.php" class="nav-item nav-link"><i class="fa fa-university me-2"></i>Kasir</a>
                    
                </div>
            </nav>
        </div>
        <!-- Sidebar End -->


        <!-- Content Start -->
        <div class="content">
            <!-- Navbar Start -->
            <nav class="navbar navbar-expand bg-light navbar-light sticky-top px-4 py-0">
                <a href="index.html" class="navbar-brand d-flex d-lg-none me-4">
                    <h2 class="text-primary mb-0"><i class="fa fa-store"></i></h2>
                </a>
                <a href="#" class="sidebar-toggler flex-shrink-0">
                    <i class="fa fa-bars"></i>
                </a>
                <form class="d-none d-md-flex ms-4">
                    
                </form>
                <div class="navbar-nav align-items-center ms-auto">
                    <div class="nav-item dropdown">
                        <a href="#" class="nav-link dropdown-toggle">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['full_name']; ?></span>
                        </a>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->


            <!-- Blank Start -->
            <div class="container-fluid pt-4 px-4">
                <?php if(!$edit_mode): ?>
            <div class="row g-4">
            <div class="col-sm-12 col-xl-6">
                <div class="bg-light rounded h-100 p-4">
                <h6 class="mb-4">Kategori Baru</h6>
            <form method="POST" action="">
                <div class="mb-3">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Deskripsi</label>
                    <input type="text" name="description" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>Status</label><br>
                    <input type="radio" name="status" value="active" checked> Aktif
                    <input type="radio" name="status" value="inactive"> Tidak Aftif
                </div>
                <button type="submit" name="submit_baru" class="btn btn-primary">Simpan</button>
                </form>
            </div>
              <?php endif; ?>
            
            </div>
                        
            <div class="col-sm-12 col-xl-6">
            <div class="bg-light rounded h-100 p-4">
            <h6 class="mb-4">Edit Kategori</h6>
            <form method="POST" action="">
                <input type="hidden" name="id" value="<?= $edit_user['id'] ?>">
                <div class="mb-3">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" class="form-control" required value="<?= $edit_user['name'] ?>">
                </div>
                <div class="mb-3">
                    <label>Username</label>
                    <input type="text" name="description" class="form-control" required value="<?= $edit_user['description'] ?>">
                </div>
                <div class="mb-3">
                    <label>Status</label><br>
                    <input type="radio" name="status" value="active" <?= $edit_user['status'] == 'active' ? 'checked' : '' ?>> Aktif
                    <input type="radio" name="status" value="inactive" <?= $edit_user['status'] == 'inactive' ? 'checked' : '' ?>> Tidak Aktif

                </div>
                <button type="submit" name="submit_edit" class="btn btn-warning">Update</button>
                <a href="user.php" class="btn btn-secondary">Batal</a>
            </form>     
                        </div>
                    </div> 
                <div class="col-12">
                    <div class="bg-light rounded h-100 p-4">
                        <h6 class="mb-4">Daftar Kategori</h6>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nama Kategori</th>
                                        <th scope="col">Deskripsi</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Urutan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $no = 1;
                                    foreach ($kategori as $k): ?>
                                    <tr>
                                        <th scope="row"><?php echo $no++; ?></th>
                                        <td><?php echo htmlspecialchars($k['name']); ?></td>
                                        <td><?php echo htmlspecialchars($k['description']); ?></td>
                                        <td><?php echo htmlspecialchars($k['status']); ?></td>
                                        <td><?php echo htmlspecialchars($k['sort_order']); ?></td>
                                        <td>
                                            <a href="kategori.php?edit=<?php echo $k['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                            <a href="kategori.php?hapus=<?php echo $k['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus kategori ini?')">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if (empty($kategori)): ?>
                                    <tr><td colspan="6" class="text-center">Belum ada kategori.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bl End -->
        </div>
        <!-- Content End -->


        <!-- Back to Top -->
        <a href="#" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/chart/chart.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="lib/tempusdominus/js/moment.min.js"></script>
    <script src="lib/tempusdominus/js/moment-timezone.min.js"></script>
    <script src="lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js"></script>

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
</body>
</html>