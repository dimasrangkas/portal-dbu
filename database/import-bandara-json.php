<?php
/**
 * ============================================================
 *  PENGIMPOR DATA RINCI BANDAR UDARA (berkas JSON resmi)
 *
 *  Membaca berkas bandara_part_*.json lalu mengisi tabel `bandaras`
 *  beserta lima tabel anaknya, sehingga seluruh tab pada halaman
 *  detail terisi data resmi.
 *
 *  Cara pakai:
 *    php database/import-bandara-json.php ~/Downloads/bandara_part_*.json
 *    php database/import-bandara-json.php --uji ~/Downloads/bandara_part_01.json
 *
 *  Pilihan:
 *    --uji         Hanya melaporkan; tidak ada satu pun tulisan ke basis data.
 *    --sunyi       Tanpa rincian per bandara, hanya ringkasan.
 *
 *  Sifat penting:
 *  - Aman dijalankan berulang. Bandara dicocokkan dengan baris yang sudah
 *    ada (lewat kode ICAO, lalu nama); TIDAK PERNAH menambah baris baru,
 *    karena 608 baris `bandaras` sudah lengkap dan id-nya dipakai tautan.
 *  - Baris anak milik bandara yang bersangkutan dihapus lebih dulu, lalu
 *    ditulis ulang. Jadi menjalankan ulang tidak menggandakan data.
 *  - Tabel `bandara_galeri` tidak disentuh sama sekali: fotonya diunggah
 *    operator lewat menu admin, tidak ada di berkas JSON.
 *  - Kolom yang tidak ada isinya di JSON dibiarkan apa adanya (tidak
 *    ditimpa NULL), supaya suntingan manual operator tidak hilang.
 * ============================================================
 */

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    exit("Skrip ini hanya untuk baris perintah.\n");
}

require __DIR__ . '/../shared/config.php';
require __DIR__ . '/../shared/db.php';

/* ---------- Argumen ---------- */
$berkas = [];
$ujiSaja = false;
$sunyi   = false;
foreach (array_slice($argv, 1) as $arg) {
    if ($arg === '--uji')        { $ujiSaja = true; continue; }
    if ($arg === '--sunyi')      { $sunyi   = true; continue; }
    if (str_starts_with($arg, '--')) {
        exit("Pilihan tidak dikenal: $arg\n");
    }
    $berkas[] = $arg;
}
if (!$berkas) {
    exit("Pemakaian: php database/import-bandara-json.php [--uji] [--sunyi] berkas.json ...\n");
}

/* ============================================================
   Pembantu penguraian
   ============================================================ */

/** Rapikan teks: buang spasi berlebih dan tanda "-" yang berarti kosong. */
function bersih(mixed $nilai): ?string
{
    if (is_array($nilai) || $nilai === null) {
        return null;
    }
    /* Data resmi banyak memuat "\r\n" dan spasi berderet dari salinan HTML. */
    $teks = preg_replace('/\s+/u', ' ', (string) $nilai);
    $teks = trim((string) $teks);
    return ($teks === '' || $teks === '-') ? null : $teks;
}

/**
 * Angka lalu lintas gaya laporan: "376 (2024)", "49.72 K (2024)",
 * "12.24 M (2024)", "1.454.25 K (2024)".
 *
 * Aturan yang terbaca dari seluruh 608 rekaman:
 * - Tanpa sufiks K/M, titik selalu pemisah ribuan  → 2.029 = 2029.
 * - Dengan sufiks, titik TERAKHIR diikuti 2 digit adalah tanda desimal,
 *   titik sebelumnya tetap pemisah ribuan → 1.454.25 K = 1.454,25 ribu.
 *
 * @return array{0: ?int, 1: ?int} [nilai, tahun]
 */
function urai_lalu_lintas(?string $teks): array
{
    $teks = bersih($teks);
    if ($teks === null) {
        return [null, null];
    }

    $tahun = null;
    if (preg_match('/\((\d{4})\)\s*$/', $teks, $m)) {
        $tahun = (int) $m[1];
        $teks  = trim(preg_replace('/\(\d{4}\)\s*$/', '', $teks) ?? '');
    }

    $pengali = 1;
    if (preg_match('/\s*([KM])$/i', $teks, $m)) {
        $pengali = strtoupper($m[1]) === 'M' ? 1000000 : 1000;
        $teks    = trim(preg_replace('/\s*[KM]$/i', '', $teks) ?? '');
    }

    if (!preg_match('/^[\d.]+$/', $teks)) {
        return [null, $tahun];
    }

    $potong = explode('.', $teks);
    if ($pengali > 1 && count($potong) > 1 && strlen(end($potong)) === 2) {
        /* Bagian terakhir adalah desimal. */
        $desimal = array_pop($potong);
        $bulat   = implode('', $potong);
        $angka   = (float) ($bulat . '.' . $desimal);
    } else {
        $angka = (float) implode('', $potong);
    }

    return [(int) round($angka * $pengali), $tahun];
}

/**
 * "67.883,00 m2" → 67883.00 ; "1.039,24 m2" → 1039.24
 *
 * Angkanya diambil lewat pencocokan pola, bukan dengan membuang karakter
 * non-angka: satuannya ditulis "m2", sehingga penyaringan karakter akan
 * ikut menyedot angka 2 dan menghasilkan 67.883,002.
 */
function urai_luas(?string $teks): ?float
{
    $teks = bersih($teks);
    if ($teks === null) {
        return null;
    }
    if (!preg_match('/\d[\d.]*(?:,\d+)?/', $teks, $m)) {
        return null;
    }
    $angka = str_replace('.', '', $m[0]);    // pemisah ribuan
    $angka = str_replace(',', '.', $angka);  // tanda desimal
    return is_numeric($angka) ? (float) $angka : null;
}

/**
 * Pisahkan "Runway (RUNWAY 02-20)" menjadi kategori dan label.
 * Tanpa tanda kurung, seluruh teks menjadi kategori sekaligus label.
 *
 * @return array{0: string, 1: string} [kategori, label]
 */
function pisah_nama(string $nama): array
{
    $nama = (string) preg_replace('/\s+/u', ' ', trim($nama));
    if (preg_match('/^(.*?)\s*\((.*)\)$/u', $nama, $m)) {
        $kategori = trim($m[1]);
        $label    = trim($m[2]);
        /* Sebagian data menulis tanda kurung berisi "-" ketika bangunannya
           tidak bernama khusus; pakai nama kategorinya saja. */
        if ($kategori !== '' && $label !== '' && $label !== '-') {
            return [$kategori, $label];
        }
        if ($kategori !== '') {
            return [$kategori, $kategori];
        }
    }
    return [$nama, $nama];
}

/** "Runway End Safety Area" → "runway-end-safety-area" */
function siput(string $teks): string
{
    $teks = mb_strtolower(trim($teks), 'UTF-8');
    $teks = preg_replace('/[^a-z0-9]+/u', '-', $teks) ?? '';
    return trim($teks, '-');
}

/**
 * Ikon Bootstrap Icons untuk label fasilitas umum.
 *
 * Berkas JSON memberi `ikon_url` berupa PNG di server hubud; menautkannya
 * berarti halaman kita bergantung pada server orang lain. Karena label yang
 * dipakai terbatas (71 macam), lebih tahan lama memetakannya ke Bootstrap
 * Icons yang sudah dimuat tema. Label di luar daftar memakai ikon umum.
 */
function ikon_fasilitas(string $label): string
{
    static $peta = [
        'toilet'             => 'bi-badge-wc',
        'kamar mandi'        => 'bi-badge-wc',
        'toilet disabilitas' => 'bi-person-wheelchair',
        'fasilitas difabel'  => 'bi-person-wheelchair',
        'mushola'            => 'bi-moon-stars',
        'masjid'             => 'bi-moon-stars',
        'gereja'             => 'bi-building',
        'area parkir'        => 'bi-p-square',
        'parkir inap'        => 'bi-p-square',
        'kantin'             => 'bi-cup-hot',
        'restoran'           => 'bi-shop',
        'food court'         => 'bi-shop',
        'coffee shop'        => 'bi-cup-hot',
        'snack bar'          => 'bi-cup-straw',
        'atm'                => 'bi-cash-coin',
        'atm center'         => 'bi-cash-coin',
        'bank'               => 'bi-bank',
        'money changer'      => 'bi-currency-exchange',
        'koperasi'           => 'bi-bank',
        'area merokok'       => 'bi-cloud-haze2',
        'ruang menyusui'     => 'bi-heart',
        'charging station'   => 'bi-plug',
        'free wifi'          => 'bi-wifi',
        'play ground'        => 'bi-balloon',
        'taman'              => 'bi-tree',
        'gazebo'             => 'bi-tree',
        'fasilitas medis'    => 'bi-hospital',
        'klinik'             => 'bi-hospital',
        'apotek'             => 'bi-capsule',
        'toko suvenir'       => 'bi-bag',
        'pusat oleh-oleh'    => 'bi-bag-heart',
        'umkm'               => 'bi-bag',
        'minimarket'         => 'bi-cart',
        'telepon umum'       => 'bi-telephone',
        'kantor pos'         => 'bi-envelope',
        'informasi cuaca'    => 'bi-cloud-sun',
        'tv hiburan'         => 'bi-tv',
        'pojok baca'         => 'bi-book',
        'galeri seni'        => 'bi-easel',
        'hotel'              => 'bi-house-door',
        'homestay'           => 'bi-house-door',
        'lounge'             => 'bi-star',
        'cip lounge'         => 'bi-star',
        'car rental'         => 'bi-car-front',
        'agen wisata'        => 'bi-compass',
        'agen perjalanan'    => 'bi-compass',
        'customer service'   => 'bi-headset',
        'concierge express'  => 'bi-headset',
        'self check in'      => 'bi-ui-checks',
        'autogate'           => 'bi-door-open',
        'baggage wrapping'   => 'bi-box-seam',
        'penitipan barang'   => 'bi-box-seam',
        'way finding'        => 'bi-signpost-2',
        'area pedestrian'    => 'bi-person-walking',
        'halte bus'          => 'bi-bus-front',
        'bus shuttle'        => 'bi-bus-front',
        'curbside'           => 'bi-sign-turn-right',
        'co-working space'   => 'bi-laptop',
        'perkantoran'        => 'bi-building',
        'fasilitas air minum'=> 'bi-droplet',
        'kantor karantina'   => 'bi-shield-plus',
        'kantor kesehatan pelabuhan' => 'bi-shield-plus',
        'kantor bea cukai'   => 'bi-briefcase',
        'kantor imigrasi'    => 'bi-passport',
        'digital banner'     => 'bi-display',
        'billboard'          => 'bi-display',
        'waving gallery'     => 'bi-binoculars',
        'fast line track'    => 'bi-lightning',
        'reservasi intermoda'=> 'bi-calendar-check',
        'flight information display system (fids)' => 'bi-airplane',
        'tourist informastion center' => 'bi-info-circle',
    ];
    return $peta[mb_strtolower(trim($label), 'UTF-8')] ?? 'bi-dot';
}

/** Ikon Bootstrap Icons untuk moda transportasi. */
function ikon_transportasi(string $label): string
{
    static $peta = [
        'taksi'                    => 'bi-taxi-front',
        'mobil sewa'               => 'bi-car-front',
        'ojek'                     => 'bi-scooter',
        'ojek online'              => 'bi-scooter',
        'sepeda motor'             => 'bi-scooter',
        'angkutan kota'            => 'bi-bus-front',
        'bus'                      => 'bi-bus-front',
        'damri'                    => 'bi-bus-front',
        'blu trans'                => 'bi-bus-front',
        'travel'                   => 'bi-car-front',
        'kereta bandara'           => 'bi-train-front',
        'kereta api'               => 'bi-train-front',
        'light rail transit (lrt)' => 'bi-train-lightrail-front',
        'speed boat'               => 'bi-water',
        'long boat'                => 'bi-water',
        'perahu'                   => 'bi-water',
        'ketinting'                => 'bi-water',
        'kapal laut'               => 'bi-tsunami',
        'kapal perintis'           => 'bi-tsunami',
        'becak'                    => 'bi-bicycle',
        'bentor'                   => 'bi-bicycle',
    ];
    return $peta[mb_strtolower(trim($label), 'UTF-8')] ?? 'bi-signpost';
}

/* ============================================================
   Muat data yang sudah ada untuk pencocokan
   ============================================================ */

$adaKolom = static function (string $tabel, string $kolom): bool {
    return (bool) db_value(
        'SELECT COUNT(*) FROM information_schema.COLUMNS
          WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?',
        [$tabel, $kolom]
    );
};

foreach ([['bandaras', 'pesawat_beroperasi'], ['bandara_rute', 'keberangkatan'],
          ['bandara_fasilitas_udara', 'declare_distance']] as [$t, $k]) {
    if (!$adaKolom($t, $k)) {
        exit("Kolom `$t`.`$k` belum ada.\n"
           . "Jalankan dulu:\n"
           . "  mysql -u root -h 127.0.0.1 " . DB_NAME . " < database/migrate-detail-bandara.sql\n"
           . "  mysql -u root -h 127.0.0.1 " . DB_NAME . " < database/migrate-detail-bandara-lanjutan.sql\n");
    }
}

$barisDb = db_all('SELECT id, nama, kode_icao FROM bandaras');
$perIcao = [];
$perNama = [];
foreach ($barisDb as $r) {
    $icao = strtoupper(trim((string) $r['kode_icao']));
    if ($icao !== '' && $icao !== '-') {
        $perIcao[$icao][] = (int) $r['id'];
    }
    $nama = mb_strtoupper(trim((string) $r['nama']), 'UTF-8');
    if ($nama !== '') {
        $perNama[$nama][] = (int) $r['id'];
    }
}

/* ============================================================
   Impor
   ============================================================ */

$jumlah = ['bandara' => 0, 'rute' => 0, 'udara' => 0, 'darat' => 0, 'umum' => 0, 'transportasi' => 0];
$lewat  = [];
$ganda  = [];

if (!$ujiSaja) {
    db()->beginTransaction();
}

foreach ($berkas as $jalur) {
    if (!is_readable($jalur)) {
        exit("Tidak bisa membaca berkas: $jalur\n");
    }
    $isi = json_decode((string) file_get_contents($jalur), true);
    if (!is_array($isi) || !isset($isi['bandara']) || !is_array($isi['bandara'])) {
        exit("Bukan berkas bagian bandara yang sah: $jalur\n");
    }

    printf("%s  (bagian %s, %d bandara)\n",
        basename($jalur), (string) ($isi['part'] ?? '?'), count($isi['bandara']));

    foreach ($isi['bandara'] as $b) {
        $info = is_array($b['informasi_bandara'] ?? null) ? $b['informasi_bandara'] : [];
        $umum = is_array($b['data_umum'] ?? null) ? $b['data_umum'] : [];

        $nama = bersih($info['Nama Bandar Udara'] ?? $umum['Nama Bandar Udara'] ?? null) ?? '';
        $icao = strtoupper((string) (bersih($info['Kode ICAO'] ?? null) ?? ''));

        /* --- Cocokkan ke baris yang sudah ada --- */
        $kandidat = [];
        if ($icao !== '' && isset($perIcao[$icao])) {
            $kandidat = $perIcao[$icao];
        } elseif ($nama !== '' && isset($perNama[mb_strtoupper($nama, 'UTF-8')])) {
            $kandidat = $perNama[mb_strtoupper($nama, 'UTF-8')];
        }
        if (!$kandidat) {
            $lewat[] = "$nama [$icao]";
            continue;
        }
        if (count($kandidat) > 1) {
            $ganda[] = "$nama [$icao] → " . count($kandidat) . ' baris';
            continue;
        }
        $id = $kandidat[0];

        /* --- Tab "Data Umum" pada tabel induk --- */
        [$nPesawat,   $thn1] = urai_lalu_lintas($info['Pesawat']   ?? null);
        [$nPenumpang, $thn2] = urai_lalu_lintas($info['Penumpang'] ?? null);
        [$nKargo,     $thn3] = urai_lalu_lintas($info['Kargo']     ?? null);

        $isian = [
            'nama'               => $nama !== '' ? $nama : null,
            'kode_iata'          => bersih($info['Kode IATA'] ?? null),
            'kode_icao'          => $icao !== '' ? $icao : null,
            'kelas'              => bersih($info['Kelas'] ?? null),
            'pengelola'          => bersih($info['Pengelola'] ?? $umum['Pengelola'] ?? null),
            'kantor_otoritas'    => bersih($info['Kantor Otoritas'] ?? null),
            'email'              => bersih($info['Email'] ?? null),
            'telepon'            => bersih($info['Telepon'] ?? null),
            'fax'                => bersih($info['Fax'] ?? null),
            'website'            => bersih($info['Website'] ?? null),
            'status_operasi'     => bersih($umum['Status Operasi'] ?? null),
            'tipe'               => bersih($umum['Penggunaan'] ?? null),
            'provinsi'           => bersih($umum['Provinsi'] ?? null),
            'kabupaten_kota'     => bersih($umum['Kabupaten / Kota'] ?? null),
            'kecamatan'          => bersih($umum['Kecamatan'] ?? null),
            'kelurahan_desa'     => bersih($umum['Kelurahan / Desa'] ?? null),
            'alamat'             => bersih($umum['Alamat Bandar Udara'] ?? null),
            'lokasi_arp'         => bersih($umum['Lokasi (ARP)'] ?? null),
            'critical_aircraft'  => bersih($umum['Critical Aircraft'] ?? null),
            'pesawat_beroperasi' => bersih($umum['Pesawat Beroperasi'] ?? null),
            'hierarki'           => bersih($umum['Hierarki'] ?? null),
            'klasifikasi'        => bersih($umum['Klasifikasi'] ?? null),
            'pkp_pk'             => bersih($umum['PKP-PK'] ?? null),
            'airnav'             => bersih($umum['Airnav Indonesia'] ?? null),
            'status_blu'         => bersih($umum['Status BLU'] ?? null),
            'dokumen_pendukung'  => bersih($umum['Dokumen Pendukung'] ?? null),
            'jumlah_pesawat'     => $nPesawat,
            'jumlah_penumpang'   => $nPenumpang,
            'jumlah_kargo'       => $nKargo,
            'tahun_data'         => $thn1 ?? $thn2 ?? $thn3,
        ];
        /* Medan kosong dibuang: lebih baik mempertahankan isi lama daripada
           menimpanya dengan NULL. */
        $isian = array_filter($isian, static fn($v) => $v !== null);

        if (!$ujiSaja && $isian) {
            db_update('bandaras', $isian, $id);
        }
        $jumlah['bandara']++;

        /* --- Kosongkan baris anak milik bandara ini (galeri tidak disentuh) ---
           db_delete() hanya menghapus per id, sedangkan di sini yang dibuang
           adalah seluruh baris milik satu bandara, jadi dijalankan langsung. */
        if (!$ujiSaja) {
            foreach (['bandara_rute', 'bandara_fasilitas_udara',
                      'bandara_fasilitas_darat', 'bandara_fasilitas_umum'] as $tabel) {
                db()->prepare("DELETE FROM `$tabel` WHERE `bandara_id` = ?")->execute([$id]);
            }
        }

        /* --- Tab "Rute Domestik" --- */
        $urut = 0;
        foreach ($b['rute_domestik'] ?? [] as $r) {
            if (!is_array($r)) {
                continue;
            }
            $tujuan = bersih($r['kedatangan'] ?? null);
            if ($tujuan === null) {
                continue;
            }
            $baris = [
                'bandara_id'    => $id,
                'keberangkatan' => bersih($r['keberangkatan'] ?? null),
                'tujuan'        => $tujuan,
                'maskapai'      => bersih($r['maskapai'] ?? null),
                'pesawat'       => bersih($r['pesawat'] ?? null),
                'frekuensi'     => bersih($r['frekuensi/minggu'] ?? null),
                'sort'          => $urut++,
            ];
            if (!$ujiSaja) {
                db_insert('bandara_rute', $baris);
            }
            $jumlah['rute']++;
        }

        /* --- Tab "Fasilitas Udara" --- */
        $urutKategori = [];
        foreach ($b['fasilitas_udara'] ?? [] as $f) {
            if (!is_array($f) || !isset($f['nama'])) {
                continue;
            }
            [$kategori, $label] = pisah_nama((string) $f['nama']);
            $spec = is_array($f['spesifikasi'] ?? null) ? $f['spesifikasi'] : [];
            $kat  = siput($kategori);

            $dd = $spec['Declare Distance'] ?? null;
            $baris = [
                'bandara_id'         => $id,
                'kategori'           => $kat,
                'nama'               => mb_substr($label, 0, 160),
                /* Sebagian fasilitas dinyatakan dengan luas, bukan dimensi. */
                'dimensi_verifikasi' => bersih($spec['Dimensi Terverifikasi'] ?? $spec['Luas Terverifikasi'] ?? null),
                'dimensi_terbangun'  => bersih($spec['Dimensi Terbangun'] ?? $spec['Luas Terbangun'] ?? null),
                'konstruksi'         => bersih($spec['Konstruksi'] ?? null),
                'daya_dukung'        => bersih($spec['Daya Dukung'] ?? null),
                'critical_aircraft'  => bersih($spec['Critical Aircraft'] ?? null),
                'pesawat_beroperasi' => bersih($spec['Pesawat Beroperasi'] ?? null),
                'azimuth'            => bersih($spec['Azimuth'] ?? null),
                'tipe_runway'        => bersih($spec['Tipe Runway'] ?? null),
                'declare_distance'   => is_array($dd) && $dd
                    ? json_encode($dd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'sort'               => $urutKategori[$kat] = ($urutKategori[$kat] ?? -1) + 1,
            ];
            if (!$ujiSaja) {
                db_insert('bandara_fasilitas_udara', $baris);
            }
            $jumlah['udara']++;
        }

        /* --- Tab "Fasilitas Darat" --- */
        $urutKategori = [];
        foreach ($b['fasilitas_darat'] ?? [] as $f) {
            if (!is_array($f) || !isset($f['nama'])) {
                continue;
            }
            [$kategori, $label] = pisah_nama((string) $f['nama']);
            $spec = is_array($f['spesifikasi'] ?? null) ? $f['spesifikasi'] : [];
            $kat  = siput($kategori);

            $baris = [
                'bandara_id' => $id,
                'kategori'   => $kat,
                'nama'       => mb_substr($label, 0, 255),
                'luas'       => urai_luas($spec['Luas'] ?? null),
                'kapasitas'  => bersih($spec['Kapasitas Tahunan'] ?? null),
                'keterangan' => bersih($spec['Kategori'] ?? null),
                'sort'       => $urutKategori[$kat] = ($urutKategori[$kat] ?? -1) + 1,
            ];
            if (!$ujiSaja) {
                db_insert('bandara_fasilitas_darat', $baris);
            }
            $jumlah['darat']++;
        }

        /* --- Tab "Fasilitas Umum" --- */
        $urut = 0;
        foreach ($b['fasilitas_umum'] ?? [] as $f) {
            $label = bersih(is_array($f) ? ($f['label'] ?? null) : $f);
            if ($label === null) {
                continue;
            }
            $baris = [
                'bandara_id' => $id,
                'jenis'      => 'umum',
                'nama'       => mb_substr($label, 0, 160),
                'icon'       => ikon_fasilitas($label),
                'sort'       => $urut++,
            ];
            if (!$ujiSaja) {
                db_insert('bandara_fasilitas_umum', $baris);
            }
            $jumlah['umum']++;
        }

        /* --- Tab "Transportasi" — satu tabel dengan fasilitas umum --- */
        $urut = 0;
        foreach ($b['transportasi'] ?? [] as $f) {
            $label = bersih(is_array($f) ? ($f['label'] ?? null) : $f);
            if ($label === null) {
                continue;
            }
            $baris = [
                'bandara_id' => $id,
                'jenis'      => 'transportasi',
                'nama'       => mb_substr($label, 0, 160),
                'icon'       => ikon_transportasi($label),
                'sort'       => $urut++,
            ];
            if (!$ujiSaja) {
                db_insert('bandara_fasilitas_umum', $baris);
            }
            $jumlah['transportasi']++;
        }

        if (!$sunyi) {
            printf("  #%-4d %-42s rute:%-3d udara:%-3d darat:%-3d umum:%-3d transportasi:%d\n",
                $id, mb_substr($nama, 0, 42), count($b['rute_domestik'] ?? []),
                count($b['fasilitas_udara'] ?? []), count($b['fasilitas_darat'] ?? []),
                count($b['fasilitas_umum'] ?? []), count($b['transportasi'] ?? []));
        }
    }
}

if (!$ujiSaja) {
    db()->commit();
}

/* ============================================================
   Ringkasan
   ============================================================ */
echo "\n" . str_repeat('-', 60) . "\n";
echo $ujiSaja ? "UJI COBA — tidak ada yang ditulis ke basis data.\n" : "Selesai ditulis.\n";
printf("  bandara diperbarui : %d\n", $jumlah['bandara']);
printf("  rute domestik      : %d\n", $jumlah['rute']);
printf("  fasilitas udara    : %d\n", $jumlah['udara']);
printf("  fasilitas darat    : %d\n", $jumlah['darat']);
printf("  fasilitas umum     : %d\n", $jumlah['umum']);
printf("  transportasi       : %d\n", $jumlah['transportasi']);

if ($lewat) {
    printf("\n  %d bandara dilewati (tidak ada padanannya di tabel):\n", count($lewat));
    foreach (array_slice($lewat, 0, 20) as $x) {
        echo "    - $x\n";
    }
    if (count($lewat) > 20) {
        printf("    ... dan %d lainnya\n", count($lewat) - 20);
    }
}
if ($ganda) {
    printf("\n  %d bandara dilewati (padanannya lebih dari satu):\n", count($ganda));
    foreach (array_slice($ganda, 0, 20) as $x) {
        echo "    - $x\n";
    }
}
