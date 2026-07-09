<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';

// Handle user status toggle (active/inactive)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['status'] === 'inactive' ? 'inactive' : 'active';
    $stmt = $conn->prepare('UPDATE users SET status = ? WHERE id = ?');
    $stmt->bind_param('si', $status, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: users.php');
    exit;
}

$result = $conn->query('SELECT id, full_name, username, email, address, status, created_at FROM users ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Website Users — Verbena & Co. Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="wrap">
  <h1>Website users</h1>
  <p class="sub">View and manage registered customer accounts.</p>

  <div class="panel">
    <table>
      <thead>
        <tr><th>ID</th><th>Full name</th><th>Username</th><th>Email</th><th>Address</th><th>Joined</th><th>Status</th><th>Action</th></tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= (int) $row['id'] ?></td>
          <td><?= htmlspecialchars($row['full_name']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?= htmlspecialchars($row['address'] ?? '') ?>">
            <?= htmlspecialchars($row['address'] ?: '—') ?>
          </td>
          <td><?= htmlspecialchars($row['created_at']) ?></td>
          <td>
            <span class="badge badge-<?= $row['status'] === 'active' ? 'confirmed' : 'cancelled' ?>">
              <?= htmlspecialchars(ucfirst($row['status'])) ?>
            </span>
          </td>
          <td>
            <form method="post" style="display:inline;">
              <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <input type="hidden" name="status" value="<?= $row['status'] === 'active' ? 'inactive' : 'active' ?>">
              <button type="submit" class="link" style="background:none; border:none; cursor:pointer; font-weight:600; padding:0;">
                Toggle Status
              </button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="7" class="empty">No registered users yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
<?php $conn->close(); ?>
