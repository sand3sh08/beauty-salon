<?php
/**
 * GET /backend/api/get_services.php
 * Returns the price list grouped by category, read live from the database.
 */
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$result = $conn->query('SELECT category, name, price FROM services ORDER BY category, id ASC');

$grouped = [];
while ($row = $result->fetch_assoc()) {
    $grouped[$row['category']][] = [
        'name' => $row['name'],
        'price' => (float) $row['price'],
    ];
}

echo json_encode(['success' => true, 'services' => $grouped]);

$conn->close();
