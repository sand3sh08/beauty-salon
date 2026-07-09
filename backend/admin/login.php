<?php
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (strtolower($username) === 'admin' && $password === 'admin123') {
        session_regenerate_id(true);
        $_SESSION['admin_id'] = 1;
        $_SESSION['admin_username'] = 'admin';
        header('Location: dashboard.php');
        exit;
    }

    $error = 'Incorrect username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — Verbena & Co.</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{ --ink:#22301F; --cream:#F6F0E4; --gold:#AD8347; --sage:#6E7F5C; }
  *{box-sizing:border-box;}
  body{
    margin:0; min-height:100vh; display:flex; align-items:center; justify-content:center;
    background:var(--ink); font-family:'Manrope',sans-serif; color:var(--ink);
  }
  .card{
    background:var(--cream); border-radius:16px; padding:40px 36px; width:100%; max-width:360px;
    box-shadow:0 30px 60px rgba(0,0,0,.35);
  }
  h1{ font-family:'Fraunces',serif; font-size:1.5rem; margin:0 0 6px; }
  p.sub{ margin:0 0 26px; color:rgba(34,48,31,.6); font-size:.88rem; }
  label{ font-size:.78rem; font-weight:700; display:block; margin-bottom:6px; }
  input{
    width:100%; padding:12px 14px; margin-bottom:18px; border-radius:10px;
    border:1px solid rgba(34,48,31,.2); font-size:.92rem; background:#fff;
  }
  .password-wrap{ position:relative; }
  .password-wrap input{ margin-bottom:0; }
  .password-toggle{ position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--sage); font-weight:700; cursor:pointer; padding:0; }
  button{
    width:100%; padding:13px; border:none; border-radius:999px; background:var(--gold);
    color:var(--cream); font-weight:700; font-size:.92rem; cursor:pointer;
  }
  .error{ background:#f6dede; color:#8a2f2f; padding:10px 14px; border-radius:8px; font-size:.84rem; margin-bottom:18px; }
  .hint{ margin-top:18px; font-size:.76rem; color:rgba(34,48,31,.5); text-align:center; }
</style>
</head>
<body>
  <div class="card">
    <h1>House admin</h1>
    <p class="sub">Sign in to manage bookings and subscribers.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post" novalidate>
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required autofocus>
      <label for="password">Password</label>
      <div class="password-wrap">
        <input type="password" id="password" name="password" required>
        <button type="button" class="password-toggle" data-target="password">Show</button>
      </div>
      <button type="submit">Sign in</button>
    </form>
    <p class="hint">Default: username or email = admin / password = admin123</p>
  </div>
<script>
  document.querySelectorAll('.password-toggle').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = document.getElementById(btn.dataset.target);
      if (!input) return;
      const show = input.type === 'password';
      input.type = show ? 'text' : 'password';
      btn.textContent = show ? 'Hide' : 'Show';
    });
  });
</script>
</body>
</html>
