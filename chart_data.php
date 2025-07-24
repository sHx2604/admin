<?php
session_start();
require_once '../admin/config/database.php';
require_once '../admin/includes/functions.php';

// Check if user is logged in
if (!isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Set content type to JSON
header('Content-Type: application/json');

// Get chart type from request
$chartType = isset($_GET['type']) ? $_GET['type'] : '';

try {
    switch ($chartType) {
        case 'daily_sales':
            $data = getDailySalesData();
            break;

        case 'weekly_reservation':
            $data = getWeeklyReservationData();
            break;

        case 'top_products':
            $data = getTopProductsData();
            break;

        case 'monthly_revenue':
            $data = getMonthlyRevenueData();
            break;

        default:
            http_response_code(400);
            echo json_encode(['error' => 'Invalid chart type']);
            exit;
    }

    // Return success response
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}
?>
