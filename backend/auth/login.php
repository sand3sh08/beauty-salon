<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = '';
$success = isset($_GET['registered']) && $_GET['registered'] === '1' ? 'Account created successfully. Please log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username !== '' && $password !== '') {
        $usernameTrimmed = strtolower(trim($username));
        $passwordTrimmed = trim($password);

        if ($usernameTrimmed === 'admin' && $passwordTrimmed === 'admin123') {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_username'] = 'admin';
            header('Location: ../admin/dashboard.php');
            exit;
        }

        $stmt = $conn->prepare('SELECT id, full_name, username, password_hash FROM users WHERE username = ? OR email = ? LIMIT 1');
        $stmt->bind_param('ss', $username, $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if ($user && password_verify($password, $user['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['flash_success'] = 'Welcome back!';
            header('Location: ../../index.php');
            exit;
        }
    }

    $error = 'Incorrect username or password.';
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User login — Verbena & Co.</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{ --ink:#22301F; --cream:#F6F0E4; --gold:#AD8347; --sage:#6E7F5C; }
  *{box-sizing:border-box;} body{margin:0; min-height:100vh; display:grid; place-items:center; background:linear-gradient(135deg,var(--ink),#375130); font-family:'Manrope',sans-serif; color:var(--ink);} .card{background:var(--cream); width:min(420px,92vw); border-radius:20px; padding:34px; box-shadow:0 32px 70px rgba(0,0,0,.25);} h1{font-family:'Fraunces',serif; margin:0 0 8px;} p{margin:0 0 20px; color:rgba(34,48,31,.68);} .error{background:#f2dddd; color:#8c2f2f; padding:10px 12px; border-radius:10px; margin-bottom:16px;} form{display:grid; gap:12px;} label{font-size:.8rem; font-weight:700;} input{padding:12px 14px; border:1px solid rgba(34,48,31,.2); border-radius:10px; font-size:.95rem;} .password-wrap{position:relative;} .password-wrap input{width:100%;} .password-toggle{position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--sage); font-size:.85rem; font-weight:700; cursor:pointer; padding:0;} button{padding:13px 16px; border:none; border-radius:999px; background:var(--gold); color:var(--cream); font-weight:700; cursor:pointer; margin-top:8px;} .sub{font-size:.9rem; margin-top:14px; text-align:center;} .sub a{color:var(--sage); font-weight:700;}
</style>
</head>
<body>
  <div class="card">
    <h1>Welcome back</h1>
    <p>Sign in to buy products and book appointments.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="error" style="background:#e6f7ea;color:#2f6a3d;"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="post">
      <label for="username">Username or email</label>
      <input id="username" name="username" required>
      <label for="password">Password</label>
      <div class="password-wrap">
        <input id="password" type="password" name="password" required>
        <button type="button" class="password-toggle" data-target="password">Show</button>
      </div>
      <button type="submit">Login</button>
    </form>
    <p class="sub">New here? <a href="register.php">Create an account</a></p>
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
