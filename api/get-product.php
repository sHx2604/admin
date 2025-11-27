<?php
require_once '../core/functions.php';
checkAuth();

header('Content-Type: application/json');

$product_id = $_GET['id'] ?? 0;

if (!$product_id) {
    jsonResponse(['success' => false, 'error' => 'Invalid ID'], 400);
}

$product = getProductById($product_id);

if (!$product) {
    jsonResponse(['success' => false, 'error' => 'Product not found'], 404);
}

jsonResponse([
    'success' => true,
    'product' => $product
]);
