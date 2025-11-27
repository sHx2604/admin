<?php
startSession();
$current_page = basename($_SERVER['PHP_SELF']);
$user_role = $_SESSION['role'] ?? '';
?>

<div class="hamburger-menu" onclick="toggleMobileMenu()">
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
    <span class="hamburger-line"></span>
</div>

<div class="sidebar">
    <div class="sidebar-header">
        <h2>Trinity POS</h2>
    </div>

    <ul class="sidebar-menu">
        <li>
            <a href="dashboard.php" class="<?= $current_page == 'dashboard.php' ? 'active' : '' ?>">
                <span class="icon">📊</span>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="pos.php" class="<?= $current_page == 'pos.php' ? 'active' : '' ?>">
                <span class="icon">🛒</span>
                <span>Point of Sale</span>
            </a>
        </li>

        <li>
            <a href="transactions.php" class="<?= $current_page == 'transactions.php' ? 'active' : '' ?>">
                <span class="icon">💳</span>
                <span>Transaksi</span>
            </a>
        </li>

        <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
        <li>
            <a href="products.php" class="<?= $current_page == 'products.php' ? 'active' : '' ?>">
                <span class="icon">📦</span>
                <span>Produk</span>
            </a>
        </li>

        <li>
            <a href="categories.php" class="<?= $current_page == 'categories.php' ? 'active' : '' ?>">
                <span class="icon">📑</span>
                <span>Kategori</span>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="reservations.php" class="<?= $current_page == 'reservations.php' ? 'active' : '' ?>">
                <span class="icon">📅</span>
                <span>Reservasi</span>
            </a>
        </li>

        <?php if ($user_role == 'admin' || $user_role == 'manager'): ?>
        <li>
            <a href="reports.php" class="<?= $current_page == 'reports.php' ? 'active' : '' ?>">
                <span class="icon">📈</span>
                <span>Laporan</span>
            </a>
        </li>
        <?php endif; ?>

        <?php if ($user_role == 'admin'): ?>
        <li>
            <a href="users.php" class="<?= $current_page == 'users.php' ? 'active' : '' ?>">
                <span class="icon">👥</span>
                <span>Pengguna</span>
            </a>
        </li>
        <?php endif; ?>

        <li>
            <a href="../auth/logout.php">
                <span class="icon">🚪</span>
                <span>Logout</span>
            </a>
        </li>
    </ul>

    <div class="user-info">
        <div class="name"><?= $_SESSION['full_name'] ?? 'User' ?></div>
        <div class="role"><?= ucfirst($_SESSION['role'] ?? 'Guest') ?></div>
    </div>
</div>
