<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (empty($_SESSION['user_id'])) {
    header('Location: backend/auth/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Verbena & Co. — Botanical Beauty House</title>
<meta name="description" content="Verbena & Co. is a botanical beauty house offering hair, skin, makeup and spa treatments.">
<link rel="icon" href="images/logo/logo.png">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,300;0,9..144,500;0,9..144,600;1,9..144,500&family=Manrope:wght@400;500;600;700&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ===== NAV ===== -->
<header class="site-header" id="siteHeader">
  <div class="wrap header-row">
    <a href="index.php" class="brand">
      <img src="images/logo/logo.png" alt="Verbena & Co. logo" class="brand-mark">
      <span class="brand-word">Verbena <em>&amp;</em> Co.</span>
    </a>

    <nav class="main-nav" id="mainNav">
      <a href="index.php">Home</a>
      <a href="about.php">About</a>
      <a href="services.php">Services</a>
      <a href="shop.php">Shop</a>
      <a href="book.php">Contact</a>
    </nav>

    <div class="header-actions">
      <a href="book.php" class="btn btn-gold nav-cta">Book a visit</a>
      <?php if (!empty($_SESSION['user_username'])): ?>
        <span class="auth-welcome" style="margin-right:15px; font-weight:600; font-size:0.9rem;">Hello, <?= htmlspecialchars($_SESSION['user_username']) ?></span>
        <a href="backend/auth/logout.php" class="btn btn-ghost auth-link" id="authLink">Logout</a>
      <?php else: ?>
        <a href="backend/auth/login.php" class="btn btn-ghost auth-link" id="authLink">Login</a>
        <a href="backend/auth/register.php" class="btn btn-line auth-link auth-link-secondary" id="registerLink">Register</a>
      <?php endif; ?>
    </div>
    <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
      <span></span><span></span><span></span>
    </button>
  </div>
</header>
