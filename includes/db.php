<?php
require_once __DIR__ . '/config.php';

// Koneksi MySQL (phpMyAdmin). Tabel dibuat otomatis kalau belum ada,
// tapi database `cahaya_computer` harus sudah dibuat dulu di phpMyAdmin.
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec("CREATE TABLE IF NOT EXISTS bookings (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        nama       VARCHAR(80)  NOT NULL,
        layanan    VARCHAR(100) NOT NULL,
        perangkat  VARCHAR(100) NOT NULL DEFAULT '',
        catatan    TEXT NOT NULL,
        status     VARCHAR(20)  NOT NULL DEFAULT 'baru',
        ip         VARCHAR(45)  NOT NULL DEFAULT '',
        created_at DATETIME     NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $pdo->exec("CREATE TABLE IF NOT EXISTS admins (
        id            INT AUTO_INCREMENT PRIMARY KEY,
        username      VARCHAR(30)  NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at    DATETIME     NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    return $pdo;
}
