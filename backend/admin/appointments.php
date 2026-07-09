<?php
require_once __DIR__ . '/includes/auth_check.php';
require_once __DIR__ . '/../config.php';

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status'])) {
    $id = (int) $_POST['id'];
    $status = $_POST['status'];
    if (in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'], true)) {
        $stmt = $conn->prepare('UPDATE appointments SET status = ? WHERE id = ?');
        $stmt->bind_param('si', $status, $id);
        $stmt->execute();
        $stmt->close();
    }
    header('Location: appointments.php');
    exit;
}

$result = $conn->query('SELECT * FROM appointments ORDER BY created_at DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Appointments — Verbena & Co. Admin</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&family=DM+Mono&display=swap" rel="stylesheet">
<link rel="stylesheet" href="admin.css">
</head>
<body>
<?php include __DIR__ . '/includes/nav.php'; ?>

<main class="wrap">
  <h1>Appointments</h1>
  <p class="sub">Confirm, cancel or mark visits complete.</p>

  <div class="panel">
    <table>
      <thead>
        <tr><th>Name</th><th>Phone</th><th>Service</th><th>Date</th><th>Notes</th><th>Status</th><th></th></tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['phone']) ?></td>
          <td><?= htmlspecialchars($row['service_category']) ?></td>
          <td><?= htmlspecialchars($row['appointment_date']) ?></td>
          <td class="notes"><?= htmlspecialchars($row['notes'] ?: '—') ?></td>
          <td><span class="badge badge-<?= $row['status'] ?>"><?= htmlspecialchars($row['status']) ?></span></td>
          <td>
            <form method="post" class="status-form">
              <input type="hidden" name="id" value="<?= (int) $row['id'] ?>">
              <select name="status" onchange="this.form.submit()">
                <?php foreach (['pending','confirmed','cancelled','completed'] as $s): ?>
                <option value="<?= $s ?>" <?= $s === $row['status'] ? 'selected' : '' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
        <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="7" class="empty">No bookings yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>
</body>
</html>
<?php $conn->close(); ?>
