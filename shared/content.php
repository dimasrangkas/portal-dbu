<?php
/* ============================================================
   Pembacaan konten portal

   Seluruh isi halaman ada pada tiga sumbu: kategori (halaman),
   sub kategori (seksi), dan tag. Berkas ini satu-satunya tempat
   yang tahu bentuk tabelnya — halaman portal cukup memanggil:

       konten('home/slider')            seluruh isi satu seksi
       konten_satu('home/sambutan')     isi tunggal
       konten('berita', ['limit' => 3]) seluruh isi satu kategori
       subkategori('profil', 'sejarah') judul seksi
       kategori('berita')               judul & meta halaman

   Baris yang dikembalikan berisi kolom tabel `contents` ditambah
   isi kolom JSON `extra` yang sudah dilebur ke tingkat atas, jadi
   kolom khusus seperti `btn1_label` atau `klasifikasi` tetap dapat
   dibaca langsung sebagai $row['btn1_label'].
   ============================================================ */
require_once __DIR__ . '/db.php';

/* ------------------------------------------------------------
   Kategori & sub kategori (jumlahnya sedikit — dibaca sekali per request)
   ------------------------------------------------------------ */

/** Seluruh kategori, dipetakan berdasarkan slug. */
function kategori_semua(): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (db_all('SELECT * FROM categories ORDER BY sort, id') as $row) {
            $cache[$row['slug']] = $row;
        }
    }
    return $cache;
}

/** Satu kategori beserta meta halamannya. */
function kategori(string $slug): array
{
    $all = kategori_semua();
    return $all[$slug] ?? [
        'id' => 0, 'slug' => $slug, 'label' => $slug, 'icon' => null, 'page_file' => null,
        'meta_title' => null, 'meta_description' => null, 'eyebrow' => null,
        'heading' => null, 'subtitle' => null, 'breadcrumb' => null, 'sort' => 0, 'is_active' => 1,
    ];
}

/**
 * Seluruh sub kategori satu kategori, dipetakan berdasarkan slug.
 * $semua = true ikut memuat yang tidak aktif dan yang berupa daftar anak.
 */
function subkategori_semua(string $kategori, bool $semua = false): array
{
    static $cache = [];
    $kunci = $kategori . ($semua ? ':semua' : '');
    if (!isset($cache[$kunci])) {
        $sql = 'SELECT s.* FROM subcategories s
                JOIN categories c ON c.id = s.category_id
                WHERE c.slug = ?' . ($semua ? '' : ' AND s.is_active = 1 AND s.is_child = 0') . '
                ORDER BY s.sort, s.id';
        $cache[$kunci] = [];
        foreach (db_all($sql, [$kategori]) as $row) {
            $cache[$kunci][$row['slug']] = $row;
        }
    }
    return $cache[$kunci];
}

/** Satu sub kategori — judul seksi sebagaimana tampil di portal. */
function subkategori(string $kategori, string $slug): array
{
    $all = subkategori_semua($kategori, true);
    return $all[$slug] ?? [
        'id' => 0, 'slug' => $slug, 'label' => $slug, 'icon' => null, 'eyebrow' => null,
        'title' => null, 'subtitle' => null, 'body' => null, 'blueprint' => 'umum',
        'is_child' => 0, 'hint' => null, 'sort' => 0, 'is_active' => 1,
    ];
}

/* ------------------------------------------------------------
   Konten
   ------------------------------------------------------------ */

/** Lebur kolom JSON `extra` ke tingkat atas; kolom asli tetap menang. */
function konten_rapikan(array $row): array
{
    $extra = [];
    if (!empty($row['extra'])) {
        $extra = json_decode((string) $row['extra'], true) ?: [];
    }
    unset($row['extra']);
    return $row + $extra;
}

/**
 * Ambil konten.
 *
 * $jalur  'kategori' atau 'kategori/sub-kategori'.
 * $opsi   limit    int          batas jumlah baris
 *         urut     string       ORDER BY, bawaan 'k.sort, k.id'
 *         sub      string|array sub kategori tambahan bila $jalur hanya kategori
 *         kecuali  string|array sub kategori yang dilewati
 *         blueprint string      hanya sub kategori bercetak-biru ini — mis. seluruh
 *                               sub kategori berita, berapa pun jumlahnya
 *         sorot    bool         hanya yang ditandai sorot (is_featured)
 *         semua    bool         ikut memuat yang tidak aktif
 *         induk    int|null     'anak' dari satu konten; bawaan hanya baris tingkat atas
 *         slug     string       hanya konten dengan slug ini
 *         tag      string       hanya konten bertag ini
 *         anak     bool         sertakan baris anak pada kunci 'anak'
 */
function konten(string $jalur, array $opsi = []): array
{
    [$kat, $sub] = array_pad(explode('/', $jalur, 2), 2, null);

    $where  = ['c.slug = ?'];
    $params = [$kat];

    $subs = $sub !== null ? [$sub] : (array) ($opsi['sub'] ?? []);
    if ($subs) {
        $where[]  = 's.slug IN (' . implode(',', array_fill(0, count($subs), '?')) . ')';
        $params   = array_merge($params, $subs);
    } else {
        /* Tanpa sub kategori: seluruh isi halaman, kecuali daftar baris anak. */
        $where[] = '(s.id IS NULL OR s.is_child = 0)';
    }
    if (!empty($opsi['blueprint'])) {
        $where[]  = 's.blueprint = ?';
        $params[] = $opsi['blueprint'];
    }
    foreach ((array) ($opsi['kecuali'] ?? []) as $lewati) {
        $where[]  = '(s.slug IS NULL OR s.slug <> ?)';
        $params[] = $lewati;
    }
    if (empty($opsi['semua'])) {
        $where[] = 'k.is_active = 1';
    }
    if (!empty($opsi['sorot'])) {
        $where[] = 'k.is_featured = 1';
    }
    if (array_key_exists('induk', $opsi)) {
        if ($opsi['induk'] === null) {
            $where[] = 'k.parent_id IS NULL';
        } elseif ($opsi['induk'] !== '*') {
            $where[]  = 'k.parent_id = ?';
            $params[] = (int) $opsi['induk'];
        }
    } else {
        $where[] = 'k.parent_id IS NULL';
    }
    if (!empty($opsi['slug'])) {
        $where[]  = 'k.slug = ?';
        $params[] = $opsi['slug'];
    }
    if (!empty($opsi['tag'])) {
        $where[]  = 'EXISTS (SELECT 1 FROM content_tags ct JOIN tags t ON t.id = ct.tag_id
                             WHERE ct.content_id = k.id AND t.slug = ?)';
        $params[] = $opsi['tag'];
    }

    $urut  = $opsi['urut'] ?? 'k.sort, k.id';
    $limit = !empty($opsi['limit']) ? ' LIMIT ' . (int) $opsi['limit'] : '';

    $rows = db_all(
        'SELECT k.*, s.slug AS sub_slug, s.label AS sub_label, c.slug AS kategori_slug
           FROM contents k
           JOIN categories c ON c.id = k.category_id
           LEFT JOIN subcategories s ON s.id = k.subcategory_id
          WHERE ' . implode(' AND ', $where) . '
          ORDER BY ' . $urut . $limit,
        $params
    );

    $out = array_map('konten_rapikan', $rows);

    if (!empty($opsi['anak']) && $out) {
        $anak = konten_anak(array_column($out, 'id'));
        foreach ($out as &$row) {
            $row['anak'] = $anak[$row['id']] ?? [];
        }
        unset($row);
    }
    if (!empty($opsi['tags'])) {
        $tags = tag_konten(array_column($out, 'id'));
        foreach ($out as &$row) {
            $row['tags'] = $tags[$row['id']] ?? [];
        }
        unset($row);
    }
    return $out;
}

/** Konten tunggal pertama pada satu jalur — untuk seksi berisi satu baris. */
function konten_satu(string $jalur, array $opsi = []): array
{
    $rows = konten($jalur, ['limit' => 1] + $opsi);
    return $rows[0] ?? [];
}

/** Satu konten berdasarkan slug, mis. berita atau layanan tertentu. */
function konten_slug(string $kategori, string $slug, array $opsi = []): array
{
    return konten_satu($kategori, ['slug' => $slug] + $opsi);
}

/**
 * Baris anak dari satu atau beberapa konten induk,
 * dikelompokkan: [id induk => [sub kategori => baris...]] bila $kelompok,
 * atau [id induk => [baris...]] bila tidak.
 */
function konten_anak(array|int $indukId, string $sub = ''): array
{
    $ids = array_values(array_filter(array_map('intval', (array) $indukId)));
    if (!$ids) {
        return [];
    }
    $params = $ids;
    $sql = 'SELECT k.*, s.slug AS sub_slug FROM contents k
            LEFT JOIN subcategories s ON s.id = k.subcategory_id
            WHERE k.parent_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')
              AND k.is_active = 1';
    if ($sub !== '') {
        $sql     .= ' AND s.slug = ?';
        $params[] = $sub;
    }
    $sql .= ' ORDER BY k.sort, k.id';

    $out = [];
    foreach (db_all($sql, $params) as $row) {
        $out[(int) $row['parent_id']][] = konten_rapikan($row);
    }
    return $out;
}

/** Baris anak satu induk pada satu sub kategori — bentuk datar. */
function anak(int $indukId, string $sub): array
{
    return konten_anak($indukId, $sub)[$indukId] ?? [];
}

/** Tag beberapa konten sekaligus: [id konten => [ [slug, label], ... ]]. */
function tag_konten(array|int $kontenId): array
{
    $ids = array_values(array_filter(array_map('intval', (array) $kontenId)));
    if (!$ids) {
        return [];
    }
    $out = [];
    foreach (db_all(
        'SELECT ct.content_id, t.slug, t.label FROM content_tags ct
         JOIN tags t ON t.id = ct.tag_id
         WHERE ct.content_id IN (' . implode(',', array_fill(0, count($ids), '?')) . ')
         ORDER BY t.sort, t.label',
        $ids
    ) as $row) {
        $out[(int) $row['content_id']][] = ['slug' => $row['slug'], 'label' => $row['label']];
    }
    return $out;
}

/** Seluruh tag yang dipakai, untuk daftar penyaring. */
function tag_semua(): array
{
    return db_all('SELECT * FROM tags WHERE is_active = 1 ORDER BY sort, label');
}
