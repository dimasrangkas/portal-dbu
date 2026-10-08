<?php
/* ============================================================
   Pengendali CRUD generik untuk seluruh modul di config/resources.php
   Aksi: list (bawaan) | form | save | delete
   ============================================================ */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/fields.php';
require_once __DIR__ . '/includes/upload.php';

$user = require_login();

$key = $_GET['r'] ?? '';
$res = admin_resource($key);
if (!$res) {
    http_response_code(404);
    flash('danger', 'Modul tidak dikenali.');
    redirect(admin_url('index.php'));
}
if (!empty($res['admin_only']) && $user['role'] !== 'admin') {
    flash('danger', 'Hanya administrator yang dapat membuka modul tersebut.');
    redirect(admin_url('index.php'));
}

$table    = $res['table'];
$fields   = $res['fields'];
$children = $res['children'] ?? [];
$action   = $_GET['a'] ?? (!empty($res['single']) ? 'form' : 'list');
$listUrl  = admin_url('resource.php?r=' . urlencode($key));

/** Kolom basis data untuk sebuah field. */
function db_column(string $name, array $field): string
{
    return $field['column'] ?? $name;
}

/* ------------------------------------------------------------
   SIMPAN
   ------------------------------------------------------------ */
if ($action === 'save' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    $id = (int) ($_POST['id'] ?? 0);

    if (!empty($res['single'])) {
        $existing = db_one("SELECT * FROM `$table` ORDER BY id LIMIT 1");
        $id = $existing ? (int) $existing['id'] : 0;
    }

    $data = [];
    foreach ($fields as $name => $field) {
        [$store, $value] = field_value($name, $field, null);
        if ($store) {
            $data[db_column($name, $field)] = $value;
        }
    }

    // Slug otomatis bila dikosongkan
    foreach ($fields as $name => $field) {
        if (($field['type'] ?? '') === 'slug' && empty($data[db_column($name, $field)])) {
            $source = $field['from'] ?? '';
            $data[db_column($name, $field)] = slugify((string) ($_POST[$source] ?? ('item-' . time())));
        }
    }

    // Kata sandi wajib diisi saat membuat akun baru
    if ($id === 0) {
        foreach ($fields as $name => $field) {
            if (($field['type'] ?? '') === 'password' && !isset($data[db_column($name, $field)])) {
                flash('danger', 'Kata sandi wajib diisi saat membuat data baru.');
                redirect($listUrl . '&a=form');
            }
        }
    }

    try {
        if ($id > 0) {
            db_update($table, $data, $id);
        } else {
            $id = db_insert($table, $data);
        }
    } catch (PDOException $ex) {
        $msg = str_contains($ex->getMessage(), 'Duplicate entry')
            ? 'Gagal menyimpan: ada nilai unik yang sudah dipakai (mis. slug atau kode).'
            : 'Gagal menyimpan data.' . (APP_DEBUG ? ' ' . $ex->getMessage() : '');
        flash('danger', $msg);
        redirect($listUrl . '&a=form' . ($id > 0 ? '&id=' . $id : ''));
    }

    /* ---- Baris anak: tulis ulang seluruhnya ---- */
    foreach ($children as $childKey => $child) {
        $rows = $_POST['children'][$childKey] ?? [];
        $old  = db_all("SELECT * FROM `{$child['table']}` WHERE `{$child['foreign_key']}` = ?", [$id]);
        $keep = [];

        $sort = 0;
        foreach ($rows as $index => $row) {
            $payload = [$child['foreign_key'] => $id, 'sort' => $sort];
            $empty   = true;
            foreach ($child['fields'] as $cName => $cField) {
                $inputName = "children[$childKey][$index][$cName]";
                [$store, $value] = field_value($inputName, $cField, null);
                if (($cField['type'] ?? '') === 'file' || ($cField['type'] ?? '') === 'image') {
                    if ($store) {
                        $payload[$cName] = $value;
                    }
                    if (!empty($value)) {
                        $empty = false;
                    }
                    continue;
                }
                $payload[$cName] = $store ? $value : null;
                if (trim((string) $payload[$cName]) !== '') {
                    $empty = false;
                }
            }
            if ($empty) {
                continue;
            }

            $rowId = (int) ($row['__id'] ?? 0);
            if ($rowId > 0) {
                db_update($child['table'], $payload, $rowId);
                $keep[] = $rowId;
            } else {
                $keep[] = db_insert($child['table'], $payload);
            }
            $sort++;
        }

        foreach ($old as $oldRow) {
            if (!in_array((int) $oldRow['id'], $keep, true)) {
                foreach ($child['fields'] as $cName => $cField) {
                    if (in_array($cField['type'] ?? '', ['file', 'image'], true)) {
                        delete_upload($oldRow[$cName] ?? null);
                    }
                }
                db_delete($child['table'], (int) $oldRow['id']);
            }
        }
    }

    flash('success', 'Perubahan berhasil disimpan.');
    redirect(!empty($res['single']) ? $listUrl : $listUrl . '&a=form&id=' . $id);
}

/* ------------------------------------------------------------
   HAPUS
   ------------------------------------------------------------ */
if ($action === 'delete' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    if (!empty($res['no_delete'])) {
        flash('danger', 'Data pada modul ini tidak dapat dihapus.');
        redirect($listUrl);
    }
    $id  = (int) ($_POST['id'] ?? 0);
    $row = db_one("SELECT * FROM `$table` WHERE id = ?", [$id]);
    if (!$row) {
        flash('danger', 'Data tidak ditemukan.');
        redirect($listUrl);
    }
    if ($table === 'users' && (int) $row['id'] === (int) $user['id']) {
        flash('danger', 'Anda tidak dapat menghapus akun Anda sendiri.');
        redirect($listUrl);
    }
    foreach ($fields as $name => $field) {
        if (in_array($field['type'] ?? '', ['file', 'image'], true)) {
            delete_upload($row[db_column($name, $field)] ?? null);
        }
    }
    db_delete($table, $id);
    flash('success', 'Data berhasil dihapus.');
    redirect($listUrl);
}

/* ------------------------------------------------------------
   UBAH URUTAN CEPAT
   ------------------------------------------------------------ */
if ($action === 'reorder' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (($_POST['sort'] ?? []) as $rowId => $sort) {
        db_exec("UPDATE `$table` SET sort = ? WHERE id = ?", [(int) $sort, (int) $rowId]);
    }
    flash('success', 'Urutan berhasil diperbarui.');
    redirect($listUrl);
}

/* ------------------------------------------------------------
   FORMULIR
   ------------------------------------------------------------ */
if ($action === 'form') {
    $row = null;
    if (!empty($res['single'])) {
        $row = db_one("SELECT * FROM `$table` ORDER BY id LIMIT 1");
    } elseif (!empty($_GET['id'])) {
        $row = db_one("SELECT * FROM `$table` WHERE id = ?", [(int) $_GET['id']]);
        if (!$row) {
            flash('danger', 'Data tidak ditemukan.');
            redirect($listUrl);
        }
    } elseif (!empty($res['no_create'])) {
        flash('danger', 'Modul ini tidak menerima penambahan data baru.');
        redirect($listUrl);
    }

    $isNew  = empty($row['id']);
    $title  = !empty($res['single']) ? $res['label'] : (($isNew ? 'Tambah ' : 'Ubah ') . $res['label']);
    $crumbs = !empty($res['single'])
        ? [['label' => $res['group']], ['label' => $res['label']]]
        : [['label' => $res['label'], 'url' => $listUrl], ['label' => $isNew ? 'Tambah' : 'Ubah']];

    admin_head($title, $crumbs);
    ?>
    <div class="page-head">
      <div>
        <h1><?= e($title) ?></h1>
        <?php if (!empty($res['single'])): ?><p>Konten ini tampil pada situs publik.</p><?php endif; ?>
      </div>
      <div class="page-head-actions">
        <?php if (!$isNew && !empty($res['preview'])):
            $previewUrl = $res['preview'];
            foreach ($row as $col => $val) {
                $previewUrl = str_replace('{' . $col . '}', rawurlencode((string) $val), $previewUrl);
            } ?>
        <a class="btn btn-ghost" href="<?= e(site_url($previewUrl)) ?>" target="_blank" rel="noopener"><i class="bi bi-eye"></i> Pratinjau</a>
        <?php endif; ?>
        <?php if (empty($res['single'])): ?>
        <a class="btn btn-ghost" href="<?= e($listUrl) ?>"><i class="bi bi-arrow-left"></i> Kembali</a>
        <?php endif; ?>
      </div>
    </div>

    <form class="card form" method="post" action="<?= e($listUrl . '&a=save') ?>" enctype="multipart/form-data">
      <?= csrf_field() ?>
      <input type="hidden" name="id" value="<?= (int) ($row['id'] ?? 0) ?>">

      <div class="form-grid">
        <?php foreach ($fields as $name => $field): ?>
        <?php render_field($name, $field, $row[db_column($name, $field)] ?? ($field['default'] ?? '')); ?>
        <?php endforeach; ?>
      </div>

      <?php foreach ($children as $childKey => $child):
          $childRows = $isNew ? [] : db_all(
              "SELECT * FROM `{$child['table']}` WHERE `{$child['foreign_key']}` = ? ORDER BY {$child['order']}",
              [$row['id']]
          ); ?>
      <div class="repeater" data-repeater="<?= e($childKey) ?>">
        <div class="repeater-head">
          <h3><?= e($child['label']) ?></h3>
          <button type="button" class="btn btn-ghost btn-sm" data-repeater-add><i class="bi bi-plus-lg"></i> Tambah Baris</button>
        </div>
        <?php if (!empty($child['hint'])): ?><p class="hint"><?= e($child['hint']) ?></p><?php endif; ?>

        <div class="repeater-rows" data-repeater-rows>
          <?php foreach ($childRows as $i => $childRow): ?>
          <div class="repeater-row" data-repeater-row>
            <span class="repeater-handle"><i class="bi bi-grip-vertical"></i></span>
            <input type="hidden" name="children[<?= e($childKey) ?>][<?= $i ?>][__id]" value="<?= (int) $childRow['id'] ?>">
            <div class="form-grid compact">
              <?php foreach ($child['fields'] as $cName => $cField): ?>
              <?php render_field("children[$childKey][$i][$cName]", $cField, $childRow[$cName] ?? ''); ?>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn-icon danger" data-repeater-remove aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
          </div>
          <?php endforeach; ?>
        </div>

        <template data-repeater-template>
          <div class="repeater-row" data-repeater-row>
            <span class="repeater-handle"><i class="bi bi-grip-vertical"></i></span>
            <input type="hidden" name="children[<?= e($childKey) ?>][__INDEX__][__id]" value="0">
            <div class="form-grid compact">
              <?php foreach ($child['fields'] as $cName => $cField): ?>
              <?php render_field("children[$childKey][__INDEX__][$cName]", $cField, ''); ?>
              <?php endforeach; ?>
            </div>
            <button type="button" class="btn-icon danger" data-repeater-remove aria-label="Hapus baris"><i class="bi bi-trash"></i></button>
          </div>
        </template>
      </div>
      <?php endforeach; ?>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Perubahan</button>
        <?php if (empty($res['single'])): ?>
        <a class="btn btn-ghost" href="<?= e($listUrl) ?>">Batal</a>
        <?php endif; ?>
      </div>
    </form>
    <?php
    admin_foot();
    exit;
}

/* ------------------------------------------------------------
   DAFTAR
   ------------------------------------------------------------ */
$search   = trim($_GET['q'] ?? '');
$page     = max(1, (int) ($_GET['page'] ?? 1));
$perPage  = (int) ($res['per_page'] ?? 50);
$where    = '';
$params   = [];

if ($search !== '' && !empty($res['search'])) {
    $parts = [];
    foreach ($res['search'] as $col) {
        $parts[]  = "`$col` LIKE ?";
        $params[] = '%' . $search . '%';
    }
    $where = 'WHERE ' . implode(' OR ', $parts);
}

$total  = (int) db_value("SELECT COUNT(*) FROM `$table` $where", $params, 0);
$pages  = max(1, (int) ceil($total / $perPage));
$page   = min($page, $pages);
$offset = ($page - 1) * $perPage;
$rows   = db_all("SELECT * FROM `$table` $where ORDER BY {$res['order']} LIMIT $perPage OFFSET $offset", $params);

$lookups = [];
foreach ($res['list_lookup'] ?? [] as $col => $def) {
    foreach (db_all("SELECT id, `{$def['column']}` AS label FROM `{$def['table']}`") as $lk) {
        $lookups[$col][(string) $lk['id']] = $lk['label'];
    }
}

admin_head($res['label'], [['label' => $res['group']], ['label' => $res['label']]]);
?>
<div class="page-head">
  <div>
    <h1><?= e($res['label']) ?></h1>
    <p><?= $total ?> data<?= $search !== '' ? ' cocok dengan pencarian' : '' ?></p>
  </div>
  <div class="page-head-actions">
    <?php if (!empty($res['search'])): ?>
    <form class="search-form" method="get" action="<?= e(admin_url('resource.php')) ?>">
      <input type="hidden" name="r" value="<?= e($key) ?>">
      <i class="bi bi-search"></i>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Cari...">
    </form>
    <?php endif; ?>
    <?php if (empty($res['no_create'])): ?>
    <a class="btn btn-primary" href="<?= e($listUrl . '&a=form') ?>"><i class="bi bi-plus-lg"></i> Tambah <?= e($res['label']) ?></a>
    <?php endif; ?>
  </div>
</div>

<div class="card">
  <?php if (!$rows): ?>
  <div class="empty-state">
    <i class="bi bi-inbox"></i>
    <p>Belum ada data pada modul ini.</p>
  </div>
  <?php else: ?>
  <form method="post" action="<?= e($listUrl . '&a=reorder') ?>">
    <?= csrf_field() ?>
    <div class="table-wrap">
      <table class="data-table">
        <thead>
          <tr>
            <?php foreach ($res['list'] as $col => $label): ?><th><?= e($label) ?></th><?php endforeach; ?>
            <th class="col-actions">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($rows as $row): ?>
          <tr>
            <?php foreach ($res['list'] as $col => $label):
                $value = $row[$col] ?? ''; ?>
            <td>
              <?php if (in_array($col, ['is_active', 'is_published', 'show_on_home', 'is_featured'], true)): ?>
                <span class="pill <?= (int) $value === 1 ? 'pill-on' : 'pill-off' ?>"><?= (int) $value === 1 ? 'Ya' : 'Tidak' ?></span>
              <?php elseif ($col === 'sort' && !empty($res['reorder'])): ?>
                <input class="sort-input" type="number" name="sort[<?= (int) $row['id'] ?>]" value="<?= (int) $value ?>">
              <?php elseif (isset($lookups[$col])): ?>
                <?= e($lookups[$col][(string) $value] ?? '—') ?>
              <?php elseif ($col === 'status'): ?>
                <span class="pill <?= $value === 'berlaku' ? 'pill-on' : 'pill-off' ?>"><?= e(ucfirst((string) $value)) ?></span>
              <?php elseif (str_contains($col, 'date') || str_contains($col, '_at')): ?>
                <?= e($value ? tanggal_id((string) $value) : '—') ?>
              <?php else: ?>
                <?= e(excerpt((string) $value, 90)) ?>
              <?php endif; ?>
            </td>
            <?php endforeach; ?>
            <td class="col-actions">
              <a class="btn-icon" href="<?= e($listUrl . '&a=form&id=' . (int) $row['id']) ?>" title="Ubah"><i class="bi bi-pencil"></i></a>
              <?php if (empty($res['no_delete'])): ?>
              <button type="submit" class="btn-icon danger" title="Hapus"
                      formaction="<?= e($listUrl . '&a=delete') ?>" formnovalidate
                      name="id" value="<?= (int) $row['id'] ?>"
                      onclick="return confirm('Hapus data ini? Tindakan tidak dapat dibatalkan.')"><i class="bi bi-trash"></i></button>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($res['reorder'])): ?>
    <div class="table-foot">
      <button type="submit" class="btn btn-ghost btn-sm"><i class="bi bi-arrow-down-up"></i> Simpan Urutan</button>
    </div>
    <?php endif; ?>
  </form>
  <?php endif; ?>
</div>

<?php if ($pages > 1): ?>
<div class="pager">
  <?php for ($p = 1; $p <= $pages; $p++): ?>
  <a class="<?= $p === $page ? 'active' : '' ?>"
     href="<?= e($listUrl . '&page=' . $p . ($search !== '' ? '&q=' . urlencode($search) : '')) ?>"><?= $p ?></a>
  <?php endfor; ?>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
