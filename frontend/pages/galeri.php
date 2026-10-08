<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('galeri', ['breadcrumbs' => [['label' => 'Galeri']]]);

$photos = konten('galeri/foto');                 /* foto tanpa album */
$videos = konten('galeri/video');
$albums = konten('galeri/album');

/* Jumlah foto per album. */
$isiAlbum = konten_anak(array_column($albums, 'id'), 'foto');
foreach ($albums as $i => $album) {
    $albums[$i]['photo_count'] = count($isiAlbum[$album['id']] ?? []);
}

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">

    <div class="tabs-nav" data-tabs="gal">
      <button class="active" data-tab="foto"><i class="bi bi-image"></i>&nbsp; Foto</button>
      <button data-tab="video"><i class="bi bi-camera-reels"></i>&nbsp; Video</button>
      <button data-tab="album"><i class="bi bi-collection"></i>&nbsp; Album</button>
    </div>

    <!-- FOTO -->
    <div class="tab-panel active" data-tab-panel="gal" data-panel-id="foto">
      <div class="gallery-grid">
        <?php foreach ($photos as $photo): ?>
        <div class="gallery-item" data-lightbox data-caption="<?= e($photo['excerpt'] ?: $photo['title']) ?>">
          <?= art_block($photo['image'], $photo['art_class'], $photo['icon'], '', $photo['title']) ?>
          <span class="zoom"><i class="bi bi-zoom-in"></i></span><span class="cap"><?= e($photo['title']) ?></span>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (!$photos): ?><p style="text-align:center;padding:40px;color:var(--text-500);">Belum ada foto.</p><?php endif; ?>
    </div>

    <!-- VIDEO -->
    <div class="tab-panel" data-tab-panel="gal" data-panel-id="video">
      <div class="grid grid-3">
        <?php foreach ($videos as $video): ?>
        <div>
          <div class="video-frame" style="aspect-ratio:16/10;">
            <?php if ($video['url']): ?>
            <iframe src="<?= e(embed_url($video['url'])) ?>" title="<?= e($video['title']) ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
            <?php else: ?>
            <?= art_block($video['image'], $video['art_class'], $video['icon'], '', $video['title']) ?>
            <button class="play-btn" aria-label="Putar video" style="width:60px;height:60px;font-size:20px;"><i class="bi bi-play-fill"></i></button>
            <?php endif; ?>
          </div>
          <h4 style="font-size:14.5px; margin-top:12px;"><?= e($video['title']) ?></h4>
        </div>
        <?php endforeach; ?>
      </div>
      <?php if (!$videos): ?><p style="text-align:center;padding:40px;color:var(--text-500);">Belum ada video.</p><?php endif; ?>
    </div>

    <!-- ALBUM -->
    <div class="tab-panel" data-tab-panel="gal" data-panel-id="album">
      <div class="grid grid-3">
        <?php foreach ($albums as $album): ?>
        <a href="<?= e(url('pages/galeri-detail?slug=' . urlencode($album['slug']))) ?>" class="card" style="overflow:hidden;">
          <div class="thumb" style="aspect-ratio:4/3;"><?= art_block($album['image'], $album['art_class'], $album['icon'], '', $album['title']) ?></div>
          <div class="card-pad" style="padding:16px 18px;">
            <h4 style="font-size:14.5px;"><?= e($album['title']) ?></h4>
            <span style="font-size:12.5px; color:var(--text-500);"><?= (int) $album['photo_count'] ?> Foto</span>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <?php if (!$albums): ?><p style="text-align:center;padding:40px;color:var(--text-500);">Belum ada album.</p><?php endif; ?>
    </div>

  </div>
</section>

<?php
partial('lightbox');
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'galeri', 'bawaan' => ['tabs','album','foto','video']]);
partial('footer');
