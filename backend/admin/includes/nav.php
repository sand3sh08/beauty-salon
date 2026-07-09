<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<header class="admin-nav">
  <div class="wrap admin-nav-row">
    <span class="admin-brand">Verbena <em>&amp;</em> Co. <small>admin</small></span>
    <nav>
      <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>">Dashboard</a>
      <a href="appointments.php" class="<?= $current === 'appointments.php' ? 'active' : '' ?>">Appointments</a>
      <a href="orders.php" class="<?= $current === 'orders.php' ? 'active' : '' ?>">Orders</a>
      <a href="subscribers.php" class="<?= $current === 'subscribers.php' ? 'active' : '' ?>">Subscribers</a>
      <a href="users.php" class="<?= $current === 'users.php' ? 'active' : '' ?>">Users</a>
    </nav>
    <a href="logout.php" class="logout">Logout</a>
  </div>
</header>
