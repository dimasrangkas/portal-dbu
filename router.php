<?php
/* ============================================================
   Router untuk server bawaan PHP (pengembangan lokal).
   Menyamai aturan .htaccess: alamat tanpa akhiran .php.

   Jalankan:  php -S localhost:8088 -t . router.php
   ============================================================ */

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$file = __DIR__ . $path;

/* Berkas nyata (css, js, gambar, unggahan) disajikan apa adanya. */
if ($path !== '/' && is_file($file)) {
    return false;
}

/* Folder: pakai index.php di dalamnya. */
if (is_dir($file)) {
    $index = rtrim($file, '/') . '/index.php';
    if (is_file($index)) {
        $_SERVER['SCRIPT_NAME']     = rtrim($path, '/') . '/index.php';
        $_SERVER['SCRIPT_FILENAME'] = $index;
        require $index;
        return true;
    }
}

/* /pages/profil -> /pages/profil.php */
$withPhp = rtrim($file, '/') . '.php';
if (is_file($withPhp)) {
    $_SERVER['SCRIPT_NAME']     = rtrim($path, '/') . '.php';
    $_SERVER['SCRIPT_FILENAME'] = $withPhp;
    require $withPhp;
    return true;
}

return false;
