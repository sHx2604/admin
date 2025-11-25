<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Bypass login for testing
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'admin';
    $_SESSION['full_name'] = 'Administrator';
    $_SESSION['role'] = 'admin';
}

echo "<h1>Chart Data Debug</h1>";
echo "<style>pre { background: #f5f5f5; padding: 15px; border-radius: 5px; }</style>";

// Test 1: Daily Sales
echo "<h2>1. Daily Sales Data</h2>";
try {
    $data = getDailySalesData();
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 2: Weekly Reservation
echo "<h2>2. Weekly Reservation Data</h2>";
try {
    $data = getWeeklyReservationData();
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 3: Top Products
echo "<h2>3. Top Products Data</h2>";
try {
    $data = getTopProductsData(5);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 4: Monthly Revenue
echo "<h2>4. Monthly Revenue Data</h2>";
try {
    $data = getMonthlyRevenueData(6);
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

// Test 5: Chart Data API
echo "<h2>5. Test Chart Data API</h2>";
echo "<p><a href='chart_data.php?type=daily_sales' target='_blank'>Test Daily Sales API</a></p>";
echo "<p><a href='chart_data.php?type=weekly_reservation' target='_blank'>Test Weekly Reservation API</a></p>";
echo "<p><a href='chart_data.php?type=top_products' target='_blank'>Test Top Products API</a></p>";
echo "<p><a href='chart_data.php?type=monthly_revenue' target='_blank'>Test Monthly Revenue API</a></p>";

// Test 6: PDF Report Functions
echo "<h2>6. PDF Report Data</h2>";
try {
    $data = getDailyReportData(date('Y-m-d'));
    echo "<h3>Daily Report Data:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

try {
    $data = getWeeklyReportData(date('Y-m-d', strtotime('monday this week')));
    echo "<h3>Weekly Report Data:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

try {
    $data = getMonthlyReportData(date('m'), date('Y'));
    echo "<h3>Monthly Report Data:</h3>";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
} catch (Exception $e) {
    echo "<p style='color:red'>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
?>
