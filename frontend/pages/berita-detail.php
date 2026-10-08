<?php
require_once dirname(__DIR__) . '/bootstrap.php';

$slug = trim($_GET['slug'] ?? '');
$item = $slug !== ''
    ? konten_slug('berita', $slug, ['blueprint' => 'berita'])
    : konten_satu('berita', ['blueprint' => 'berita', 'urut' => 'k.published_at DESC']);

if (!$item) {
    http_response_code(404);
    page_start('berita', ['title' => 'Berita tidak ditemukan', 'breadcrumbs' => [['label' => 'Berita', 'url' => 'pages/berita'], ['label' => 'Tidak ditemukan']]]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Berita tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/berita')) . '" class="btn btn-primary btn-sm">Kembali ke Berita</a></div></section>';
    partial('footer');
    exit;
}

db_exec('UPDATE contents SET views = views + 1 WHERE id = ?', [$item['id']]);

page_start('berita', [
    'title'       => $item['title'] . ' — Berita — ' . setting('site_name'),
    'description' => $item['excerpt'] ?: excerpt($item['body'], 200),
    'breadcrumbs' => [['label' => 'Berita', 'url' => 'pages/berita'], ['label' => excerpt($item['title'], 60)]],
]);

$catLabel = $item['sub_label'] ?? $item['sub_slug'];

/* Berita terkait: sekategori lebih dulu, lalu yang paling baru. */
$related = array_values(array_filter(
    konten('berita', ['blueprint' => 'berita', 'urut' => 'k.published_at DESC, k.id DESC']),
    fn($r) => (int) $r['id'] !== (int) $item['id']
));
usort($related, fn($a, $b) => ($b['sub_slug'] === $item['sub_slug']) <=> ($a['sub_slug'] === $item['sub_slug']));
$related = array_slice($related, 0, 3);

$tags     = array_column(tag_konten((int) $item['id'])[$item['id']] ?? [], 'label');
$shareUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http') . '://'
          . ($_SERVER['HTTP_HOST'] ?? 'localhost') . ($_SERVER['REQUEST_URI'] ?? '');

partial('header');
?>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start; gap:40px;">

      <article>
        <span class="chip"><i class="bi <?= e($item['icon'] ?: 'bi-newspaper') ?>"></i> <?= e($catLabel) ?></span>
        <h1 style="font-size:clamp(22px,3vw,32px); margin:14px 0 16px; line-height:1.25;"><?= e($item['title']) ?></h1>
        <div class="flex items-center gap-12" style="color:var(--text-500); font-size:13.5px; margin-bottom:26px;">
          <span><i class="bi bi-calendar3"></i> <?= e(tanggal_id($item['published_at'])) ?></span>
          <?php if (!empty($item['author'])): ?>
          <span>&middot;</span>
          <span><i class="bi bi-person"></i> <?= e($item['author']) ?></span>
          <?php endif; ?>
        </div>

        <div class="director-photo" style="aspect-ratio:16/8; margin-bottom:28px;">
          <?= art_block($item['image'], $item['art_class'], $item['icon'], '', $item['title']) ?>
        </div>

        <div style="font-size:15px; color:var(--text-700);">
          <?= paragraphs($item['body'], 'news-body') ?>
        </div>

        <?php if ($tags): ?>
        <div class="flex gap-8" style="flex-wrap:wrap; margin:28px 0 10px;">
          <?php foreach ($tags as $tag): ?><span class="badge badge-gray">#<?= e($tag) ?></span><?php endforeach; ?>
        </div>
        <?php endif; ?>
      </article>

      <aside>
        <?php if ($related): ?>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Berita Terkait</h4>
          <ul style="display:flex; flex-direction:column; gap:16px;">
            <?php foreach ($related as $rel): ?>
            <li><a href="<?= e(url('pages/berita-detail?slug=' . urlencode($rel['slug']))) ?>" style="display:block; color:var(--text-900); font-weight:600; font-size:13.5px; line-height:1.4;"><?= e($rel['title']) ?></a></li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
        <div class="card card-pad">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Bagikan</h4>
          <div class="footer-social" style="margin-top:0;">
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($shareUrl) ?>" target="_blank" rel="noopener" style="background:var(--sky-50); color:var(--primary);" aria-label="Bagikan ke Facebook"><i class="bi bi-facebook"></i></a>
            <a href="https://twitter.com/intent/tweet?url=<?= urlencode($shareUrl) ?>&text=<?= urlencode($item['title']) ?>" target="_blank" rel="noopener" style="background:var(--sky-50); color:var(--primary);" aria-label="Bagikan ke Twitter"><i class="bi bi-twitter-x"></i></a>
            <a href="https://wa.me/?text=<?= urlencode($item['title'] . ' ' . $shareUrl) ?>" target="_blank" rel="noopener" style="background:var(--sky-50); color:var(--primary);" aria-label="Bagikan ke WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="<?= e($shareUrl) ?>" style="background:var(--sky-50); color:var(--primary);" aria-label="Salin tautan"><i class="bi bi-link-45deg"></i></a>
          </div>
        </div>
      </aside>

    </div>
    <div style="margin-top:40px;">
      <a href="<?= e(url('pages/berita')) ?>" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Berita</a>
    </div>
  </div>
</section>

<?php partial('footer'); ?>
