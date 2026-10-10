<?php
require_once __DIR__ . '/../includes/auth.php';
start_session();

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$setup = admin_count() === 0; // belum ada admin -> mode buat akun pertama
$error = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    $username = trim((string) ($_POST['username'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($setup) {
        $konfirmasi = (string) ($_POST['konfirmasi'] ?? '');
        if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
            $error = 'Username 3-30 karakter (huruf, angka, titik, strip, garis bawah).';
        } elseif (strlen($password) < 8) {
            $error = 'Password minimal 8 karakter.';
        } elseif ($password !== $konfirmasi) {
            $error = 'Konfirmasi password tidak sama.';
        } else {
            $st = db()->prepare('INSERT INTO admins (username, password_hash, created_at) VALUES (?, ?, ?)');
            $st->execute([$username, password_hash($password, PASSWORD_DEFAULT), date('Y-m-d H:i:s')]);
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) db()->lastInsertId();
            header('Location: index.php');
            exit;
        }
    } else {
        $st = db()->prepare('SELECT id, password_hash FROM admins WHERE username = ?');
        $st->execute([$username]);
        $row = $st->fetch();
        if ($row && password_verify($password, $row['password_hash'])) {
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $row['id'];
            header('Location: index.php');
            exit;
        }
        sleep(1); // perlambat tebak-tebakan password
        $error = 'Username atau password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= $setup ? 'Buat Akun Admin' : 'Login Admin' ?> – Cahaya Computer</title>
  <meta name="robots" content="noindex">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #FFFDF5; color: #2A1B0A; }
    .box { max-width: 400px; margin: 8vh auto; background: #fff; border-radius: 18px; padding: 2rem; box-shadow: 0 6px 24px rgba(42,27,10,.12); }
    .btn-sun { background: #FFB300; border: 0; font-weight: 700; color: #2A1B0A; }
    .btn-sun:hover { background: #FF8A00; }
  </style>
</head>
<body>
<div class="box">
  <div class="text-center mb-3">
    <img src="../images/logo.jpg" alt="Logo" width="64" height="64" style="border-radius:50%">
    <h1 class="h4 mt-2"><?= $setup ? 'Buat Akun Admin' : 'Login Admin' ?></h1>
    <?php if ($setup): ?><p class="small text-muted mb-0">Belum ada admin. Buat akun pertama sekarang.</p><?php endif; ?>
  </div>
  <?php if ($error): ?><div class="alert alert-danger py-2"><?= e($error) ?></div><?php endif; ?>
  <form method="post" autocomplete="off">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
    <div class="mb-3"><label class="form-label" for="username">Username</label>
      <input class="form-control" id="username" name="username" required value="<?= e($_POST['username'] ?? '') ?>"></div>
    <div class="mb-3"><label class="form-label" for="password">Password</label>
      <input class="form-control" type="password" id="password" name="password" required></div>
    <?php if ($setup): ?>
    <div class="mb-3"><label class="form-label" for="konfirmasi">Ulangi password</label>
      <input class="form-control" type="password" id="konfirmasi" name="konfirmasi" required></div>
    <?php endif; ?>
    <button class="btn btn-sun w-100 rounded-pill" type="submit"><?= $setup ? 'Buat akun' : 'Masuk' ?></button>
  </form>
  <p class="text-center mt-3 mb-0"><a href="../index.html" class="small">&larr; Kembali ke website</a></p>
</div>
</body>
</html>
