<?php
require_once __DIR__ . '/includes/bootstrap.php';

if (current_user()) {
    redirect(admin_url('index.php'));
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $username = trim($_POST['username'] ?? '');
    $password = (string) ($_POST['password'] ?? '');

    $user = db_one('SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1', [$username, $username]);
    if ($user && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        db_exec('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
        redirect(admin_url('index.php'));
    }
    $error = 'Nama pengguna atau kata sandi salah.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk — CMS Direktorat Bandar Udara</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= e(asset_version(admin_url('assets/admin.css'), ADMIN_PATH, 'assets/admin.css')) ?>">
</head>
<body class="login-body">
  <div class="login-card">
    <div class="login-brand">
      <div class="logo"><i class="bi bi-airplane-fill"></i></div>
      <h1>CMS Direktorat Bandar Udara</h1>
      <p>Masuk untuk mengelola konten situs.</p>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill"></i> <span><?= e($error) ?></span></div>
    <?php endif; ?>

    <form method="post" class="form">
      <?= csrf_field() ?>
      <div class="field">
        <label for="username">Nama Pengguna atau Email</label>
        <input type="text" id="username" name="username" required autofocus value="<?= e($_POST['username'] ?? '') ?>">
      </div>
      <div class="field">
        <label for="password">Kata Sandi</label>
        <input type="password" id="password" name="password" required>
      </div>
      <button type="submit" class="btn btn-primary btn-block">Masuk <i class="bi bi-box-arrow-in-right"></i></button>
    </form>

    <a class="login-back" href="<?= e(site_url('index.php')) ?>"><i class="bi bi-arrow-left"></i> Kembali ke situs</a>
  </div>
</body>
</html>
