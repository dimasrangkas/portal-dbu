<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
$svc  = $slug !== ''
    ? konten_slug('layanan', $slug, ['blueprint' => 'layanan'])
    : konten_satu('layanan', ['blueprint' => 'layanan']);

if (!$svc) {
    http_response_code(404);
    $meta = page_start('layanan', ['title' => 'Layanan tidak ditemukan', 'breadcrumbs' => [['label' => 'Layanan', 'url' => 'pages/layanan'], ['label' => 'Tidak ditemukan']]]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Layanan tidak ditemukan</h1>'
       . '<p style="color:var(--text-500);margin-bottom:24px;">Layanan yang Anda cari tidak tersedia atau telah dinonaktifkan.</p>'
       . '<a href="' . e(url('pages/layanan')) . '" class="btn btn-primary btn-sm">Kembali ke Daftar Layanan</a></div></section>';
    partial('footer');
    exit;
}

$meta = page_start('layanan', [
    'title'       => $svc['title'] . ' — Layanan — ' . setting('site_name'),
    'description' => excerpt($svc['body'], 200),
    'breadcrumbs' => [['label' => 'Layanan', 'url' => 'pages/layanan'], ['label' => excerpt($svc['title'], 60)]],
]);

$reqs      = anak((int) $svc['id'], 'persyaratan');
$steps     = anak((int) $svc['id'], 'tahap');
$downloads = anak((int) $svc['id'], 'unduhan');
/* Layanan tanpa alur/persyaratan sendiri memakai yang umum. */
if (!$steps) {
    $steps = konten('layanan/alur');
}
if (!$reqs) {
    $reqs = konten('layanan/persyaratan-umum');
}
$related = [];
foreach (konten('layanan/' . $svc['sub_slug'], ['limit' => 4]) as $rel) {
    if ((int) $rel['id'] !== (int) $svc['id'] && count($related) < 3) {
        $related[] = $rel;
    }
}

partial('header');
?>

<section class="section" style="padding-bottom:0;">
  <div class="container">
    <div class="grid" style="grid-template-columns:auto 1fr; align-items:center; gap:20px; margin-bottom:8px;">
      <div class="logo-badge" style="width:64px;height:64px;"><i class="bi <?= e($svc['icon'] ?: 'bi-patch-check-fill') ?>" style="font-size:26px"></i></div>
      <div>
        <?php if (!empty($svc['chip_label']) || !empty($svc['sifat'])): ?>
        <span class="chip"><i class="bi bi-headset"></i> <?= e($svc['chip_label'] ?? $svc['sifat'] ?? '') ?></span>
        <?php endif; ?>
        <h1 style="font-size:clamp(24px,3vw,32px); margin-top:10px;"><?= e($svc['title']) ?></h1>
      </div>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start; gap:40px;">

      <div>
        <div style="font-size:15px; color:var(--text-700); margin-bottom:28px;"><?= paragraphs($svc['body']) ?></div>

        <?php if ($reqs): ?>
        <h3 style="font-size:17px; margin-bottom:16px;"><i class="bi bi-clipboard-check" style="color:var(--primary)"></i>&nbsp; Persyaratan</h3>
        <ul style="display:flex; flex-direction:column; gap:12px; margin-bottom:32px;">
          <?php foreach ($reqs as $r): ?>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-check-circle-fill" style="color:var(--success)"></i> <?= e($r['body']) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>

        <?php if ($steps): ?>
        <h3 style="font-size:17px; margin-bottom:16px;"><i class="bi bi-signpost-split" style="color:var(--primary)"></i>&nbsp; Alur Proses</h3>
        <div class="stepper" style="margin-bottom:36px;">
          <?php foreach ($steps as $i => $step): ?>
          <div class="step"><div class="circ"><?= $i + 1 ?></div><h4><?= e($step['title']) ?></h4><p><?= e($step['body']) ?></p></div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($downloads): ?>
        <h3 style="font-size:17px; margin-bottom:16px;"><i class="bi bi-download" style="color:var(--primary)"></i>&nbsp; Unduhan Formulir</h3>
        <div class="grid grid-2">
          <?php foreach ($downloads as $dl): ?>
          <div class="card download-card">
            <div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div>
            <div><h4><?= e($dl['title']) ?></h4><span><?= e($dl['excerpt']) ?></span></div>
            <a href="<?= e($dl['file'] ? asset_url($dl['file']) : '#') ?>" class="go" aria-label="Unduh"<?= $dl['file'] ? ' download' : '' ?>><i class="bi bi-download"></i></a>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <div>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Ringkasan Layanan</h4>
          <div style="display:flex; flex-direction:column; gap:14px; font-size:13.5px;">
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Waktu Proses</span><b><?= e($svc['time_process'] ?? '') ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Biaya</span><b><?= e($svc['cost'] ?? '') ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Masa Berlaku</span><b><?= e($svc['validity'] ?? '') ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Metode</span><b><?= e($svc['method'] ?? '') ?></b></div>
          </div>
          <a href="<?= e(url($svc['cta_url'] ?? 'pages/kontak')) ?>" class="btn btn-primary btn-block" style="margin-top:22px;"><?= e($svc['cta_label'] ?? 'Ajukan Layanan Sekarang') ?></a>
        </div>
        <?php if ($related): ?>
        <div class="card card-pad">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Layanan Terkait</h4>
          <ul class="footer-links" style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach ($related as $rel): ?>
            <li><a href="<?= e(url('pages/layanan-detail?slug=' . urlencode($rel['slug']))) ?>" style="color:var(--primary-dark); font-weight:600; display:flex; align-items:center; gap:8px;"><i class="bi <?= e($rel['icon'] ?: 'bi-dot') ?>"></i> <?= e(excerpt($rel['title'], 60)) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <div style="margin-top:40px;">
      <a href="<?= e(url('pages/layanan')) ?>" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Layanan</a>
    </div>
  </div>
</section>

<?php partial('footer'); ?>
