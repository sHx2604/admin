<?php
require_once '../core/functions.php';

// Redirect if already logged in
startSession();
if (isLoggedIn()) {
    header('Location: ../pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    if (login($username, $password)) {
        header('Location: ../pages/dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Trinity Restaurant POS</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Trinity Restaurant</h1>
                <p>Point of Sale System</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= $error ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    Login
                </button>
            </form>

            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid var(--border);">
                <p style="font-size: 12px; color: var(--secondary); text-align: center;">
                    <strong>Demo Accounts:</strong><br>
                    Admin: admin / admin123<br>
                    Kasir: kasir1 / kasir123<br>
                    Manager: manager1 / manager123
                </p>
            </div>
        </div>
    </div>

    <script src="../assets/js/main.js"></script>
</body>
</html>
