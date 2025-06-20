<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

requireLogin();

$products = getProducts();
$categories = getCategories();




// Handle checkout
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'checkout') {
    $invoiceNumber = generateInvoiceNumber();
    $cartItems = json_decode($_POST['cart_items'], true);
    $customerName = $_POST['customer_name'] ?? '';
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    $totalAmount = 0;

    foreach ($cartItems as $item) {
        $totalAmount += $item['price'] * $item['quantity'];
    }

    try {
        $pdo->beginTransaction();

        $stmt = $pdo->prepare("INSERT INTO sales (user_id, invoice_number, total_amount, payment_method, customer_name, status) VALUES (?, ?, ?, ?, ?, 'completed')");
        $stmt->execute([$_SESSION['user_id'], $invoiceNumber, $totalAmount, $paymentMethod, $customerName]);
        $saleId = $pdo->lastInsertId();

        foreach ($cartItems as $item) {
            // Ambil data produk dari database untuk validasi harga dan stok
            $product = getProductById($item['id']);
            $productName = $product['name'];
            $sku = isset($product['sku_product']) ? $product['sku_product'] : '';
            $dbPrice = $product['price'];
            $dbStock = $product['stock'];
            $qty = $item['quantity'];

            // Validasi harga dan stok
            if ($dbPrice != $item['price']) {
                throw new Exception('Harga produk tidak valid.');
            }
            if ($dbStock < $qty) {
                throw new Exception('Stok produk tidak cukup.');
            }

            $stmt = $pdo->prepare("INSERT INTO sale_items (sale_id, product_id, product_name, sku_product, quantity, price, total) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $itemTotal = $dbPrice * $qty;
            $stmt->execute([$saleId, $item['id'], $productName, $sku, $qty, $dbPrice, $itemTotal]);

            $stmt = $pdo->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $stmt->execute([$qty, $item['id']]);
        }

       $pdo->commit();
       header("Location: pos.php?success=1&invoice=" . urlencode($invoiceNumber));
       exit;


    } catch (Exception $e) {
        $pdo->rollback();
        $error = "Error: " . $e->getMessage();
    }
}
?>

<?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
<?php endif; ?>


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
                    <a href="../admin/kategori.php" class="nav-item nav-link"><i class="fa fa-check-square me-2"></i>Kategori</a>
                    <a href="../admin/reservation.php" class="nav-item nav-link"><i class="fa fa-briefcase me-2"></i>Reservasi</a>
                    <a href="../admin/user.php" class="nav-item nav-link"><i class="fa fa-users me-2"></i>User</a>
                    <a href="../admin/sales.php" class="nav-item nav-link"><i class="fa fa-chart-line me-2"></i>Laporan</a>
                    <a href="../admin/pos.php" class="nav-item nav-link active"><i class="fa fa-university me-2"></i>Kasir</a>
                    
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
               

            <!-- Alert -->
            <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
                <div class="alert alert-success">
                    Transaksi berhasil! Invoice: <?php echo htmlspecialchars($_GET['invoice']); ?>
                </div>
            <?php endif; ?>

            <div class="p-3">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
            </div>

            <!-- Content Grid -->
            <div class="row p-3">
                <!-- Produk -->
                <div class="col-md-8 mb-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Pilih Produk</h5>
                            <input type="text" class="form-control w-50" placeholder="Cari produk..." onkeyup="searchProducts(this.value)">
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php foreach ($products as $product): ?>
                                    <div class="col-sm-6 col-md-4 mb-3">
                                        <div class="card h-100 product-card" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>)">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo $product['name']; ?></h5>
                                                <p class="card-text"><?php echo formatCurrency($product['price']); ?></p>
                                                <small class="text-muted">Stok: <?php echo $product['stock']; ?></small>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Keranjang -->
                <div class="col-md-4">
                    <div class="card h-100">
                        <div class="card-header">
                            <h5 class="mb-0">Keranjang</h5>
                        </div>
                        <div class="card-body" id="cart-items">
                            <p class="text-center text-muted">Keranjang kosong</p>
                        </div>
                        <div class="card-footer">
                            <div class="mb-3">
                                <strong>Total: <span id="cart-total">Rp 0</span></strong>
                            </div>

                            <form method="POST" id="checkout-form">
                                <input type="hidden" name="action" value="checkout">
                                <input type="hidden" name="cart_items" id="cart-items-input">

                                <div class="mb-2">
                                    <input type="text" name="customer_name" class="form-control" placeholder="Nama Pelanggan (Opsional)">
                                </div>

                                <div class="mb-3">
                                    <select name="payment_method" class="form-select">
                                        <option value="cash">Tunai</option>
                                        <option value="card">Kartu</option>
                                        <option value="transfer">Transfer</option>
                                    </select>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="button" onclick="processCheckout()" class="btn btn-success" id="checkout-btn" disabled>
                                        Checkout
                                    </button>
                                    <button type="button" onclick="clearCart()" class="btn btn-secondary">
                                        Clear
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            </div>
            </div> <!-- End Row -->
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
       <script src="assets/js/main.js"></script>
   <script>
    // Inisialisasi cart kosong
    let cart = [];

    // Fungsi untuk menambahkan produk ke keranjang
    function addToCart(id, name, price) {
        let item = cart.find(i => i.id === id);
        if (item) {
            item.quantity++;
        } else {
            cart.push({ id, name, price, quantity: 1 });
        }
        renderCart();
    }

    // Render isi keranjang
    function renderCart() {
        const container = document.getElementById('cart-items');
        const totalEl = document.getElementById('cart-total');
        const checkoutBtn = document.getElementById('checkout-btn');

        container.innerHTML = '';
        let total = 0;

        if (cart.length === 0) {
            container.innerHTML = '<p class="text-center text-muted">Keranjang kosong</p>';
            checkoutBtn.disabled = true;
            totalEl.textContent = 'Rp 0';
            return;
        }

        cart.forEach(item => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            const div = document.createElement('div');
            div.className = 'cart-item';
            div.innerHTML = `
                <div><strong>${item.name}</strong><br><small>Qty: ${item.quantity}</small></div>
                <div>${formatCurrency(itemTotal)}</div>
            `;
            container.appendChild(div);
        });

        totalEl.textContent = formatCurrency(total);
        checkoutBtn.disabled = false;
    }

    // Clear keranjang
    function clearCart() {
        cart = [];
        renderCart();
    }

    function processCheckout() {
        if (cart.length === 0) return;

        // Masukkan cart ke input hidden
        document.getElementById('cart-items-input').value = JSON.stringify(cart);

        // Kirim form dengan submit() lalu reload halaman setelahnya
        document.getElementById('checkout-form').submit();

        // Optional: Bisa juga reload manual setelah delay, tapi tidak perlu kalau pakai PHP redirect
    }

    function formatCurrency(number) {
        return 'Rp ' + number.toLocaleString('id-ID');
    }
</script>
</body>

</html>