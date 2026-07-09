<?php
/**
 * Include this at the very top of any protected admin page.
 * Redirects to login.php if no admin is signed in.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

