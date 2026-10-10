<?php
// Konfigurasi umum Cahaya Computer (PHP)
date_default_timezone_set('Asia/Makassar'); // WITA

define('APP_NAME', 'Cahaya Computer');
// Koneksi MySQL (XAMPP default: user root, password kosong)
define('DB_HOST', 'localhost');
define('DB_NAME', 'cahaya_computer');
define('DB_USER', 'root');
define('DB_PASS', '');
define('SESSION_NAME', 'cahaya_admin');

// Batas booking per IP (anti spam)
define('RATE_LIMIT_MAX', 5);       // maksimal booking
define('RATE_LIMIT_MINUTES', 10);  // dalam rentang menit

// Daftar layanan yang sah (harus sama dengan <option> di kontak.html)
function allowed_services(): array {
    return [
        'Install Ulang Windows', 'Ganti SSD', 'Upgrade RAM', 'Ganti Keyboard',
        'Service Engsel', 'Ganti Speaker', 'Ganti Baterai', 'Ganti Pasta',
        'Pasang Anti Gores', 'Pasang Stiker Laptop',
        'Beli Mouse', 'Beli Pelindung Keyboard', 'Beli Flashdisk', 'Beli Box HDD',
        'Beli Tas Laptop', 'Beli RAM', 'Beli SSD', 'Beli Baterai', 'Beli Adaptor',
        'Beli Anti Gores', 'Beli Garskin',
    ];
}

function allowed_status(): array {
    return ['baru' => 'Baru', 'diproses' => 'Diproses', 'selesai' => 'Selesai'];
}
