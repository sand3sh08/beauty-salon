<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$result = $conn->query('SELECT po.id, po.product_name, po.unit_price, po.quantity, po.total_amount, po.created_at, u.username FROM product_orders po JOIN users u ON po.user_id = u.id ORDER BY po.created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Orders — Verbena & Co. Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>
<main class="wrap">
  <h1>Product orders</h1>
  <p class="sub">Review customer purchases and quantities.</p>
  <div class="panel">
    <table>
      <thead>
        <tr><th>Customer</th><th>Product</th><th>Qty</th><th>Unit price</th><th>Total</th><th>Placed</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['product_name']) ?></td>
          <td><?= (int) $row['quantity'] ?></td>
          <td>₹<?= number_format((float) $row['unit_price'], 2) ?></td>
          <td>₹<?= number_format((float) $row['total_amount'], 2) ?></td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="6" class="empty">No product orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
<?php $conn->close(); ?>
