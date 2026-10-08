<?php
require_once __DIR__ . '/bootstrap.php';
$meta = page_start('home');

$slides    = konten('home/slider');
$stats     = konten('home/statistik');
$director  = konten_satu('home/sambutan');
$aboutSec  = section('home', 'about');
$aboutCard = konten('home/about');
$petaSec   = section('home', 'peta');
$regions   = konten('home/peta');
$regionAir = konten_anak(array_column($regions, 'id'), 'bandara-wilayah');
$rpjmnSec  = section('home', 'rpjmn');
$rpjmn     = konten('home/rpjmn');
foreach ($rpjmn as $i => $program) {
    $rpjmn[$i]['areas'] = anak((int) $program['id'], 'area-rpjmn');
}
$quickSec  = section('home', 'quick');
$quick     = konten('home/quick');
$newsSec   = section('home', 'berita-terbaru');
$news      = konten('berita', ['blueprint' => 'berita', 'urut' => 'k.published_at DESC, k.id DESC', 'limit' => 3]);
$announce  = konten('berita/pengumuman-ringkas', ['urut' => 'k.published_at DESC, k.sort', 'limit' => 4]);
$galSec    = section('home', 'galeri-beranda');
/* Foto yang ditandai "sorot" — boleh berasal dari album mana pun. */
$photos    = konten('galeri/foto', ['sorot' => true, 'induk' => '*', 'limit' => 8]);
$video     = konten_satu('home/video');

partial('header');
?>

<!-- ===================== HERO CAROUSEL ===================== -->
<section class="hero" aria-label="Sorotan utama">
  <?php foreach ($slides as $i => $slide): ?>
  <div class="hero-slide<?= $i === 0 ? ' active' : '' ?>">
    <?= art_block($slide['image'], $slide['art_class'], $slide['icon'], '', $slide['title']) ?>
    <div class="scrim"></div>
    <?php if ($i === 0): ?>
    <svg class="flightpath" viewBox="0 0 800 500" preserveAspectRatio="none"><path d="M-20,420 C 200,380 350,460 800,300" stroke="#8FC4F2" stroke-width="2" fill="none"/></svg>
    <?php endif; ?>
    <div class="container">
      <div class="hero-content">
        <?php if ($slide['eyebrow']): ?><div class="eyebrow"><?= e($slide['eyebrow']) ?></div><?php endif; ?>
        <h1><?= e($slide['title']) ?></h1>
        <?php if ($slide['body']): ?><p class="tag"><?= e($slide['body']) ?></p><?php endif; ?>
        <div class="hero-actions">
          <?php if (!empty($slide['btn1_label'])): ?>
          <a href="<?= e(url($slide['btn1_url'] ?? '')) ?>" class="btn btn-white"><?= e($slide['btn1_label']) ?></a>
          <?php endif; ?>
          <?php if (!empty($slide['btn2_label'])): ?>
          <a href="<?= e(url($slide['btn2_url'] ?? '')) ?>" class="btn btn-outline"><?= e($slide['btn2_label']) ?> <i class="bi bi-arrow-right"></i></a>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
  <button class="hero-arrow prev" aria-label="Sebelumnya"><i class="bi bi-chevron-left"></i></button>
  <button class="hero-arrow next" aria-label="Berikutnya"><i class="bi bi-chevron-right"></i></button>
  <div class="hero-dots"></div>
</section>

<!-- ===================== STATS BAND ===================== -->
<?php if ($stats): ?>
<div class="container" style="margin-top:-64px; position:relative; z-index:10;">
  <div class="stats-band">
    <div class="grid grid-<?= min(count($stats), 5) ?>">
      <?php foreach ($stats as $stat): ?>
      <div class="stat">
        <div class="num"><span data-count="<?= (int) ($stat['value'] ?? 0) ?>"<?= !empty($stat['suffix']) ? ' data-suffix="' . e($stat['suffix']) . '"' : '' ?>>0<?= e($stat['suffix'] ?? '') ?></span></div>
        <div class="lbl"><?= e($stat['title']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>
<?php endif; ?>

<!-- ===================== SAMBUTAN DIREKTUR ===================== -->
<?php if ($director): ?>
<section class="section">
  <div class="container">
    <div class="welcome-grid">
      <div class="director-photo">
        <?= art_block($director['image'], $director['art_class'], $director['icon'], '', $director['director_name'] ?? '') ?>
        <?php if (!empty($director['director_name'])): ?>
        <div class="frame-tag"><b><?= e($director['director_name']) ?></b><span><?= e($director['director_position'] ?? '') ?></span></div>
        <?php endif; ?>
      </div>
      <div>
        <?php if ($director['eyebrow']): ?><div class="eyebrow"><?= e($director['eyebrow']) ?></div><?php endif; ?>
        <h2 style="font-size:clamp(22px,2.8vw,30px); margin-bottom:18px;"><?= e($director['title']) ?></h2>
        <span class="quote-mark">&ldquo;</span>
        <div style="font-size:15.5px; color:var(--text-700); margin-top:-18px;">
          <?= paragraphs($director['body']) ?>
        </div>
        <?php if (!empty($director['btn_label'])): ?>
        <div style="margin-top:24px; display:flex; gap:14px; flex-wrap:wrap;">
          <a href="<?= e(url($director['btn_url'] ?? '')) ?>" class="btn btn-primary btn-sm"><?= e($director['btn_label']) ?></a>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ===================== TENTANG DIREKTORAT ===================== -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head center">
      <?php if ($aboutSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($aboutSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($aboutSec['title']) ?></h2>
      <?php if ($aboutSec['subtitle']): ?><p><?= e($aboutSec['subtitle']) ?></p><?php endif; ?>
    </div>
    <div class="grid grid-<?= min(max(count($aboutCard), 1), 5) ?>">
      <?php foreach ($aboutCard as $card): ?>
      <div class="card icon-card card-pad">
        <div class="ic"><i class="bi <?= e($card['icon']) ?>"></i></div>
        <h3><?= e($card['title']) ?></h3>
        <p><?= e($card['body']) ?></p>
        <?php if ($card['url']): ?><a href="<?= e(url($card['url'])) ?>" class="more">Lihat detail <i class="bi bi-arrow-right"></i></a><?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== PETA WILAYAH ===================== -->
<section class="section" id="peta-wilayah">
  <div class="container">
    <div class="section-head center">
      <?php if ($petaSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($petaSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($petaSec['title']) ?></h2>
      <?php if ($petaSec['subtitle']): ?><p><?= e($petaSec['subtitle']) ?></p><?php endif; ?>
    </div>
    <?php partial('peta-wilayah', ['regions' => $regions, 'airports' => $regionAir]); ?>
  </div>
</section>

<!-- ===================== INDIKASI PEMBANGUNAN / PENGEMBANGAN BANDAR UDARA (RPJMN) ===================== -->
<?php if ($rpjmn): ?>
<section class="section section-alt" id="rpjmn">
  <div class="container">
    <div class="section-head center">
      <?php if ($rpjmnSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($rpjmnSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($rpjmnSec['title']) ?></h2>
      <?php if ($rpjmnSec['subtitle']): ?><p><?= e($rpjmnSec['subtitle']) ?></p><?php endif; ?>
    </div>
    <?php partial('rpjmn-carousel', ['programs' => $rpjmn, 'regions' => $regions]); ?>
    <?php if (!empty($rpjmnSec['body'])): ?>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<!-- ===================== AKSES CEPAT ===================== -->
<section class="section">
  <div class="container">
    <div class="section-head center">
      <?php if ($quickSec['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($quickSec['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($quickSec['title']) ?></h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($quick as $link): ?>
      <a href="<?= e(url($link['url'])) ?>" class="card quick-card"><div class="ic"><i class="bi <?= e($link['icon']) ?>"></i></div><span><?= e($link['title']) ?></span></a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== BERITA & PENGUMUMAN ===================== -->
<section class="section section-alt">
  <div class="container">
    <div class="section-head-row">
      <div class="section-head">
        <?php if ($newsSec['eyebrow']): ?><div class="eyebrow"><?= e($newsSec['eyebrow']) ?></div><?php endif; ?>
        <h2><?= e($newsSec['title']) ?></h2>
      </div>
      <a href="<?= e(url('pages/berita')) ?>" class="btn btn-outline-blue btn-sm">Lihat Semua Berita <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start;">
      <div class="grid grid-3">
        <?php foreach ($news as $item):
            $detail = url('pages/berita-detail?slug=' . urlencode($item['slug'])); ?>
        <div class="card news-card">
          <div class="thumb"><?= art_block($item['image'], $item['art_class'], $item['icon'], '', $item['title']) ?><span class="cat"><?= e($item['sub_label'] ?? '') ?></span></div>
          <div class="body">
            <div class="date"><i class="bi bi-calendar3"></i> <?= e(tanggal_id($item['published_at'])) ?></div>
            <h3><?= e($item['title']) ?></h3>
            <p><?= e($item['excerpt']) ?></p>
            <a href="<?= e($detail) ?>" class="more">Baca selengkapnya <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="card card-pad">
        <h3 style="font-size:16px; margin-bottom:6px;">Pengumuman</h3>
        <div class="announce-list">
          <?php foreach ($announce as $a): ?>
          <a class="announce-item" href="<?= e(url($a['url'] ?: '#')) ?>">
            <div class="announce-date"><div class="d"><?= e(date('d', strtotime($a['published_at']))) ?></div><div class="m"><?= e(bulan_singkat($a['published_at'])) ?></div></div>
            <div class="announce-info"><h4><?= e($a['title']) ?></h4><span><?= e($a['category'] ?? '') ?></span></div>
          </a>
          <?php endforeach; ?>
        </div>
        <a href="<?= e(url('pages/berita?cat=pengumuman')) ?>" class="btn btn-outline-blue btn-sm btn-block" style="margin-top:18px;">Semua Pengumuman</a>
      </div>
    </div>
  </div>
</section>

<!-- ===================== GALERI ===================== -->
<section class="section section-sky">
  <div class="container">
    <div class="section-head-row">
      <div class="section-head">
        <?php if ($galSec['eyebrow']): ?><div class="eyebrow"><?= e($galSec['eyebrow']) ?></div><?php endif; ?>
        <h2><?= e($galSec['title']) ?></h2>
      </div>
      <a href="<?= e(url('pages/galeri')) ?>" class="btn btn-outline-blue btn-sm">Lihat Galeri Lengkap <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="gallery-grid">
      <?php foreach ($photos as $photo): ?>
      <div class="gallery-item" data-lightbox data-caption="<?= e($photo['excerpt'] ?: $photo['title']) ?>">
        <?= art_block($photo['image'], $photo['art_class'], $photo['icon'], '', $photo['title']) ?>
        <span class="zoom"><i class="bi bi-zoom-in"></i></span><span class="cap"><?= e($photo['title']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ===================== VIDEO PROFIL ===================== -->
<?php if ($video): ?>
<section class="section">
  <div class="container" style="max-width:920px;">
    <div class="section-head center">
      <?php if ($video['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($video['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($video['title']) ?></h2>
    </div>
    <?php if ($video['url']): ?>
    <div class="video-frame">
      <iframe src="<?= e(embed_url($video['url'])) ?>" title="<?= e($video['title']) ?>" style="position:absolute;inset:0;width:100%;height:100%;border:0;" allowfullscreen loading="lazy"></iframe>
    </div>
    <?php else: ?>
    <div class="video-frame">
      <?= art_block($video['image'], $video['art_class'], $video['icon'], '', $video['title']) ?>
      <button class="play-btn" aria-label="Putar video"><i class="bi bi-play-fill"></i></button>
    </div>
    <?php endif; ?>
    <?php if ($video['excerpt']): ?>
    <p style="text-align:center; color:var(--text-500); font-size:13px; margin-top:14px;"><?= e($video['excerpt']) ?></p>
    <?php endif; ?>
  </div>
</section>
<?php endif; ?>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'home', 'bawaan' => ['slider','statistik','sambutan','about','quick','services','peta','rpjmn','berita-terbaru','galeri-beranda','video','partners','newsletter']]);
partial('footer');
?>
