<?php
/* ============================================================
   UNIFIKASI KONTEN
   Memindahkan isi ~40 tabel konten skema lama ke lima tabel baru:
   categories, subcategories, tags, contents, content_tags.

   Jalankan dari baris perintah, dari folder proyek:

       php database/migrate-unifikasi-konten.php          (jalankan)
       php database/migrate-unifikasi-konten.php --ulang  (kosongkan dulu, lalu ulangi)

   Tabel lama TIDAK dihapus — skrip ini hanya menyalin. Setelah situs
   diperiksa dan benar, tabel lama dapat dibuang dengan
   database/drop-tabel-lama.sql.
   ============================================================ */

if (PHP_SAPI !== 'cli') {
    exit("Skrip ini hanya untuk baris perintah.\n");
}

require_once dirname(__DIR__) . '/shared/db.php';
require_once dirname(__DIR__) . '/shared/functions.php';

$ulang = in_array('--ulang', $argv, true);

/* ------------------------------------------------------------
   1. Pastikan tabel baru ada — DDL-nya dibaca dari schema.sql
      supaya hanya ada satu sumber kebenaran.
   ------------------------------------------------------------ */
const TABEL_BARU = ['categories', 'subcategories', 'tags', 'contents', 'content_tags'];

function tabel_ada(string $nama): bool
{
    return (bool) db_value(
        'SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ?',
        [DB_NAME, $nama],
        0
    );
}

echo "Menyiapkan tabel baru...\n";
/* CREATE harus urut induk → anak karena ada foreign key. */
foreach (TABEL_BARU as $nama) {
    if (!tabel_ada($nama)) {
        $sql = file_get_contents(__DIR__ . '/schema.sql');
        preg_match('/CREATE TABLE `' . $nama . '` \(.*?\n\) ENGINE=[^;]*;/s', $sql, $m)
            or exit("Gagal membaca DDL tabel `$nama` dari schema.sql\n");
        db()->exec($m[0]);
        echo "  + tabel `$nama` dibuat\n";
    }
}

$sudahAda = (int) db_value('SELECT COUNT(*) FROM contents', [], 0);
if ($sudahAda > 0 && !$ulang) {
    exit("Tabel `contents` sudah berisi $sudahAda baris. Jalankan ulang dengan --ulang untuk menimpanya.\n");
}
if ($ulang) {
    db()->exec('SET FOREIGN_KEY_CHECKS = 0');
    foreach (['content_tags', 'contents', 'subcategories', 'tags', 'categories'] as $t) {
        db()->exec("TRUNCATE TABLE `$t`");
    }
    db()->exec('SET FOREIGN_KEY_CHECKS = 1');
    echo "  · isi lama tabel baru dikosongkan\n";
}

/* ------------------------------------------------------------
   2. Kategori — satu baris per halaman portal
   ------------------------------------------------------------ */
$KATEGORI = [
    ['home',             'Beranda',          'bi-house-door',        'index.php'],
    ['profil',           'Profil',           'bi-info-square',       'pages/profil.php'],
    ['organisasi',       'Organisasi',       'bi-diagram-3',         'pages/organisasi.php'],
    ['layanan',          'Layanan',          'bi-headset',           'pages/layanan.php'],
    ['regulasi',         'Regulasi',         'bi-journal-text',      'pages/regulasi.php'],
    ['berita',           'Berita',           'bi-newspaper',         'pages/berita.php'],
    ['galeri',           'Galeri',           'bi-images',            'pages/galeri.php'],
    ['kontak',           'Kontak',           'bi-telephone',         'pages/kontak.php'],
    ['informasi-publik', 'Informasi Publik', 'bi-building',          'pages/informasi-publik.php'],
    ['dokumen-publik',   'Dokumen Publik',   'bi-file-earmark-text', 'pages/dokumen-publik.php'],
    ['tugas-fungsi',     'Tugas & Fungsi',   'bi-bullseye',          'pages/profil.php#tugas-pokok'],
];

$katId = [];
$sort  = 0;
foreach ($KATEGORI as [$slug, $label, $icon, $file]) {
    $meta = db_one('SELECT * FROM page_meta WHERE page_key = ?', [$slug]) ?: [];
    $katId[$slug] = db_insert('categories', [
        'slug'             => $slug,
        'label'            => $meta['page_label'] ?? $label,
        'icon'             => $icon,
        'page_file'        => $file,
        'meta_title'       => $meta['meta_title'] ?? null,
        'meta_description' => $meta['meta_description'] ?? null,
        'eyebrow'          => $meta['eyebrow'] ?? null,
        'heading'          => $meta['heading'] ?? null,
        'subtitle'         => $meta['subtitle'] ?? null,
        'breadcrumb'       => $meta['breadcrumb'] ?? null,
        'sort'             => $sort += 10,
        'is_active'        => 1,
    ]);
}
echo 'Kategori: ' . count($katId) . " baris\n";

/* ------------------------------------------------------------
   3. Sub kategori — seksi tetap, ditambah yang berasal dari
      tabel kategori lama (berita, regulasi, layanan).
      Kolom: [kategori, slug, label, blueprint, is_child, hint]
   ------------------------------------------------------------ */
$SUB = [
    /* ---- Beranda ---- */
    ['home', 'slider',          'Slider Utama',            'slide',      0, 'Tampil paling atas beranda.'],
    ['home', 'statistik',       'Statistik',               'statistik',  0, 'Deretan angka di bawah slider.'],
    ['home', 'sambutan',        'Sambutan Direktur',       'sambutan',   0, 'Cukup satu konten.'],
    ['home', 'about',           'Tentang Kami',            'kartu',      0, 'Kartu ringkas tentang direktorat.'],
    ['home', 'quick',           'Akses Cepat',             'tautan',     0, ''],
    ['home', 'services',        'Layanan Unggulan',        'kartu',      0, ''],
    ['home', 'peta',            'Peta Wilayah OBU',        'wilayah',    0, 'Terhubung ke bentuk peta SVG lewat slug wilayah.'],
    ['home', 'bandara-wilayah', 'Bandar Udara Wilayah',    'nama',       1, 'Daftar bandar udara pada satu wilayah OBU.'],
    ['home', 'rpjmn',           'Program RPJMN 2025–2029', 'rpjmn',      0, ''],
    ['home', 'area-rpjmn',      'Wilayah Program RPJMN',   'area',       1, 'Wilayah yang menyala di peta saat bagian ini aktif.'],
    ['home', 'berita-terbaru',  'Judul Seksi Berita',      'judul',      0, 'Judul seksi saja — isinya diambil dari kategori Berita.'],
    ['home', 'galeri-beranda',  'Judul Seksi Galeri',      'judul',      0, 'Judul seksi saja — fotonya dari Galeri yang ditandai "sorot".'],
    ['home', 'video',           'Video Profil',            'video',      0, 'Cukup satu konten.'],
    ['home', 'partners',        'Mitra Kerja',             'mitra',      0, ''],
    ['home', 'newsletter',      'Ajakan Newsletter',       'judul',      0, 'Judul seksi saja.'],

    /* ---- Profil ---- */
    ['profil', 'tentang',     'Tentang & Visi',    'tentang',  0, 'Cukup satu konten.'],
    ['profil', 'sejarah',     'Sejarah',           'linimasa', 0, ''],
    ['profil', 'visi',        'Judul Seksi Visi',  'judul',    0, 'Judul seksi saja — teks visinya ada di "Tentang & Visi".'],
    ['profil', 'misi',        'Misi',              'teks',     0, ''],
    ['profil', 'nilai',       'Nilai Organisasi',  'ikon',     0, ''],
    ['profil', 'sasaran',     'Sasaran Strategis', 'kartu',    0, ''],
    ['profil', 'tugas-pokok', 'Tugas Pokok',       'judul',    0, 'Isi tugas pokok ditulis pada kolom "Teks Seksi".'],
    ['profil', 'fungsi',      'Fungsi',            'teks',     0, ''],

    /* ---- Organisasi ---- */
    ['organisasi', 'intro',       'Pengantar',   'judul', 0, ''],
    ['organisasi', 'top',         'Node Puncak', 'judul', 0, ''],
    ['organisasi', 'unit',        'Unit Kerja',  'unit',  0, ''],
    ['organisasi', 'fungsi-unit', 'Fungsi Unit', 'baris', 1, 'Rincian fungsi yang tampil pada kartu unit.'],

    /* ---- Layanan ---- */
    ['layanan', 'daftar',           'Judul Seksi Daftar Layanan', 'judul',    0, ''],
    ['layanan', 'online',           'Layanan Daring',             'judul',    0, ''],
    ['layanan', 'cta',              'Kartu Ajakan',               'judul',    0, ''],
    ['layanan', 'persyaratan-umum', 'Persyaratan Umum',           'teks',     0, 'Dipakai layanan yang tidak punya persyaratan sendiri.'],
    ['layanan', 'alur',             'Alur Proses',                'tahapan',  0, 'Dipakai layanan yang tidak punya alur sendiri.'],
    ['layanan', 'faq',              'FAQ',                        'faq',      0, ''],
    ['layanan', 'persyaratan',      'Persyaratan Layanan',        'baris',    1, 'Persyaratan khusus satu layanan.'],
    ['layanan', 'tahap',            'Alur Layanan',               'tahap',    1, 'Alur khusus satu layanan.'],
    ['layanan', 'unduhan',          'Unduhan Formulir',           'unduhan',  1, ''],

    /* ---- Regulasi ---- */
    ['regulasi', 'ruang-lingkup', 'Ruang Lingkup Utama', 'baris', 1, ''],

    /* ---- Berita ---- */
    ['berita', 'pengumuman-ringkas', 'Pengumuman Ringkas', 'pengumuman', 0, 'Daftar pendek di beranda dan halaman Berita.'],

    /* ---- Galeri ---- */
    ['galeri', 'tabs',  'Judul Seksi Galeri', 'judul',        0, ''],
    ['galeri', 'album', 'Album',              'album',        0, ''],
    ['galeri', 'foto',  'Foto',               'foto',         0, 'Foto tanpa album tampil pada tab Foto.'],
    ['galeri', 'video', 'Video',              'video-galeri', 0, ''],

    /* ---- Kontak ---- */
    ['kontak', 'informasi', 'Informasi Kontak',     'kontak-info', 0, ''],
    ['kontak', 'subjek',    'Pilihan Subjek Pesan', 'nama',        0, 'Pilihan pada formulir kontak.'],

    /* ---- Tugas & Fungsi (halaman lama, kini dialihkan ke Profil) ---- */
    ['tugas-fungsi', 'tugas',  'Judul Seksi Tugas',  'judul', 0, ''],
    ['tugas-fungsi', 'fungsi', 'Judul Seksi Fungsi', 'judul', 0, ''],
];

/* Sub kategori yang dulu berupa tabel kategori tersendiri. */
foreach (db_all('SELECT slug, label, sort FROM news_categories ORDER BY sort, id') as $r) {
    $SUB[] = ['berita', $r['slug'], $r['label'], 'berita', 0, ''];
}
foreach (db_all('SELECT slug, label, sort FROM regulation_categories ORDER BY sort, id') as $r) {
    $SUB[] = ['regulasi', $r['slug'], $r['label'], 'regulasi', 0, ''];
}
foreach ([['sertifikasi', 'Sertifikasi'], ['perizinan', 'Perizinan'], ['layanan', 'Layanan']] as [$s, $l]) {
    $SUB[] = ['layanan', $s, $l, 'layanan', 0, 'Kategori filter pada halaman Layanan.'];
}

/* Judul seksi lama (tabel `sections`) menempel pada sub kategori dengan slug sama. */
$sectionMap = [];   // "kategori/slug" => baris sections
foreach (db_all('SELECT * FROM sections') as $r) {
    $sectionMap[$r['page'] . '/' . $r['section_key']] = $r;
}
/* Dua seksi berganti nama agar jelas bahwa isinya diambil dari kategori lain. */
foreach (['home/berita-terbaru' => 'home/news', 'home/galeri-beranda' => 'home/gallery'] as $baru => $lama) {
    if (isset($sectionMap[$lama])) {
        $sectionMap[$baru] = $sectionMap[$lama];
    }
}

$subId  = [];
$urutan = [];
foreach ($SUB as [$kat, $slug, $label, $blueprint, $isChild, $hint]) {
    if (!isset($katId[$kat])) {
        continue;
    }
    $sec  = $sectionMap[$kat . '/' . $slug] ?? [];
    $urutan[$kat] = ($urutan[$kat] ?? 0) + 10;
    $subId[$kat . '/' . $slug] = db_insert('subcategories', [
        'category_id' => $katId[$kat],
        'slug'        => $slug,
        'label'       => $label,
        'eyebrow'     => $sec['eyebrow']  ?? null,
        'title'       => $sec['title']    ?? null,
        'subtitle'    => $sec['subtitle'] ?? null,
        'body'        => $sec['body']     ?? null,
        'blueprint'   => $blueprint,
        'is_child'    => $isChild,
        'hint'        => $hint ?: null,
        'sort'        => $urutan[$kat],
        'is_active'   => isset($sec['is_active']) ? (int) $sec['is_active'] : 1,
    ]);
}
echo 'Sub kategori: ' . count($subId) . " baris\n";

/* Tabel `tf_overview` hanya berisi judul + teks seksi: dilipat ke sub kategori. */
$tf = db_one('SELECT * FROM tf_overview ORDER BY id LIMIT 1');
if ($tf) {
    foreach ([
        ['profil/tugas-pokok', $tf['tugas_eyebrow'],  $tf['tugas_title'],  $tf['tugas'],  null],
        ['profil/fungsi',      $tf['fungsi_eyebrow'], $tf['fungsi_title'], null,          $tf['fungsi_intro']],
        ['tugas-fungsi/tugas',  $tf['tugas_eyebrow'],  $tf['tugas_title'],  $tf['tugas'], null],
        ['tugas-fungsi/fungsi', $tf['fungsi_eyebrow'], $tf['fungsi_title'], null,         $tf['fungsi_intro']],
    ] as [$kunci, $eyebrow, $judul, $body, $subtitle]) {
        if (!isset($subId[$kunci])) {
            continue;
        }
        $isi = array_filter([
            'eyebrow'  => $eyebrow,
            'title'    => $judul,
            'body'     => $body,
            'subtitle' => $subtitle,
        ], fn($v) => $v !== null && $v !== '');
        if ($isi) {
            db_update('subcategories', $isi, $subId[$kunci]);
        }
    }
    echo "  · tf_overview dilipat ke judul sub kategori\n";
}

/* ------------------------------------------------------------
   4. Peta pemindahan konten
      kolom : kolom lama => kolom baru
      extra : kolom lama yang disimpan sebagai JSON
   ------------------------------------------------------------ */
$UMUM  = ['sort' => 'sort', 'is_active' => 'is_active'];
$VISUAL = ['image' => 'image', 'art_class' => 'art_class', 'icon' => 'icon'];

$PETA = [
    'hero_slides' => ['home/slider', 'sort, id',
        ['eyebrow' => 'eyebrow', 'title' => 'title', 'tagline' => 'body'] + $VISUAL + $UMUM,
        ['btn1_label', 'btn1_url', 'btn2_label', 'btn2_url']],

    'stats' => ['home/statistik', 'sort, id',
        ['label' => 'title'] + $UMUM,
        ['value', 'suffix']],

    'director_message' => ['home/sambutan', 'id',
        ['eyebrow' => 'eyebrow', 'title' => 'title', 'body' => 'body'] + $VISUAL,
        ['director_name', 'director_position', 'btn_label', 'btn_url']],

    'about_cards' => ['home/about', 'sort, id',
        ['icon' => 'icon', 'title' => 'title', 'description' => 'body', 'url' => 'url'] + $UMUM, []],

    'quick_links' => ['home/quick', 'sort, id',
        ['icon' => 'icon', 'label' => 'title', 'url' => 'url'] + $UMUM, []],

    'featured_services' => ['home/services', 'sort, id',
        ['icon' => 'icon', 'title' => 'title', 'description' => 'body', 'url' => 'url'] + $UMUM, []],

    'regions' => ['home/peta', 'sort, id',
        ['region_code' => 'slug', 'title' => 'title'] + $UMUM,
        ['numeral', 'hq', 'hq_short', 'coverage', 'coverage_short']],

    'rpjmn_programs' => ['home/rpjmn', 'sort, id',
        ['title' => 'title', 'eyebrow' => 'eyebrow', 'icon' => 'icon', 'summary' => 'body'] + $UMUM,
        ['focus', 'note']],

    'home_video' => ['home/video', 'id',
        ['eyebrow' => 'eyebrow', 'title' => 'title', 'video_url' => 'url', 'caption' => 'excerpt'] + $VISUAL, []],

    'partners' => ['home/partners', 'sort, id',
        ['name' => 'title', 'logo' => 'image', 'url' => 'url'] + $UMUM, []],

    /* `tugas_pokok` tidak ikut: teksnya kini menempel pada sub kategori
       profil/tugas-pokok, bukan pada kontennya. */
    'profil_about' => ['profil/tentang', 'id',
        ['eyebrow' => 'eyebrow', 'title' => 'title', 'body' => 'body'] + $VISUAL,
        ['visi']],

    'timeline_items' => ['profil/sejarah', 'sort, id',
        ['title' => 'title', 'description' => 'body'] + $UMUM,
        ['year']],

    'missions' => ['profil/misi', 'sort, id',
        ['content' => 'body'] + $UMUM, []],

    'value_cards' => ['profil/nilai', 'sort, id',
        ['icon' => 'icon', 'title' => 'title'] + $UMUM, []],

    'strategic_goals' => ['profil/sasaran', 'sort, id',
        ['icon' => 'icon', 'title' => 'title', 'description' => 'body'] + $UMUM, []],

    'tf_functions' => ['profil/fungsi', 'sort, id',
        ['icon' => 'icon', 'content' => 'body'] + $UMUM, []],

    'org_units' => ['organisasi/unit', 'sort, id',
        ['unit_key' => 'slug', 'icon' => 'icon', 'title' => 'title', 'description' => 'excerpt', 'tugas' => 'body'] + $UMUM,
        ['chips', 'branch_class', 'head_name', 'head_position', 'head_photo',
         'team_lead_name', 'team_lead_position', 'team_lead_photo']],

    'general_requirements' => ['layanan/persyaratan-umum', 'sort, id',
        ['content' => 'body'] + $UMUM, []],

    'process_steps' => ['layanan/alur', 'sort, id',
        ['title' => 'title', 'description' => 'body'] + $UMUM, []],

    'faqs' => ['layanan/faq', 'sort, id',
        ['question' => 'title', 'answer' => 'body'] + $UMUM, []],

    'announcements' => ['berita/pengumuman-ringkas', 'published_at DESC, sort, id',
        ['title' => 'title', 'published_at' => 'published_at', 'url' => 'url'] + $UMUM,
        ['category']],

    'gallery_albums' => ['galeri/album', 'sort, id',
        ['slug' => 'slug', 'title' => 'title', 'description' => 'body'] + $VISUAL + $UMUM, []],

    'gallery_videos' => ['galeri/video', 'sort, id',
        ['title' => 'title', 'video_url' => 'url'] + $VISUAL + $UMUM, []],

    'contact_info' => ['kontak/informasi', 'sort, id',
        ['icon' => 'icon', 'label' => 'title', 'value' => 'body'] + $UMUM, []],

    'contact_subjects' => ['kontak/subjek', 'sort, id',
        ['label' => 'title'] + $UMUM, []],
];

/** Susun satu baris konten dari baris tabel lama. */
function bangun(array $row, array $kolom, array $extraCols, int $catId, ?int $subCatId, int $sort): array
{
    $data = [
        'category_id'    => $catId,
        'subcategory_id' => $subCatId,
        'sort'           => $sort,
        'is_active'      => 1,
    ];
    foreach ($kolom as $lama => $baru) {
        if (!array_key_exists($lama, $row)) {
            continue;
        }
        $nilai = $row[$lama];
        if ($baru === 'sort' || $baru === 'is_active' || $baru === 'is_featured' || $baru === 'views') {
            $data[$baru] = (int) $nilai;
            continue;
        }
        $data[$baru] = ($nilai === '' ? null : $nilai);
    }
    $extra = [];
    foreach ($extraCols as $col) {
        if (isset($row[$col]) && $row[$col] !== '' && $row[$col] !== null) {
            $extra[$col] = $row[$col];
        }
    }
    $data['extra'] = $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;
    return $data;
}

$jumlah = 0;
$idLama = [];   // "tabel:id lama" => id baru

foreach ($PETA as $tabel => [$kunci, $order, $kolom, $extraCols]) {
    if (!isset($subId[$kunci])) {
        echo "  ! sub kategori $kunci tidak ada — $tabel dilewati\n";
        continue;
    }
    [$kat] = explode('/', $kunci);
    $i = 0;
    foreach (db_all("SELECT * FROM `$tabel` ORDER BY $order") as $row) {
        $data = bangun($row, $kolom, $extraCols, $katId[$kat], $subId[$kunci], (int) ($row['sort'] ?? $i));
        $idLama["$tabel:{$row['id']}"] = db_insert('contents', $data);
        $jumlah++;
        $i += 10;
    }
}

/* ---- Konten dengan sub kategori dinamis ---- */

/* Layanan → sub kategori sesuai kolom `category`. */
$unitSlug = [];
foreach (db_all('SELECT id, unit_key FROM org_units') as $u) {
    $unitSlug[(int) $u['id']] = $u['unit_key'];
}
foreach (db_all('SELECT * FROM services ORDER BY sort, id') as $row) {
    $kunci = 'layanan/' . $row['category'];
    if (!isset($subId[$kunci])) {
        $kunci = 'layanan/layanan';
    }
    $data = bangun(
        $row,
        ['slug' => 'slug', 'name' => 'title', 'description' => 'body', 'icon' => 'icon', 'sort' => 'sort', 'is_active' => 'is_active'],
        ['unit_pengelola', 'sifat', 'sifat_badge', 'klasifikasi', 'klasifikasi_badge', 'chip_label',
         'time_process', 'cost', 'validity', 'method', 'cta_label', 'cta_url'],
        $katId['layanan'],
        $subId[$kunci],
        (int) $row['sort']
    );
    /* Relasi ke unit kerja disimpan sebagai slug unit — tahan terhadap
       pergantian id saat migrasi. */
    if (!empty($row['org_unit_id']) && isset($unitSlug[(int) $row['org_unit_id']])) {
        $extra = json_decode($data['extra'] ?? '{}', true) ?: [];
        $extra['unit_slug'] = $unitSlug[(int) $row['org_unit_id']];
        $data['extra'] = json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    $idLama["services:{$row['id']}"] = db_insert('contents', $data);
    $jumlah++;
}

/* Regulasi → sub kategori sesuai kolom `category`. */
foreach (db_all('SELECT * FROM regulations ORDER BY year DESC, sort, id') as $row) {
    $kunci = 'regulasi/' . $row['category'];
    if (!isset($subId[$kunci])) {
        $kunci = 'regulasi/umum';
    }
    $data = bangun(
        $row,
        ['slug' => 'slug', 'title' => 'title', 'about' => 'body', 'file' => 'file',
         'source_url' => 'url', 'date_set' => 'published_at', 'sort' => 'sort', 'is_active' => 'is_active'],
        /* `number` menjadi subtitle dan `date_set` menjadi published_at,
           jadi keduanya tidak perlu disalin lagi ke extra. */
        ['year', 'status', 'date_published'],
        $katId['regulasi'],
        $subId[$kunci],
        (int) $row['sort']
    );
    $data['subtitle'] = $row['number'];
    $idLama["regulations:{$row['id']}"] = db_insert('contents', $data);
    $jumlah++;
}

/* Berita → sub kategori sesuai kolom `category`, tagar → tabel tags. */
$tagId = [];
function tag_id(string $label): int
{
    global $tagId;
    $slug = slugify($label);
    if (isset($tagId[$slug])) {
        return $tagId[$slug];
    }
    $ada = db_one('SELECT id FROM tags WHERE slug = ?', [$slug]);
    return $tagId[$slug] = $ada
        ? (int) $ada['id']
        : db_insert('tags', ['slug' => $slug, 'label' => $label, 'sort' => count($GLOBALS['tagId']) * 10]);
}

foreach (db_all('SELECT * FROM news ORDER BY published_at DESC, id DESC') as $row) {
    $kunci = 'berita/' . $row['category'];
    if (!isset($subId[$kunci])) {
        $kunci = 'berita/kegiatan';
    }
    $data = bangun(
        $row,
        ['slug' => 'slug', 'title' => 'title', 'excerpt' => 'excerpt', 'content' => 'body',
         'published_at' => 'published_at', 'is_featured' => 'is_featured', 'is_published' => 'is_active',
         'views' => 'views', 'sort' => 'sort'] + $VISUAL,
        ['author'],
        $katId['berita'],
        $subId[$kunci],
        (int) $row['sort']
    );
    $baru = db_insert('contents', $data);
    $idLama["news:{$row['id']}"] = $baru;
    $jumlah++;

    foreach (split_list($row['tags'] ?? '') as $label) {
        db_exec('INSERT IGNORE INTO content_tags (content_id, tag_id) VALUES (?, ?)', [$baru, tag_id($label)]);
    }
}

/* ------------------------------------------------------------
   5. Baris anak — parent_id menunjuk konten induk yang baru
      [tabel, kolom induk, tabel induk, sub kategori, kolom, extra]
   ------------------------------------------------------------ */
$ANAK = [
    ['region_airports',      'region_id',     'regions',         'home/bandara-wilayah',
        ['name' => 'title', 'sort' => 'sort'], []],
    ['rpjmn_areas',          'program_id',    'rpjmn_programs',  'home/area-rpjmn',
        ['region_code' => 'slug', 'airports' => 'body', 'sort' => 'sort'], []],
    ['org_unit_functions',   'unit_id',       'org_units',       'organisasi/fungsi-unit',
        ['content' => 'body', 'sort' => 'sort'], []],
    ['service_requirements', 'service_id',    'services',        'layanan/persyaratan',
        ['content' => 'body', 'sort' => 'sort'], []],
    ['service_steps',        'service_id',    'services',        'layanan/tahap',
        ['title' => 'title', 'description' => 'body', 'sort' => 'sort'], []],
    ['service_downloads',    'service_id',    'services',        'layanan/unduhan',
        ['title' => 'title', 'meta' => 'excerpt', 'file' => 'file', 'sort' => 'sort'], []],
    ['regulation_scopes',    'regulation_id', 'regulations',     'regulasi/ruang-lingkup',
        ['content' => 'body', 'sort' => 'sort'], []],
];

foreach ($ANAK as [$tabel, $fk, $induk, $kunci, $kolom, $extraCols]) {
    if (!isset($subId[$kunci])) {
        continue;
    }
    [$kat] = explode('/', $kunci);
    foreach (db_all("SELECT * FROM `$tabel` ORDER BY `$fk`, sort, id") as $row) {
        $parent = $idLama["$induk:{$row[$fk]}"] ?? null;
        if (!$parent) {
            continue;
        }
        $data = bangun($row, $kolom, $extraCols, $katId[$kat], $subId[$kunci], (int) ($row['sort'] ?? 0));
        $data['parent_id'] = $parent;
        db_insert('contents', $data);
        $jumlah++;
    }
}

/* Foto galeri: album_id boleh kosong (tampil pada tab Foto). */
foreach (db_all('SELECT * FROM gallery_photos ORDER BY sort, id') as $row) {
    $data = bangun(
        $row,
        ['title' => 'title', 'caption' => 'excerpt', 'show_on_home' => 'is_featured', 'sort' => 'sort', 'is_active' => 'is_active'] + $VISUAL,
        [],
        $katId['galeri'],
        $subId['galeri/foto'],
        (int) $row['sort']
    );
    $data['parent_id'] = $row['album_id'] ? ($idLama["gallery_albums:{$row['album_id']}"] ?? null) : null;
    db_insert('contents', $data);
    $jumlah++;
}

echo "Konten: $jumlah baris\n";
echo 'Tag: ' . (int) db_value('SELECT COUNT(*) FROM tags', [], 0) . ' baris, '
   . (int) db_value('SELECT COUNT(*) FROM content_tags', [], 0) . " relasi\n";
echo "Selesai. Tabel lama dibiarkan utuh.\n";
