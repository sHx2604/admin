<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

requireLogin();

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

    <!-- Chart Container Styles -->
    <style>
        .chart-container {
            position: relative;
            height: 400px !important;
            width: 100% !important;
            overflow: hidden;
        }

        .chart-container canvas {
            position: absolute !important;
            top: 0;
            left: 0;
            width: 100% !important;
            height: 100% !important;
        }

        /* Prevent Bootstrap h-100 conflicts */
        .chart-wrapper {
            min-height: 450px;
            max-height: 450px;
            height: 450px;
        }
    </style>
</head>

<body>
    <div class="container-xxl position-relative bg-white d-flex p-0">
        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
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
                   <a href="dashboard.php" class="nav-item nav-link"><i class="fa fa-th me-2"></i>Dashboard</a>
                    <a href="menu.php" class="nav-item nav-link"><i class="fa fa-box me-2"></i>Menu</a>
                    <a href="transaction.php" class="nav-item nav-link"><i class="fa fa-receipt me-2"></i>Transaksi</a>
                    <a href="kategori.php" class="nav-item nav-link"><i class="fa fa-check-square me-2"></i>Kategori</a>
                    <a href="reservation.php" class="nav-item nav-link"><i class="fa fa-briefcase me-2"></i>Reservasi</a>
                    <a href="user.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>User</a>
                    <a href="sales.php" class="nav-item nav-link active"><i class="fa fa-chart-line me-2"></i>Laporan</a>
                    <a href="pos.php" class="nav-item nav-link"><i class="fa fa-university me-2"></i>Kasir</a>

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
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle me-lg-2" src="img/user.jpg" alt="" style="width: 40px; height: 40px;">
                            <span class="d-none d-lg-inline-flex"><?php echo $_SESSION['full_name']; ?></span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end bg-light border-0 rounded-0 rounded-bottom m-0">
                            <a href="#" class="dropdown-item">My Profile</a>
                            <a href="#" class="dropdown-item">Settings</a>
                            <a href="#" class="dropdown-item">Log Out</a>
                        </div>
                    </div>
                </div>
            </nav>
            <!-- Navbar End -->

            <!-- Export Controls Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4 mb-4">
                    <div class="col-12">
                        <div class="bg-light rounded h-100 p-4">
                            <h6 class="mb-4">📊 Ekspor Laporan PDF</h6>
                            <div class="row">
                                <!-- Laporan Harian -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-primary">
                                        <div class="card-header bg-primary text-white">
                                            <h6 class="mb-0">📅 Laporan Harian</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="daily_date" class="form-label">Pilih Tanggal:</label>
                                                <input type="date" class="form-control" id="daily_date" value="<?php echo date('Y-m-d'); ?>">
                                            </div>
                                            <button class="btn btn-primary w-100" onclick="exportDailyReport()">
                                                <i class="fa fa-download me-2"></i>Download PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Laporan Mingguan -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">📈 Laporan Mingguan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="weekly_date" class="form-label">Mulai Minggu:</label>
                                                <input type="date" class="form-control" id="weekly_date" value="<?php echo date('Y-m-d', strtotime('monday this week')); ?>">
                                            </div>
                                            <button class="btn btn-success w-100" onclick="exportWeeklyReport()">
                                                <i class="fa fa-download me-2"></i>Download PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Laporan Bulanan -->
                                <div class="col-md-4 mb-3">
                                    <div class="card border-warning">
                                        <div class="card-header bg-warning text-white">
                                            <h6 class="mb-0">📊 Laporan Bulanan</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-6 mb-3">
                                                    <label for="monthly_month" class="form-label">Bulan:</label>
                                                    <select class="form-control" id="monthly_month">
                                                        <?php for($i = 1; $i <= 12; $i++): ?>
                                                            <option value="<?php echo $i; ?>" <?php echo ($i == date('n')) ? 'selected' : ''; ?>>
                                                                <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-3">
                                                    <label for="monthly_year" class="form-label">Tahun:</label>
                                                    <select class="form-control" id="monthly_year">
                                                        <?php for($y = date('Y') - 2; $y <= date('Y'); $y++): ?>
                                                            <option value="<?php echo $y; ?>" <?php echo ($y == date('Y')) ? 'selected' : ''; ?>>
                                                                <?php echo $y; ?>
                                                            </option>
                                                        <?php endfor; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <button class="btn btn-warning w-100" onclick="exportMonthlyReport()">
                                                <i class="fa fa-download me-2"></i>Download PDF
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Export Controls End -->

            <!-- Charts Start -->
            <div class="container-fluid pt-4 px-4">
                <div class="row g-4">
                    <!-- Chart Penjualan Harian -->
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light rounded p-4" style="min-height: 450px;">
                            <h6 class="mb-4">Penjualan Harian (7 Hari Terakhir)</h6>
                            <div class="chart-container">
                                <canvas id="daily-sales-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Reservasi -->
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light rounded p-4" style="min-height: 450px;">
                            <h6 class="mb-4">Jumlah Reservasi (Mingguan)</h6>
                            <div class="chart-container">
                                <canvas id="reservation-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Produk Terjual -->
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light rounded p-4" style="min-height: 450px;">
                            <h6 class="mb-4">Top 5 Produk Terjual</h6>
                            <div class="chart-container">
                                <canvas id="products-chart"></canvas>
                            </div>
                        </div>
                    </div>

                    <!-- Chart Revenue Bulanan -->
                    <div class="col-sm-12 col-xl-6">
                        <div class="bg-light rounded p-4" style="min-height: 450px;">
                            <h6 class="mb-4">Revenue Bulanan</h6>
                            <div class="chart-container">
                                <canvas id="revenue-chart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Charts End -->
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
    <script src="js/chart.js"></script>
</body>

</html>
