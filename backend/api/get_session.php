<?php
session_start();
header('Content-Type: application/json');

echo json_encode([
    'loggedIn' => !empty($_SESSION['user_id']),
    'user' => [
        'id' => $_SESSION['user_id'] ?? null,
        'name' => $_SESSION['user_name'] ?? null,
        'username' => $_SESSION['user_username'] ?? null,
    ],
]);
