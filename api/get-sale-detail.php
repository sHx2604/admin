<?php
require_once '../core/functions.php';
checkAuth();

header('Content-Type: application/json');

$sale_id = $_GET['id'] ?? 0;

if (!$sale_id) {
    jsonResponse(['success' => false, 'error' => 'Invalid ID'], 400);
}

$sale = getSaleById($sale_id);
$items = getSaleItems($sale_id);

if (!$sale) {
    jsonResponse(['success' => false, 'error' => 'Sale not found'], 404);
}

jsonResponse([
    'success' => true,
    'sale' => $sale,
    'items' => $items
]);
