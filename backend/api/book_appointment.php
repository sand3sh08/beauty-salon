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
    echo json_encode(['success' => false, 'message' => 'Please log in to book an appointment.']);
    exit;
}

$input = $_POST;
$name = trim($input['name'] ?? '');
$phone = trim($input['phone'] ?? '');
$email = trim($input['email'] ?? '');
$service = trim($input['service'] ?? '');
$date = trim($input['date'] ?? '');
$notes = trim($input['notes'] ?? '');

$errors = [];
if ($name === '' || strlen($name) > 120) $errors[] = 'Please enter a valid name.';
if (!preg_match('/^[0-9+\-\s()]{7,20}$/', $phone)) $errors[] = 'Please enter a valid phone number.';
if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email.';
if (!in_array($service, ['Hair', 'Skin', 'Makeup', 'Spa'], true)) $errors[] = 'Please choose a service.';
$dateObj = DateTime::createFromFormat('Y-m-d', $date);
$today = new DateTime('today');
if (!$dateObj || $dateObj < $today) $errors[] = 'Please choose a valid, upcoming date.';

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

$stmt = $conn->prepare(
    'INSERT INTO appointments (user_id, name, phone, email, service_category, appointment_date, notes)
     VALUES (?, ?, ?, ?, ?, ?, ?)'
);
$stmt->bind_param('issssss', $_SESSION['user_id'], $name, $phone, $email, $service, $date, $notes);

if ($stmt->execute()) {
    echo json_encode([
        'success' => true,
        'message' => 'Appointment request confirmed. We will contact you shortly.',
        'id' => $stmt->insert_id,
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not save your appointment. Please try again.']);
}

$stmt->close();
$conn->close();
