<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to place an order.']);
    exit;
}

$input = $_POST;
$productId = (int) ($input['product_id'] ?? 0);
$quantity = max(1, (int) ($input['quantity'] ?? 1));

if ($productId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$stmt = $conn->prepare('SELECT id, name, price, stock FROM products WHERE id = ?');
$stmt->bind_param('i', $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

if ($quantity > (int) $product['stock']) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Requested quantity exceeds stock.']);
    exit;
}

$total = (float) $product['price'] * $quantity;
$insert = $conn->prepare('INSERT INTO product_orders (user_id, product_id, product_name, unit_price, quantity, total_amount) VALUES (?, ?, ?, ?, ?, ?)');
$insert->bind_param('iisdid', $_SESSION['user_id'], $product['id'], $product['name'], $product['price'], $quantity, $total);

if ($insert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Order confirmed for ' . $product['name'] . ' x' . $quantity,
        'total' => number_format($total, 2),
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not place your order.']);
}

$insert->close();
$conn->close();
