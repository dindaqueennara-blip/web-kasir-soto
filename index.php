<?php
require_once 'config.php';
if (!empty($_SESSION['user'])) { header('Location: dashboard.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim($_POST['username'] ?? '');
    $p = $_POST['password'] ?? '';
    $st = db()->prepare('SELECT * FROM users WHERE username = ?');
    $st->execute([$u]);
    $user = $st->fetch();
    $ok = false;
    if ($user) {
        if (password_verify($p, $user['password'])) {
            $ok = true;
        } elseif (hash_equals($user['password'], hash('sha256', $p))) {
            // hash awal dari soto-tan-nyana.sql -> upgrade ke bcrypt
            $ok = true;
            db()->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($p, PASSWORD_DEFAULT), $user['id']]);
        }
    }
    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['user'] = ['id'=>$user['id'], 'username'=>$user['username'], 'nama'=>$user['nama']];
        header('Location: dashboard.php'); exit;
    }
    $error = 'Username atau password salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - Soto Semarang Tan Nyana</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="login-page">
  <div class="login-card">
    <header class="header">
      <div class="brand">
        <img class="brand-logo" src="logo-soto.svg" alt="">
        <span class="brand-sep"></span>
        <div class="brand-teks"><span>SOTO SEMARANG</span><span>TAN NYANA</span></div>
      </div>
      <img class="header-mangkuk" src="mangkuk-header.svg" alt="">
    </header>
    <form class="login-form" method="post" autocomplete="off">
      <h3>Login Sistem Kasir</h3>
      <label for="username">Username</label>
      <input type="text" id="username" name="username" placeholder="Username" value="<?= e($_POST['username'] ?? '') ?>" required autofocus>
      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="••••••••" required>
      <?php if ($error): ?><p class="login-error"><?= e($error) ?></p><?php endif; ?>
      <button type="submit" class="btn-login">Masuk</button>
    </form>
  </div>
</body>
</html>