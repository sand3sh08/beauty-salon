<?php
session_start();
require_once __DIR__ . '/../config.php';

$error = '';
$success = $_SESSION['flash_success'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($fullName === '' || strlen($fullName) > 120) {
        $error = 'Please enter your full name.';
    } elseif ($username === '' || strlen($username) < 3 || strlen($username) > 40) {
        $error = 'Choose a username between 3 and 40 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif ($address === '') {
        $error = 'Please enter your shipping address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        $check = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ? LIMIT 1');
        $check->bind_param('ss', $username, $email);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $error = 'That username or email is already registered.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare('INSERT INTO users (full_name, username, email, password_hash, address) VALUES (?, ?, ?, ?, ?)');
            $stmt->bind_param('sssss', $fullName, $username, $email, $hash, $address);
            if ($stmt->execute()) {
                session_regenerate_id(true);
                $_SESSION['flash_success'] = 'Account created successfully. Please log in.';
                header('Location: login.php?registered=1');
                exit;
            }
            $error = 'We could not create your account right now. Please try again.';
        }
        $check->close();
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create account — Verbena & Co.</title>
<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600&family=Manrope:wght@400;600;700&display=swap" rel="stylesheet">
<style>
  :root{ --ink:#22301F; --cream:#F6F0E4; --gold:#AD8347; --sage:#6E7F5C; }
  *{box-sizing:border-box;} body{margin:0; min-height:100vh; display:grid; place-items:center; background:linear-gradient(135deg,var(--ink),#375130); font-family:'Manrope',sans-serif; color:var(--ink);} .card{background:var(--cream); width:min(460px,92vw); border-radius:20px; padding:34px; box-shadow:0 32px 70px rgba(0,0,0,.25);} h1{font-family:'Fraunces',serif; margin:0 0 8px;} p{margin:0 0 20px; color:rgba(34,48,31,.68);} .error{background:#f2dddd; color:#8c2f2f; padding:10px 12px; border-radius:10px; margin-bottom:16px;} form{display:grid; gap:12px;} label{font-size:.8rem; font-weight:700;} input{padding:12px 14px; border:1px solid rgba(34,48,31,.2); border-radius:10px; font-size:.95rem;} .password-wrap{position:relative;} .password-wrap input{width:100%;} .password-toggle{position:absolute; right:10px; top:50%; transform:translateY(-50%); background:none; border:none; color:var(--sage); font-size:.85rem; font-weight:700; cursor:pointer; padding:0;} button{padding:13px 16px; border:none; border-radius:999px; background:var(--gold); color:var(--cream); font-weight:700; cursor:pointer; margin-top:8px;} .sub{font-size:.9rem; margin-top:14px; text-align:center;} .sub a{color:var(--sage); font-weight:700;}
</style>
</head>
<body>
  <div class="card">
    <h1>Create your account</h1>
    <p>Register to buy products and book appointments with ease.</p>
    <?php if ($error): ?><div class="error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <?php if ($success): ?><div class="error" style="background:#e6f7ea;color:#2f6a3d;"><?= htmlspecialchars($success) ?></div><?php endif; ?>
    <form method="post">
      <label for="full_name">Full name</label>
      <input id="full_name" name="full_name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
      <label for="username">Username</label>
      <input id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
      <label for="email">Email</label>
      <input id="email" type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      <label for="address">Shipping Address</label>
      <input id="address" name="address" required value="<?= htmlspecialchars($_POST['address'] ?? '') ?>" placeholder="Your delivery address">
      <label for="password">Password</label>
      <div class="password-wrap">
        <input id="password" type="password" name="password" required>
        <button type="button" class="password-toggle" data-target="password">Show</button>
      </div>
      <label for="confirm_password">Confirm password</label>
      <div class="password-wrap">
        <input id="confirm_password" type="password" name="confirm_password" required>
        <button type="button" class="password-toggle" data-target="confirm_password">Show</button>
      </div>
      <button type="submit">Register</button>
    </form>
    <p class="sub">Already have an account? <a href="login.php">Login here</a></p>
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
