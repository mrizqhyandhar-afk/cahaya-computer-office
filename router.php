<?php
// Dipakai HANYA untuk server bawaan PHP:  php -S localhost:8000 router.php
// Fungsi: memblokir akses langsung ke folder data/, includes/ dan file tersembunyi.
$path = rawurldecode((string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH));
if (preg_match('#^/(data|includes)(/|$)#', $path) || preg_match('#/\.#', $path)) {
    http_response_code(403);
    echo '403 Forbidden';
    return true;
}
return false; // selain itu, layani file seperti biasa
