<?php
/* ============================================================
   MEMBUANG TABEL KONTEN LAMA

   Isi ~40 tabel konten skema lama sudah disalin ke `contents` oleh
   migrate-unifikasi-konten.php. Selama tabel lama masih ada, situs
   tetap berjalan penuh dari tabel baru — jadi tidak ada alasan
   terburu-buru menjalankan berkas ini.

       php database/drop-tabel-lama.php        lihat rencananya saja
       php database/drop-tabel-lama.php --ya   benar-benar membuang

   Skrip menolak berjalan bila tabel baru belum terisi.
   CADANGKAN BASIS DATA LEBIH DULU:
       mysqldump -u USER -p NAMA_DB > backup-$(date +%F-%H%M).sql
   ============================================================ */

if (PHP_SAPI !== 'cli') {
    exit("Skrip ini hanya untuk baris perintah.\n");
}

require_once dirname(__DIR__) . '/shared/db.php';

/* Urutan penting: anak lebih dulu, induk belakangan. */
const TABEL_LAMA = [
    'hero_slides', 'stats', 'director_message', 'about_cards', 'quick_links', 'featured_services',
    'region_airports', 'regions', 'rpjmn_areas', 'rpjmn_programs', 'home_video', 'partners',
    'profil_about', 'timeline_items', 'missions', 'value_cards', 'strategic_goals', 'directorate_functions',
    'org_unit_functions', 'org_units', 'tf_details', 'tf_units', 'tf_functions', 'tf_overview',
    'service_requirements', 'service_steps', 'service_downloads', 'services',
    'general_requirements', 'process_steps', 'faqs',
    'regulation_scopes', 'regulation_categories', 'regulations',
    'news_categories', 'news', 'announcements',
    'gallery_photos', 'gallery_albums', 'gallery_videos',
    'contact_info', 'contact_subjects',
    /* Meta halaman & judul seksi kini menjadi kolom pada
       `categories` dan `subcategories`. */
    'page_meta', 'sections',
];

$jalankan = in_array('--ya', $argv, true);

/* ---- Pengaman: tabel baru harus sudah terisi ---- */
foreach (['categories', 'subcategories', 'contents'] as $wajib) {
    $ada = db_value(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
        [DB_NAME, $wajib],
        0
    );
    if (!$ada) {
        exit("BATAL: tabel `$wajib` belum ada. Jalankan migrate-unifikasi-konten.php lebih dulu.\n");
    }
}
$isi = (int) db_value('SELECT COUNT(*) FROM contents', [], 0);
if ($isi === 0) {
    exit("BATAL: tabel `contents` masih kosong. Jalankan migrate-unifikasi-konten.php lebih dulu.\n");
}
echo "Tabel baru berisi $isi konten.\n\n";

/* ---- Daftar tabel lama yang masih ada ---- */
$ada = [];
foreach (TABEL_LAMA as $tabel) {
    $cek = db_value(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
        [DB_NAME, $tabel],
        0
    );
    if ($cek) {
        $ada[$tabel] = (int) db_value("SELECT COUNT(*) FROM `$tabel`", [], 0);
    }
}
if (!$ada) {
    exit("Tidak ada tabel lama yang tersisa — tidak ada yang perlu dibuang.\n");
}

printf("%d tabel lama akan dibuang (%d baris):\n", count($ada), array_sum($ada));
foreach ($ada as $tabel => $n) {
    printf("  %-24s %6d baris\n", $tabel, $n);
}

if (!$jalankan) {
    echo "\nIni baru rencana. Jalankan ulang dengan --ya untuk benar-benar membuangnya.\n";
    exit;
}

db()->exec('SET FOREIGN_KEY_CHECKS = 0');
foreach (array_keys($ada) as $tabel) {
    db()->exec("DROP TABLE IF EXISTS `$tabel`");
}
db()->exec('SET FOREIGN_KEY_CHECKS = 1');

$sisa = (int) db_value(
    'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ?',
    [DB_NAME],
    0
);
echo "\nSelesai. Basis data kini berisi $sisa tabel.\n";
