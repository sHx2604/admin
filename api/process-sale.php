<?php
require_once '../core/functions.php';
checkAuth();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'error' => 'Invalid method'], 400);
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['items']) || empty($data['items'])) {
    jsonResponse(['success' => false, 'error' => 'Invalid data'], 400);
}

$sale_data = [
    'user_id' => $_SESSION['user_id'],
    'total_amount' => $data['total_amount'],
    'payment_method' => $data['payment_method'],
    'customer_name' => $data['customer_name'] ?? '',
    'customer_phone' => $data['customer_phone'] ?? null
];

$result = createSale($sale_data, $data['items']);

jsonResponse($result);
