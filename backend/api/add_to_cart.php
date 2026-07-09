<?php
/**
 * POST /backend/api/add_to_cart.php
 * Body: product_id
 * Logs the "add to bag" action against a per-visitor session id.
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

$input = $_POST;
if (empty($input)) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) $input = $json;
}

$productId = (int) ($input['product_id'] ?? 0);

if ($productId <= 0) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Invalid product.']);
    exit;
}

$stmt = $conn->prepare('SELECT id, name, price FROM products WHERE id = ?');
$stmt->bind_param('i', $productId);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$product) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found.']);
    exit;
}

$sessionId = session_id();

$insert = $conn->prepare(
    'INSERT INTO cart_orders (session_id, product_id, product_name, price, quantity) VALUES (?, ?, ?, ?, 1)'
);
$insert->bind_param('siss', $sessionId, $product['id'], $product['name'], $product['price']);

if ($insert->execute()) {
    echo json_encode([
        'success' => true,
        'message' => $product['name'] . ' added to your bag',
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not add item to bag.']);
}

$insert->close();
$conn->close();
