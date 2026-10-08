<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('berita', ['breadcrumbs' => [['label' => 'Berita']]]);

$items = konten('berita', ['blueprint' => 'berita', 'urut' => 'k.published_at DESC, k.id DESC']);
/* Kategori berita kini berupa sub kategori. */
$cats   = array_values(array_filter(subkategori_semua('berita'), fn($c) => $c['blueprint'] === 'berita'));
$preselect = trim($_GET['cat'] ?? '');

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">

    <div class="filter-bar">
      <div class="grow">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari berita..." data-news-search>
      </div>
      <select data-news-cat>
        <option value="all">Semua Kategori</option>
        <?php foreach ($cats as $c): ?>
        <option value="<?= e($c['slug']) ?>"<?= $preselect === $c['slug'] ? ' selected' : '' ?>><?= e($c['label']) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="grid grid-3" data-news-grid>
      <?php foreach ($items as $item):
          $detail = url('pages/berita-detail?slug=' . urlencode($item['slug'])); ?>
      <div class="card news-card" data-news-item data-cat="<?= e($item['sub_slug']) ?>">
        <div class="thumb"><?= art_block($item['image'], $item['art_class'], $item['icon'], '', $item['title']) ?><span class="cat"><?= e($item['sub_label'] ?? $item['sub_slug']) ?></span></div>
        <div class="body">
          <div class="date"><i class="bi bi-calendar3"></i> <?= e(tanggal_id($item['published_at'])) ?></div>
          <h3><?= e($item['title']) ?></h3>
          <p><?= e($item['excerpt']) ?></p>
          <a href="<?= e($detail) ?>" class="more">Baca selengkapnya <i class="bi bi-arrow-right"></i></a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (!$items): ?>
    <p style="text-align:center; padding:40px; color:var(--text-500);">Belum ada berita yang dipublikasikan.</p>
    <?php endif; ?>

    <div class="pagination" data-news-pager></div>

  </div>
</section>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'berita', 'bawaan' => ['pengumuman-ringkas'], 'bawaan_bp' => ['berita']]);
partial('footer');
?>
