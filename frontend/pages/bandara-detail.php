<?php
/* Detail satu bandar udara.
   Sumber data: tabel `bandaras` beserta tabel anaknya — lihat
   database/migrate-detail-bandara.sql dan pembantu bandara_*()
   pada shared/functions.php.

   Susunan halaman mengikuti hubud.kemenhub.go.id/bandara/<id>:
   hero biru (ringkasan + carousel foto), kartu "Informasi Bandar Udara"
   berdampingan dengan peta dan spesifikasi teknis, lalu deretan tab. */
require_once dirname(__DIR__) . '/bootstrap.php';

$id      = (int) ($_GET['id'] ?? 0);
$bandara = $id ? db_one('SELECT * FROM bandaras WHERE id = ?', [$id]) : null;

if (!$bandara) {
    http_response_code(404);
    page_start('informasi-publik', [
        'title'       => 'Bandar udara tidak ditemukan',
        'breadcrumbs' => [['label' => 'Daftar Bandar Udara', 'url' => 'pages/informasi-publik'], ['label' => 'Tidak ditemukan']],
    ]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Bandar udara tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/informasi-publik')) . '" class="btn btn-primary btn-sm">Kembali ke Daftar Bandar Udara</a>'
       . '</div></section>';
    partial('footer');
    exit;
}

$lokasi = array_filter([
    $bandara['kelurahan_desa'] ? 'Kel./Desa ' . $bandara['kelurahan_desa'] : '',
    $bandara['kecamatan']      ? 'Kec. ' . $bandara['kecamatan'] : '',
    $bandara['kabupaten_kota'],
    $bandara['provinsi'],
], 'strlen');

/* Baris "Lokasi" pada kartu ringkasan: dari yang terkecil ke terbesar,
   tanpa awalan "Kel./Desa". */
$lokasiRingkas = array_filter([
    $bandara['kelurahan_desa'],
    $bandara['kecamatan'],
    $bandara['kabupaten_kota'],
    $bandara['provinsi'],
], 'strlen');

/* Data tiap tab. Yang berkategori dikelompokkan agar sub-tabnya
   tinggal dirunut dari daftar tetap di shared/functions.php. */
$terdekat        = bandara_terdekat($bandara);
$rute            = bandara_anak($id, 'bandara_rute');
$fasUdara        = bandara_anak_kelompok($id, 'bandara_fasilitas_udara', 'kategori');
$fasDarat        = bandara_anak_kelompok($id, 'bandara_fasilitas_darat', 'kategori');
$fasUmumSemua    = bandara_anak_kelompok($id, 'bandara_fasilitas_umum', 'jenis');
/* Dummy sampai endpoint/API resminya siap — lihat bandara_peralatan_dummy(). */
$peralatan       = bandara_peralatan_dummy($bandara);
$fasUmum         = $fasUmumSemua['umum'] ?? [];
$transportasi    = $fasUmumSemua['transportasi'] ?? [];
/* Baris galeri boleh dibuat operator sebelum fotonya diunggah; yang belum
   punya berkas tidak ada gunanya bagi pengunjung, jadi disaring di sini. */
$galeri          = array_values(array_filter(
    bandara_anak($id, 'bandara_galeri'),
    static fn($row) => trim((string) $row['image']) !== ''
));
$koordinat       = arp_to_latlng($bandara['lokasi_arp']);

/* Kolom cuaca dan microsite disediakan migrate-bandara-cuaca-microsite.sql
   dan diisi operator lewat menu admin — tidak ada di berkas JSON resmi.
   Selama kosong, slotnya tetap tampil bertuliskan "—". */
$cuacaAda = $bandara['cuaca_suhu'] !== null || trim((string) $bandara['cuaca_kondisi']) !== '';

/* Tab tanpa isi tetap ditampilkan agar susunannya konsisten antar bandara,
   tetapi jumlahnya dipakai untuk menandai tab yang masih kosong. */
$jumlahFasUdara = array_sum(array_map('count', $fasUdara));
$jumlahFasDarat = array_sum(array_map('count', $fasDarat));

/* ------------------------------------------------------------
   Bekal peta. Tiga peta memakai data yang sama-sama berasal dari
   kolom `lokasi_arp`, jadi disiapkan sekali lalu dikirim ke
   frontend/js/bandara-detail.js sebagai satu blok JSON.
   ------------------------------------------------------------ */

/* Koordinat bandara tujuan rute dicocokkan lewat nama: tabel `bandara_rute`
   hanya menyimpan nama tujuan, bukan id-nya. Tujuan yang tak ditemukan tetap
   tampil pada daftar, hanya tidak menerima garis rute di peta. */
$titikRute = [];
if ($rute) {
    foreach (db_all('SELECT id, nama, lokasi_arp FROM bandaras') as $row) {
        $kunci = mb_strtoupper(trim((string) $row['nama']));
        if ($kunci !== '' && !isset($titikRute[$kunci])) {
            $titikRute[$kunci] = $row;
        }
    }
}

/* Satu tujuan bisa dilayani beberapa maskapai; peta cukup satu penanda
   per tujuan, dengan seluruh maskapainya di dalam popup. */
$rutePeta = [];
foreach ($rute as $row) {
    $tujuan = $titikRute[mb_strtoupper(trim((string) $row['tujuan']))] ?? null;
    $titik  = $tujuan ? arp_to_latlng($tujuan['lokasi_arp']) : null;
    if (!$titik) {
        continue;
    }
    $kunci = (int) $tujuan['id'];
    if (!isset($rutePeta[$kunci])) {
        $rutePeta[$kunci] = [
            'nama'     => $tujuan['nama'],
            'lat'      => $titik['lat'],
            'lng'      => $titik['lng'],
            'url'      => url('pages/bandara-detail?id=' . $kunci),
            'maskapai' => [],
        ];
    }
    if ($row['maskapai'] && !in_array($row['maskapai'], $rutePeta[$kunci]['maskapai'], true)) {
        $rutePeta[$kunci]['maskapai'][] = $row['maskapai'];
    }
}

$terdekatPeta = [];
foreach ($terdekat as $row) {
    $titik = arp_to_latlng($row['lokasi_arp']);
    if (!$titik) {
        continue;
    }
    $terdekatPeta[] = [
        'nama'  => $row['nama'],
        'lat'   => $titik['lat'],
        'lng'   => $titik['lng'],
        'jarak' => round((float) $row['jarak'], 1),
        'url'   => url('pages/bandara-detail?id=' . (int) $row['id']),
    ];
}

$petaLapis = bandara_peta_lapis($bandara['kode_icao']);

$petaData = [
    'ini' => $koordinat ? [
        'nama' => $bandara['nama'],
        'icao' => $bandara['kode_icao'],
        'lat'  => $koordinat['lat'],
        'lng'  => $koordinat['lng'],
    ] : null,
    'terdekat' => $terdekatPeta,
    'rute'     => array_values($rutePeta),
    /* Lapisan KKOP dan cakupan baru dipunyai sebagian bandara; berkasnya
       diambil JavaScript saat lapisannya dinyalakan, bukan ikut termuat
       bersama halaman. null berarti datanya belum ada. */
    'lapis'    => $petaLapis
        ? asset_version(url($petaLapis), FRONTEND_PATH, $petaLapis)
        : null,
];

/* Tiap kategori peralatan berdiri sendiri sebagai tab, sejajar dengan
   Fasilitas Udara/Darat — bukan lagi satu tab "Peralatan" berisi kartu. */
$tabPeralatan = [];
foreach (BANDARA_PERALATAN as $kunci => $label) {
    $tabPeralatan[$kunci] = ['label' => $label, 'jumlah' => count($peralatan[$kunci] ?? [])];
}

$tabs = [
    'terdekat'     => ['label' => 'Bandar Udara Terdekat', 'jumlah' => count($terdekat)],
    'rute'         => ['label' => 'Rute Domestik',       'jumlah' => count($rute)],
    'fas-udara'    => ['label' => 'Fasilitas Udara',     'jumlah' => $jumlahFasUdara],
    'fas-darat'    => ['label' => 'Fasilitas Darat',     'jumlah' => $jumlahFasDarat],
] + $tabPeralatan + [
    'fas-umum'     => ['label' => 'Fasilitas Umum',      'jumlah' => count($fasUmum)],
    'transportasi' => ['label' => 'Transportasi',        'jumlah' => count($transportasi)],
    'galeri'       => ['label' => 'Galeri',              'jumlah' => count($galeri)],
];

$GLOBALS['dbu_head'] = '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">';

page_start('informasi-publik', [
    'title'       => $bandara['nama'] . ' — Daftar Bandar Udara — ' . setting('site_name'),
    'description' => 'Data Bandar Udara ' . $bandara['nama'] . ($bandara['kode_icao'] ? ' (' . $bandara['kode_icao'] . ')' : '')
                     . ($lokasi ? ' di ' . implode(', ', array_slice($lokasi, -2)) : '') . '.',
    'breadcrumbs' => [['label' => 'Daftar Bandar Udara', 'url' => 'pages/informasi-publik'], ['label' => $bandara['nama']]],
]);
partial('header');

/**
 * Ikon Bootstrap yang mewakili sebuah kondisi cuaca.
 *
 * Kolom `cuaca_kondisi` diisi bebas oleh operator, jadi pencocokannya
 * memakai kata kunci — bukan daftar tertutup. Kondisi yang tidak dikenali
 * (termasuk yang masih kosong) memakai ikon awan netral.
 */
if (!function_exists('bdr_ikon_cuaca')) {
    function bdr_ikon_cuaca(?string $kondisi): string
    {
        $teks = mb_strtolower(trim((string) $kondisi));
        if ($teks === '') {
            return 'bi-cloud-sun';
        }
        foreach ([
            'petir'   => 'bi-cloud-lightning-rain',
            'lebat'   => 'bi-cloud-rain-heavy',
            'hujan'   => 'bi-cloud-rain',
            'kabut'   => 'bi-cloud-fog2',
            'asap'    => 'bi-cloud-haze2',
            'berawan' => 'bi-clouds',
            'awan'    => 'bi-cloud',
            'cerah'   => 'bi-sun',
        ] as $kunci => $ikon) {
            if (str_contains($teks, $kunci)) {
                return $ikon;
            }
        }
        return 'bi-cloud-sun';
    }
}

/**
 * Satu pasangan label/nilai pada .def-grid; dilewati bila nilainya kosong.
 *
 * @param string $lebar Kelas pelebaran kolom, mis. 'def-full'.
 * @param string $isi   HTML siap pakai; bila diisi, $nilai hanya penentu
 *                      apakah barisnya ditampilkan.
 */
if (!function_exists('bdr_def')) {
    function bdr_def(string $label, ?string $nilai, bool $selalu = false, string $lebar = '', string $isi = ''): void
    {
        if (!$selalu && trim((string) $nilai) === '') {
            return;
        }
        $teks = trim((string) $nilai) !== '' ? e((string) $nilai) : '—';
        echo '<div' . ($lebar ? ' class="' . e($lebar) . '"' : '') . '>'
           . '<dt>' . e($label) . '</dt>'
           . '<dd>' . ($isi !== '' ? $isi : $teks) . '</dd>'
           . '</div>';
    }
}
?>

<!-- ===================== HERO ===================== -->
<div class="bdr-hero">
  <div class="container bdr-hero-grid">

    <div class="bdr-hero-teks">
      <div class="eyebrow">Bandar Udara</div>
      <h1><?= e($bandara['nama']) ?></h1>
      <p>Halaman ini menyajikan informasi mengenai bandar udara, meliputi rute penerbangan,
         fasilitas, data operasional, spesifikasi teknis, serta dokumen pendukung bandar udara.</p>

      <div class="bdr-chips">
        <?php /* Kolom cuaca belum terisi untuk sebagian besar bandara; slotnya
                 tetap ditampilkan dengan "—" agar susunannya tidak berubah
                 begitu operator mengisinya. */ ?>
        <div class="bdr-chip wide<?= $cuacaAda ? '' : ' sepi' ?>">
          <i class="bi <?= e(bdr_ikon_cuaca($bandara['cuaca_kondisi'])) ?>"></i>
          <div>
            <b><?= $bandara['cuaca_suhu'] !== null ? (int) $bandara['cuaca_suhu'] . '°C' : '—' ?></b>
            <span><?= e($bandara['cuaca_kondisi'] ?: 'Cuaca') ?></span>
          </div>
        </div>
        <div class="bdr-chip"><div><b><?= count($rute) ?></b><span>Rute</span></div></div>
        <div class="bdr-chip"><div><b><?= count($fasUmum) ?></b><span>Fasilitas</span></div></div>
        <div class="bdr-chip"><div><b><?= count($transportasi) ?></b><span>Transport</span></div></div>
        <?php if ($bandara['microsite_url']): ?>
        <a class="bdr-chip tautan" href="<?= eurl($bandara['microsite_url']) ?>" target="_blank" rel="noopener">
          <div><i class="bi bi-box-arrow-up-right"></i><b>Microsite</b></div>
        </a>
        <?php else: ?>
        <div class="bdr-chip tautan sepi">
          <div><i class="bi bi-box-arrow-up-right"></i><b>Microsite</b><span>—</span></div>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <div class="bdr-hero-media">
      <?php if ($galeri): ?>
      <div class="bdr-carousel" data-carousel>
        <div class="bdr-carousel-rel">
          <?php foreach ($galeri as $i => $row): ?>
          <a class="bdr-slide<?= $i === 0 ? ' active' : '' ?>" href="<?= e(asset_url($row['image'])) ?>" data-lightbox>
            <img src="<?= e(asset_url($row['image'])) ?>"
                 alt="<?= e($row['keterangan'] ?: $bandara['nama']) ?>"
                 loading="<?= $i === 0 ? 'eager' : 'lazy' ?>">
            <?php if ($row['keterangan']): ?>
            <span class="bdr-slide-cap"><?= e($row['keterangan']) ?></span>
            <?php endif; ?>
          </a>
          <?php endforeach; ?>
        </div>
        <button type="button" class="bdr-carousel-nav prev" data-arah="-1" aria-label="Foto sebelumnya"><i class="bi bi-chevron-left"></i></button>
        <button type="button" class="bdr-carousel-nav next" data-arah="1" aria-label="Foto berikutnya"><i class="bi bi-chevron-right"></i></button>
      </div>
      <?php else: ?>
      <div class="bdr-media-kosong">
        <i class="bi bi-image"></i>
        <p>Foto bandar udara belum tersedia.</p>
      </div>
      <?php endif; ?>
    </div>

  </div>
</div>

<section class="section">
  <div class="container">

    <!-- ===================== INFORMASI BANDAR UDARA ===================== -->
    <h2 class="bdr-h2">Informasi Bandar Udara</h2>

    <div class="bdr-ringkas">

      <div class="card card-pad">
        <dl class="def-grid kol-3">
          <?php
            bdr_def('Nama Bandar Udara', $bandara['nama'], true, 'def-full kuat');
            bdr_def('Kode ICAO', $bandara['kode_icao'], true);
            bdr_def('Kode IATA', $bandara['kode_iata'], true);
            bdr_def('Kelas', $bandara['kelas'], true);
            bdr_def('Pengelola', $bandara['pengelola'], true, 'def-full');
            bdr_def('Kantor Otoritas', $bandara['kantor_otoritas'], true, 'def-full');
            bdr_def('Telepon', $bandara['telepon'], false, 'def-full');
            bdr_def('Faksimile', $bandara['fax'], false, 'def-full');
            bdr_def('Email', $bandara['email'], false, 'def-full',
                    '<a href="mailto:' . e((string) $bandara['email']) . '">' . e((string) $bandara['email']) . '</a>');
            bdr_def('Website', $bandara['website'], false, 'def-full',
                    '<a href="' . eurl($bandara['website']) . '" target="_blank" rel="noopener">' . e((string) $bandara['website']) . '</a>');
            /* Kolom baru; ditampilkan walau kosong supaya operator melihat
               tempatnya, sesuai dengan slot Microsite pada hero. */
            bdr_def('Microsite', $bandara['microsite_url'], true, 'def-full', $bandara['microsite_url']
                ? '<a href="' . eurl($bandara['microsite_url']) . '" target="_blank" rel="noopener">' . e((string) $bandara['microsite_url']) . '</a>'
                : '');
            bdr_def('Lokasi', $lokasiRingkas ? implode(', ', $lokasiRingkas) : null, true, 'def-full');
          ?>
        </dl>

        <div class="bdr-lalin">
          <h4>Lalu Lintas Udara<?= $bandara['tahun_data'] ? ' (' . (int) $bandara['tahun_data'] . ')' : '' ?></h4>
          <dl class="def-grid kol-3 rapat">
            <div><dt>Pesawat</dt><dd class="angka"><?= number_format((int) $bandara['jumlah_pesawat'], 0, ',', '.') ?></dd></div>
            <div><dt>Penumpang</dt><dd class="angka"><?= number_format((int) $bandara['jumlah_penumpang'], 0, ',', '.') ?></dd></div>
            <div><dt>Kargo</dt><dd class="angka"><?= number_format((int) $bandara['jumlah_kargo'], 0, ',', '.') ?></dd></div>
          </dl>
        </div>
      </div>

      <div class="bdr-ringkas-sisi">
        <?php if ($koordinat): ?>
        <div class="card bdr-peta-kartu">
          <div id="bdrPetaLokasi" class="bdr-peta"></div>
          <a class="bdr-peta-aksi" target="_blank" rel="noopener"
             href="https://www.google.com/maps/search/?api=1&amp;query=<?= $koordinat['lat'] ?>,<?= $koordinat['lng'] ?>">
            Buka di Google Maps
          </a>

          <?php /* Pengalih lapisan peta. Gambarnya diambil frontend/js/bandara-detail.js
                   dari berkas data/peta/<ICAO>.json saat tombolnya ditekan pertama kali.
                   Bandara yang berkasnya belum ada tetap menampilkan tombol ini, hanya
                   disertai keterangan bahwa datanya belum tersedia. */ ?>
          <div class="peta-lapis">
            <button type="button" class="peta-lapis-btn" data-lapis="kkop" aria-pressed="false">
              <span class="kotak kotak-kkop"></span> KKOP
            </button>
            <button type="button" class="peta-lapis-btn" data-lapis="cakupan" aria-pressed="false">
              <span class="kotak kotak-cakupan"></span> Cakupan Bandara
            </button>
            <div class="peta-lapis-legenda" data-lapis-legenda hidden></div>
            <p class="peta-lapis-nota" data-lapis-nota hidden></p>
          </div>
        </div>
        <?php else: ?>
        <div class="card card-pad empty-note"><i class="bi bi-geo"></i>
          <p>Koordinat bandar udara ini belum dapat dibaca, sehingga petanya belum bisa ditampilkan.</p></div>
        <?php endif; ?>

        <div class="spec-card">
          <h3>Spesifikasi Teknis</h3>
          <dl>
            <div><dt>Critical Aircraft</dt><dd><?= e($bandara['critical_aircraft'] ?: '—') ?></dd></div>
            <div><dt>PKP-PK</dt><dd><?= e($bandara['pkp_pk'] ?: '—') ?></dd></div>
            <div><dt>Koordinat ARP</dt><dd><?= e($bandara['lokasi_arp'] ?: '—') ?></dd></div>
            <div><dt>Cuaca Terkini</dt><dd><?= $cuacaAda
                ? e(trim(($bandara['cuaca_suhu'] !== null ? (int) $bandara['cuaca_suhu'] . '°C' : '')
                    . ($bandara['cuaca_kondisi'] ? ' · ' . $bandara['cuaca_kondisi'] : ''), ' ·'))
                : '—' ?></dd></div>
            <?php if ($bandara['cuaca_diperbarui']): ?>
            <div><dt>Cuaca Diperbarui</dt><dd><?= e(date('d/m/Y H:i', strtotime((string) $bandara['cuaca_diperbarui']))) ?></dd></div>
            <?php endif; ?>
          </dl>
        </div>
      </div>

    </div>

    <!-- ===================== TAB ===================== -->
    <div class="tabs-nav" data-tabs="bdr">
      <?php $pertama = true; foreach ($tabs as $kunci => $tab): ?>
      <button type="button" class="<?= $pertama ? 'active' : '' ?>" data-tab="<?= e($kunci) ?>">
        <?= e($tab['label']) ?><?php if ($tab['jumlah']): ?> <span class="tab-count"><?= (int) $tab['jumlah'] ?></span><?php endif; ?>
      </button>
      <?php $pertama = false; endforeach; ?>
    </div>

    <!-- ============ Bandar Udara Terdekat ============ -->
    <div class="tab-panel active" data-tab-panel="bdr" data-panel-id="terdekat">
      <?php if (!$terdekat): ?>
        <div class="card card-pad empty-note"><i class="bi bi-geo"></i>
          <p>Koordinat bandar udara ini belum tersedia, sehingga jarak ke bandar udara lain belum dapat dihitung.</p></div>
      <?php else: ?>
      <div class="peta-split">
        <div class="card peta-split-peta"><div id="bdrPetaTerdekat" class="bdr-peta"></div></div>
        <div class="peta-split-daftar">
          <h3>Daftar Bandara Terdekat</h3>
          <ul>
            <?php foreach ($terdekat as $row): ?>
            <li class="bdr-dekat-item">
              <span class="nama"><?= e($row['nama']) ?></span>
              <span class="kanan">
                <span class="jarak"><?= angka_id($row['jarak'], 1) ?> km</span>
                <a href="<?= e(url('pages/bandara-detail?id=' . (int) $row['id'])) ?>"
                   aria-label="Lihat <?= e($row['nama']) ?>"><i class="bi bi-arrow-right-circle"></i></a>
              </span>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Rute Domestik ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="rute">
      <?php if (!$rute): ?>
        <div class="card card-pad empty-note"><i class="bi bi-signpost-split"></i><p>Belum ada data rute untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="peta-split">
        <div class="card peta-split-peta"><div id="bdrPetaRute" class="bdr-peta"></div></div>
        <div class="peta-split-daftar">
          <h3>Daftar Rute Domestik</h3>
          <ul>
            <?php foreach ($rute as $row): ?>
            <li class="bdr-rute-item">
              <div class="atas">
                <div>
                  <span class="lbl">Tujuan</span>
                  <b><?= e($row['tujuan']) ?></b>
                </div>
                <?php if ($row['maskapai']): ?>
                <span class="maskapai"><?= e($row['maskapai']) ?></span>
                <?php endif; ?>
              </div>
              <div class="bawah">
                <div><span class="lbl">Pesawat</span><span><?= e($row['pesawat'] ?: '—') ?></span></div>
                <div class="kanan"><span class="lbl">Frekuensi</span>
                  <span><?= $row['frekuensi'] ? e($row['frekuensi']) . 'x / minggu' : '—' ?></span></div>
              </div>
            </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Fasilitas Udara ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="fas-udara">
      <?php if (!$jumlahFasUdara): ?>
        <div class="card card-pad empty-note"><i class="bi bi-airplane"></i><p>Belum ada data fasilitas sisi udara untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="sub-tab-layout">
        <div class="card sub-tab-nav" data-tabs="fu">
          <?php $pertama = true; foreach (BANDARA_FASILITAS_UDARA as $kunci => $label):
              if (empty($fasUdara[$kunci])) { continue; } ?>
          <button type="button" class="<?= $pertama ? 'active' : '' ?>" data-tab="<?= e($kunci) ?>">
            <span class="dot"></span> <?= e($label) ?>
          </button>
          <?php $pertama = false; endforeach; ?>
        </div>

        <div class="sub-tab-body">
          <?php $pertama = true; foreach (BANDARA_FASILITAS_UDARA as $kunci => $label):
              if (empty($fasUdara[$kunci])) { continue; } ?>
          <div class="card card-pad tab-panel <?= $pertama ? 'active' : '' ?>" data-tab-panel="fu" data-panel-id="<?= e($kunci) ?>">
            <h3 class="bdr-kartu-judul"><?= e($label) ?></h3>
            <?php foreach ($fasUdara[$kunci] as $row): ?>
            <?php
              /* Sebagian fasilitas dinyatakan dengan luas (mis. turning area),
                 sebagian dengan dimensi panjang x lebar. Keduanya memakai kolom
                 yang sama, jadi judulnya menyesuaikan isi. */
              $pakaiLuas = str_contains((string) $row['dimensi_verifikasi'], 'm2')
                        || str_contains((string) $row['dimensi_terbangun'], 'm2');
              $ukuran    = $pakaiLuas ? 'Luas' : 'Dimensi';
              $jarakDek  = json_decode((string) $row['declare_distance'], true);
              /* Banyak fasilitas tidak bernama khusus, sehingga namanya sama
                 dengan kategorinya — tampilkan sekali saja. */
              $judul = ($row['nama'] !== '' && mb_strtolower($row['nama']) !== mb_strtolower($label))
                  ? $row['nama']
                  : $label;
            ?>
            <div class="fas-item">
              <h4><?= e($judul) ?></h4>
              <div class="fas-grid">
                <?php if ($row['dimensi_verifikasi']): ?>
                <div><span><?= $ukuran ?> Terverifikasi</span><b><?= e($row['dimensi_verifikasi']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['dimensi_terbangun']): ?>
                <div><span><?= $ukuran ?> Terbangun</span><b><?= e($row['dimensi_terbangun']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['konstruksi']): ?>
                <div><span>Konstruksi</span><b><?= e($row['konstruksi']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['daya_dukung']): ?>
                <div><span>Daya Dukung</span><b><?= e($row['daya_dukung']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['azimuth']): ?>
                <div><span>Azimuth</span><b><?= e($row['azimuth']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['critical_aircraft']): ?>
                <div><span>Critical Aircraft</span><b><?= e($row['critical_aircraft']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['pesawat_beroperasi']): ?>
                <div><span>Pesawat Beroperasi</span><b><?= e($row['pesawat_beroperasi']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['tipe_runway']): ?>
                <div><span>Tipe Runway</span><b><?= e($row['tipe_runway']) ?></b></div>
                <?php endif; ?>
              </div>

              <?php if (is_array($jarakDek) && $jarakDek): ?>
              <div class="table-wrap" style="margin-top:14px;">
                <table class="reg-table">
                  <thead><tr><th>Declare Distance</th><th>TORA</th><th>TODA</th><th>ASDA</th><th>LDA</th></tr></thead>
                  <tbody>
                    <?php foreach ($jarakDek as $ujung => $nilai): if (!is_array($nilai)) { continue; } ?>
                    <tr>
                      <td><?= e((string) $ujung) ?></td>
                      <td><?= e((string) ($nilai['TORA'] ?? '—')) ?></td>
                      <td><?= e((string) ($nilai['TODA'] ?? '—')) ?></td>
                      <td><?= e((string) ($nilai['ASDA'] ?? '—')) ?></td>
                      <td><?= e((string) ($nilai['LDA']  ?? '—')) ?></td>
                    </tr>
                    <?php endforeach; ?>
                  </tbody>
                </table>
              </div>
              <?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
          <?php $pertama = false; endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Fasilitas Darat ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="fas-darat">
      <?php if (!$jumlahFasDarat): ?>
        <div class="card card-pad empty-note"><i class="bi bi-buildings"></i><p>Belum ada data fasilitas sisi darat untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="sub-tab-layout">
        <div class="card sub-tab-nav" data-tabs="fd">
          <?php $pertama = true; foreach (BANDARA_FASILITAS_DARAT as $kunci => $label):
              if (empty($fasDarat[$kunci])) { continue; } ?>
          <button type="button" class="<?= $pertama ? 'active' : '' ?>" data-tab="<?= e($kunci) ?>">
            <span class="dot"></span> <?= e($label) ?>
          </button>
          <?php $pertama = false; endforeach; ?>
        </div>

        <div class="sub-tab-body">
          <?php $pertama = true; foreach (BANDARA_FASILITAS_DARAT as $kunci => $label):
              if (empty($fasDarat[$kunci])) { continue; } ?>
          <div class="card card-pad tab-panel <?= $pertama ? 'active' : '' ?>" data-tab-panel="fd" data-panel-id="<?= e($kunci) ?>">
            <h3 class="bdr-kartu-judul"><?= e($label) ?></h3>
            <?php foreach ($fasDarat[$kunci] as $row): ?>
            <div class="fas-item">
              <?php
                /* Data resmi menulis "Mushola (Mushola)" saat gedungnya tidak
                   bernama khusus; judulnya cukup sekali. */
                $judul = ($row['nama'] !== '' && mb_strtolower($row['nama']) !== mb_strtolower($label))
                    ? $label . ' (' . $row['nama'] . ')'
                    : $label;
              ?>
              <h4><?= e($judul) ?></h4>
              <div class="fas-grid">
                <?php if ($row['luas'] !== null): ?>
                <div><span>Luas</span><b><?= angka_id((float) $row['luas']) ?> m<sup>2</sup></b></div>
                <?php endif; ?>
                <?php if ($row['kapasitas']): ?>
                <div><span>Kapasitas</span><b><?= e($row['kapasitas']) ?></b></div>
                <?php endif; ?>
                <?php if ($row['keterangan']): ?>
                <div><span>Kategori</span><b><?= e($row['keterangan']) ?></b></div>
                <?php endif; ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
          <?php $pertama = false; endforeach; ?>
        </div>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Peralatan: satu tab per kategori ============ -->
    <?php foreach (BANDARA_PERALATAN as $kunci => $label):
        $baris = $peralatan[$kunci] ?? [];
        $medan = BANDARA_PERALATAN_MEDAN[$kunci] ?? []; ?>
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="<?= e($kunci) ?>">
      <?php if (!$baris): ?>
        <div class="card card-pad empty-note"><i class="bi bi-tools"></i>
          <p>Belum ada data <?= e(mb_strtolower($label)) ?> untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="prlt-grid">
        <?php foreach ($baris as $row): ?>
        <div class="card card-pad prlt-card">
          <dl class="prlt-def">
            <?php foreach ($medan as $kolom => $judul): if (trim((string) ($row[$kolom] ?? '')) === '') { continue; } ?>
            <div><dt><?= e($judul) ?></dt><dd><?= e((string) $row[$kolom]) ?></dd></div>
            <?php endforeach; ?>
            <?php /* Jumlah 0 berarti peralatannya ada tetapi belum dihitung. */ ?>
            <div><dt>Jumlah</dt><dd><?= ((int) $row['jumlah']) ? (int) $row['jumlah'] . ' unit' : '&mdash;' ?></dd></div>
          </dl>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>

    <!-- ============ Fasilitas Umum ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="fas-umum">
      <?php if (!$fasUmum): ?>
        <div class="card card-pad empty-note"><i class="bi bi-shop"></i><p>Belum ada data fasilitas umum untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="fas-tile-grid">
        <?php foreach ($fasUmum as $row): ?>
        <div class="card fas-tile">
          <span class="ic"><i class="bi <?= e($row['icon'] ?: 'bi-check-circle') ?>"></i></span>
          <span class="nama"><?= e($row['nama']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Transportasi ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="transportasi">
      <?php if (!$transportasi): ?>
        <div class="card card-pad empty-note"><i class="bi bi-bus-front"></i><p>Belum ada data transportasi untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="fas-tile-grid baris">
        <?php foreach ($transportasi as $row): ?>
        <div class="card fas-tile hijau">
          <span class="ic"><i class="bi <?= e($row['icon'] ?: 'bi-bus-front') ?>"></i></span>
          <span class="nama"><?= e($row['nama']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <!-- ============ Galeri ============ -->
    <div class="tab-panel" data-tab-panel="bdr" data-panel-id="galeri">
      <?php if (!$galeri): ?>
        <div class="card card-pad empty-note"><i class="bi bi-images"></i><p>Belum ada foto untuk bandar udara ini.</p></div>
      <?php else: ?>
      <div class="galeri-grid">
        <?php foreach ($galeri as $row): ?>
        <a class="galeri-item" href="<?= e(asset_url($row['image'])) ?>" data-lightbox>
          <img src="<?= e(asset_url($row['image'])) ?>"
               alt="<?= e($row['keterangan'] ?: $bandara['nama']) ?>" loading="lazy">
          <?php if ($row['keterangan']): ?>
          <span class="galeri-cap"><?= e($row['keterangan']) ?></span>
          <?php endif; ?>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

    <div style="margin-top:28px;">
      <a href="<?= e(url('pages/informasi-publik')) ?>" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Bandar Udara</a>
    </div>

  </div>
</section>

<script id="bdrPetaData" type="application/json"><?= json_encode($petaData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="<?= e(asset_version(url('js/bandara-detail.js'), FRONTEND_PATH, 'js/bandara-detail.js')) ?>"></script>

<?php partial('footer'); ?>
