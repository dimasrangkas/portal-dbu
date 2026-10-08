<?php
/* ============================================================
   Fungsi pembantu bersama (escaping, format, konten)
   ============================================================ */
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/content.php';

/** Escape HTML. */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Escape untuk atribut yang berisi URL. */
function eurl(?string $value): string
{
    $value = trim((string) $value);
    if ($value === '') {
        return '#';
    }
    return e($value);
}

/** Ubah teks polos multi-paragraf menjadi <p>. */
function paragraphs(?string $text, string $class = ''): string
{
    $text = trim((string) $text);
    if ($text === '') {
        return '';
    }
    $blocks = preg_split('/\R{2,}/', $text);
    $attr   = $class !== '' ? ' class="' . e($class) . '"' : '';
    $html   = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block === '') {
            continue;
        }
        $html .= '<p' . $attr . '>' . nl2br(e($block)) . '</p>';
    }
    return $html;
}

/** Pecah string berpemisah koma menjadi array bersih. */
function split_list(?string $value, string $sep = ','): array
{
    if (trim((string) $value) === '') {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode($sep, $value)), fn($v) => $v !== ''));
}

/** Tanggal panjang bahasa Indonesia: 14 Juli 2026. */
function tanggal_id(?string $date, bool $withDay = false): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    if ($ts === false) {
        return '';
    }
    $bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
              'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    $hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    $out   = (int) date('j', $ts) . ' ' . $bulan[(int) date('n', $ts)] . ' ' . date('Y', $ts);
    return $withDay ? $hari[(int) date('w', $ts)] . ', ' . $out : $out;
}

/** Bulan singkat 3 huruf: Jul. */
function bulan_singkat(?string $date): string
{
    if (!$date) {
        return '';
    }
    $ts = strtotime($date);
    $b  = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
    return $b[(int) date('n', $ts)] ?? '';
}

/** Buat slug URL-aman. */
function slugify(string $text): string
{
    $text = strtolower(trim($text));
    $text = str_replace(['&', '/'], [' dan ', '-'], $text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    return trim($text, '-') ?: 'item-' . substr(md5(microtime(true)), 0, 6);
}

/** Potong teks dengan elipsis. */
function excerpt(?string $text, int $limit = 140): string
{
    $text = trim(strip_tags((string) $text));
    if (mb_strlen($text) <= $limit) {
        return $text;
    }
    return mb_substr($text, 0, $limit) . '…';
}

/* ------------------------------------------------------------
   Pengambilan konten yang dipakai lintas halaman
   ------------------------------------------------------------ */

/** Seluruh pengaturan sebagai peta key => value (di-cache per request). */
function settings(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT setting_key, value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['value'];
        }
    }
    return $cache;
}

function setting(string $key, string $default = ''): string
{
    $all = settings();
    return isset($all[$key]) && $all[$key] !== null && $all[$key] !== '' ? $all[$key] : $default;
}

/**
 * Meta satu halaman — kini berasal dari tabel `categories`.
 * Bentuk kembaliannya dipertahankan agar partial lama tetap bekerja.
 */
function page_meta(string $key): array
{
    $kat = kategori($key);
    if (!$kat['id']) {
        return ['page_key' => $key, 'page_label' => $key,
                'meta_title' => setting('site_title'), 'meta_description' => setting('site_description'),
                'eyebrow' => null, 'heading' => null, 'subtitle' => null, 'breadcrumb' => null];
    }
    return $kat + ['page_key' => $kat['slug'], 'page_label' => $kat['label']];
}

/**
 * Judul seluruh seksi satu halaman, dipetakan berdasarkan slug sub kategori.
 * Dipakai halaman sebagai $s['nama-seksi']['title'].
 */
function sections(string $page): array
{
    return subkategori_semua($page);
}

function section(string $page, string $key): array
{
    return subkategori($page, $key);
}

/** Item menu untuk satu lokasi (hanya level atas; anak diambil terpisah). */
function menu_items(string $location): array
{
    static $cache = [];
    if (!isset($cache[$location])) {
        $rows = db_all(
            'SELECT * FROM menu_items WHERE location = ? AND is_active = 1 ORDER BY sort, id',
            [$location]
        );
        $tree = [];
        foreach ($rows as $row) {
            if ($row['parent_id'] === null) {
                $row['children'] = [];
                $tree[$row['id']] = $row;
            }
        }
        foreach ($rows as $row) {
            if ($row['parent_id'] !== null && isset($tree[$row['parent_id']])) {
                $tree[$row['parent_id']]['children'][] = $row;
            }
        }
        $cache[$location] = array_values($tree);
    }
    return $cache[$location];
}

/** Tautan sosial aktif. */
function social_links(): array
{
    $map = [
        'social_facebook'  => ['bi-facebook', 'Facebook'],
        'social_instagram' => ['bi-instagram', 'Instagram'],
        'social_twitter'   => ['bi-twitter-x', 'Twitter'],
        'social_youtube'   => ['bi-youtube', 'YouTube'],
    ];
    $out = [];
    foreach ($map as $key => [$icon, $label]) {
        $url = setting($key);
        if ($url !== '') {
            $out[] = ['url' => $url, 'icon' => $icon, 'label' => $label];
        }
    }
    return $out;
}

/**
 * Render blok visual: gambar unggahan bila ada, jika tidak gunakan
 * gradien placeholder `.art art-N` dengan ikon Bootstrap — sama seperti desain awal.
 */
function art_block(?string $image, ?string $artClass, ?string $icon, string $extraClass = '', string $alt = ''): string
{
    $artClass = $artClass ?: 'art-1';
    if ($image) {
        $src = asset_url($image);
        return '<div class="art ' . e($artClass) . ' ' . e($extraClass) . '" style="background-image:url(' . e($src) . ');'
             . 'background-size:cover;background-position:center;"><img src="' . e($src) . '" alt="' . e($alt)
             . '" style="width:100%;height:100%;object-fit:cover;"></div>';
    }
    $iconHtml = $icon ? '<div class="art-inner"><i class="bi ' . e($icon) . '"></i></div>' : '';
    return '<div class="art ' . e($artClass) . ' ' . e($extraClass) . '">' . $iconHtml . '</div>';
}

/** URL untuk berkas media: unggahan (uploads/) atau aset bawaan (frontend/). */
function asset_url(?string $path): string
{
    $path = ltrim((string) $path, '/');
    if ($path === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }
    if (str_starts_with($path, 'media/') || str_starts_with($path, 'uploads/')) {
        return UPLOAD_URL . '/' . preg_replace('#^uploads/#', '', $path);
    }
    return FRONTEND_URL . '/' . $path;
}

/** Ubah URL YouTube/Vimeo menjadi URL sematan. */
function embed_url(?string $url): string
{
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    if (preg_match('#youtube\.com/watch\?v=([\w-]+)#i', $url, $m) ||
        preg_match('#youtu\.be/([\w-]+)#i', $url, $m)) {
        return 'https://www.youtube.com/embed/' . $m[1];
    }
    if (preg_match('#vimeo\.com/(\d+)#i', $url, $m)) {
        return 'https://player.vimeo.com/video/' . $m[1];
    }
    return $url;
}

/**
 * Ubah koordinat ARP bentuk derajat-menit-detik menjadi desimal.
 * Contoh masukan: 03° 17' 24.36" LS 102° 54' 50.08" BT
 * Arah diterima dalam istilah Indonesia (LS/LU/BT/BB) maupun Inggris (S/N/E/W).
 * Mengembalikan ['lat' => float, 'lng' => float] atau null bila tidak terbaca.
 */
function arp_to_latlng(?string $arp): ?array
{
    $arp = trim((string) $arp);
    if ($arp === '') {
        return null;
    }
    /* Sebagian data memakai koma sebagai tanda desimal (31,62") dan sebagai pemisah. */
    $arp   = preg_replace('/(\d),(\d)/', '$1.$2', $arp);
    $angka = "(\d+(?:\.\d+)?)";
    $sela  = '[\s,]*';
    $pola  = '#' . $angka . $sela . '°' . $sela . $angka . $sela . "'" . $sela . '(?:' . $angka . $sela . '"?' . $sela . ')?(LS|LU|S|N)'
           . $sela . $angka . $sela . '°' . $sela . $angka . $sela . "'" . $sela . '(?:' . $angka . $sela . '"?' . $sela . ')?(BT|BB|E|W)#i';
    if (!preg_match($pola, $arp, $m)) {
        return null;
    }
    $lat = (float) $m[1] + (float) $m[2] / 60 + (float) ($m[3] ?: 0) / 3600;
    $lng = (float) $m[5] + (float) $m[6] / 60 + (float) ($m[7] ?: 0) / 3600;

    $utara = in_array(strtoupper($m[4]), ['LU', 'N'], true);
    $timur = in_array(strtoupper($m[8]), ['BT', 'E'], true);

    /* Di luar rentang wilayah Indonesia berarti pembacaan meleset. */
    if ($lat > 90 || $lng > 180) {
        return null;
    }
    return ['lat' => $utara ? $lat : -$lat, 'lng' => $timur ? $lng : -$lng];
}

/* ------------------------------------------------------------
   Data rinci satu bandar udara — tab pada halaman detail.
   Tabelnya dibuat oleh database/migrate-detail-bandara.sql.
   ------------------------------------------------------------ */

/* Sub-tab kedua tab fasilitas. Urutan larik = urutan tampil pada sidebar;
   yang paling sering dipakai diletakkan di atas. Daftar ini disusun dari
   seluruh kategori yang muncul pada data resmi 608 bandara, jadi tidak ada
   kategori yang jatuh ke luar daftar — lihat database/import-bandara-json.php
   yang memakai siput yang sama. */

/** Sub-tab tab "Fasilitas Udara". */
const BANDARA_FASILITAS_UDARA = [
    'runway'                   => 'Runway',
    'runway-turn-pads'         => 'Runway Turn Pads',
    'taxiway'                  => 'Taxiway',
    'taxiway-paralel'          => 'Taxiway Paralel',
    'apron'                    => 'Apron',
    'runway-strip'             => 'Runway Strip',
    'end-runway-strip'         => 'End Runway Strip',
    'stop-way'                 => 'Stop Way',
    'runway-end-safety-area'   => 'Runway End Safety Area (RESA)',
    'clearway'                 => 'Clearway',
    'helipad'                  => 'Helipad',
    'hanggar'                  => 'Hanggar',
];

/** Sub-tab tab "Fasilitas Darat". */
const BANDARA_FASILITAS_DARAT = [
    'gedung-terminal'                            => 'Gedung Terminal',
    'terminal-vip'                               => 'Terminal VIP',
    'gedung-administrasi'                        => 'Gedung Administrasi',
    'gedung-operasional'                         => 'Gedung Operasional',
    'gedung-kargo'                               => 'Gedung Kargo',
    'gedung-pkp-pk'                              => 'Gedung PKP-PK',
    'gedung-keamanan'                            => 'Gedung Keamanan',
    'gedung-powerhouse'                          => 'Gedung Powerhouse',
    'gedung-workshop-a2b'                        => 'Gedung Workshop A2B',
    'gedung-airnav'                              => 'Gedung Airnav',
    'gedung-bangland'                            => 'Gedung Bangland',
    'gedung-alat-besar'                          => 'Gedung Alat Besar',
    'gedung-teknik-dan-dan-operasi'              => 'Gedung Teknik Dan Operasi',
    'gedung-foc'                                 => 'Gedung FOC',
    'gedung-rapat'                               => 'Gedung Rapat',
    'gedung-panel-surya-20-kwp'                  => 'Gedung Panel Surya 20 KWp',
    'bangunan-panel'                             => 'Bangunan Panel',
    'rumah-dinas-tipe-36'                        => 'Rumah Dinas Tipe 36',
    'rumah-dinas-tipe-50'                        => 'Rumah Dinas Tipe 50',
    'rumah-dinas-tipe-72'                        => 'Rumah Dinas Tipe 72',
    'rumah-dinas-tipe-120'                       => 'Rumah Dinas Tipe 120',
    'rumah-negara'                               => 'Rumah Negara',
    'mess-pegawai-tipe-1'                        => 'Mess Pegawai Tipe 1',
    'mushola'                                    => 'Mushola',
    'gym-center'                                 => 'Gym Center',
    'lahan-parkir-gedung-terminal-dan-landscape' => 'Lahan Parkir & Landscape',
    'jalan-lingkungan'                           => 'Jalan Lingkungan',
    'jalan-inspeksi'                             => 'Jalan Inspeksi',
    'jalan-khusus-kompleks'                      => 'Jalan Khusus Kompleks',
    'gapura-bandara'                             => 'Gapura Bandara',
    'pagar-sisi-darat'                           => 'Pagar Sisi Darat',
    'taman-sisi-darat'                           => 'Taman Sisi Darat',
    'taman-sisi-udara'                           => 'Taman Sisi Udara',
    'talut-penahan-tanah'                        => 'Talut Penahan Tanah',
    'saluran-terbuka'                            => 'Saluran Terbuka',
    'saluran-tertutup'                           => 'Saluran Tertutup',
    'air-bersih'                                 => 'Air Bersih',
    'bangunan-penampung-air-baku'                => 'Bangunan Penampung Air Baku',
    'water-treatment-plant'                      => 'Water Treatment Plant',
    'sewage-treatment-plant'                     => 'Sewage Treatment Plant',
];

/* Zona Kawasan Keselamatan Operasi Penerbangan pada lapisan peta halaman
   detail. Urutan larik = dari kawasan paling dalam ke paling luar; itulah
   yang dipakai database/import-peta-kkop.php untuk menyusun urutan gambar
   (terluar lebih dulu) dan frontend/js/bandara-detail.js untuk urutan
   keterangan warnanya. Nilai kanan harus sama persis dengan medan "ZONA"
   pada GeoJSON resmi — pengimpor mencocokkannya apa adanya dan melaporkan
   zona yang tidak ada di daftar ini. */
const BANDARA_KKOP_ZONA = [
    'utama'            => 'PERMUKAAN UTAMA',
    'bahaya'           => 'KAWASAN KEMUNGKINAN BAHAYA KECELAKAAN',
    'ancangan'         => 'KAWASAN ANCANGAN PENDARATAN DAN LEPAS LANDAS',
    'transisi'         => 'KAWASAN DI BAWAH PERMUKAAN TRANSISI',
    'horizontal-dalam' => 'KAWASAN DI BAWAH PERMUKAAN HORIZONTAL DALAM',
    'kerucut'          => 'KAWASAN DI BAWAH PERMUKAAN KERUCUT',
    'horizontal-luar'  => 'KAWASAN DI BAWAH PERMUKAAN HORIZONTAL LUAR',
];

/** Nama pendek tiap zona KKOP, untuk keterangan warna yang sempit di peta. */
const BANDARA_KKOP_ZONA_PENDEK = [
    'utama'            => 'Permukaan Utama',
    'bahaya'           => 'Bahaya Kecelakaan',
    'ancangan'         => 'Ancangan Pendaratan',
    'transisi'         => 'Transisi',
    'horizontal-dalam' => 'Horizontal Dalam',
    'kerucut'          => 'Kerucut',
    'horizontal-luar'  => 'Horizontal Luar',
];

/* Peralatan bandar udara. Tiap kunci = satu tab tersendiri pada halaman
   detail, sejajar dengan "Fasilitas Udara" dan "Fasilitas Darat". */
const BANDARA_PERALATAN = [
    'kendaraan-utama'       => 'Kendaraan Utama',
    'kendaraan-pendukung'   => 'Kendaraan Pendukung',
    'catu-daya'             => 'Catu Daya',
    'alat-bantu-pendaratan' => 'Alat Bantu Pendaratan',
    'lampu-pendaratan'      => 'Lampu Pendaratan',
];

/* Medan yang ditampilkan pada kartu tiap kategori peralatan, beserta
   labelnya. Urutan larik = urutan baris pada kartu; medan yang nilainya
   kosong dilewati. Baris "Jumlah" tidak perlu ditulis di sini — halaman
   detail selalu menambahkannya paling bawah. */
const BANDARA_PERALATAN_MEDAN = [
    'kendaraan-utama'       => ['kategori' => 'Kat. Kendaraan', 'nama' => 'Nama', 'call_sign' => 'Call Sign'],
    'kendaraan-pendukung'   => ['kategori' => 'Kat. Kendaraan', 'nama' => 'Nama', 'call_sign' => 'Call Sign'],
    'catu-daya'             => ['kategori' => 'Kat. Peralatan', 'nama' => 'Nama', 'kapasitas' => 'Kapasitas'],
    'alat-bantu-pendaratan' => ['kategori' => 'Kat. Peralatan', 'nama' => 'Nama', 'lokasi' => 'Lokasi'],
    'lampu-pendaratan'      => ['kategori' => 'Kat. Peralatan', 'nama' => 'Nama', 'lokasi' => 'Lokasi'],
];

/**
 * Baris satu tabel anak bandara, terurut.
 * Tabel dibatasi daftar putih karena namanya disisipkan ke SQL.
 */
function bandara_anak(int $bandaraId, string $tabel): array
{
    $izin = ['bandara_rute', 'bandara_fasilitas_udara', 'bandara_fasilitas_darat',
             'bandara_fasilitas_umum', 'bandara_galeri'];
    if (!in_array($tabel, $izin, true) || $bandaraId <= 0) {
        return [];
    }
    return db_all("SELECT * FROM `$tabel` WHERE bandara_id = ? ORDER BY sort, id", [$bandaraId]);
}

/**
 * Baris satu tabel anak, dikelompokkan berdasarkan sebuah kolom —
 * dipakai tab yang punya sub-tab (kategori) atau dua jenis daftar.
 * Hasil: ['runway' => [baris...], 'taxiway' => [...]]
 */
function bandara_anak_kelompok(int $bandaraId, string $tabel, string $kolom): array
{
    $out = [];
    foreach (bandara_anak($bandaraId, $tabel) as $row) {
        $out[(string) ($row[$kolom] ?? '')][] = $row;
    }
    return $out;
}

/**
 * Data dummy tab peralatan — sengaja BUKAN dari tabel basis data.
 *
 * Portal ini belum punya sumber data resmi untuk peralatan bandar udara.
 * Daftar di bawah dipakai apa adanya untuk SELURUH bandara: angkanya sama
 * persis di mana pun, atas permintaan pemilik proyek. Pangkalnya tangkapan
 * layar AEMS (Soekarno-Hatta), ditambah peralatan baku lain supaya kelima
 * tabnya terisi. Seluruh situs sudah memasang penafian data contoh.
 *
 * Catatan bila kelak ingin dibedakan per bandara: kolom `pkp_pk` (kategori
 * PKP-PK) adalah patokan yang paling masuk akal — bukan `kelas`, karena
 * Soekarno-Hatta, Ngurah Rai, dan Kualanamu semuanya tercatat "Non Kelas".
 *
 * Saat API tersedia, ganti isi fungsi ini dengan pemanggilan endpoint
 * tersebut; bentuk kembaliannya (dikelompokkan per kunci BANDARA_PERALATAN,
 * tiap baris berisi medan sesuai BANDARA_PERALATAN_MEDAN ditambah 'jumlah')
 * dipertahankan supaya frontend/pages/bandara-detail.php tidak perlu diubah.
 */
function bandara_peralatan_dummy(array $bandara): array
{
    return [
        /* Kendaraan PKP-PK utama dan pendukung dipisah menurut
           SKEP/94/IV/1998: yang utama memadamkan, yang pendukung
           mengangkut, mengevakuasi, dan memimpin di lapangan. */
        'kendaraan-utama' => [
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Foam Tender Type IV',        'call_sign' => 'FT-1ER',  'jumlah' => 1],
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Foam Tender Type IV',        'call_sign' => 'FT-2ER',  'jumlah' => 1],
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Foam Tender Type V',         'call_sign' => 'FT-3ER',  'jumlah' => 1],
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Rapid Intervention Vehicle', 'call_sign' => 'RIV-1ER', 'jumlah' => 1],
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Rapid Intervention Vehicle', 'call_sign' => 'RIV-2ER', 'jumlah' => 1],
            ['kategori' => 'Kendaraan Utama', 'nama' => 'Nurse Tender',               'call_sign' => 'NT-1ER',  'jumlah' => 1],
        ],
        'kendaraan-pendukung' => [
            ['kategori' => 'Kendaraan Pendukung', 'nama' => 'Commando Car',  'call_sign' => 'CC-4ER',  'jumlah' => 1],
            ['kategori' => 'Kendaraan Pendukung', 'nama' => 'Ambulans',      'call_sign' => 'AMB-1ER', 'jumlah' => 1],
            ['kategori' => 'Kendaraan Pendukung', 'nama' => 'Rescue Boat',   'call_sign' => 'RB-1ER',  'jumlah' => 1],
            ['kategori' => 'Kendaraan Pendukung', 'nama' => 'Utility Truck', 'call_sign' => 'UT-1ER',  'jumlah' => 1],
        ],
        'catu-daya' => [
            ['kategori' => 'Catu Daya', 'nama' => 'Daya Langganan PLN',                 'kapasitas' => '555 kVA', 'jumlah' => 1],
            ['kategori' => 'Catu Daya', 'nama' => 'Generator Set',                      'kapasitas' => '500 kVA', 'jumlah' => 2],
            ['kategori' => 'Catu Daya', 'nama' => 'Transformator',                      'kapasitas' => '630 kVA', 'jumlah' => 1],
            ['kategori' => 'Catu Daya', 'nama' => 'Uninterruptible Power Supply (UPS)', 'kapasitas' => '20 kVA',  'jumlah' => 1],
        ],
        'alat-bantu-pendaratan' => [
            ['kategori' => 'Alat Bantu Pendaratan', 'nama' => 'Precision Approach Path Indicator (PAPI)', 'lokasi' => 'Kedua ujung runway', 'jumlah' => 2],
            ['kategori' => 'Alat Bantu Pendaratan', 'nama' => 'VOR/DME',                                  'lokasi' => 'Sisi utara runway',  'jumlah' => 1],
            ['kategori' => 'Alat Bantu Pendaratan', 'nama' => 'Instrument Landing System (ILS) Localizer', 'lokasi' => 'Ujung runway',       'jumlah' => 1],
            ['kategori' => 'Alat Bantu Pendaratan', 'nama' => 'Instrument Landing System (ILS) Glide Path', 'lokasi' => 'Sisi threshold',    'jumlah' => 1],
        ],
        'lampu-pendaratan' => [
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Rotating Beacon',                          'lokasi' => 'Gedung operasi',      'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Runway Edge Light',                        'lokasi' => 'Sisi runway',         'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Threshold Light',                          'lokasi' => 'Kedua ujung runway',  'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Runway Threshold Identification Light',    'lokasi' => 'Kedua ujung runway',  'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Taxiway Edge Light',                       'lokasi' => 'Sisi taxiway',        'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Approach Light (PALS)',                    'lokasi' => 'Ancangan pendaratan', 'jumlah' => 1],
            ['kategori' => 'Lampu Pendaratan', 'nama' => 'Sequence Flashing Light',                  'lokasi' => 'Ancangan pendaratan', 'jumlah' => 1],
        ],
    ];
}

/**
 * Bandar udara terdekat beserta jaraknya dalam kilometer.
 *
 * Tidak ada tabel khusus: jarak dihitung dari kolom `lokasi_arp` yang
 * sudah dimiliki setiap bandara (rumus haversine).
 */
function bandara_terdekat(array $bandara, int $limit = 10): array
{
    $asal = arp_to_latlng($bandara['lokasi_arp'] ?? '');
    if (!$asal) {
        return [];
    }

    $rows = db_all('SELECT id, nama, kode_iata, kode_icao, lokasi_arp FROM bandaras WHERE id <> ?',
                   [(int) $bandara['id']]);

    $out = [];
    foreach ($rows as $row) {
        $titik = arp_to_latlng($row['lokasi_arp']);
        if (!$titik) {
            continue;
        }
        $row['jarak'] = haversine_km($asal['lat'], $asal['lng'], $titik['lat'], $titik['lng']);
        $out[] = $row;
    }

    usort($out, static fn($a, $b) => $a['jarak'] <=> $b['jarak']);
    return array_slice($out, 0, $limit);
}

/**
 * Jalur berkas lapisan peta (KKOP dan cakupan) satu bandar udara,
 * relatif terhadap folder frontend/ — atau null bila belum ada.
 *
 * Berkasnya dibuat database/import-peta-kkop.php dari GeoJSON resmi dan
 * dinamai menurut kode ICAO. Baru lima bandara yang datanya tersedia, jadi
 * halaman detail memakai kembalian null untuk memberi tahu pengunjung bahwa
 * lapisannya belum ada — bukan menyembunyikan tombolnya.
 */
function bandara_peta_lapis(?string $icao): ?string
{
    $icao = strtoupper(trim((string) $icao));
    /* Kode ICAO ikut menyusun nama berkas; bentuknya dibatasi supaya isi
       kolom yang aneh tidak bisa menunjuk ke luar folder data/peta. */
    if (!preg_match('/^[A-Z0-9]{4}$/', $icao)) {
        return null;
    }
    $rel = 'data/peta/' . $icao . '.json';
    return is_file(FRONTEND_PATH . '/' . $rel) ? $rel : null;
}

/** Jarak lingkaran besar antara dua titik, dalam kilometer. */
function haversine_km(float $lat1, float $lng1, float $lat2, float $lng2): float
{
    $r = 6371.0;
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    $a = sin($dLat / 2) ** 2
       + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;
    return $r * 2 * atan2(sqrt($a), sqrt(1 - $a));
}

/** Angka gaya Indonesia: 1.600,00 — dipakai luas dan dimensi fasilitas. */
function angka_id(?float $angka, int $desimal = 2): string
{
    return $angka === null ? '—' : number_format($angka, $desimal, ',', '.');
}

/**
 * Ekstensi yang diizinkan untuk sebuah jenis unggahan.
 *
 * UPLOAD_ALLOWED_IMAGE dan UPLOAD_ALLOWED_FILE didefinisikan di
 * shared/config.php. Daftar video ditaruh di sini karena config.php tidak
 * dilacak git — server lama tidak akan memilikinya.
 */
function upload_allowed_ext(string $type): array
{
    $video = defined('UPLOAD_ALLOWED_VIDEO')
        ? UPLOAD_ALLOWED_VIDEO
        : ['mp4', 'webm', 'ogv', 'mov', 'm4v'];

    return match ($type) {
        'image' => UPLOAD_ALLOWED_IMAGE,
        'video' => $video,
        default => array_merge(UPLOAD_ALLOWED_FILE, UPLOAD_ALLOWED_IMAGE, $video),
    };
}

/**
 * Batas unggahan yang benar-benar berlaku untuk sebuah jenis berkas.
 *
 * Dua lapis batas harus dihormati sekaligus:
 *
 *   1. Batas yang diinginkan aplikasi — berbeda per jenis: foto 20 MB,
 *      video 100 MB, dokumen 20 MB. Dapat ditimpa lewat shared/config.php
 *      dengan mendefinisikan UPLOAD_MAX_IMAGE_BYTES, UPLOAD_MAX_VIDEO_BYTES,
 *      atau UPLOAD_MAX_FILE_BYTES.
 *   2. Batas PHP — upload_max_filesize menolak berkasnya, sedangkan
 *      post_max_size mengosongkan seluruh $_POST tanpa galat apa pun.
 *
 * Menyebut angka aplikasi saja membuat CMS menjanjikan ukuran yang sebetulnya
 * ditolak server, dan gejalanya menyamar sebagai sesi kedaluwarsa.
 */
function upload_limit_bytes(string $type = 'file'): int
{
    $MB = 1048576;

    $diinginkan = [
        'image' => defined('UPLOAD_MAX_IMAGE_BYTES') ? UPLOAD_MAX_IMAGE_BYTES :  20 * $MB,
        'video' => defined('UPLOAD_MAX_VIDEO_BYTES') ? UPLOAD_MAX_VIDEO_BYTES : 100 * $MB,
        'file'  => defined('UPLOAD_MAX_FILE_BYTES')  ? UPLOAD_MAX_FILE_BYTES  :  20 * $MB,
    ];

    $limits = [$diinginkan[$type] ?? $diinginkan['file']];

    foreach (['upload_max_filesize', 'post_max_size'] as $key) {
        $raw = trim((string) ini_get($key));
        if ($raw === '') {
            continue;
        }
        $unit = strtolower(substr($raw, -1));
        $num  = (int) $raw;
        $mul  = ['k' => 1024, 'm' => $MB, 'g' => 1073741824][$unit] ?? 1;
        if ($num > 0) {
            $limits[] = $num * $mul;
        }
    }

    return min($limits);
}

/**
 * Perkecil gambar yang terlalu besar setelah diunggah.
 *
 * Kamera dan ponsel menghasilkan foto belasan megapiksel — berkas 3648x5472
 * berukuran beberapa MB dan disajikan apa adanya ke setiap pengunjung. Untuk
 * situs ini sisi terpanjang 2400 px sudah lebih dari cukup, dan hasilnya
 * biasanya turun ke ratusan KB.
 *
 * Mengembalikan true bila berkas benar-benar ditulis ulang.
 *
 * Dilewati dengan aman (mengembalikan false, berkas asli tetap utuh) bila:
 *   - GD tidak tersedia, atau formatnya bukan JPEG/PNG/WebP;
 *   - gambarnya sudah cukup kecil;
 *   - memori yang tersedia diperkirakan tidak cukup untuk mendekodenya.
 *     GD memuat seluruh bitmap ke memori (~4 byte per piksel), jadi menebak
 *     lebih dulu jauh lebih baik daripada proses mati di tengah jalan.
 */
function shrink_uploaded_image(string $path, int $maxDim = 0, int $quality = 82): bool
{
    if (!function_exists('imagecreatefromjpeg')) {
        return false;
    }
    $maxDim = $maxDim > 0 ? $maxDim
        : (defined('UPLOAD_IMAGE_MAX_DIM') ? UPLOAD_IMAGE_MAX_DIM : 2400);

    $info = @getimagesize($path);
    if ($info === false) {
        return false;
    }
    [$w, $h] = $info;
    $type    = $info[2];

    $muat = [
        IMAGETYPE_JPEG => ['imagecreatefromjpeg', 'imagejpeg'],
        IMAGETYPE_PNG  => ['imagecreatefrompng',  'imagepng'],
        IMAGETYPE_WEBP => ['imagecreatefromwebp', 'imagewebp'],
    ];
    if (!isset($muat[$type]) || !function_exists($muat[$type][0])) {
        return false;
    }
    if ($w <= $maxDim && $h <= $maxDim) {
        return false;
    }

    // Perkiraan memori: bitmap sumber + bitmap tujuan, 4 byte per piksel,
    // ditambah 25% kelonggaran untuk overhead GD.
    $rasio = $maxDim / max($w, $h);
    $nw    = max(1, (int) round($w * $rasio));
    $nh    = max(1, (int) round($h * $rasio));
    $butuh = (int) ((($w * $h) + ($nw * $nh)) * 4 * 1.25);

    $batas = trim((string) ini_get('memory_limit'));
    if ($batas !== '' && $batas !== '-1') {
        $unit    = strtolower(substr($batas, -1));
        $mul     = ['k' => 1024, 'm' => 1048576, 'g' => 1073741824][$unit] ?? 1;
        $tersedia = ((int) $batas * $mul) - memory_get_usage(true);
        if ($butuh > $tersedia) {
            return false;
        }
    }

    [$buka, $simpan] = $muat[$type];
    $src = @$buka($path);
    if (!$src) {
        return false;
    }

    // Foto dari ponsel/kamera sering mengandalkan tag orientasi EXIF; tanpa
    // diputar lebih dulu, hasil resize akan tampil miring.
    if ($type === IMAGETYPE_JPEG && function_exists('exif_read_data')) {
        $exif = @exif_read_data($path);
        $deg  = match ($exif['Orientation'] ?? 1) { 3 => 180, 6 => -90, 8 => 90, default => 0 };
        if ($deg !== 0) {
            $diputar = @imagerotate($src, $deg, 0);
            if ($diputar) {
                imagedestroy($src);
                $src = $diputar;
                $w   = imagesx($src);
                $h   = imagesy($src);
                $rasio = $maxDim / max($w, $h);
                $nw  = max(1, (int) round($w * $rasio));
                $nh  = max(1, (int) round($h * $rasio));
            }
        }
    }

    $dst = imagecreatetruecolor($nw, $nh);
    if ($type === IMAGETYPE_PNG || $type === IMAGETYPE_WEBP) {
        imagealphablending($dst, false);
        imagesavealpha($dst, true);
    }
    imagecopyresampled($dst, $src, 0, 0, 0, 0, $nw, $nh, $w, $h);

    $tmp = $path . '.tmp';
    $ok  = $type === IMAGETYPE_PNG
        ? @$simpan($dst, $tmp)              // PNG: kompresi bawaan
        : @$simpan($dst, $tmp, $quality);

    imagedestroy($src);
    imagedestroy($dst);

    if (!$ok || !is_file($tmp)) {
        @unlink($tmp);
        return false;
    }
    // Hanya pakai hasilnya bila memang lebih kecil.
    if (filesize($tmp) >= filesize($path)) {
        @unlink($tmp);
        return false;
    }
    return @rename($tmp, $path);
}

/**
 * Tambahkan penanda versi pada URL aset statis.
 *
 * Aset dilayani dengan Cache-Control max-age 30 hari. Tanpa penanda versi,
 * perubahan CSS/JS tidak akan sampai ke pengunjung lama selama sebulan penuh.
 * Nilai penandanya memakai waktu ubah berkas, sehingga URL berganti sendiri
 * setiap kali berkasnya benar-benar berubah — dan tetap stabil bila tidak.
 *
 * $rel adalah path relatif terhadap $basePath (mis. "css/style.css").
 */
function asset_version(string $url, string $basePath, string $rel): string
{
    $berkas = rtrim($basePath, '/') . '/' . ltrim($rel, '/');
    $waktu  = @filemtime($berkas);
    return $waktu ? $url . '?v=' . $waktu : $url;
}
