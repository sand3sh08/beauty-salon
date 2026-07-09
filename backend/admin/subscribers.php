<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';

$result = $conn->query('SELECT email, subscribed_at FROM newsletter_subscribers ORDER BY subscribed_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Subscribers — Verbena & Co. Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="wrap">
  <h1>Newsletter subscribers</h1>
  <p class="sub">Everyone who signed up from the footer form.</p>

  <div class="panel">
    <table>
      <thead><tr><th>Email</th><th>Subscribed</th></tr></thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= htmlspecialchars($row['subscribed_at']) ?></td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="2" class="empty">No subscribers yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
<?php $conn->close(); ?>
