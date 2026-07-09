<?php
/**
 * POST /backend/api/subscribe_newsletter.php
 * Body: email
 */
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

$email = trim($input['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$stmt = $conn->prepare('INSERT IGNORE INTO newsletter_subscribers (email) VALUES (?)');
$stmt->bind_param('s', $email);

if ($stmt->execute()) {
    if ($stmt->affected_rows === 0) {
        echo json_encode(['success' => true, 'message' => "You're already on the list — welcome back."]);
    } else {
        echo json_encode(['success' => true, 'message' => "You're on the list — welcome to the house."]);
    }
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not subscribe right now. Please try again.']);
}

$stmt->close();
$conn->close();
