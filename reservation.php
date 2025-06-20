<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

requireLogin();
$tblreservasi = getReservations();

// Tambah: proses hapus reservasi
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    global $pdo;
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM reservasi WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: reservation.php?success=1');
    exit();
}

// Proses edit reservasi
if (isset($_POST['edit_reservasi'])) {
    global $pdo;
    $id = (int)$_POST['id'];
    $nama = trim($_POST['nama']);
    $no_hp = trim($_POST['no_hp']);
    $email = trim($_POST['email']);
    $jumlah_anggota = (int)$_POST['jumlah_anggota'];
    $tanggal_pemesanan = trim($_POST['tanggal_pemesanan']);

    $stmt = $pdo->prepare("UPDATE reservasi SET nama = ?, no_hp = ?, email = ?, jumlah_anggota = ?, tanggal_pemesanan = ? WHERE id = ?");
    $stmt->execute([$nama, $no_hp, $email, $jumlah_anggota, $tanggal_pemesanan, $id]);
    header('Location: reservation.php?success=2');
    exit();
}

// Ambil data reservasi untuk form edit jika ada parameter edit
$edit_reservasi = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    global $pdo;
    $id = (int)$_GET['edit'];
    $stmt = $pdo->prepare("SELECT * FROM reservasi WHERE id = ?");
    $stmt->execute([$id]);
    $edit_reservasi = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>DASHMIN - Bootstrap Admin Template</title>
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
                    <h3 class="text-primary"><i class="fa fa-hashtag me-2"></i>DASHMIN</h3>
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
                    <a href="../admin/menu.php" class="nav-item nav-link"><i class="fa fa-keyboard me-2"></i>Menu</a>
                    <a href="../admin/transaction.php" class="nav-item nav-link"><i class="fa fa-usd me-2"></i>Transaksi</a>
                    <a href="../admin/kategori.php" class="nav-item nav-link"><i class="fa fa-check-square me-2"></i>Kategori</a>
                    <a href="../admin/reservation.php" class="nav-item nav-link active"><i class="fa fa-handshake-o me-2"></i>Reservasi</a>
                    <a href="../admin/user.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>User</a>
                    <a href="../admin/sales.php" class="nav-item nav-link"><i class="fa fa-bar-chart me-2"></i>Laporan</a>
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
                    <h2 class="text-primary mb-0"><i class="fa fa-hashtag"></i></h2>
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
               <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">Daftar reservasi</h6>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">Nama</th>
                                        <th scope="col">No Hp</th>
                                        <th scope="col">Email</th>
                                        <th scope="col">Jumlah</th>
                                        <th scope="col">Tanggal Pemesanan</th>
                                        <th scope="col">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $reservasi = getReservations(); // ambil data reservasi dari fungsi
                                    $no = 1;
                                    foreach ($reservasi as $r):
                                    ?>
                                    <tr>
                                        <th scope="row"><?php echo $no++; ?></th>
                                        <td><?php echo htmlspecialchars($r['nama']); ?></td>
                                        <td><?php echo htmlspecialchars($r['no_hp']); ?></td>
                                        <td><?php echo htmlspecialchars($r['email']); ?></td>
                                        <td><?php echo htmlspecialchars($r['jumlah_anggota']); ?></td>
                                        <td><?php echo formatDate($r['tanggal_pemesanan']); ?></td>
                                        <td>
                                            <a href="reservation.php?edit=<?php echo $r['id']; ?>" class="btn btn-primary btn-sm">Edit</a>
                                            <a href="reservation.php?delete=<?php echo $r['id']; ?>" class="btn btn-danger btn-sm">Hapus</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                            </div>

                             <?php if ($edit_reservasi): ?>
            <div class="col-12 mb-4">
                <div class="bg-light rounded h-100 p-4">
                    <h6 class="mb-4">Edit Reservasi</h6>
                    <form method="POST" action="">
                        <input type="hidden" name="id" value="<?= htmlspecialchars($edit_reservasi['id']) ?>">
                        <div class="mb-3">
                            <label>Nama</label>
                            <input type="text" name="nama" class="form-control" required value="<?= htmlspecialchars($edit_reservasi['nama']) ?>">
                        </div>
                        <div class="mb-3">
                            <label>No HP</label>
                            <input type="text" name="no_hp" class="form-control" required value="<?= htmlspecialchars($edit_reservasi['no_hp']) ?>">
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($edit_reservasi['email']) ?>">
                        </div>
                        <div class="mb-3">
                            <label>Jumlah Anggota</label>
                            <input type="number" name="jumlah_anggota" class="form-control" required value="<?= htmlspecialchars($edit_reservasi['jumlah_anggota']) ?>">
                        </div>
                        <div class="mb-3">
                            <label>Tanggal Pemesanan</label>
                            <input type="datetime-local" name="tanggal_pemesanan" class="form-control" required value="<?= date('Y-m-d', strtotime($edit_reservasi['tanggal_pemesanan'])) ?>">
                        </div>
                        <button type="submit" name="edit_reservasi" class="btn btn-warning">Update</button>
                        <a href="reservation.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
<?php endif; ?>

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