<?php
/**
 * ============================================================
 *  PENGIMPOR LAPISAN PETA: KKOP DAN CAKUPAN BANDAR UDARA
 *
 *  Mengubah berkas GeoJSON resmi menjadi satu berkas kecil per bandar
 *  udara di frontend/data/peta/<ICAO>.json. Berkas itulah yang diambil
 *  halaman detail saat pengunjung menyalakan lapisan petanya, sehingga
 *  peramban tidak perlu mengunduh GeoJSON nasional yang ratusan KB.
 *
 *  Cara pakai:
 *    php database/import-peta-kkop.php ~/Downloads/kkop_5_bandara.json \
 *                                      ~/Downloads/tatanan_cakupan_5_bandara.json
 *    php database/import-peta-kkop.php --uji ~/Downloads/kkop_5_bandara.json
 *
 *  Pilihan:
 *    --uji     Hanya melaporkan; tidak ada berkas yang ditulis.
 *
 *  Sifat penting:
 *  - Peran tiap berkas dikenali dari isinya, jadi urutan argumen bebas:
 *    yang fiturnya bermedan "BANDARA" dibaca sebagai KKOP, sisanya cakupan.
 *  - Aman dijalankan berulang. Lapisan yang tidak ada pada berkas kali ini
 *    dipertahankan dari berkas lama, jadi mengimpor KKOP saja tidak
 *    menghapus cakupan yang sudah tertulis.
 *  - Bandara dicocokkan ke tabel `bandaras` lewat nama; bila namanya tidak
 *    cocok, dicari baris terdekat dari pusat gambar KKOP-nya. Cara
 *    pencocokan selalu disebut di laporan supaya bisa diperiksa.
 *  - Koordinat dibulatkan ke 6 angka di belakang koma (~0,1 m) dan medan
 *    bawaan QGIS (LAYER_SRC, path, dll.) dibuang — tidak ada gunanya bagi
 *    pengunjung dan memuat jalur berkas komputer pembuat datanya.
 * ============================================================
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Skrip ini hanya untuk baris perintah.\n");
}

require __DIR__ . '/../shared/config.php';
require __DIR__ . '/../shared/db.php';
require __DIR__ . '/../shared/functions.php';

/* Tanpa ini json_encode() menulis pecahan dengan 17 angka penting, sehingga
   koordinat yang sudah dibulatkan ke 6 angka kembali panjang
   (98.885277999999995) dan berkasnya membengkak sampai empat kali lipat.
   Nilai -1 berarti "cukup angka untuk kembali ke nilai semula". */
ini_set('serialize_precision', '-1');

const PETA_TUJUAN    = FRONTEND_PATH . '/data/peta';
const PETA_DESIMAL   = 6;      /* ~0,1 m — jauh lebih rapat dari ketelitian datanya. */
const PETA_JARAK_MAX = 15.0;   /* km; batas kemiripan letak saat nama tak cocok. */

/* ---------- Argumen ---------- */
$berkas  = [];
$ujiSaja = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--uji') { $ujiSaja = true; continue; }
    if (str_starts_with($arg, '--')) {
        exit("Pilihan tidak dikenal: $arg\n");
    }
    $berkas[] = $arg;
}
if (!$berkas) {
    exit("Pemakaian: php database/import-peta-kkop.php [--uji] kkop.json [cakupan.json]\n");
}

/* ============================================================
   Pembantu
   ============================================================ */

/** Nama bandara tanpa spasi dan tanda baca, untuk dibandingkan. */
function peta_kunci(string $nama): string
{
    return preg_replace('/[^A-Z0-9]/', '', mb_strtoupper($nama)) ?? '';
}

/** Apakah titik [lng, lat] ada di dalam satu cincin GeoJSON? */
function peta_dalam_cincin(array $titik, array $cincin): bool
{
    [$x, $y] = $titik;
    $di = false;
    $n  = count($cincin);
    for ($i = 0, $j = $n - 1; $i < $n; $j = $i++) {
        [$xi, $yi] = $cincin[$i];
        [$xj, $yj] = $cincin[$j];
        if (($yi > $y) !== ($yj > $y)
            && $x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi) {
            $di = !$di;
        }
    }
    return $di;
}

/** Apakah titik ada di dalam poligon (cincin pertama dikurangi lubangnya)? */
function peta_dalam_poligon(array $titik, array $poligon): bool
{
    if (!peta_dalam_cincin($titik, $poligon[0])) {
        return false;
    }
    foreach (array_slice($poligon, 1) as $lubang) {
        if (peta_dalam_cincin($titik, $lubang)) {
            return false;
        }
    }
    return true;
}

/** Bulatkan seluruh koordinat sebuah geometri dan buang angka berlebih. */
function peta_bulatkan(array $cincin): array
{
    foreach ($cincin as &$c) {
        $c = [round((float) $c[0], PETA_DESIMAL), round((float) $c[1], PETA_DESIMAL)];
    }
    return $cincin;
}

/** Kotak pembatas [minLng, minLat, maxLng, maxLat] dari daftar cincin. */
function peta_kotak(array $cincinDaftar): array
{
    $x = [];
    $y = [];
    foreach ($cincinDaftar as $cincin) {
        foreach ($cincin as $c) {
            $x[] = $c[0];
            $y[] = $c[1];
        }
    }
    return [min($x), min($y), max($x), max($y)];
}

/* ============================================================
   Baca berkas sumber
   ============================================================ */

$sumberKkop    = null;
$sumberCakupan = null;

foreach ($berkas as $jalur) {
    if (!is_file($jalur)) {
        exit("Berkas tidak ditemukan: $jalur\n");
    }
    $isi = json_decode((string) file_get_contents($jalur), true);
    if (!is_array($isi) || !isset($isi['features'])) {
        exit("Bukan GeoJSON FeatureCollection: $jalur\n");
    }
    /* Peran berkas dikenali dari isinya, bukan dari namanya. */
    $adaBandara = false;
    foreach ($isi['features'] as $f) {
        if (isset($f['properties']['BANDARA'])) { $adaBandara = true; break; }
    }
    if ($adaBandara) {
        $sumberKkop = ['jalur' => $jalur, 'isi' => $isi];
    } else {
        $sumberCakupan = ['jalur' => $jalur, 'isi' => $isi];
    }
}

echo "Sumber KKOP    : " . ($sumberKkop['jalur'] ?? '(tidak diberikan)') . "\n";
echo "Sumber cakupan : " . ($sumberCakupan['jalur'] ?? '(tidak diberikan)') . "\n\n";

/* ============================================================
   Kelompokkan fitur KKOP per bandara
   ============================================================ */

$zonaTakDikenal = [];
$kkopPerBandara = [];

foreach ($sumberKkop['isi']['features'] ?? [] as $fitur) {
    $nama = trim((string) ($fitur['properties']['BANDARA'] ?? ''));
    $zona = trim((string) ($fitur['properties']['ZONA'] ?? ''));
    if ($nama === '' || !isset($fitur['geometry']['coordinates'])) {
        continue;
    }

    $kode = array_search($zona, BANDARA_KKOP_ZONA, true);
    if ($kode === false) {
        $zonaTakDikenal[$zona] = ($zonaTakDikenal[$zona] ?? 0) + 1;
        continue;
    }

    /* Sumbernya memakai Polygon; MultiPolygon dipecah agar seragam. */
    $poligon = $fitur['geometry']['type'] === 'MultiPolygon'
        ? $fitur['geometry']['coordinates']
        : [$fitur['geometry']['coordinates']];

    foreach ($poligon as $p) {
        $kkopPerBandara[$nama][] = [
            'zona'    => $kode,
            'cincin'  => array_map('peta_bulatkan', $p),
        ];
    }
}

if ($zonaTakDikenal) {
    echo "PERINGATAN — zona di luar BANDARA_KKOP_ZONA (shared/functions.php), fiturnya dilewati:\n";
    foreach ($zonaTakDikenal as $zona => $jumlah) {
        echo "  $jumlah fitur  \"$zona\"\n";
    }
    echo "\n";
}

/* ============================================================
   Daftar bandara dari basis data, untuk mencocokkan nama/letak
   ============================================================ */

$dbBandara = [];
foreach (db_all("SELECT id, nama, kode_icao, lokasi_arp FROM bandaras WHERE kode_icao <> ''") as $row) {
    $titik = arp_to_latlng($row['lokasi_arp']);
    if (!$titik) {
        continue;
    }
    $dbBandara[] = $row + $titik + ['kunci' => peta_kunci((string) $row['nama'])];
}

/**
 * Cari baris `bandaras` untuk satu nama pada berkas KKOP.
 *
 * Nama pada berkas GeoJSON tidak selalu sama dengan nama resmi
 * ("Sultan Aji MS" vs "SULTAN AJI MUHAMMAD SULAIMAN"), jadi bila nama
 * gagal dicocokkan dipakai letaknya: baris terdekat dari pusat gambar
 * KKOP-nya. Cara yang dipakai ikut dikembalikan untuk laporan.
 */
function peta_cari_bandara(string $nama, array $pusat, array $dbBandara): array
{
    $kunci = peta_kunci($nama);

    foreach ($dbBandara as $b) {
        if ($b['kunci'] === $kunci) {
            return [$b, 'nama sama'];
        }
    }
    /* "Kualanamu" vs "KUALANAMU INTERNASIONAL". */
    $awalan = array_values(array_filter($dbBandara, static fn($b) =>
        str_starts_with($b['kunci'], $kunci) || str_starts_with($kunci, $b['kunci'])));
    if (count($awalan) === 1) {
        return [$awalan[0], 'awalan nama'];
    }

    $terdekat = null;
    $jarak    = PHP_FLOAT_MAX;
    foreach ($dbBandara as $b) {
        $d = haversine_km($pusat['lat'], $pusat['lng'], (float) $b['lat'], (float) $b['lng']);
        if ($d < $jarak) {
            $jarak    = $d;
            $terdekat = $b;
        }
    }
    if ($terdekat && $jarak <= PETA_JARAK_MAX) {
        return [$terdekat, sprintf('letak (%.1f km)', $jarak)];
    }
    return [null, $terdekat ? sprintf('terdekat %.0f km — terlalu jauh', $jarak) : 'tak ada pembanding'];
}

/* ============================================================
   Susun dan tulis satu berkas per bandara
   ============================================================ */

$poligonCakupan = [];
foreach ($sumberCakupan['isi']['features'] ?? [] as $fitur) {
    $g = $fitur['geometry'] ?? [];
    if (($g['type'] ?? '') === 'MultiPolygon') {
        foreach ($g['coordinates'] as $p) { $poligonCakupan[] = $p; }
    } elseif (($g['type'] ?? '') === 'Polygon') {
        $poligonCakupan[] = $g['coordinates'];
    }
}

if (!$ujiSaja && !is_dir(PETA_TUJUAN) && !mkdir(PETA_TUJUAN, 0775, true)) {
    exit('Gagal membuat folder ' . PETA_TUJUAN . "\n");
}

$ditulis = 0;
$gagal   = 0;

foreach ($kkopPerBandara as $nama => $fitur) {
    /* Pusat gambar KKOP dipakai sebagai penebak letak bandaranya. */
    [$minX, $minY, $maxX, $maxY] = peta_kotak(array_map(static fn($f) => $f['cincin'][0], $fitur));
    $pusat = ['lat' => ($minY + $maxY) / 2, 'lng' => ($minX + $maxX) / 2];

    [$db, $cara] = peta_cari_bandara((string) $nama, $pusat, $dbBandara);
    if (!$db) {
        printf("  ✗ %-24s tidak ada padanannya di tabel bandaras (%s)\n", $nama, $cara);
        $gagal++;
        continue;
    }

    $icao = strtoupper(trim((string) $db['kode_icao']));
    if (!preg_match('/^[A-Z0-9]{4}$/', $icao)) {
        printf("  ✗ %-24s kode ICAO tidak lazim: \"%s\"\n", $nama, $icao);
        $gagal++;
        continue;
    }

    /* Urutan gambar: kawasan terluar lebih dulu supaya kawasan kecil di
       tengahnya tetap terlihat dan bisa diklik. BANDARA_KKOP_ZONA sudah
       tersusun dari dalam ke luar, jadi urutannya tinggal dibalik. */
    $urutan = array_flip(array_keys(BANDARA_KKOP_ZONA));
    usort($fitur, static fn($a, $b) => $urutan[$b['zona']] <=> $urutan[$a['zona']]);

    $kkop = [];
    $ada  = [];
    foreach ($fitur as $f) {
        $ada[$f['zona']] = true;
        $kkop[] = [
            'type'       => 'Feature',
            'properties' => ['zona' => $f['zona']],
            'geometry'   => ['type' => 'Polygon', 'coordinates' => $f['cincin']],
        ];
    }

    /* Cakupan disajikan sebagai satu poligon: sumbernya sudah tergabung
       (dissolve), jadi yang dipakai adalah poligon yang memuat titik ARP
       bandara ini — bisa saja membentang jauh melewati bandara lain. */
    $cakupan = null;
    foreach ($poligonCakupan as $p) {
        if (peta_dalam_poligon([(float) $db['lng'], (float) $db['lat']], $p)) {
            $cakupan = [[
                'type'       => 'Feature',
                'properties' => new stdClass(),
                'geometry'   => ['type' => 'Polygon', 'coordinates' => array_map('peta_bulatkan', $p)],
            ]];
            break;
        }
    }

    $tujuan = PETA_TUJUAN . '/' . $icao . '.json';
    $lama   = is_file($tujuan) ? json_decode((string) file_get_contents($tujuan), true) : null;

    $data = [
        'bandara' => $db['nama'],
        'icao'    => $icao,
        /* Nama zona ikut dikirim supaya tidak perlu disalin ulang ke
           JavaScript: yang pendek untuk keterangan warna, yang resmi untuk
           popup. Hanya zona yang benar-benar ada pada bandara ini. */
        'zona'    => array_values(array_map(
            static fn($kode) => [
                'kode'  => $kode,
                'nama'  => BANDARA_KKOP_ZONA_PENDEK[$kode],
                'resmi' => BANDARA_KKOP_ZONA[$kode],
            ],
            array_values(array_filter(array_keys(BANDARA_KKOP_ZONA), static fn($k) => isset($ada[$k])))
        )),
        'kkop'    => ['type' => 'FeatureCollection', 'features' => $kkop],
    ];
    /* Lapisan yang tidak ikut pada berkas sumber kali ini dipertahankan. */
    $cakupanFitur = $cakupan ?? ($sumberCakupan ? null : ($lama['cakupan']['features'] ?? null));
    if ($cakupanFitur) {
        $data['cakupan'] = ['type' => 'FeatureCollection', 'features' => $cakupanFitur];
    }

    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        printf("  ✗ %-24s gagal menyusun JSON\n", $nama);
        $gagal++;
        continue;
    }

    if (!$ujiSaja && file_put_contents($tujuan, $json) === false) {
        printf("  ✗ %-24s gagal menulis %s\n", $nama, $tujuan);
        $gagal++;
        continue;
    }

    printf(
        "  ✓ %-24s → %s.json  %2d fitur KKOP · %d zona · cakupan %-5s · %5.1f KB  [%s: %s]\n",
        $nama,
        $icao,
        count($kkop),
        count($data['zona']),
        isset($data['cakupan']) ? 'ada' : 'tidak',
        strlen($json) / 1024,
        $cara,
        $db['nama']
    );
    $ditulis++;
}

echo "\n" . ($ujiSaja ? 'UJI — tidak ada berkas yang ditulis. ' : '')
   . "Siap: $ditulis bandara" . ($gagal ? ", gagal: $gagal" : '') . "\n";
echo 'Tujuan: ' . PETA_TUJUAN . "/\n";
