<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$action = $_GET['action'] ?? 'list';
$products = getProducts();
$categories = getCategories();

// Tambahkan inisialisasi $editProduct untuk form edit
if ($action === 'edit' && isset($_GET['id'])) {
    $editProduct = getProductById((int)$_GET['id']);
    if (!$editProduct) {
        header("Location: menu.php");
        exit;
    }
}

if ($action === 'delete' && isset($_GET['id'])) {
    global $pdo;
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: menu.php");
    exit;
}

// Pastikan koneksi PDO sudah disiapkan sebagai $pdo

$uploadDir = __DIR__ . '../admin/uploads'; // Folder untuk menyimpan gambar

$allowedTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
$maxFileSize = 2 * 1024 * 1024; // 2MB

function handleImageUpload($fieldName, &$error)
{
    global $allowedTypes, $maxFileSize, $uploadDir;

    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $fileTmp  = $_FILES[$fieldName]['tmp_name'];
        $fileName = basename($_FILES[$fieldName]['name']);
        $fileType = mime_content_type($fileTmp);
        $fileSize = $_FILES[$fieldName]['size'];

        if (!in_array($fileType, $allowedTypes)) {
            $error = "Format gambar tidak valid. Hanya JPG, PNG, dan WEBP yang diperbolehkan.";
            return null;
        }

        if ($fileSize > $maxFileSize) {
            $error = "Ukuran gambar melebihi 2MB.";
            return null;
        }

        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newName = uniqid('img_', true) . '.' . $ext;

        if (move_uploaded_file($fileTmp, $uploadDir . $newName)) {
            return $newName;
        } else {
            $error = "Gagal mengunggah gambar.";
        }
    }

    return null;
}

// ==========================
// TAMBAH PRODUK
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_product'])) {
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $cost_price = floatval($_POST['cost_price']);
    $stock = intval($_POST['stock']);
    $min_stock = intval($_POST['min_stock']);
    $sku = trim($_POST['sku']);
    $status = trim($_POST['status']);

    $image = handleImageUpload('product_image', $error);

    if (!$error && !empty($name) && $price > 0) {
        try {
            $stmt = $pdo->prepare("INSERT INTO products 
                (category_id, name, description, price, cost_price, stock, min_stock, sku, status, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $category_id, $name, $description, $price, $cost_price,
                $stock, $min_stock, $sku, $status, $image
            ]);
            $success = "Produk berhasil ditambahkan!";
            $action = 'list';
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = $error ?? "Nama dan harga produk harus diisi!";
    }
}

// ==========================
// UPDATE PRODUK
// ==========================
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_product']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $category_id = intval($_POST['category_id']);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $cost_price = floatval($_POST['cost_price']);
    $stock = intval($_POST['stock']);
    $min_stock = intval($_POST['min_stock']);
    $sku = trim($_POST['sku']);
    $status = trim($_POST['status']);

    $image = handleImageUpload('product_image', $error);

    if (!$error && !empty($name) && $price > 0) {
        try {
            if ($image) {
                $stmt = $pdo->prepare("UPDATE products SET 
                    category_id=?, name=?, description=?, price=?, cost_price=?, 
                    stock=?, min_stock=?, sku=?, status=?, image=? 
                    WHERE id=?");
                $stmt->execute([
                    $category_id, $name, $description, $price, $cost_price,
                    $stock, $min_stock, $sku, $status, $image, $id
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE products SET 
                    category_id=?, name=?, description=?, price=?, cost_price=?, 
                    stock=?, min_stock=?, sku=?, status=?
                    WHERE id=?");
                $stmt->execute([
                    $category_id, $name, $description, $price, $cost_price,
                    $stock, $min_stock, $sku, $status, $id
                ]);
            }

            $success = "Produk berhasil diperbarui!";
            $action = 'list';
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } else {
        $error = $error ?? "Nama dan harga produk harus diisi!";
        $action = 'edit';
    }
}
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
                    <a href="../admin/menu.php" class="nav-item nav-link active"><i class="fa fa-box me-2"></i>Menu</a>
                    <a href="../admin/transaction.php" class="nav-item nav-link"><i class="fa fa-receipt me-2"></i>Transaksi</a>
                    <a href="../admin/kategori.php" class="nav-item nav-link"><i class="fa fa-check-square me-2"></i>Kategori</a>
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
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <?php if ($action === 'list'): ?>
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3 class="card-title">Daftar Produk</h3>
                            <a href="?action=add" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Tambah Produk
                            </a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>SKU</th>
                                        <th>Harga</th>
                                        <th>Stok</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo $product['name']; ?></td>
                                        <td><?php echo $product['sku']; ?></td>
                                        <td><?php echo formatCurrency($product['price']); ?></td>
                                        <td><?php echo $product['stock']; ?></td>
                                        <td><?php echo ucfirst($product['status']); ?></td>
                                        <td>
                                            <a href="?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                                    </a>
                                            
                                            <a href="?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                                    </a>

                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <?php elseif ($action === 'add'): ?>
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Produk</h3>
                    </div>
                    <div class="card-body">
                       <form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="idcat" class="form-label">Kategori Id</label>
        <input type="text" class="form-control" name="category_id" id="idcat" required>
    </div>
    <div class="mb-3">
        <label for="name" class="form-label">Nama Produk</label>
        <input type="text" class="form-control" name="name" id="name" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <input type="text" class="form-control" name="description" id="description">
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Harga</label>
        <input type="number" step="0.01" class="form-control" name="price" id="price" required>
    </div>
    <div class="mb-3">
        <label for="cost_price" class="form-label">Harga Beli</label>
        <input type="number" step="0.01" class="form-control" name="cost_price" id="cost_price">
    </div>
    <div class="mb-3">
        <label for="stock" class="form-label">Stok</label>
        <input type="number" class="form-control" name="stock" id="stock" required>
    </div>
    <div class="mb-3">
        <label for="minstock" class="form-label">Min Stok</label>
        <input type="number" class="form-control" name="min_stock" id="minstock">
    </div>
    <div class="mb-3">
        <label for="sku" class="form-label">SKU</label>
        <input type="text" class="form-control" name="sku" id="sku">
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <input type="text" class="form-control" name="status" id="status">
    </div>
    <div class="mb-3">
        <label for="formFile" class="form-label">Foto Produk (max 2MB)</label>
        <input class="form-control" type="file" name="product_image" id="formFile">
    </div>
    <div class="d-flex gap-2">
        <button type="submit" name="add_product" class="btn btn-success">
            <i class="fas fa-save"></i> Simpan
        </button>
        <a href="?action=list" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>
</form>

            </div>
        </div>
        <?php elseif ($action === 'edit' && isset($editProduct)): ?>
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Produk</h3>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="catid" class="form-label">Kategori ID</label>
                            <input type="text" class="form-control" name="category_id" id="catid"
                                   value="<?= htmlspecialchars($editProduct['category_id']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Produk</label>
                            <input type="text" class="form-control" name="name" id="name"
                                   value="<?= htmlspecialchars($editProduct['name']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Deskripsi</label>
                            <input type="text" class="form-control" name="description" id="description"
                                   value="<?= htmlspecialchars($editProduct['description']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label">Harga *</label>
                            <input type="number" step="0.01" class="form-control" name="price" id="price"
                                   value="<?= $editProduct['price'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="cost_price" class="form-label">Harga Beli</label>
                            <input type="number" step="0.01" class="form-control" name="cost_price" id="cost_price"
                                   value="<?= $editProduct['cost_price'] ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="stock" class="form-label">Stok Barang</label>
                            <input type="number" class="form-control" name="stock" id="stock"
                                   value="<?= htmlspecialchars($editProduct['stock']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="min_stock" class="form-label">Min Stok</label>
                            <input type="number" class="form-control" name="min_stock" id="min_stock"
                                   value="<?= htmlspecialchars($editProduct['min_stock']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input type="text" class="form-control" name="sku" id="sku"
                                   value="<?= htmlspecialchars($editProduct['sku']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select name="status" id="status" class="form-control" required>
                                <option value="aktif" <?= $editProduct['status'] === 'active' ? 'selected' : ''?>>Aktif</option>
                                <option value="nonaktif" <?= $editProduct['status'] === 'habis' ? 'selected' : ''?>>Habis</option>
                                <option value="discontinued" <?= $editProduct['status'] === 'discontinued' ? 'selected' : ''?>>Nonaktif</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="product_image" class="form-label">Gambar Produk (opsional)</label>
                            <input type="file" class="form-control" name="product_image" id="product_image" accept="image/*">
                            <?php if (!empty($editProduct['image'])): ?>
                                <div class="mt-2">
                                    <img src="uploads/<?= htmlspecialchars($editProduct['image']) ?>" alt="gambar"
                                         style="max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" name="update_product" class="btn btn-success">
                                <i class="fas fa-save"></i> Update
                            </button>
                            <a href="?action=list" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
<?php endif; ?>

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