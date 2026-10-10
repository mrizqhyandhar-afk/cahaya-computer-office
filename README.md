# Cahaya Computer – Website + PHP

## Cara menjalankan
**Opsi A – server bawaan PHP (paling cepat)**
```
cd cahaya-computer
php -S localhost:8000 router.php
```
Buka http://localhost:8000

**Opsi B – XAMPP / Laragon**
Salin folder `cahaya-computer` ke `htdocs` (XAMPP) atau `www` (Laragon), lalu buka
http://localhost/cahaya-computer/

Syarat: PHP 7.4+ dengan ekstensi pdo_mysql, MySQL aktif (XAMPP), dan database dari database.sql.

## Admin
Buka `/admin/login.php`. Saat pertama kali, halaman meminta Anda membuat akun admin
(buat segera setelah pertama kali dijalankan). Setelah itu login seperti biasa.

## File yang TIDAK diubah
`index.html`, `style.css`, `script.js` (dan halaman lain kecuali `kontak.html`,
yang hanya ditambah 1 baris `<script src="assets/booking-php.js">`).
