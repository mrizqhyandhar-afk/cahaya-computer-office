<?php
// Endpoint penerima booking dari form kontak.html (dipanggil lewat AJAX).
require_once __DIR__ . '/../includes/db.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

function respond(int $code, array $data): void {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function clip(string $s, int $max): string {
    $s = trim($s);
    return function_exists('mb_substr') ? mb_substr($s, 0, $max, 'UTF-8') : substr($s, 0, $max);
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    respond(405, ['ok' => false, 'error' => 'Gunakan metode POST.']);
}

$nama      = clip((string) ($_POST['nama'] ?? ''), 80);
$layanan   = trim((string) ($_POST['layanan'] ?? ''));
$perangkat = clip((string) ($_POST['perangkat'] ?? ''), 100);
$catatan   = clip((string) ($_POST['catatan'] ?? ''), 1000);

if ($nama === '') {
    respond(422, ['ok' => false, 'error' => 'Nama wajib diisi.']);
}
if (!in_array($layanan, allowed_services(), true)) {
    respond(422, ['ok' => false, 'error' => 'Layanan tidak valid.']);
}

$ip  = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
$pdo = db();

// Anti spam: batasi jumlah booking per IP
$batas = date('Y-m-d H:i:s', time() - RATE_LIMIT_MINUTES * 60);
$cek = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE ip = ? AND created_at >= ?');
$cek->execute([$ip, $batas]);
if ((int) $cek->fetchColumn() >= RATE_LIMIT_MAX) {
    respond(429, ['ok' => false, 'error' => 'Terlalu banyak booking. Coba lagi beberapa menit.']);
}

$ins = $pdo->prepare('INSERT INTO bookings (nama, layanan, perangkat, catatan, status, ip, created_at)
                      VALUES (?, ?, ?, ?, ?, ?, ?)');
$ins->execute([$nama, $layanan, $perangkat, $catatan, 'baru', $ip, date('Y-m-d H:i:s')]);

respond(201, ['ok' => true, 'id' => (int) $pdo->lastInsertId()]);
