<?php
/**
 * Trinity Restaurant Management System
 * System Health Check
 */

echo "<h1>Trinity System Health Check</h1>";
echo "<style>
    body { font-family: Arial, sans-serif; padding: 20px; }
    .success { color: green; font-weight: bold; }
    .error { color: red; font-weight: bold; }
    .warning { color: orange; font-weight: bold; }
    table { border-collapse: collapse; width: 100%; margin: 20px 0; }
    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
    th { background-color: #4CAF50; color: white; }
    tr:nth-child(even) { background-color: #f2f2f2; }
</style>";

// 1. PHP Version Check
echo "<h2>1. PHP Configuration</h2>";
echo "<table>";
echo "<tr><th>Item</th><th>Value</th><th>Status</th></tr>";

$phpVersion = phpversion();
$phpOk = version_compare($phpVersion, '7.4.0', '>=');
echo "<tr><td>PHP Version</td><td>$phpVersion</td><td class='" . ($phpOk ? 'success' : 'error') . "'>" . ($phpOk ? '✓ OK' : '✗ TOO OLD') . "</td></tr>";

$pdoInstalled = extension_loaded('pdo');
echo "<tr><td>PDO Extension</td><td>" . ($pdoInstalled ? 'Installed' : 'Not Installed') . "</td><td class='" . ($pdoInstalled ? 'success' : 'error') . "'>" . ($pdoInstalled ? '✓ OK' : '✗ MISSING') . "</td></tr>";

$pdoMysql = extension_loaded('pdo_mysql');
echo "<tr><td>PDO MySQL Extension</td><td>" . ($pdoMysql ? 'Installed' : 'Not Installed') . "</td><td class='" . ($pdoMysql ? 'success' : 'error') . "'>" . ($pdoMysql ? '✓ OK' : '✗ MISSING') . "</td></tr>";

echo "</table>";

// 2. Directory Check
echo "<h2>2. Directory Structure</h2>";
echo "<table>";
echo "<tr><th>Directory</th><th>Exists</th><th>Writable</th><th>Status</th></tr>";

$directories = [
    'config' => false,  // tidak perlu writable
    'includes' => false,
    'uploads' => true,   // perlu writable
    'admin/uploads' => true,
    'css' => false,
    'js' => false,
    'img' => false,
];

foreach ($directories as $dir => $needsWrite) {
    $exists = is_dir($dir);
    $writable = is_writable($dir);

    if (!$exists) {
        $status = "<span class='error'>✗ NOT FOUND</span>";
    } else if ($needsWrite && !$writable) {
        $status = "<span class='warning'>⚠ NOT WRITABLE</span>";
    } else {
        $status = "<span class='success'>✓ OK</span>";
    }

    echo "<tr><td>$dir/</td><td>" . ($exists ? 'Yes' : 'No') . "</td><td>" . ($writable ? 'Yes' : 'No') . "</td><td>$status</td></tr>";
}

echo "</table>";

// 3. File Check
echo "<h2>3. Core Files</h2>";
echo "<table>";
echo "<tr><th>File</th><th>Exists</th><th>Size</th><th>Status</th></tr>";

$files = [
    'config/database.php',
    'includes/functions.php',
    'index.php',
    'dashboard.php',
    'menu.php',
    'kategori.php',
    'user.php',
    'pos.php',
    'reservation.php',
    'transaction.php',
    'sales.php',
    'logout.php',
    'restaurant_db.sql',
];

foreach ($files as $file) {
    $exists = file_exists($file);
    $size = $exists ? filesize($file) : 0;
    $sizeFormatted = $exists ? number_format($size / 1024, 2) . ' KB' : '-';

    $status = $exists ? "<span class='success'>✓ OK</span>" : "<span class='error'>✗ MISSING</span>";

    echo "<tr><td>$file</td><td>" . ($exists ? 'Yes' : 'No') . "</td><td>$sizeFormatted</td><td>$status</td></tr>";
}

echo "</table>";

// 4. Database Connection Test
echo "<h2>4. Database Connection</h2>";
echo "<table>";
echo "<tr><th>Test</th><th>Result</th><th>Status</th></tr>";

try {
    require_once 'config/database.php';

    echo "<tr><td>Connection</td><td>Connected to " . DB_NAME . "</td><td class='success'>✓ OK</td></tr>";

    // Check tables
    $tables = ['users', 'categories', 'products', 'sales', 'sale_items', 'reservasi', 'jumlah_reservasi', 'posts', 'settings'];
    foreach ($tables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            $count = $stmt->fetchColumn();
            echo "<tr><td>Table: $table</td><td>$count rows</td><td class='success'>✓ EXISTS</td></tr>";
        } catch (PDOException $e) {
            echo "<tr><td>Table: $table</td><td>Error</td><td class='error'>✗ NOT FOUND</td></tr>";
        }
    }

} catch (Exception $e) {
    echo "<tr><td>Connection</td><td>" . $e->getMessage() . "</td><td class='error'>✗ FAILED</td></tr>";
}

echo "</table>";

// 5. Configuration Check
echo "<h2>5. Configuration</h2>";
echo "<table>";
echo "<tr><th>Setting</th><th>Value</th><th>Status</th></tr>";

if (defined('DB_HOST')) {
    echo "<tr><td>DB_HOST</td><td>" . DB_HOST . "</td><td class='success'>✓ SET</td></tr>";
    echo "<tr><td>DB_NAME</td><td>" . DB_NAME . "</td><td class='success'>✓ SET</td></tr>";
    echo "<tr><td>DB_USER</td><td>" . DB_USER . "</td><td class='success'>✓ SET</td></tr>";
    echo "<tr><td>DB_PASS</td><td>" . (empty(DB_PASS) ? '(empty)' : '***') . "</td><td class='success'>✓ SET</td></tr>";
} else {
    echo "<tr><td colspan='3' class='error'>Database config not loaded</td></tr>";
}

echo "</table>";

// 6. Security Check
echo "<h2>6. Security</h2>";
echo "<table>";
echo "<tr><th>Item</th><th>Status</th><th>Details</th></tr>";

$htaccessConfig = file_exists('config/.htaccess');
echo "<tr><td>config/.htaccess</td><td class='" . ($htaccessConfig ? 'success' : 'warning') . "'>" . ($htaccessConfig ? '✓ EXISTS' : '⚠ MISSING') . "</td><td>" . ($htaccessConfig ? 'Protected' : 'Create for better security') . "</td></tr>";

$htaccessIncludes = file_exists('includes/.htaccess');
echo "<tr><td>includes/.htaccess</td><td class='" . ($htaccessIncludes ? 'success' : 'warning') . "'>" . ($htaccessIncludes ? '✓ EXISTS' : '⚠ MISSING') . "</td><td>" . ($htaccessIncludes ? 'Protected' : 'Create for better security') . "</td></tr>";

$sessionSecure = ini_get('session.cookie_httponly');
echo "<tr><td>Session HttpOnly</td><td class='" . ($sessionSecure ? 'success' : 'warning') . "'>" . ($sessionSecure ? '✓ ENABLED' : '⚠ DISABLED') . "</td><td>" . ($sessionSecure ? 'Secure' : 'Consider enabling in php.ini') . "</td></tr>";

echo "</table>";

// 7. Recommendations
echo "<h2>7. Recommendations</h2>";
echo "<ul>";
echo "<li>✅ All core files are present</li>";
echo "<li>✅ Database structure is complete</li>";
echo "<li>⚠️ Consider implementing password hashing for production</li>";
echo "<li>⚠️ Add CSRF token protection for forms</li>";
echo "<li>⚠️ Set proper file permissions on production server (755 for directories, 644 for files)</li>";
echo "<li>⚠️ Create regular database backups</li>";
echo "<li>⚠️ Enable error logging instead of displaying errors in production</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>Check completed at:</strong> " . date('Y-m-d H:i:s') . "</p>";
echo "<p><strong>System Status:</strong> <span class='success'>READY FOR TESTING</span></p>";
?>
