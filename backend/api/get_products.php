<?php
/**
 * GET /backend/api/get_products.php
 * Returns the product shelf as JSON, read live from the database.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$result = $conn->query('SELECT id, name, price, image, description, stock FROM products ORDER BY id ASC');

function normalizeProductImagePath($image) {
    $image = trim((string) $image);
    if ($image === '') {
        return 'images/products/face-wash.jpg';
    }

    $image = str_replace('\\', '/', $image);
    $image = str_replace('C:/xampp/htdocs/beauty salon/', '', $image);
    $image = ltrim($image, '/');

    if (preg_match('#/face-wash\.png$#i', $image) || preg_match('#^face-wash\.png$#i', $image)) {
        return 'images/products/face-wash.jpg';
    }

    return $image;
}

$products = [];
while ($row = $result->fetch_assoc()) {
    $row['price'] = (float) $row['price'];
    $row['image'] = normalizeProductImagePath($row['image']);
    $products[] = $row;
}

echo json_encode(['success' => true, 'products' => $products]);

$conn->close();
