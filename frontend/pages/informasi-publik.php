<?php
/* Informasi Publik — Daftar Bandar Udara (tampilan peta & tabel).
   Sumber data: tabel `bandaras` (lihat database/bandaras.sql).
   Koordinat marker dihitung dari kolom `lokasi_arp` lewat arp_to_latlng(). */
require_once dirname(__DIR__) . '/bootstrap.php';

$rows = db_all('SELECT * FROM bandaras ORDER BY nama');

/* Kunci JSON sengaja dipendekkan — 608 bandara dikirim sekaligus ke peramban. */
$data = [];
foreach ($rows as $r) {
    $koordinat = arp_to_latlng($r['lokasi_arp']);
    $data[] = [
        'i'  => (int) $r['id'],
        'n'  => $r['nama'],
        'ia' => $r['kode_iata'],
        'ic' => $r['kode_icao'],
        'al' => $r['alamat'],
        'em' => $r['email'],
        'fx' => $r['fax'],
        'kd' => $r['kelurahan_desa'],
        'kc' => $r['kecamatan'],
        'kk' => $r['kabupaten_kota'],
        'pr' => $r['provinsi'],
        'ko' => $r['kantor_otoritas'],
        'kl' => $r['kelas'],
        'ar' => $r['lokasi_arp'],
        'pg' => $r['pengelola'],
        'tl' => $r['telepon'],
        'tp' => $r['tipe'],
        'ws' => $r['website'],
        'jp' => (int) $r['jumlah_pesawat'],
        'jn' => (int) $r['jumlah_penumpang'],
        'jg' => (int) $r['jumlah_kargo'],
        'la' => $koordinat ? round($koordinat['lat'], 6) : null,
        'lo' => $koordinat ? round($koordinat['lng'], 6) : null,
    ];
}

/* Daftar pengelola disusun dari data, bukan didaftar manual, agar ikut bila datanya berubah. */
$pengelola = [];
foreach ($rows as $r) {
    $nama = trim($r['pengelola']) ?: 'Lainnya';
    $pengelola[$nama] = ($pengelola[$nama] ?? 0) + 1;
}
ksort($pengelola);

$berkoordinat = count(array_filter($data, static fn($b) => $b['la'] !== null));

$GLOBALS['dbu_head'] = <<<HTML
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/MarkerCluster.Default.min.css">
HTML;

page_start('informasi-publik', [
    'title'       => 'Daftar Bandar Udara — Direktorat Bandar Udara',
    'description' => 'Peta dan tabel ' . count($data) . ' bandar udara di Indonesia: kode ICAO/IATA, lokasi, kelas, penggunaan, dan pengelola.',
    'breadcrumbs' => [['label' => 'Informasi Publik']],
]);
partial('header');
?>

<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Informasi Publik</div>
    <h1>Daftar Bandar Udara</h1>
    <p>Data bandar udara di seluruh Indonesia beserta kode ICAO/IATA, lokasi, kelas, penggunaan, dan pengelolanya.</p>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="view-switch">
      <button type="button" class="active" data-view="peta"><i class="bi bi-map"></i> Peta</button>
      <button type="button" data-view="tabel"><i class="bi bi-table"></i> Tabel</button>
    </div>

    <div class="card card-pad bandara-filter">
      <label class="filter-label" for="bandaraCari">Pencarian Bandar Udara</label>
      <div class="filter-bar" style="margin-bottom:22px;">
        <div class="grow">
          <i class="bi bi-search"></i>
          <input type="text" id="bandaraCari" placeholder="Cari nama bandara atau IATA/ICAO..." data-bandara-cari>
        </div>
      </div>

      <div class="filter-label">Filter Pengelola</div>
      <div class="chip-filter" data-bandara-pengelola>
        <label class="chip-radio active">
          <input type="radio" name="pengelola" value="" checked>
          <span class="dot" data-pengelola="__semua"></span> Semua
        </label>
        <?php foreach ($pengelola as $nama => $jumlah): ?>
        <label class="chip-radio">
          <input type="radio" name="pengelola" value="<?= e($nama) ?>">
          <span class="dot" data-pengelola="<?= e($nama) ?>"></span> <?= e($nama) ?> (<?= (int) $jumlah ?>)
        </label>
        <?php endforeach; ?>
      </div>
    </div>

    <div data-view-panel="peta">
      <div id="bandaraPeta" class="bandara-peta"></div>
      <div class="peta-kaki">
        <span>* Arahkan kursor ke marker untuk melihat seluruh data bandara. Warna marker mengikuti pengelola.</span>
        <span data-bandara-hitung></span>
      </div>
    </div>

    <div data-view-panel="tabel" hidden>
      <p class="peta-kaki" style="justify-content:flex-start; margin-bottom:12px;"><span data-bandara-hitung></span></p>
      <div class="table-wrap">
        <table class="reg-table">
          <thead>
            <tr>
              <th>No.</th><th>ICAO</th><th>IATA</th><th>Nama Bandar Udara</th>
              <th>Provinsi</th><th>Kabupaten / Kota</th><th>Penggunaan</th>
              <th>Kelas</th><th>Pengelola</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody data-bandara-tbody></tbody>
        </table>
      </div>
      <div class="pagination" data-bandara-pagination></div>
    </div>

  </div>
</section>

<script id="bandaraData" type="application/json"><?= json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP) ?></script>
<script>
  window.BANDARA_DETAIL_URL = <?= json_encode(url('pages/bandara-detail')) ?>;
  window.BANDARA_BERKOORDINAT = <?= (int) $berkoordinat ?>;
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.5.3/leaflet.markercluster.min.js"></script>
<script src="<?= e(asset_version(url('js/bandara-map.js'), FRONTEND_PATH, 'js/bandara-map.js')) ?>"></script>

<?php partial('footer'); ?>
