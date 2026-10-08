<?php
/* Pengaturan situs — tersimpan sebagai pasangan kunci/nilai di tabel settings */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/layout.php';
require_once __DIR__ . '/includes/fields.php';
require_once __DIR__ . '/includes/upload.php';

require_login();

$groupLabels = [
    'umum'   => ['Identitas Situs', 'bi-globe'],
    'kontak' => ['Kontak & Lokasi', 'bi-geo-alt'],
    'sosial' => ['Media Sosial', 'bi-share'],
    'footer' => ['Footer & Disclaimer', 'bi-layout-text-window-reverse'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_check();
    foreach (db_all('SELECT * FROM settings') as $setting) {
        $key = $setting['setting_key'];

        if ($setting['type'] === 'image') {
            [$store, $value] = upload_value($key, 'image');
            if (!$store) {
                continue;
            }
        } else {
            if (!array_key_exists($key, $_POST)) {
                continue;
            }
            $value = trim((string) $_POST[$key]);
        }
        db_exec('UPDATE settings SET value = ? WHERE setting_key = ?', [$value, $key]);
    }
    flash('success', 'Pengaturan situs berhasil disimpan.');
    redirect(admin_url('settings.php'));
}

$grouped = [];
foreach (db_all('SELECT * FROM settings ORDER BY group_key, sort, id') as $setting) {
    $grouped[$setting['group_key']][] = $setting;
}

admin_head('Pengaturan Situs', [['label' => 'Pengaturan Situs']]);
?>

<div class="page-head">
  <div>
    <h1>Pengaturan Situs</h1>
    <p>Identitas, kontak, media sosial, dan teks footer yang dipakai di seluruh halaman.</p>
  </div>
</div>

<form class="form" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>

  <?php foreach ($grouped as $group => $items):
      [$label, $icon] = $groupLabels[$group] ?? [ucfirst($group), 'bi-sliders']; ?>
  <div class="card">
    <div class="card-head"><h2><i class="bi <?= e($icon) ?>"></i> <?= e($label) ?></h2></div>
    <div class="form-grid">
      <?php foreach ($items as $setting):
          $type  = $setting['type'] === 'url' || $setting['type'] === 'email' ? 'text' : $setting['type'];
          $field = [
              'label' => $setting['label'],
              'type'  => $type,
              'hint'  => $setting['hint'],
              'col'   => in_array($type, ['textarea', 'image'], true) ? 'full' : 'half',
          ];
          render_field($setting['setting_key'], $field, $setting['value']); ?>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endforeach; ?>

  <div class="form-actions">
    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg"></i> Simpan Pengaturan</button>
  </div>
</form>

<?php admin_foot(); ?>
