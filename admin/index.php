<?php
require_once __DIR__ . '/../includes/auth.php';
require_login();

$pdo = db();
$statusList = allowed_status();

// ---- Aksi (ubah status / hapus), memakai POST + token CSRF ----
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);
    $aksi = (string) ($_POST['aksi'] ?? '');
    if ($id > 0 && $aksi === 'status') {
        $s = (string) ($_POST['status'] ?? '');
        if (isset($statusList[$s])) {
            $pdo->prepare('UPDATE bookings SET status = ? WHERE id = ?')->execute([$s, $id]);
        }
    } elseif ($id > 0 && $aksi === 'hapus') {
        $pdo->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]);
    }
    $back = (string) ($_POST['back'] ?? '');
    header('Location: index.php' . ($back !== '' ? '?' . $back : ''));
    exit;
}

// ---- Filter & pencarian ----
$fs = (string) ($_GET['status'] ?? '');
$q  = trim((string) ($_GET['q'] ?? ''));
$where = [];
$args = [];
if (isset($statusList[$fs])) {
    $where[] = 'status = ?';
    $args[] = $fs;
}
if ($q !== '') {
    $where[] = '(nama LIKE ? OR layanan LIKE ? OR perangkat LIKE ? OR catatan LIKE ?)';
    $like = '%' . $q . '%';
    array_push($args, $like, $like, $like, $like);
}
$sqlWhere = $where ? ' WHERE ' . implode(' AND ', $where) : '';

// ---- Ekspor CSV ----
if (($_GET['export'] ?? '') === 'csv') {
    $st = $pdo->prepare('SELECT id, created_at, nama, layanan, perangkat, catatan, status FROM bookings' . $sqlWhere . ' ORDER BY id DESC');
    $st->execute($args);
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="booking-cahaya-computer-' . date('Ymd') . '.csv"');
    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // BOM supaya Excel membaca UTF-8
    fputcsv($out, ['ID', 'Waktu', 'Nama', 'Layanan', 'Perangkat', 'Catatan', 'Status']);
    while ($r = $st->fetch()) {
        foreach ($r as $k => $v) { // cegah CSV injection
            if (is_string($v) && $v !== '' && strpos('=+-@', $v[0]) !== false) {
                $r[$k] = "'" . $v;
            }
        }
        fputcsv($out, $r);
    }
    fclose($out);
    exit;
}

// ---- Data tampilan ----
$st = $pdo->prepare('SELECT * FROM bookings' . $sqlWhere . ' ORDER BY id DESC LIMIT 200');
$st->execute($args);
$rows = $st->fetchAll();

$stat = ['total' => 0, 'baru' => 0, 'diproses' => 0, 'selesai' => 0];
foreach ($pdo->query('SELECT status, COUNT(*) AS n FROM bookings GROUP BY status') as $r) {
    if (isset($stat[$r['status']])) { $stat[$r['status']] = (int) $r['n']; }
    $stat['total'] += (int) $r['n'];
}
$backQuery = http_build_query(array_filter(['status' => $fs, 'q' => $q], 'strlen'));
$badge = ['baru' => 'warning text-dark', 'diproses' => 'info text-dark', 'selesai' => 'success'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Dashboard Booking – Cahaya Computer</title>
  <meta name="robots" content="noindex">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background: #FFFDF5; color: #2A1B0A; }
    .topbar { background: #FFB300; }
    .card-stat { background: #fff; border-radius: 14px; padding: 1rem 1.25rem; box-shadow: 0 3px 12px rgba(42,27,10,.08); }
    .card-stat strong { font-size: 1.8rem; }
    .btn-sun { background: #FFB300; border: 0; font-weight: 700; color: #2A1B0A; }
    .btn-sun:hover { background: #FF8A00; }
    td.catatan { max-width: 280px; white-space: pre-wrap; word-break: break-word; }
  </style>
</head>
<body>
<nav class="topbar py-2">
  <div class="container d-flex justify-content-between align-items-center">
    <span class="fw-bold d-flex align-items-center gap-2">
      <img src="../images/logo.jpg" alt="" width="34" height="34" style="border-radius:50%"> Dashboard Booking
    </span>
    <span class="d-flex gap-2">
      <a class="btn btn-sm btn-dark rounded-pill" href="../index.html" target="_blank" rel="noopener">Lihat website</a>
      <a class="btn btn-sm btn-outline-dark rounded-pill" href="logout.php">Keluar</a>
    </span>
  </div>
</nav>

<main class="container my-4">
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3"><div class="card-stat">Total<br><strong><?= $stat['total'] ?></strong></div></div>
    <div class="col-6 col-md-3"><div class="card-stat">Baru<br><strong><?= $stat['baru'] ?></strong></div></div>
    <div class="col-6 col-md-3"><div class="card-stat">Diproses<br><strong><?= $stat['diproses'] ?></strong></div></div>
    <div class="col-6 col-md-3"><div class="card-stat">Selesai<br><strong><?= $stat['selesai'] ?></strong></div></div>
  </div>

  <form class="row g-2 mb-3" method="get">
    <div class="col-12 col-md-5"><input class="form-control" type="search" name="q" value="<?= e($q) ?>" placeholder="Cari nama, layanan, perangkat..."></div>
    <div class="col-6 col-md-3">
      <select class="form-select" name="status">
        <option value="">Semua status</option>
        <?php foreach ($statusList as $k => $label): ?>
          <option value="<?= e($k) ?>" <?= $fs === $k ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="col-6 col-md-4 d-flex gap-2">
      <button class="btn btn-sun rounded-pill px-4" type="submit">Terapkan</button>
      <a class="btn btn-outline-dark rounded-pill" href="?<?= e(http_build_query(array_filter(['status' => $fs, 'q' => $q, 'export' => 'csv'], 'strlen'))) ?>">Ekspor CSV</a>
    </div>
  </form>

  <div class="table-responsive bg-white rounded-3 shadow-sm">
    <table class="table align-middle mb-0">
      <thead><tr><th>#</th><th>Waktu</th><th>Nama</th><th>Layanan</th><th>Perangkat</th><th>Catatan</th><th>Status</th><th></th></tr></thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="8" class="text-center text-muted py-4">Belum ada booking.</td></tr>
      <?php endif; ?>
      <?php foreach ($rows as $r): ?>
        <tr>
          <td><?= (int) $r['id'] ?></td>
          <td class="text-nowrap small"><?= e($r['created_at']) ?></td>
          <td><?= e($r['nama']) ?></td>
          <td><?= e($r['layanan']) ?></td>
          <td><?= e($r['perangkat']) ?></td>
          <td class="catatan small"><?= e($r['catatan']) ?></td>
          <td>
            <form method="post" class="d-flex gap-1">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="aksi" value="status">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="back" value="<?= e($backQuery) ?>">
              <select class="form-select form-select-sm" name="status" onchange="this.form.submit()">
                <?php foreach ($statusList as $k => $label): ?>
                  <option value="<?= e($k) ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </form>
          </td>
          <td>
            <form method="post" onsubmit="return confirm('Hapus booking ini?')">
              <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
              <input type="hidden" name="aksi" value="hapus">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <input type="hidden" name="back" value="<?= e($backQuery) ?>">
              <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <p class="small text-muted mt-2">Menampilkan maksimal 200 booking terbaru.</p>
</main>
</body>
</html>
