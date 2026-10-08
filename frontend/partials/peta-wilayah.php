<?php
/* Peta Otoritas Bandar Udara — bentuk peta dari indonesia-map.php, metadata wilayah dari basis data.
   $regions  konten sub kategori home/peta
   $airports id wilayah => daftar konten anak home/bandara-wilayah */
$airports   = $airports ?? [];
$obuRegions = [];
foreach ($regions as $r) {
    $list = $airports[$r['id']] ?? [];
    $obuRegions[$r['slug']] = [
        'attrs' => [
            'data-num'       => $r['numeral'] ?? '',
            'data-title'     => $r['title'],
            'data-hq'        => $r['hq_short'] ?? $r['hq'] ?? '',
            'data-coverage'  => $r['coverage'] ?? '',
            'data-airports'  => count($list),
            'aria-label'     => 'Otoritas Bandar Udara ' . $r['title'] . ' — ' . count($list) . ' bandar udara',
        ],
    ];
}
?>
    <div class="map-wrap">
      <div class="map-toolbar">
        <div class="map-hint"><i class="bi bi-hand-index-thumb"></i> Klik salah satu wilayah pada peta untuk melihat daftar bandar udaranya</div>
        <div class="map-zoom-controls">
          <button type="button" class="icon-btn" data-map-zoom-out aria-label="Perkecil peta"><i class="bi bi-dash-lg"></i></button>
          <button type="button" class="icon-btn" data-map-zoom-reset aria-label="Atur ulang zoom"><i class="bi bi-arrow-counterclockwise"></i></button>
          <button type="button" class="icon-btn" data-map-zoom-in aria-label="Perbesar peta"><i class="bi bi-plus-lg"></i></button>
        </div>
      </div>

      <div class="map-stage" data-map-stage>
        <div class="map-zoom-inner" data-map-zoom-inner>
          <?php partial('indonesia-map', [
              'mapId'      => 'obu',
              'mapRegions' => $obuRegions,
              'mapMuted'   => false,
              'mapLabel'   => 'Peta wilayah kerja Otoritas Bandar Udara Indonesia',
          ]); ?>
        </div>

        <div class="map-tooltip" data-map-tooltip>
          <span class="map-tooltip-num" data-tt-num>I</span>
          <div class="map-tooltip-body">
            <h4 data-tt-title>Wilayah I</h4>
            <span class="map-tooltip-hq" data-tt-hq></span>
            <p data-tt-coverage></p>
            <span class="map-tooltip-count" data-tt-count></span>
          </div>
        </div>
      </div>

      <!-- Daftar bandar udara wilayah yang sedang disorot -->
      <div class="map-airports" data-map-airports>
        <div class="map-airports-empty" data-airports-empty>
          <i class="bi bi-hand-index-thumb"></i> Klik salah satu wilayah pada peta atau pada daftar di bawah untuk melihat bandar udaranya.
        </div>
        <?php foreach ($regions as $r): $list = $airports[$r['id']] ?? []; if (!$list) continue; ?>
        <div class="map-airports-panel" data-airports-region="<?= e($r['slug']) ?>" hidden>
          <div class="map-airports-head">
            <span class="map-airports-num"><?= e($r['numeral'] ?? '') ?></span>
            <div>
              <h4>Otoritas Bandar Udara <?= e($r['title']) ?><?= !empty($r['hq_short']) ? ' — ' . e($r['hq_short']) : '' ?></h4>
              <span><?= count($list) ?> bandar udara &middot; <?= e($r['coverage_short'] ?? $r['coverage'] ?? '') ?></span>
            </div>
          </div>
          <ol class="map-airports-list">
            <?php foreach ($list as $a): ?><li><?= e($a['title']) ?></li><?php endforeach; ?>
          </ol>
          <?php /* Kepanjangan singkatan pengelola, muncul hanya bila dipakai di wilayah ini. */
          $adaApindo = false;
          foreach ($list as $a) {
              if (strpos((string) $a['title'], '(APINDO)') !== false) { $adaApindo = true; break; }
          }
          if ($adaApindo): ?>
          <p class="map-airports-note">APINDO — PT Angkasa Pura Indonesia</p>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>

      <div class="map-legend">
        <?php foreach ($regions as $r): ?>
        <div class="map-legend-item" data-legend-region="<?= e($r['slug']) ?>">
          <div class="map-legend-num"><?= e($r['numeral'] ?? '') ?></div>
          <div><h4><?= e($r['title']) ?><?= !empty($r['hq_short']) ? ' — ' . e($r['hq_short']) : '' ?></h4>
          <span><?= count($airports[$r['id']] ?? []) ?> bandar udara</span>
          <p><?= e($r['coverage_short'] ?? $r['coverage'] ?? '') ?></p></div>
        </div>
        <?php endforeach; ?>
      </div>

    </div>
