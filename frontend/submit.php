<?php
/* ============================================================
   Penerima kiriman formulir publik: kontak, pengajuan layanan,
   dan langganan newsletter. Membalas JSON untuk fetch(), atau
   redirect bila JavaScript nonaktif.
   ============================================================ */
require_once __DIR__ . '/bootstrap.php';

$wantsJson = str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json')
          || strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

function respond(bool $ok, string $message, string $back = 'index.php'): void
{
    global $wantsJson;
    if ($wantsJson) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'message' => $message]);
        exit;
    }
    $sep = str_contains($back, '?') ? '&' : '?';
    header('Location: ' . url($back) . $sep . ($ok ? 'sukses' : 'gagal') . '=' . rawurlencode($message));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(false, 'Metode tidak diizinkan.');
}

$form = $_POST['form'] ?? '';

/* ---- Unggahan dokumen pemohon ---- */
function store_upload(string $field): ?string
{
    if (empty($_FILES[$field]['name']) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    if ($_FILES[$field]['size'] > upload_limit_bytes('file')) {
        return null;
    }
    $ext = strtolower(pathinfo($_FILES[$field]['name'], PATHINFO_EXTENSION));
    if (!in_array($ext, array_merge(UPLOAD_ALLOWED_FILE, UPLOAD_ALLOWED_IMAGE), true)) {
        return null;
    }
    $dir = UPLOAD_PATH . '/permohonan';
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }
    $name = date('Ymd-His') . '-' . bin2hex(random_bytes(4)) . '.' . $ext;
    if (!move_uploaded_file($_FILES[$field]['tmp_name'], $dir . '/' . $name)) {
        return null;
    }
    return 'permohonan/' . $name;
}

switch ($form) {

    case 'kontak':
        $name    = trim($_POST['name'] ?? '');
        $email   = trim($_POST['email'] ?? '');
        $message = trim($_POST['message'] ?? '');
        if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || $message === '') {
            respond(false, 'Nama, email, dan pesan wajib diisi dengan benar.', 'pages/kontak.php');
        }
        db_insert('contact_messages', [
            'name'    => $name,
            'email'   => $email,
            'phone'   => trim($_POST['phone'] ?? '') ?: null,
            'subject' => trim($_POST['subject'] ?? '') ?: null,
            'message' => $message,
        ]);
        respond(true, 'Pesan Anda berhasil terkirim. Terima kasih!', 'pages/kontak.php');

    case 'pengajuan':
        $service   = trim($_POST['service_name'] ?? '');
        $applicant = trim($_POST['applicant_name'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        if ($service === '' || $applicant === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(false, 'Nama pemohon dan email wajib diisi dengan benar.', 'pages/layanan.php');
        }
        db_insert('service_applications', [
            'service_name'   => $service,
            'applicant_name' => $applicant,
            'institution'    => trim($_POST['institution'] ?? '') ?: null,
            'email'          => $email,
            'phone'          => trim($_POST['phone'] ?? '') ?: null,
            'notes'          => trim($_POST['notes'] ?? '') ?: null,
            'file'           => store_upload('document'),
        ]);
        respond(true, 'Permohonan Anda berhasil diajukan. Tim kami akan segera menghubungi Anda.', 'pages/layanan.php');

    case 'newsletter':
        $email = trim($_POST['email'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            respond(false, 'Alamat email tidak valid.');
        }
        db_exec(
            'INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active = 1',
            [$email]
        );
        respond(true, 'Berhasil berlangganan');

    default:
        respond(false, 'Formulir tidak dikenali.');
}
