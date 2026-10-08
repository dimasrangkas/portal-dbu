<?php
/* ============================================================
   Seksi tambahan buatan CMS

   Tiap halaman portal merender sendiri seksi bawaannya. Partial ini
   melengkapi: seksi apa pun yang ditambahkan lewat panel CMS pada
   halaman ini — dan karenanya tidak dikenal templat — ikut tampil di
   sini, mengikuti urutan seksi yang diatur di CMS.

   $kategori   slug kategori halaman, mis. 'profil'
   $bawaan     slug seksi yang sudah dirender templat halaman ini
   $bawaan_bp  cetak biru yang sudah dirender templat — dipakai halaman
               yang seksinya bisa bertambah sendiri (berita, regulasi, layanan)
   ============================================================ */
$bawaan    = $bawaan ?? [];
$bawaan_bp = $bawaan_bp ?? [];
$tambahan  = [];

foreach (subkategori_semua($kategori) as $slug => $seksi) {
    if (in_array($slug, $bawaan, true) || in_array($seksi['blueprint'], $bawaan_bp, true)) {
        continue;
    }
    $isi = konten($kategori . '/' . $slug);
    if (!$isi && trim((string) $seksi['body']) === '' && trim((string) $seksi['title']) === '') {
        continue;
    }
    $tambahan[] = [$seksi, $isi];
}

foreach ($tambahan as $i => [$seksi, $isi]):
    $kolom = min(max(count($isi), 1), 4);
?>
<section class="section<?= $i % 2 === 0 ? ' section-alt' : '' ?>" id="<?= e($seksi['slug']) ?>">
  <div class="container">
    <?php if ($seksi['eyebrow'] || $seksi['title'] || $seksi['subtitle']): ?>
    <div class="section-head center">
      <?php if ($seksi['eyebrow']): ?><div class="eyebrow" style="justify-content:center"><?= e($seksi['eyebrow']) ?></div><?php endif; ?>
      <?php if ($seksi['title']): ?><h2><?= e($seksi['title']) ?></h2><?php endif; ?>
      <?php if ($seksi['subtitle']): ?><p><?= e($seksi['subtitle']) ?></p><?php endif; ?>
    </div>
    <?php endif; ?>

    <?php if (trim((string) $seksi['body']) !== ''): ?>
    <div class="info-box"><?= paragraphs($seksi['body']) ?></div>
    <?php endif; ?>

    <?php if ($isi): ?>
    <div class="grid grid-<?= $kolom ?>">
      <?php foreach ($isi as $item): ?>
      <div class="card icon-card card-pad">
        <?php if (!empty($item['image'])): ?>
        <div class="thumb" style="aspect-ratio:16/10; margin:-22px -22px 16px;">
          <?= art_block($item['image'], $item['art_class'], $item['icon'], '', (string) $item['title']) ?>
        </div>
        <?php elseif (!empty($item['icon'])): ?>
        <div class="ic"><i class="bi <?= e($item['icon']) ?>"></i></div>
        <?php endif; ?>

        <?php if (!empty($item['title'])): ?><h3><?= e($item['title']) ?></h3><?php endif; ?>
        <?php if (!empty($item['excerpt'])): ?><p><?= e($item['excerpt']) ?></p><?php endif; ?>
        <?php if (!empty($item['body'])): ?><div style="font-size:14px; color:var(--text-700);"><?= paragraphs($item['body']) ?></div><?php endif; ?>

        <?php if (!empty($item['file'])): ?>
        <a href="<?= e(asset_url($item['file'])) ?>" class="more" download><i class="bi bi-download"></i> Unduh berkas</a>
        <?php elseif (!empty($item['url'])): ?>
        <a href="<?= e(url($item['url'])) ?>" class="more">Selengkapnya <i class="bi bi-arrow-right"></i></a>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>
<?php endforeach; ?>
