<?php
/* ============================================================
   Bootstrap panel admin — sesi, autentikasi, CSRF, flash.
   ============================================================ */
require_once dirname(__DIR__, 2) . '/shared/functions.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name(SESSION_NAME);
    session_start();
}

/* ---------- URL ---------- */
function admin_url(string $path = ''): string
{
    return ADMIN_URL . '/' . ltrim($path, '/');
}

function site_url(string $path = ''): string
{
    return FRONTEND_URL . '/' . ltrim($path, '/');
}

/* ---------- Autentikasi ---------- */
function current_user(): ?array
{
    static $user = null;
    if ($user === null && !empty($_SESSION['user_id'])) {
        $user = db_one('SELECT * FROM users WHERE id = ? AND is_active = 1', [$_SESSION['user_id']]) ?: false;
    }
    return $user ?: null;
}

function require_login(): array
{
    $user = current_user();
    if (!$user) {
        header('Location: ' . admin_url('login.php'));
        exit;
    }
    return $user;
}

function require_admin(): array
{
    $user = require_login();
    if ($user['role'] !== 'admin') {
        flash('danger', 'Hanya administrator yang dapat membuka halaman tersebut.');
        header('Location: ' . admin_url('index.php'));
        exit;
    }
    return $user;
}

/* ---------- CSRF ---------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    /* POST yang melampaui post_max_size membuat PHP mengosongkan $_POST dan
       $_FILES tanpa memunculkan galat apa pun. Akibatnya _token ikut hilang dan
       gejalanya menyamar sebagai sesi kedaluwarsa, padahal sesinya sehat dan
       yang sebenarnya salah adalah ukuran unggahan. Bedakan lebih dulu. */
    $len = (int) ($_SERVER['CONTENT_LENGTH'] ?? 0);
    if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && $len > 0 && $_POST === []) {
        http_response_code(413);
        exit(
            'Data yang dikirim terlalu besar (' . round($len / 1048576, 1) . ' MB). '
            . 'Batas server saat ini post_max_size = ' . ini_get('post_max_size')
            . ' dan upload_max_filesize = ' . ini_get('upload_max_filesize') . '. '
            . 'Perkecil ukuran berkas lalu coba lagi.'
        );
    }

    $token = $_POST['_token'] ?? '';
    if (!hash_equals($_SESSION['csrf'] ?? '', $token)) {
        http_response_code(419);
        exit('Sesi kedaluwarsa. Muat ulang halaman lalu coba lagi.');
    }
}

/* ---------- Flash ---------- */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function take_flashes(): array
{
    $out = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $out;
}

/* ---------- Redirect ---------- */
function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}

/* ---------- Bantu tampilan ---------- */
function old(string $key, $default = '')
{
    return $_SESSION['old'][$key] ?? $default;
}

function is_active_nav(string $file, string $resource = ''): bool
{
    $current = basename($_SERVER['SCRIPT_NAME']);
    if ($current !== $file) {
        return false;
    }
    return $resource === '' || ($_GET['r'] ?? '') === $resource;
}
