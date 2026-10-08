<?php
/* ============================================================
   Peta Indonesia yang dapat di-hover, dirender dari map-shapes.php.

   Variabel yang diterima (lewat partial('indonesia-map', [...])):
     $mapId       Awalan id unik — wajib bila ada lebih dari satu peta per halaman.
     $mapRegions  region_code => ['attrs' => ['data-x' => 'y', ...], 'class' => '...'].
                  Hanya kode pada daftar ini yang mendapat atribut & fokus keyboard.
     $mapMuted    true  = kode di luar $mapRegions tetap digambar sebagai bentuk pasif.
                  false = kode di luar $mapRegions tidak digambar sama sekali.
     $mapLabel    Teks aria-label untuk keseluruhan peta.
   ============================================================ */
$shapes     = require __DIR__ . '/map-shapes.php';
$mapId      = $mapId      ?? 'map';
$mapRegions = $mapRegions ?? [];
$mapMuted   = $mapMuted   ?? true;
$mapLabel   = $mapLabel   ?? 'Peta Indonesia';
$gridId     = $mapId . 'Grid';
?>
<svg viewBox="0 0 1180 464" class="indonesia-map" role="img" aria-label="<?= e($mapLabel) ?>">
  <defs>
    <pattern id="<?= e($gridId) ?>" width="36" height="36" patternUnits="userSpaceOnUse">
      <circle cx="1.2" cy="1.2" r="1.2" fill="#005BAC" opacity="0.10"></circle>
    </pattern>
  </defs>
  <rect x="0" y="0" width="1180" height="464" fill="url(#<?= e($gridId) ?>)"></rect>

  <?php foreach ($shapes['regions'] as $code => $shape):
      $conf = $mapRegions[$code] ?? null;
      if (!$conf && !$mapMuted) {
          continue;
      }
      $classes = 'region' . ($conf ? '' : ' is-muted') . (isset($conf['class']) ? ' ' . $conf['class'] : ''); ?>
  <g class="<?= e($classes) ?>" data-region="<?= e((string) $code) ?>"
     <?php if ($conf): ?>tabindex="0" focusable="true" role="button"<?php endif; ?>
     <?php foreach (($conf['attrs'] ?? []) as $attr => $val): ?><?= e($attr) ?>="<?= e((string) $val) ?>" <?php endforeach; ?>>
    <?php foreach ($shape['paths'] as $d): ?>
    <path d="<?= e($d) ?>" fill-rule="evenodd"></path>
    <?php endforeach; ?>
    <?php /* dy .35em memusatkan glif pada titik jangkar — titik itulah yang diuji berada di daratan. */ ?>
    <text x="<?= (int) $shape['label']['x'] ?>" y="<?= (int) $shape['label']['y'] ?>" dy=".35em"><?= e($shape['label']['text']) ?></text>
  </g>
  <?php endforeach; ?>

  <?php foreach ($shapes['islands'] as $island): ?>
  <text class="map-island-label" x="<?= (int) $island['x'] ?>" y="<?= (int) $island['y'] ?>"><?= e($island['text']) ?></text>
  <?php endforeach; ?>
</svg>
