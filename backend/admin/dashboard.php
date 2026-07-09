<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$totalAppointments = $conn->query("SELECT COUNT(*) c FROM appointments")->fetch_assoc()['c'];
$pendingAppointments = $conn->query("SELECT COUNT(*) c FROM appointments WHERE status='pending'")->fetch_assoc()['c'];
$totalSubscribers = $conn->query("SELECT COUNT(*) c FROM newsletter_subscribers")->fetch_assoc()['c'];
$totalOrders = $conn->query("SELECT COUNT(*) c FROM product_orders")->fetch_assoc()['c'];
$totalUsers = $conn->query("SELECT COUNT(*) c FROM users")->fetch_assoc()['c'];
$totalProducts = $conn->query("SELECT COUNT(*) c FROM products")->fetch_assoc()['c'];

$recent = $conn->query("SELECT name, service_category, appointment_date, status, created_at FROM appointments ORDER BY created_at DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard — Verbena & Co. Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="wrap">
  <h1>Good day, <?= htmlspecialchars($_SESSION['admin_username']) ?>.</h1>
  <p class="sub">Here's how the house is doing.</p>

  <div class="stat-grid">
    <div class="stat-card"><span><?= $totalAppointments ?></span><label>Total bookings</label></div>
    <div class="stat-card highlight"><span><?= $pendingAppointments ?></span><label>Pending confirmation</label></div>
    <div class="stat-card"><span><?= $totalUsers ?></span><label>Registered users</label></div>
    <div class="stat-card"><span><?= $totalProducts ?></span><label>Total products</label></div>
    <div class="stat-card"><span><?= $totalSubscribers ?></span><label>Subscribers</label></div>
    <div class="stat-card"><span><?= $totalOrders ?></span><label>Product orders</label></div>
  </div>

  <div class="panel">
    <div class="panel-head">
      <h2>Recent bookings</h2>
      <a href="appointments.php" class="link">View all →</a>
    </div>
    <table>
      <thead><tr><th>Name</th><th>Service</th><th>Date</th><th>Status</th><th>Requested</th></tr></thead>
      <tbody>
        <?php while ($row = $recent->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['service_category']) ?></td>
          <td><?= htmlspecialchars($row['appointment_date']) ?></td>
          <td><span class="badge badge-<?= $row['status'] ?>"><?= htmlspecialchars($row['status']) ?></span></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($recent->num_rows === 0): ?>
        <tr><td colspan="5" class="empty">No bookings yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
<?php $conn->close(); ?>
