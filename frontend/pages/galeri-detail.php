<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$slug  = trim($_GET['slug'] ?? '');
$album = $slug !== ''
    ? konten_slug('galeri', $slug, ['sub' => 'album'])
    : konten_satu('galeri/album');

if (!$album) {
    http_response_code(404);
    page_start('galeri', ['title' => 'Album tidak ditemukan', 'breadcrumbs' => [['label' => 'Galeri', 'url' => 'pages/galeri'], ['label' => 'Tidak ditemukan']]]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Album tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/galeri')) . '" class="btn btn-primary btn-sm">Kembali ke Galeri</a></div></section>';
    partial('footer');
    exit;
}

$photos = anak((int) $album['id'], 'foto');

page_start('galeri', [
    'title'       => $album['title'] . ' — Galeri — ' . setting('site_name'),
    'description' => excerpt($album['body'], 200),
    'breadcrumbs' => [['label' => 'Galeri', 'url' => 'pages/galeri'], ['label' => excerpt($album['title'], 60)]],
]);

$meta = [
    'eyebrow'  => 'Album Foto',
    'heading'  => $album['title'],
    'subtitle' => trim(($album['body'] ?? '') . ' · ' . count($photos) . ' Foto'),
];

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">
    <div class="gallery-grid">
      <?php foreach ($photos as $photo): ?>
      <div class="gallery-item" data-lightbox data-caption="<?= e($photo['excerpt'] ?: $photo['title']) ?>">
        <?= art_block($photo['image'], $photo['art_class'], $photo['icon'], '', $photo['title']) ?>
        <span class="zoom"><i class="bi bi-zoom-in"></i></span><span class="cap"><?= e($photo['title']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php if (!$photos): ?><p style="text-align:center;padding:40px;color:var(--text-500);">Album ini belum memiliki foto.</p><?php endif; ?>
    <div style="margin-top:40px;">
      <a href="<?= e(url('pages/galeri')) ?>" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Galeri</a>
    </div>
  </div>
</section>

<?php
partial('lightbox');
partial('footer');
