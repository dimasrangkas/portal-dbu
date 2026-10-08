<?php
/* ============================================================
   Korsel "Indikasi Pembangunan/Pengembangan Bandar Udara" (RPJMN 2025–2029).
   Setiap bagian menyorot wilayah kerja Otoritas Bandar Udara terkait pada
   satu peta Indonesia bersama; sorotan berganti mengikuti bagian yang aktif.

   Variabel: $programs (beserta kunci 'areas'), $regions (konten home/peta).
   ============================================================ */
$regionMeta = [];
foreach ($regions as $r) {
    $regionMeta[$r['slug']] = $r;
}

/* Hanya wilayah yang dipakai minimal satu bagian yang dibuat interaktif. */
$mapRegions = [];
foreach ($programs as $program) {
    foreach ($program['areas'] as $area) {
        $code = $area['slug'];
        if (isset($mapRegions[$code])) {
            continue;
        }
        $r = $regionMeta[$code] ?? null;
        $mapRegions[$code] = ['attrs' => [
            'data-num'      => $r['numeral'] ?? $code,
            'data-title'    => $r ? 'Otoritas Bandar Udara ' . $r['title'] : 'Wilayah ' . $code,
            'data-coverage' => $r ? ($r['coverage_short'] ?? $r['coverage'] ?? '') : '',
            'aria-label'    => $r ? 'Otoritas Bandar Udara ' . $r['title'] : 'Wilayah ' . $code,
        ]];
    }
}
$total = count($programs);
?>
<div class="rpjmn" data-rpjmn data-rpjmn-interval="3000">
  <div class="rpjmn-grid">

    <div class="rpjmn-panes">
      <?php foreach ($programs as $i => $program): ?>
      <article class="rpjmn-pane<?= $i === 0 ? ' active' : '' ?>" data-rpjmn-pane="<?= $i ?>"
               role="tabpanel" aria-label="Bagian <?= $i + 1 ?> dari <?= $total ?>"<?= $i === 0 ? '' : ' aria-hidden="true"' ?>>
        <div class="rpjmn-pane-head">
          <span class="rpjmn-step">Bagian <?= $i + 1 ?> / <?= $total ?></span>
          <?php if ($program['eyebrow']): ?><span class="rpjmn-eyebrow"><?= e($program['eyebrow']) ?></span><?php endif; ?>
        </div>

        <div class="rpjmn-title">
          <?php if ($program['icon']): ?><span class="rpjmn-icon"><i class="bi <?= e($program['icon']) ?>"></i></span><?php endif; ?>
          <h3><?= e($program['title']) ?></h3>
        </div>

        <?php if ($program['body']): ?><p class="rpjmn-summary"><?= e($program['body']) ?></p><?php endif; ?>

        <?php if (!empty($program['focus'])): ?>
        <div class="rpjmn-note focus"><i class="bi bi-flag-fill"></i><span><?= e($program['focus']) ?></span></div>
        <?php endif; ?>
        <?php if (!empty($program['note'])): ?>
        <div class="rpjmn-note"><i class="bi bi-info-circle-fill"></i><span><?= e($program['note']) ?></span></div>
        <?php endif; ?>

        <?php if ($program['areas']): ?>
        <ul class="rpjmn-areas">
          <?php foreach ($program['areas'] as $area):
              $r = $regionMeta[$area['slug']] ?? null; ?>
          <li class="rpjmn-area" data-rpjmn-area="<?= e($area['slug']) ?>"
              data-airports="<?= e($area['body']) ?>">
            <span class="rpjmn-area-num"><?= e($r['numeral'] ?? $area['slug']) ?></span>
            <div>
              <b><?= e($r ? 'Otoritas Bandar Udara ' . $r['title'] : 'Wilayah ' . $area['slug']) ?></b>
              <span><?= e($area['body']) ?></span>
            </div>
          </li>
          <?php endforeach; ?>
        </ul>
        <?php endif; ?>
      </article>
      <?php endforeach; ?>
    </div>

    <div class="rpjmn-map">
      <div class="rpjmn-map-hint"><i class="bi bi-hand-index-thumb"></i> Arahkan kursor ke wilayah yang menyala</div>
      <div class="rpjmn-map-stage" data-rpjmn-map>
        <?php partial('indonesia-map', [
            'mapId'      => 'rpjmn',
            'mapRegions' => $mapRegions,
            'mapMuted'   => true,
            'mapLabel'   => 'Peta wilayah indikasi pembangunan dan pengembangan bandar udara',
        ]); ?>
        <div class="map-tooltip" data-rpjmn-tooltip>
          <span class="map-tooltip-num" data-rt-num>I</span>
          <div class="map-tooltip-body">
            <h4 data-rt-title></h4>
            <span class="map-tooltip-hq" data-rt-coverage></span>
            <p data-rt-airports></p>
          </div>
        </div>
      </div>
    </div>

  </div>

  <div class="rpjmn-nav">
    <button type="button" class="icon-btn" data-rpjmn-prev aria-label="Bagian sebelumnya"><i class="bi bi-chevron-left"></i></button>
    <div class="rpjmn-dots" role="tablist">
      <?php foreach ($programs as $i => $program): ?>
      <button type="button" class="rpjmn-dot<?= $i === 0 ? ' active' : '' ?>" data-rpjmn-dot="<?= $i ?>"
              role="tab" aria-label="<?= e($program['title']) ?>"<?= $i === 0 ? ' aria-selected="true"' : '' ?>></button>
      <?php endforeach; ?>
    </div>
    <button type="button" class="icon-btn" data-rpjmn-next aria-label="Bagian berikutnya"><i class="bi bi-chevron-right"></i></button>
  </div>
</div>
