<?php
/* ============================================================
   Pengelola konten portal

   Satu berkas untuk seluruh isi situs. Susunannya mengikuti basis data:

       kategori (halaman)  →  sub kategori (seksi)  →  konten

   Tiap halaman CMS punya tombol "Tambah Konten" pada setiap seksinya,
   dan urutan tampil di portal — baik urutan seksi maupun urutan konten
   di dalamnya — diatur dari sini.

   Kolom formulir tiap seksi ditentukan cetak biru di config/blueprints.php.
   ============================================================ */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/fields.php';
require_once __DIR__ . '/includes/upload.php';

$user = require_login();

/* ------------------------------------------------------------
   Cetak biru
   ------------------------------------------------------------ */
function blueprints(): array
{
    static $all = null;
    if ($all === null) {
        $all = require __DIR__ . '/config/blueprints.php';
    }
    return $all;
}

function blueprint(string $key): array
{
    $all = blueprints();
    $bp  = $all[$key] ?? $all['umum'];
    return $bp + ['list' => ['title' => 'Judul'], 'fields' => [], 'anak' => [], 'urut' => 'k.sort, k.id'];
}

/** Kolom asli tabel `contents`? Selebihnya masuk ke JSON `extra`. */
function kolom_asli(string $nama): bool
{
    return in_array($nama, KOLOM_KONTEN, true);
}

/* ------------------------------------------------------------
   Pembacaan kategori & sub kategori (versi admin: ikut yang nonaktif)
   ------------------------------------------------------------ */
function kat_row(string $slug): ?array
{
    return db_one('SELECT * FROM categories WHERE slug = ?', [$slug]);
}

function sub_rows(int $catId): array
{
    return db_all('SELECT * FROM subcategories WHERE category_id = ? ORDER BY sort, id', [$catId]);
}

function sub_row(int $catId, string $slug): ?array
{
    return db_one('SELECT * FROM subcategories WHERE category_id = ? AND slug = ?', [$catId, $slug]);
}

/** Jumlah konten per sub kategori. */
function jumlah_konten(int $catId): array
{
    $out = [];
    foreach (db_all(
        'SELECT subcategory_id, COUNT(*) n FROM contents WHERE category_id = ? GROUP BY subcategory_id',
        [$catId]
    ) as $row) {
        $out[(int) $row['subcategory_id']] = (int) $row['n'];
    }
    return $out;
}

/* ------------------------------------------------------------
   Penyimpanan satu konten
   ------------------------------------------------------------ */

/**
 * Baca kolom formulir lalu simpan sebagai satu baris `contents`.
 * $prefix dipakai baris anak, mis. "children[persyaratan][0]".
 */
function simpan_konten(array $fields, array $ctx, int $id, string $prefix = ''): int
{
    $lama  = $id > 0 ? db_one('SELECT * FROM contents WHERE id = ?', [$id]) : null;
    $extra = $lama && $lama['extra'] ? (json_decode($lama['extra'], true) ?: []) : [];

    $data = [
        'category_id'    => $ctx['category_id'],
        'subcategory_id' => $ctx['subcategory_id'],
    ];
    if (array_key_exists('parent_id', $ctx)) {
        $data['parent_id'] = $ctx['parent_id'];
    }
    $tagInput = null;

    foreach ($fields as $nama => $field) {
        $input = $prefix !== '' ? $prefix . "[$nama]" : $nama;

        if (($field['type'] ?? '') === 'tags') {
            $tagInput = (string) (post_get($input) ?? '');
            continue;
        }

        [$simpan, $nilai] = field_value($input, $field, null);

        if (kolom_asli($nama)) {
            if ($simpan) {
                $data[$nama] = $nilai;
            }
            continue;
        }
        /* Kolom khusus → JSON extra. Unggahan yang tidak diubah tetap dipakai. */
        if (!$simpan) {
            continue;
        }
        if ($nilai === null || $nilai === '') {
            unset($extra[$nama]);
        } else {
            $extra[$nama] = $nilai;
        }
    }

    /* Slug otomatis bila dikosongkan. */
    foreach ($fields as $nama => $field) {
        if (($field['type'] ?? '') === 'slug' && empty($data[$nama])) {
            $sumber = $field['from'] ?? '';
            $asal   = $sumber !== '' ? (string) (post_get($prefix !== '' ? $prefix . "[$sumber]" : $sumber) ?? '') : '';
            $data[$nama] = slugify($asal !== '' ? $asal : 'item-' . time());
        }
    }

    $data['extra'] = $extra ? json_encode($extra, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null;

    if ($id > 0) {
        db_update('contents', $data, $id);
    } else {
        if (!isset($data['sort'])) {
            $data['sort'] = (int) db_value(
                'SELECT COALESCE(MAX(sort), 0) + 10 FROM contents WHERE category_id = ? AND subcategory_id <=> ?',
                [$ctx['category_id'], $ctx['subcategory_id']],
                0
            );
        }
        $id = db_insert('contents', $data);
    }

    if ($tagInput !== null) {
        simpan_tag($id, $tagInput);
    }
    return $id;
}

/** Sinkronkan tagar satu konten; tag baru dibuat otomatis. */
function simpan_tag(int $kontenId, string $daftar): void
{
    $ids = [];
    foreach (split_list($daftar) as $label) {
        $slug = slugify($label);
        $tag  = db_one('SELECT id FROM tags WHERE slug = ?', [$slug]);
        $ids[] = $tag
            ? (int) $tag['id']
            : db_insert('tags', ['slug' => $slug, 'label' => $label,
                                 'sort' => (int) db_value('SELECT COALESCE(MAX(sort), 0) + 10 FROM tags', [], 0)]);
    }
    db_exec('DELETE FROM content_tags WHERE content_id = ?', [$kontenId]);
    foreach (array_unique($ids) as $tagId) {
        db_exec('INSERT IGNORE INTO content_tags (content_id, tag_id) VALUES (?, ?)', [$kontenId, $tagId]);
    }
}

/**
 * Hapus satu konten beserta berkas unggahannya.
 *
 * Baris anak yang memang disunting di dalam formulir konten ini
 * (daftar `anak` pada cetak birunya) ikut terhapus lewat foreign key.
 * Anak lain — mis. foto yang kebetulan berada di dalam album — hanya
 * dilepas dari induknya, persis seperti perilaku ON DELETE SET NULL
 * pada skema lama: menghapus album tidak menghapus fotonya.
 */
function hapus_konten(array $row, array $fields, array $anakSlug = []): void
{
    $id = (int) $row['id'];

    $lepas = db_all(
        'SELECT k.id FROM contents k
         LEFT JOIN subcategories s ON s.id = k.subcategory_id
         WHERE k.parent_id = ?' . ($anakSlug
            ? ' AND (s.slug IS NULL OR s.slug NOT IN (' . implode(',', array_fill(0, count($anakSlug), '?')) . '))'
            : ''),
        array_merge([$id], $anakSlug)
    );
    foreach ($lepas as $anak) {
        db_exec('UPDATE contents SET parent_id = NULL WHERE id = ?', [(int) $anak['id']]);
    }

    /* Berkas milik baris anak yang ikut terhapus. */
    foreach (db_all('SELECT id, image, file, extra FROM contents WHERE parent_id = ?', [$id]) as $anak) {
        delete_upload($anak['image'] ?: null);
        delete_upload($anak['file'] ?: null);
    }

    $extra = $row['extra'] ? (json_decode($row['extra'], true) ?: []) : [];
    foreach ($fields as $nama => $field) {
        if (!in_array($field['type'] ?? '', ['image', 'file', 'video'], true)) {
            continue;
        }
        delete_upload((kolom_asli($nama) ? $row[$nama] : ($extra[$nama] ?? null)) ?: null);
    }
    db_delete('contents', $id);
}

/* ------------------------------------------------------------
   Konteks permintaan
   ------------------------------------------------------------ */
$aksi    = $_GET['a'] ?? 'daftar';
$katSlug = trim($_GET['kategori'] ?? '');
$subSlug = trim($_GET['sub'] ?? '');

/* ============================================================
   KATEGORI (halaman)
   ============================================================ */
const KATEGORI_FIELDS = [
    'label'            => ['label' => 'Nama Halaman', 'type' => 'text', 'required' => true, 'col' => 'half'],
    'slug'             => ['label' => 'Kunci Halaman', 'type' => 'slug', 'from' => 'label', 'required' => true, 'col' => 'half',
                           'hint'  => 'Dipakai halaman portal lewat page_start() — ubah hanya bila berkasnya ikut disesuaikan'],
    'icon'             => ['label' => 'Ikon', 'type' => 'icon', 'col' => 'half',
                           'hint'  => 'Nama ikon Bootstrap Icons, contoh: bi-newspaper'],
    'page_file'        => ['label' => 'Berkas Halaman', 'type' => 'text', 'col' => 'half',
                           'hint'  => 'Relatif terhadap folder frontend, contoh: pages/berita.php'],
    'meta_title'       => ['label' => 'Judul Tab / SEO', 'type' => 'text'],
    'meta_description' => ['label' => 'Deskripsi SEO', 'type' => 'textarea'],
    'eyebrow'          => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
    'breadcrumb'       => ['label' => 'Teks Remah Roti', 'type' => 'text', 'col' => 'half'],
    'heading'          => ['label' => 'Judul Besar Halaman', 'type' => 'text'],
    'subtitle'         => ['label' => 'Sub Judul', 'type' => 'textarea'],
    'sort'             => ['label' => 'Urutan Menu', 'type' => 'number', 'col' => 'half', 'default' => 0],
    'is_active'        => ['label' => 'Aktif', 'type' => 'checkbox', 'default' => 1, 'col' => 'half'],
];

if ($aksi === 'kategori-simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id   = (int) ($_POST['id'] ?? 0);
    $data = [];
    foreach (KATEGORI_FIELDS as $nama => $field) {
        [$simpan, $nilai] = field_value($nama, $field, null);
        if ($simpan) {
            $data[$nama] = $nilai;
        }
    }
    if (empty($data['slug'])) {
        $data['slug'] = slugify((string) ($_POST['label'] ?? 'halaman'));
    }
    try {
        $id > 0 ? db_update('categories', $data, $id) : $id = db_insert('categories', $data);
    } catch (PDOException $ex) {
        flash('danger', 'Gagal menyimpan: kunci halaman sudah dipakai.');
        redirect(admin_url('konten.php'));
    }
    flash('success', 'Halaman berhasil disimpan.');
    redirect(admin_url('konten.php?kategori=' . urlencode($data['slug'])));
}

if ($aksi === 'kategori-hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id  = (int) ($_POST['id'] ?? 0);
    $row = db_one('SELECT * FROM categories WHERE id = ?', [$id]);
    $isi = (int) db_value('SELECT COUNT(*) FROM contents WHERE category_id = ?', [$id], 0);
    if ($row && $isi === 0) {
        db_delete('categories', $id);
        flash('success', 'Halaman berhasil dihapus.');
    } else {
        flash('danger', $row ? "Halaman masih berisi $isi konten — kosongkan lebih dulu." : 'Halaman tidak ditemukan.');
    }
    redirect(admin_url('index.php'));
}

if ($aksi === 'kategori-form') {
    $row   = $katSlug !== '' ? kat_row($katSlug) : null;
    $baru  = !$row;
    $judul = $baru ? 'Tambah Halaman' : 'Pengaturan Halaman ' . $row['label'];
    admin_head($judul, [['label' => 'Konten'], ['label' => $judul]]);
    ?>
    <div class="page-head">
      <div><h1><?= e($judul) ?></h1><p>Judul, alamat, dan meta SEO halaman portal.</p></div>
      <div class="page-head-actions">
        <?php if (!$baru): ?>
        <a class="btn btn-ghost" href="<?= e(admin_url('konten.php?kategori=' . urlencode($row['slug']))) ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
        <?php endif; ?>
      </div>
    </div>
    <form class="card form" method="post" action="<?= e(admin_url('konten.php?a=kategori-simpan')) ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">
      <div class="form-grid">
        <?php foreach (KATEGORI_FIELDS as $nama => $field): ?>
        <?php render_field($nama, $field, $row[$nama] ?? ($field['default'] ?? '')); ?>
        <?php endforeach; ?>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Halaman</button>
        <?php if (!$baru): ?>
        <button type="submit" class="btn btn-ghost" formaction="<?= e(admin_url('konten.php?a=kategori-hapus')) ?>" formnovalidate
                name="id" value="<?= (int) $row['id'] ?>"
                onclick="return confirm('Hapus halaman ini beserta seluruh seksinya?')">Hapus Halaman</button>
        <?php endif; ?>
      </div>
    </form>
    <?php
    admin_foot();
    exit;
}

/* Mulai di sini seluruh aksi butuh kategori yang sah. */
$kat = kat_row($katSlug);
if (!$kat) {
    flash('danger', 'Halaman tidak dikenali.');
    redirect(admin_url('index.php'));
}
$katUrl = admin_url('konten.php?kategori=' . urlencode($kat['slug']));

/* ============================================================
   SUB KATEGORI (seksi)
   ============================================================ */
function sub_fields(): array
{
    $pilihan = [];
    foreach (array_keys(blueprints()) as $key) {
        $pilihan[$key] = $key;
    }
    return [
        'label'     => ['label' => 'Nama Seksi', 'type' => 'text', 'required' => true, 'col' => 'half',
                        'hint'  => 'Nama yang dipakai di panel CMS'],
        'slug'      => ['label' => 'Kunci Seksi', 'type' => 'slug', 'from' => 'label', 'required' => true, 'col' => 'half',
                        'hint'  => 'Dipakai halaman portal, mis. sections() dan konten()'],
        'eyebrow'   => ['label' => 'Label Atas di Portal', 'type' => 'text', 'col' => 'half'],
        'title'     => ['label' => 'Judul Seksi di Portal', 'type' => 'text', 'col' => 'half'],
        'subtitle'  => ['label' => 'Sub Judul', 'type' => 'textarea'],
        'body'      => ['label' => 'Teks Seksi', 'type' => 'textarea',
                        'hint'  => 'Dipakai sebagian seksi untuk teks panjang, mis. Tugas Pokok'],
        'blueprint' => ['label' => 'Jenis Konten', 'type' => 'select', 'options' => $pilihan, 'default' => 'umum', 'col' => 'half',
                        'hint'  => 'Menentukan kolom yang muncul saat menambah konten'],
        'icon'      => ['label' => 'Ikon', 'type' => 'icon', 'col' => 'half'],
        'hint'      => ['label' => 'Keterangan untuk Editor', 'type' => 'text'],
        'sort'      => ['label' => 'Urutan Seksi', 'type' => 'number', 'col' => 'half', 'default' => 0,
                        'hint'  => 'Angka kecil tampil lebih dahulu'],
        'is_active' => ['label' => 'Tampilkan di situs', 'type' => 'checkbox', 'default' => 1, 'col' => 'half'],
    ];
}

if ($aksi === 'sub-simpan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id   = (int) ($_POST['id'] ?? 0);
    $data = ['category_id' => (int) $kat['id']];
    foreach (sub_fields() as $nama => $field) {
        [$simpan, $nilai] = field_value($nama, $field, null);
        if ($simpan) {
            $data[$nama] = $nilai;
        }
    }
    if (empty($data['slug'])) {
        $data['slug'] = slugify((string) ($_POST['label'] ?? 'seksi'));
    }
    $data['blueprint'] = $data['blueprint'] ?: 'umum';
    try {
        $id > 0 ? db_update('subcategories', $data, $id) : $id = db_insert('subcategories', $data);
    } catch (PDOException $ex) {
        flash('danger', 'Gagal menyimpan: kunci seksi sudah dipakai pada halaman ini.');
        redirect($katUrl);
    }
    flash('success', 'Seksi berhasil disimpan.');
    redirect($katUrl . '&sub=' . urlencode($data['slug']));
}

if ($aksi === 'sub-hapus' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id  = (int) ($_POST['id'] ?? 0);
    $isi = (int) db_value('SELECT COUNT(*) FROM contents WHERE subcategory_id = ?', [$id], 0);
    if ($isi > 0) {
        flash('danger', "Seksi masih berisi $isi konten — hapus atau pindahkan lebih dulu.");
    } else {
        db_delete('subcategories', $id);
        flash('success', 'Seksi berhasil dihapus.');
    }
    redirect($katUrl);
}

if ($aksi === 'sub-urutan' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (($_POST['sort'] ?? []) as $id => $sort) {
        db_exec('UPDATE subcategories SET sort = ? WHERE id = ? AND category_id = ?',
                [(int) $sort, (int) $id, (int) $kat['id']]);
    }
    flash('success', 'Urutan seksi berhasil diperbarui.');
    redirect($katUrl);
}

if ($aksi === 'sub-form') {
    $row   = $subSlug !== '' ? sub_row((int) $kat['id'], $subSlug) : null;
    $baru  = !$row;
    $judul = $baru ? 'Tambah Seksi — ' . $kat['label'] : 'Ubah Seksi ' . $row['label'];
    admin_head($judul, [['label' => $kat['label'], 'url' => $katUrl], ['label' => $baru ? 'Tambah Seksi' : $row['label']]]);
    ?>
    <div class="page-head">
      <div><h1><?= e($judul) ?></h1><p>Seksi adalah sub kategori: satu bagian konten pada halaman <?= e($kat['label']) ?>.</p></div>
      <div class="page-head-actions">
        <a class="btn btn-ghost" href="<?= e($katUrl) ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
      </div>
    </div>
    <form class="card form" method="post" action="<?= e($katUrl . '&a=sub-simpan') ?>">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">
      <div class="form-grid">
        <?php foreach (sub_fields() as $nama => $field): ?>
        <?php render_field($nama, $field, $row[$nama] ?? ($field['default'] ?? '')); ?>
        <?php endforeach; ?>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Seksi</button>
        <a class="btn btn-ghost" href="<?= e($katUrl) ?>">Batal</a>
      </div>
    </form>
    <?php
    admin_foot();
    exit;
}

/* ============================================================
   KONTEN
   ============================================================ */
$sub = $subSlug !== '' ? sub_row((int) $kat['id'], $subSlug) : null;
$bp  = $sub ? blueprint($sub['blueprint']) : blueprint('umum');
$subUrl = $sub ? $katUrl . '&sub=' . urlencode($sub['slug']) : $katUrl;

if ($aksi === 'simpan' && $_SERVER['REQUEST_METHOD'] === 'POST' && $sub) {
    csrf_check();
    $id  = (int) ($_POST['id'] ?? 0);
    $ctx = ['category_id' => (int) $kat['id'], 'subcategory_id' => (int) $sub['id']];

    /* Seksi bertipe tunggal selalu menyunting baris yang sama. */
    if (!empty($bp['tunggal']) && $id === 0) {
        $ada = db_one('SELECT id FROM contents WHERE subcategory_id = ? ORDER BY id LIMIT 1', [$sub['id']]);
        $id  = $ada ? (int) $ada['id'] : 0;
    }

    try {
        $id = simpan_konten($bp['fields'], $ctx, $id);
    } catch (PDOException $ex) {
        flash('danger', 'Gagal menyimpan data.' . (APP_DEBUG ? ' ' . $ex->getMessage() : ''));
        redirect($subUrl . '&a=form' . ($id > 0 ? '&id=' . $id : ''));
    }

    /* Baris anak: tulis ulang seluruhnya. */
    foreach ($bp['anak'] as $anakSlug) {
        $anakSub = sub_row((int) $kat['id'], $anakSlug);
        if (!$anakSub) {
            continue;
        }
        $anakBp = blueprint($anakSub['blueprint']);
        /* Baris anak tidak menampilkan kolom urutan dan status: urutannya
           mengikuti posisi baris, dan status aktifnya dibiarkan apa adanya
           (baris baru aktif mengikuti bawaan tabel). */
        $anakFields = $anakBp['fields'];
        unset($anakFields['is_active'], $anakFields['sort']);

        $lama   = db_all('SELECT * FROM contents WHERE parent_id = ? AND subcategory_id = ?', [$id, $anakSub['id']]);
        $simpan = [];
        $urut   = 0;

        foreach ($_POST['children'][$anakSlug] ?? [] as $i => $baris) {
            $prefix = "children[$anakSlug][$i]";
            $kosong = true;
            foreach ($anakFields as $nama => $field) {
                if (in_array($field['type'] ?? '', ['image', 'file', 'video'], true)) {
                    continue;
                }
                if (trim((string) (post_get($prefix . "[$nama]") ?? '')) !== '') {
                    $kosong = false;
                }
            }
            if ($kosong) {
                continue;
            }
            $_POST['children'][$anakSlug][$i]['sort'] = $urut;
            $simpan[] = simpan_konten(
                $anakFields + ['sort' => ['label' => 'Urutan', 'type' => 'number']],
                ['category_id' => (int) $kat['id'], 'subcategory_id' => (int) $anakSub['id'], 'parent_id' => $id],
                (int) ($baris['__id'] ?? 0),
                $prefix
            );
            $urut += 10;
        }
        foreach ($lama as $baris) {
            if (!in_array((int) $baris['id'], $simpan, true)) {
                hapus_konten($baris, $anakFields);
            }
        }
    }

    flash('success', 'Perubahan berhasil disimpan.');
    redirect(!empty($bp['tunggal']) ? $subUrl : $subUrl . '&a=form&id=' . $id);
}

if ($aksi === 'hapus' && $_SERVER['REQUEST_METHOD'] === 'POST' && $sub) {
    csrf_check();
    $row = db_one('SELECT * FROM contents WHERE id = ? AND subcategory_id = ?', [(int) ($_POST['id'] ?? 0), $sub['id']]);
    if ($row) {
        hapus_konten($row, $bp['fields'], $bp['anak']);
        flash('success', 'Konten berhasil dihapus.');
    } else {
        flash('danger', 'Konten tidak ditemukan.');
    }
    redirect($subUrl);
}

if ($aksi === 'urutan' && $_SERVER['REQUEST_METHOD'] === 'POST' && $sub) {
    csrf_check();
    foreach (($_POST['sort'] ?? []) as $id => $sort) {
        db_exec('UPDATE contents SET sort = ? WHERE id = ? AND subcategory_id = ?', [(int) $sort, (int) $id, (int) $sub['id']]);
    }
    flash('success', 'Urutan konten berhasil diperbarui.');
    redirect($subUrl);
}

/* ------------------------------------------------------------
   FORMULIR KONTEN
   ------------------------------------------------------------ */
if (($aksi === 'form' || (!empty($bp['tunggal']) && $sub && $aksi === 'daftar')) && $sub) {
    $row = null;
    if (!empty($bp['tunggal'])) {
        $row = db_one('SELECT * FROM contents WHERE subcategory_id = ? ORDER BY id LIMIT 1', [$sub['id']]);
    } elseif (!empty($_GET['id'])) {
        $row = db_one('SELECT * FROM contents WHERE id = ? AND subcategory_id = ?', [(int) $_GET['id'], $sub['id']]);
        if (!$row) {
            flash('danger', 'Konten tidak ditemukan.');
            redirect($subUrl);
        }
    }
    $isi   = $row ? konten_rapikan($row) : [];
    $baru  = empty($row['id']);
    $judul = !empty($bp['tunggal']) ? $sub['label'] : (($baru ? 'Tambah ' : 'Ubah ') . $sub['label']);

    if ($row) {
        $isi['tags'] = implode(', ', array_column(tag_konten((int) $row['id'])[$row['id']] ?? [], 'label'));
    }

    admin_head($judul, [
        ['label' => $kat['label'], 'url' => $katUrl],
        ['label' => $sub['label'], 'url' => $subUrl],
        ['label' => $baru ? 'Tambah' : 'Ubah'],
    ]);
    ?>
    <div class="page-head">
      <div>
        <h1><?= e($judul) ?></h1>
        <p>Tampil di halaman <?= e($kat['label']) ?>, seksi <?= e($sub['label']) ?>.</p>
      </div>
      <div class="page-head-actions">
        <?php if (!$baru && !empty($bp['pratinjau']) && !empty($isi['slug'])): ?>
        <a class="btn btn-ghost" target="_blank" rel="noopener"
           href="<?= e(site_url(str_replace('{slug}', rawurlencode((string) $isi['slug']), $bp['pratinjau']))) ?>"><i class="bi bi-eye"></i> Pratinjau</a>
        <?php endif; ?>
        <a class="btn btn-ghost" href="<?= e($subUrl) ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
      </div>
    </div>

    <form class="card form" method="post" action="<?= e($subUrl . '&a=simpan') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">

      <div class="form-grid">
        <?php foreach ($bp['fields'] as $nama => $field): ?>
        <?php render_field($nama, $field, $isi[$nama] ?? ($field['default'] ?? '')); ?>
        <?php endforeach; ?>
      </div>

      <?php foreach ($bp['anak'] as $anakSlug):
          $anakSub = sub_row((int) $kat['id'], $anakSlug);
          if (!$anakSub) { continue; }
          $anakBp   = blueprint($anakSub['blueprint']);
          $anakRows = $baru ? [] : db_all(
              'SELECT * FROM contents WHERE parent_id = ? AND subcategory_id = ? ORDER BY sort, id',
              [$row['id'], $anakSub['id']]
          ); ?>
      <div class="repeater" data-repeater="<?= e($anakSlug) ?>">
        <div class="repeater-head">
          <h3><?= e($anakSub['label']) ?></h3>
          <button type="button" class="btn btn-ghost btn-sm" data-repeater-add><i class="bi bi-plus-lg"></i> Tambah Baris</button>
        </div>
        <?php if (!empty($anakSub['hint'])): ?><p class="hint"><?= e($anakSub['hint']) ?></p><?php endif; ?>

        <div class="repeater-rows" data-repeater-rows>
          <?php foreach ($anakRows as $i => $anakRow): $anakIsi = konten_rapikan($anakRow); ?>
          <div class="repeater-row" data-repeater-row>
            <span class="repeater-handle"><i class="bi bi-grip-vertical"></i></span>
            <input type="hidden" name="children[<?= e($anakSlug) ?>][<?= $i ?>][__id]" value="<?= (int) $anakRow['id'] ?>">
            <div class="form-grid compact">
              <?php foreach ($anakBp['fields'] as $nama => $field): ?>
              <?php if (in_array($nama, ['sort', 'is_active'], true)) { continue; } ?>
              <?php render_field("children[$anakSlug][$i][$nama]", $field, $anakIsi[$nama] ?? ''); ?>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn-icon danger" data-repeater-remove aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
          </div>
          <?php endforeach; ?>
        </div>

        <template data-repeater-template>
          <div class="repeater-row" data-repeater-row>
            <span class="repeater-handle"><i class="bi bi-grip-vertical"></i></span>
            <input type="hidden" name="children[<?= e($anakSlug) ?>][__INDEX__][__id]" value="0">
            <div class="form-grid compact">
              <?php foreach ($anakBp['fields'] as $nama => $field): ?>
              <?php if (in_array($nama, ['sort', 'is_active'], true)) { continue; } ?>
              <?php render_field("children[$anakSlug][__INDEX__][$nama]", $field, ''); ?>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn-icon danger" data-repeater-remove aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
          </div>
        </template>
      </div>
      <?php endforeach; ?>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
        <a class="btn btn-ghost" href="<?= e($subUrl) ?>">Batal</a>
      </div>
    </form>
    <?php
    admin_foot();
    exit;
}

/* ------------------------------------------------------------
   DAFTAR KONTEN SATU SEKSI
   ------------------------------------------------------------ */
if ($sub) {
    $cari  = trim($_GET['q'] ?? '');
    $rows  = konten($kat['slug'] . '/' . $sub['slug'], ['semua' => true, 'induk' => '*', 'urut' => $bp['urut']]);

    if ($cari !== '' && !empty($bp['cari'])) {
        $rows = array_values(array_filter($rows, function ($row) use ($bp, $cari) {
            foreach ($bp['cari'] as $kolom) {
                if (stripos((string) ($row[$kolom] ?? ''), $cari) !== false) {
                    return true;
                }
            }
            return false;
        }));
    }

    $perHal = 30;
    $halTot = max(1, (int) ceil(count($rows) / $perHal));
    $hal    = min(max(1, (int) ($_GET['page'] ?? 1)), $halTot);
    $tampil = array_slice($rows, ($hal - 1) * $perHal, $perHal);

    /* Label induk (album, dsb.) untuk kolom daftar bertipe parent_id. */
    $indukLabel = [];
    if (isset($bp['list']['parent_id'])) {
        foreach (db_all('SELECT id, title FROM contents WHERE category_id = ?', [$kat['id']]) as $r) {
            $indukLabel[(int) $r['id']] = $r['title'];
        }
    }

    admin_head($sub['label'], [['label' => $kat['label'], 'url' => $katUrl], ['label' => $sub['label']]]);
    ?>
    <div class="page-head">
      <div>
        <h1><?= e($sub['label']) ?></h1>
        <p><?= count($rows) ?> konten pada halaman <?= e($kat['label']) ?><?= $cari !== '' ? ' cocok dengan pencarian' : '' ?>.</p>
      </div>
      <div class="page-head-actions">
        <?php if (!empty($bp['cari'])): ?>
        <form class="search-form" method="get" action="<?= e(admin_url('konten.php')) ?>">
          <input type="hidden" name="kategori" value="<?= e($kat['slug']) ?>">
          <input type="hidden" name="sub" value="<?= e($sub['slug']) ?>">
          <i class="bi bi-search"></i>
          <input type="text" name="q" value="<?= e($cari) ?>" placeholder="Cari...">
        </form>
        <?php endif; ?>
        <a class="btn btn-ghost" href="<?= e($katUrl . '&a=sub-form&sub=' . urlencode($sub['slug'])) ?>"><i class="bi bi-pencil"></i> Judul Seksi</a>
        <a class="btn btn-primary" href="<?= e($subUrl . '&a=form') ?>"><i class="bi bi-plus-lg"></i> Tambah Konten</a>
      </div>
    </div>

    <?php if (!empty($sub['hint'])): ?>
    <div class="alert alert-info"><i class="bi bi-info-circle-fill"></i> <span><?= e($sub['hint']) ?></span></div>
    <?php endif; ?>

    <div class="card">
      <?php if (!$tampil): ?>
      <div class="empty-state">
        <i class="bi bi-inbox"></i>
        <p>Belum ada konten pada seksi ini. Klik <b>Tambah Konten</b> untuk menampilkannya di portal.</p>
      </div>
      <?php else: ?>
      <form method="post" action="<?= e($subUrl . '&a=urutan') ?>">
        <?= csrf_field() ?>
        <div class="table-wrap">
          <table class="data-table">
            <thead>
              <tr>
                <th style="width:96px;">Urutan</th>
                <?php foreach ($bp['list'] as $kolom => $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
                <th>Aktif</th>
                <th class="col-actions">Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($tampil as $row): ?>
              <tr>
                <td><input class="sort-input" type="number" name="sort[<?= (int) $row['id'] ?>]" value="<?= (int) $row['sort'] ?>"></td>
                <?php foreach ($bp['list'] as $kolom => $label): $nilai = $row[$kolom] ?? ''; ?>
                <td>
                  <?php if ($kolom === 'parent_id'): ?>
                    <?= e($indukLabel[(int) $nilai] ?? '—') ?>
                  <?php elseif (in_array($kolom, ['is_featured', 'is_active'], true)): ?>
                    <span class="pill <?= (int) $nilai === 1 ? 'pill-on' : 'pill-off' ?>"><?= (int) $nilai === 1 ? 'Ya' : 'Tidak' ?></span>
                  <?php elseif ($kolom === 'published_at'): ?>
                    <?= e($nilai ? tanggal_id((string) $nilai) : '—') ?>
                  <?php else: ?>
                    <?= e(excerpt((string) $nilai, 90)) ?>
                  <?php endif; ?>
                </td>
                <?php endforeach; ?>
                <td><span class="pill <?= (int) $row['is_active'] === 1 ? 'pill-on' : 'pill-off' ?>"><?= (int) $row['is_active'] === 1 ? 'Ya' : 'Tidak' ?></span></td>
                <td class="col-actions">
                  <a class="btn-icon" href="<?= e($subUrl . '&a=form&id=' . (int) $row['id']) ?>" title="Ubah"><i class="bi bi-pencil"></i></a>
                  <button type="submit" class="btn-icon danger" title="Hapus"
                          formaction="<?= e($subUrl . '&a=hapus') ?>" formnovalidate
                          name="id" value="<?= (int) $row['id'] ?>"
                          onclick="return confirm('Hapus konten ini? Tindakan tidak dapat dibatalkan.')"><i class="bi bi-trash"></i></button>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <div class="table-foot">
          <button type="submit" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-down-up"></i> Simpan Urutan</button>
        </div>
      </form>
      <?php endif; ?>
    </div>

    <?php if ($halTot > 1): ?>
    <div class="pager">
      <?php for ($p = 1; $p <= $halTot; $p++): ?>
      <a class="<?= $p === $hal ? 'active' : '' ?>"
         href="<?= e($subUrl . '&page=' . $p . ($cari !== '' ? '&q=' . urlencode($cari) : '')) ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
    <?php
    admin_foot();
    exit;
}

/* ------------------------------------------------------------
   DAFTAR SEKSI SATU HALAMAN
   ------------------------------------------------------------ */
$subs   = sub_rows((int) $kat['id']);
$jumlah = jumlah_konten((int) $kat['id']);

admin_head($kat['label'], [['label' => 'Konten'], ['label' => $kat['label']]]);
?>
<div class="page-head">
  <div>
    <h1><?= e($kat['label']) ?></h1>
    <p><?= count($subs) ?> seksi &middot; <?= array_sum($jumlah) ?> konten. Urutan seksi menentukan urutan tampil di halaman portal.</p>
  </div>
  <div class="page-head-actions">
    <?php if (!empty($kat['page_file'])): ?>
    <a class="btn btn-ghost" href="<?= e(site_url($kat['page_file'])) ?>" target="_blank" rel="noopener"><i class="bi bi-eye"></i> Lihat Halaman</a>
    <?php endif; ?>
    <a class="btn btn-ghost" href="<?= e($katUrl . '&a=kategori-form') ?>"><i class="bi bi-sliders"></i> Pengaturan Halaman</a>
    <a class="btn btn-primary" href="<?= e($katUrl . '&a=sub-form') ?>"><i class="bi bi-plus-lg"></i> Tambah Seksi</a>
  </div>
</div>

<div class="card">
  <?php if (!$subs): ?>
  <div class="empty-state">
    <i class="bi bi-inbox"></i>
    <p>Halaman ini belum memiliki seksi. Klik <b>Tambah Seksi</b> untuk membuat bagian konten pertama.</p>
  </div>
  <?php else: ?>
  <form method="post" action="<?= e($katUrl . '&a=sub-urutan') ?>">
    <?= csrf_field() ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <th style="width:96px;">Urutan</th>
            <th>Seksi</th>
            <th>Judul di Portal</th>
            <th>Jenis</th>
            <th>Konten</th>
            <th>Aktif</th>
            <th class="col-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($subs as $row):
              $isiUrl = $katUrl . '&sub=' . urlencode($row['slug']);
              $n      = $jumlah[(int) $row['id']] ?? 0; ?>
          <tr>
            <td><input class="sort-input" type="number" name="sort[<?= (int) $row['id'] ?>]" value="<?= (int) $row['sort'] ?>"></td>
            <td>
              <a href="<?= e($isiUrl) ?>" style="font-weight:600; color:var(--primary-dark);">
                <i class="bi <?= e($row['icon'] ?: 'bi-collection') ?>"></i> <?= e($row['label']) ?>
              </a>
              <?php if ((int) $row['is_child'] === 1): ?>
              <span class="pill pill-off" title="Baris anak — disunting di dalam formulir induknya">baris anak</span>
              <?php endif; ?>
            </td>
            <td><?= e(excerpt((string) ($row['title'] ?? ''), 60)) ?: '—' ?></td>
            <td><?= e($row['blueprint']) ?></td>
            <td><?= $n ?></td>
            <td><span class="pill <?= (int) $row['is_active'] === 1 ? 'pill-on' : 'pill-off' ?>"><?= (int) $row['is_active'] === 1 ? 'Ya' : 'Tidak' ?></span></td>
            <td class="col-actions">
              <a class="btn-icon" href="<?= e($isiUrl . '&a=form') ?>" title="Tambah konten"><i class="bi bi-plus-lg"></i></a>
              <a class="btn-icon" href="<?= e($katUrl . '&a=sub-form&sub=' . urlencode($row['slug'])) ?>" title="Ubah seksi"><i class="bi bi-pencil"></i></a>
              <button type="submit" class="btn-icon danger" title="Hapus seksi"
                      formaction="<?= e($katUrl . '&a=sub-hapus') ?>" formnovalidate
                      name="id" value="<?= (int) $row['id'] ?>"
                      onclick="return confirm('Hapus seksi ini? Hanya bisa bila seksinya sudah kosong.')"><i class="bi bi-trash"></i></button>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <div class="table-foot">
      <button type="submit" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-down-up"></i> Simpan Urutan Seksi</button>
    </div>
  </form>
  <?php endif; ?>
</div>

<?php admin_foot(); ?>
