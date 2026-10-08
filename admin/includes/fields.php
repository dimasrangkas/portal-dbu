<?php
/* ============================================================
   Perender kolom formulir + pembacaan nilai dari POST
   ============================================================ */

/**
 * Sisipkan akhiran ke dalam nama field, termasuk nama bertingkat.
 * "gambar" + "__current"                  => "gambar__current"
 * "children[x][0][file]" + "__current"    => "children[x][0][file__current]"
 */
function name_suffix(string $name, string $suffix): string
{
    if (str_ends_with($name, ']')) {
        return preg_replace('/\[([^\[\]]*)\]$/', '[$1' . $suffix . ']', $name);
    }
    return $name . $suffix;
}

/** Baca nilai POST untuk nama datar maupun bertingkat (a[b][c]). */
function post_get(string $name)
{
    if (!str_contains($name, '[')) {
        return $_POST[$name] ?? null;
    }
    preg_match('/^([^\[]+)/', $name, $root);
    $node = $_POST[$root[1]] ?? null;
    preg_match_all('/\[([^\]]*)\]/', $name, $parts);
    foreach ($parts[1] as $piece) {
        if (!is_array($node) || !array_key_exists($piece, $node)) {
            return null;
        }
        $node = $node[$piece];
    }
    return $node;
}

/**
 * Opsi select: statis (`options`), dari kueri (`options_sql`), atau dari
 * konten sub kategori lain (`options_konten`, mis. 'galeri/album').
 * `options_nilai` menentukan kolom yang dipakai sebagai nilai — bawaan 'id'.
 */
function field_options(array $field): array
{
    if (!empty($field['options'])) {
        return $field['options'];
    }
    if (!empty($field['options_sql'])) {
        $out = [];
        foreach (db_all($field['options_sql']) as $row) {
            $out[(string) $row['value']] = $row['label'];
        }
        return $out;
    }
    if (!empty($field['options_konten'])) {
        $nilai = $field['options_nilai'] ?? 'id';
        $out   = [];
        foreach (konten($field['options_konten'], ['semua' => true]) as $row) {
            $out[(string) $row[$nilai]] = $row['title'] ?: $row['slug'];
        }
        return $out;
    }
    return [];
}

/**
 * Render satu kolom formulir.
 * $name dipakai sebagai atribut name; $value nilai saat ini.
 */
function render_field(string $name, array $field, $value): void
{
    $type     = $field['type'] ?? 'text';
    $id       = 'f_' . preg_replace('/[^a-z0-9]+/i', '_', $name);
    $required = !empty($field['required']) ? ' required' : '';
    $readonly = !empty($field['readonly']) ? ' readonly' : '';
    $col      = ($field['col'] ?? 'full') === 'half' ? 'col-half' : 'col-full';
    ?>
    <div class="field <?= $col ?>" data-field-type="<?= e($type) ?>">
      <?php if ($type !== 'checkbox'): ?>
      <label for="<?= e($id) ?>"><?= e($field['label']) ?><?= $required ? ' <span class="req">*</span>' : '' ?></label>
      <?php endif; ?>

      <?php switch ($type):
        case 'textarea': ?>
          <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" rows="<?= (int) ($field['rows'] ?? 3) ?>"<?= $required . $readonly ?>><?= e((string) $value) ?></textarea>
        <?php break;

        case 'richtext': ?>
          <textarea id="<?= e($id) ?>" name="<?= e($name) ?>" rows="<?= (int) ($field['rows'] ?? 8) ?>" class="richtext"<?= $required . $readonly ?>><?= e((string) $value) ?></textarea>
        <?php break;

        case 'number': ?>
          <input type="number" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"<?= $required . $readonly ?>>
        <?php break;

        /* Angka berkoma — luas gedung dan sejenisnya, yang akan terpotong
           bila dibulatkan ke bilangan bulat seperti tipe 'number'. */
        case 'decimal': ?>
          <input type="number" step="<?= e($field['step'] ?? '0.01') ?>" id="<?= e($id) ?>" name="<?= e($name) ?>"
                 value="<?= e((string) $value) ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'date': ?>
          <input type="date" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e($value ? date('Y-m-d', strtotime((string) $value)) : '') ?>"<?= $required . $readonly ?>>
        <?php break;

        /* Kolom DATETIME. Nilainya dikirim dalam bentuk "Y-m-dTH:i" oleh
           peramban, dan MySQL menerimanya apa adanya. */
        case 'datetime': ?>
          <input type="datetime-local" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e($value ? date('Y-m-d\TH:i', strtotime((string) $value)) : '') ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'password': ?>
          <input type="password" id="<?= e($id) ?>" name="<?= e($name) ?>" value="" autocomplete="new-password"<?= $readonly ?>>
        <?php break;

        case 'checkbox': ?>
          <label class="switch">
            <input type="hidden" name="<?= e($name) ?>" value="0">
            <input type="checkbox" id="<?= e($id) ?>" name="<?= e($name) ?>" value="1"<?= (int) $value === 1 ? ' checked' : '' ?>>
            <span class="track"></span>
            <span class="switch-label"><?= e($field['label']) ?></span>
          </label>
        <?php break;

        case 'select':
          $options = field_options($field); ?>
          <select id="<?= e($id) ?>" name="<?= e($name) ?>"<?= $required ?>>
            <?php if (isset($field['empty'])): ?><option value=""><?= e($field['empty']) ?></option><?php endif; ?>
            <?php foreach ($options as $optValue => $optLabel): ?>
            <option value="<?= e((string) $optValue) ?>"<?= (string) $value === (string) $optValue ? ' selected' : '' ?>><?= e($optLabel) ?></option>
            <?php endforeach; ?>
          </select>
        <?php break;

        case 'tags': ?>
          <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"
                 placeholder="Sertifikasi, Keselamatan"<?= $required ?>>
        <?php break;

        case 'slug': ?>
          <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"
                 data-slug-from="<?= e($field['from'] ?? '') ?>"<?= $required . $readonly ?>>
        <?php break;

        case 'icon': ?>
          <div class="icon-input">
            <span class="icon-preview"><i class="bi <?= e($value ?: 'bi-app') ?>"></i></span>
            <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>" placeholder="bi-airplane-fill" data-icon-input>
          </div>
        <?php break;

        case 'image': ?>
          <div class="upload-field">
            <?php if ($value): ?>
            <div class="upload-preview">
              <img src="<?= e(asset_url((string) $value)) ?>" alt="">
              <label class="upload-remove"><input type="checkbox" name="<?= e(name_suffix($name, '__remove')) ?>" value="1"> Hapus gambar</label>
            </div>
            <?php endif; ?>
            <input type="hidden" name="<?= e(name_suffix($name, '__current')) ?>" value="<?= e((string) $value) ?>">
            <input type="file" id="<?= e($id) ?>" name="<?= e($name) ?>" accept="image/*">
          </div>
        <?php break;

        case 'file': ?>
          <div class="upload-field">
            <?php if ($value): ?>
            <div class="upload-preview file">
              <a href="<?= e(asset_url((string) $value)) ?>" target="_blank" rel="noopener"><i class="bi bi-file-earmark-arrow-down"></i> <?= e(basename((string) $value)) ?></a>
              <label class="upload-remove"><input type="checkbox" name="<?= e(name_suffix($name, '__remove')) ?>" value="1"> Hapus berkas</label>
            </div>
            <?php endif; ?>
            <input type="hidden" name="<?= e(name_suffix($name, '__current')) ?>" value="<?= e((string) $value) ?>">
            <input type="file" id="<?= e($id) ?>" name="<?= e($name) ?>">
          </div>
        <?php break;

        case 'video': ?>
          <div class="upload-field">
            <?php if ($value): ?>
            <div class="upload-preview file">
              <a href="<?= e(asset_url((string) $value)) ?>" target="_blank" rel="noopener"><i class="bi bi-play-btn"></i> <?= e(basename((string) $value)) ?></a>
              <label class="upload-remove"><input type="checkbox" name="<?= e(name_suffix($name, '__remove')) ?>" value="1"> Hapus video</label>
            </div>
            <?php endif; ?>
            <input type="hidden" name="<?= e(name_suffix($name, '__current')) ?>" value="<?= e((string) $value) ?>">
            <input type="file" id="<?= e($id) ?>" name="<?= e($name) ?>" accept="video/*">
          </div>
        <?php break;

        default: ?>
          <input type="text" id="<?= e($id) ?>" name="<?= e($name) ?>" value="<?= e((string) $value) ?>"<?= $required . $readonly ?>>
        <?php endswitch; ?>

      <?php
      /* Sebutkan batas ukuran yang benar-benar berlaku pada tiap field
         unggahan, supaya pengguna tahu sebelum mencoba dan gagal. */
      $hint = $field['hint'] ?? '';
      if (in_array($type, ['image', 'file', 'video'], true)) {
          $batas = 'Maksimal ' . round(upload_limit_bytes($type) / 1048576, 1) . ' MB.';
          $hint  = $hint ? $hint . ' — ' . $batas : $batas;
      }
      ?>
      <?php if ($hint !== ''): ?><small class="hint"><?= e($hint) ?></small><?php endif; ?>
    </div>
    <?php
}

/**
 * Ambil nilai satu kolom dari request untuk disimpan.
 * Mengembalikan [simpan?, nilai].
 */
function field_value(string $name, array $field, ?array $existing = null): array
{
    $type = $field['type'] ?? 'text';

    if ($type === 'image' || $type === 'file' || $type === 'video') {
        return upload_value($name, $type);
    }

    if ($type === 'password') {
        $raw = (string) (post_get($name) ?? '');
        if ($raw === '') {
            return [false, null];
        }
        return [true, password_hash($raw, PASSWORD_BCRYPT)];
    }

    if ($type === 'checkbox') {
        return [true, (string) (post_get($name) ?? '0') === '1' ? 1 : 0];
    }

    $raw = post_get($name);
    if ($raw === null) {
        return [false, null];
    }
    $raw = is_string($raw) ? trim($raw) : $raw;

    if ($type === 'number') {
        /* Sebagian kolom angka membedakan "nol" dari "belum diisi" — mis.
           suhu cuaca, yang 0 °C-nya sah. Kolom itu ditandai 'nullable'. */
        if ($raw === '') {
            return [true, !empty($field['nullable']) ? null : 0];
        }
        return [true, (int) $raw];
    }
    if ($type === 'decimal') {
        return [true, $raw === '' ? null : (float) $raw];
    }
    if ($type === 'datetime') {
        /* Peramban mengirim "Y-m-dTH:i"; MySQL DATETIME ingin spasi. */
        return [true, $raw === '' ? null : str_replace('T', ' ', (string) $raw)];
    }
    if ($type === 'date') {
        return [true, $raw === '' ? null : $raw];
    }
    if ($type === 'slug') {
        return [true, $raw === '' ? '' : slugify($raw)];
    }
    if ($type === 'select' && $raw === '') {
        return [true, null];
    }

    return [true, $raw === '' ? null : $raw];
}
