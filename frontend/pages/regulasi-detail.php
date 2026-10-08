<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
$reg  = $slug !== ''
    ? konten_slug('regulasi', $slug, ['blueprint' => 'regulasi'])
    : konten_satu('regulasi', ['blueprint' => 'regulasi', 'urut' => 'k.published_at DESC, k.sort']);

if (!$reg) {
    http_response_code(404);
    page_start('regulasi', ['title' => 'Regulasi tidak ditemukan', 'breadcrumbs' => [['label' => 'Regulasi', 'url' => 'pages/regulasi'], ['label' => 'Tidak ditemukan']]]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Regulasi tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/regulasi')) . '" class="btn btn-primary btn-sm">Kembali ke Daftar Regulasi</a></div></section>';
    partial('footer');
    exit;
}

page_start('regulasi', [
    'title'       => $reg['subtitle'] . ' — Regulasi — ' . setting('site_name'),
    'description' => excerpt($reg['body'], 200),
    'breadcrumbs' => [['label' => 'Regulasi', 'url' => 'pages/regulasi'], ['label' => $reg['subtitle']]],
]);

$catLabel = $reg['sub_label'] ?? $reg['sub_slug'];
$scopes   = anak((int) $reg['id'], 'ruang-lingkup');

/* Regulasi terkait: sekategori lebih dulu, lalu yang paling baru. */
$related = array_values(array_filter(
    konten('regulasi', ['blueprint' => 'regulasi']),
    fn($r) => (int) $r['id'] !== (int) $reg['id']
));
usort($related, fn($a, $b) => [$b['sub_slug'] === $reg['sub_slug'], (int) $b['year'], (int) $a['id']]
                          <=> [$a['sub_slug'] === $reg['sub_slug'], (int) $a['year'], (int) $b['id']]);
$related = array_slice($related, 0, 3);

partial('header');
?>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start; gap:40px;">

      <div>
        <div class="flex gap-8" style="margin-bottom:16px;">
          <span class="badge badge-gray"><?= e($catLabel) ?></span>
          <span class="badge <?= ($reg['status'] ?? '') === 'berlaku' ? 'badge-success' : 'badge-gray' ?>"><?= ($reg['status'] ?? '') === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></span>
        </div>
        <h1 style="font-size:clamp(22px,2.8vw,30px); margin-bottom:8px;"><?= e($reg['subtitle']) ?></h1>
        <p style="font-size:16px; color:var(--text-700); font-weight:600; margin-bottom:26px;"><?= e($reg['title']) ?></p>

        <?php if ($reg['body']): ?>
        <h3 style="font-size:16px; margin-bottom:12px;">Tentang Regulasi Ini</h3>
        <div style="font-size:14.5px; color:var(--text-700); margin-bottom:24px;"><?= paragraphs($reg['body']) ?></div>
        <?php endif; ?>

        <?php if ($scopes): ?>
        <h3 style="font-size:16px; margin-bottom:14px;">Ruang Lingkup Utama</h3>
        <ul style="display:flex; flex-direction:column; gap:12px; margin-bottom:10px;">
          <?php foreach ($scopes as $sc): ?>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-check-circle-fill" style="color:var(--success)"></i> <?= e($sc['body']) ?></li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </div>

      <div>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Detail Regulasi</h4>
          <div style="display:flex; flex-direction:column; gap:14px; font-size:13.5px;">
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Nomor</span><b><?= e($reg['subtitle']) ?></b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Kategori</span><b><?= e($catLabel) ?></b></div>
            <?php if (!empty($reg['published_at'])): ?>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tanggal Ditetapkan</span><b><?= e(tanggal_id($reg['published_at'])) ?></b></div>
            <?php endif; ?>
            <?php if (!empty($reg['date_published'])): ?>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tanggal Diundangkan</span><b><?= e(tanggal_id($reg['date_published'])) ?></b></div>
            <?php endif; ?>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Status</span><b style="color:<?= ($reg['status'] ?? '') === 'berlaku' ? 'var(--success)' : 'var(--text-500)' ?>"><?= ($reg['status'] ?? '') === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></b></div>
          </div>
          <?php if ($reg['file']): ?>
          <a href="<?= e(asset_url($reg['file'])) ?>" class="btn btn-primary btn-block" style="margin-top:22px;" download><i class="bi bi-download"></i> Unduh Peraturan (PDF)</a>
          <?php endif; ?>
          <?php if (!empty($reg['url'])): ?>
          <a href="<?= e($reg['url']) ?>" class="btn <?= $reg['file'] ? 'btn-outline-blue' : 'btn-primary' ?> btn-block" style="margin-top:12px;" target="_blank" rel="noopener"><i class="bi bi-box-arrow-up-right"></i> Lihat di JDIH Kemenhub</a>
          <p style="font-size:12px; color:var(--text-500); margin-top:12px; line-height:1.5;">Dokumen resmi beserta berkas PDF-nya tersedia pada Jaringan Dokumentasi dan Informasi Hukum Kementerian Perhubungan.</p>
          <?php endif; ?>
        </div>
        <?php if ($related): ?>
        <div class="card card-pad">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Regulasi Terkait</h4>
          <ul style="display:flex; flex-direction:column; gap:12px;">
            <?php foreach ($related as $rel): ?>
            <li><a href="<?= e(url('pages/regulasi-detail?slug=' . urlencode($rel['slug']))) ?>" style="color:var(--primary-dark); font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:8px;"><i class="bi bi-journal-text"></i> <?= e($rel['subtitle']) ?> — <?= e(excerpt($rel['title'], 45)) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </div>

    </div>
    <div style="margin-top:40px;">
      <a href="<?= e(url('pages/regulasi')) ?>" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Daftar Regulasi</a>
    </div>
  </div>
</section>

<?php partial('footer'); ?>
