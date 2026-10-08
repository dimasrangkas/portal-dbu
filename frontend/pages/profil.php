<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('profil', ['breadcrumbs' => [['label' => 'Profil']]]);

$about    = konten_satu('profil/tentang');
$timeline = konten('profil/sejarah');
$missions = konten('profil/misi');
$values   = konten('profil/nilai');
$goals    = konten('profil/sasaran');
$funcs    = konten('profil/fungsi');
$s        = sections('profil');

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<!-- sub nav lengket -->
<div style="position:sticky; top:0; z-index:150; background:#fff; border-bottom:1px solid var(--border);">
  <div class="container">
    <div class="tabs-nav" style="margin-bottom:0; border-bottom:none;">
      <button onclick="document.getElementById('tentang').scrollIntoView({behavior:'smooth'})">Tentang</button>
      <button onclick="document.getElementById('sejarah').scrollIntoView({behavior:'smooth'})">Sejarah</button>
      <button onclick="document.getElementById('visi-misi').scrollIntoView({behavior:'smooth'})">Visi &amp; Misi</button>
      <button onclick="document.getElementById('nilai').scrollIntoView({behavior:'smooth'})">Nilai Organisasi</button>
      <button onclick="document.getElementById('sasaran').scrollIntoView({behavior:'smooth'})">Sasaran Strategis</button>
      <button onclick="document.getElementById('tugas-pokok').scrollIntoView({behavior:'smooth'})">Tugas Pokok</button>
      <button onclick="document.getElementById('fungsi').scrollIntoView({behavior:'smooth'})">Fungsi</button>
    </div>
  </div>
</div>

<!-- TENTANG -->
<section class="section" id="tentang">
  <div class="container">
    <div class="welcome-grid">
      <div>
        <?php if (!empty($about['eyebrow'])): ?><div class="eyebrow"><?= e($about['eyebrow']) ?></div><?php endif; ?>
        <h2 style="font-size:clamp(22px,2.8vw,30px); margin-bottom:18px;"><?= e($about['title'] ?? '') ?></h2>
        <div style="font-size:15px; color:var(--text-700);">
          <?= paragraphs($about['body'] ?? '') ?>
        </div>
      </div>
      <div class="director-photo" style="aspect-ratio:4/3.6;">
        <?= art_block($about['image'] ?? null, $about['art_class'] ?? 'art-6', $about['icon'] ?? null, '', $about['title'] ?? '') ?>
      </div>
    </div>
  </div>
</section>

<!-- SEJARAH -->
<?php if ($timeline): ?>
<section class="section section-alt" id="sejarah">
  <div class="container" style="max-width:820px;">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($s['sejarah']['eyebrow'] ?? 'Sejarah') ?></div>
      <h2><?= e($s['sejarah']['title'] ?? '') ?></h2>
    </div>
    <div class="timeline">
      <?php foreach ($timeline as $item): ?>
      <div class="timeline-item">
        <div class="yr"><?= e($item['year'] ?? '') ?></div>
        <h4><?= e($item['title']) ?></h4>
        <p><?= e($item['body']) ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- VISI MISI -->
<section class="section" id="visi-misi">
  <div class="container">
    <div class="grid grid-2" style="align-items:start; gap:40px;">
      <div>
        <div class="eyebrow"><?= e($s['visi']['eyebrow'] ?? 'Visi') ?></div>
        <h2 style="font-size:24px; margin-bottom:18px;"><?= e($s['visi']['title'] ?? 'Visi Direktorat') ?></h2>
        <div class="info-box">
          <p style="font-size:16px; font-style:italic; color:var(--primary-dark); font-weight:600;">
            <?= e($about['visi'] ?? '') ?>
          </p>
        </div>
      </div>
      <div>
        <div class="eyebrow"><?= e($s['misi']['eyebrow'] ?? 'Misi') ?></div>
        <h2 style="font-size:24px; margin-bottom:18px;"><?= e($s['misi']['title'] ?? 'Misi Direktorat') ?></h2>
        <?php foreach ($missions as $i => $m): ?>
        <div class="mission-item"><div class="n"><?= $i + 1 ?></div><p><?= e($m['body']) ?></p></div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>

<!-- NILAI ORGANISASI -->
<?php if ($values): ?>
<section class="section section-alt" id="nilai">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($s['nilai']['eyebrow'] ?? 'Nilai Organisasi') ?></div>
      <h2><?= e($s['nilai']['title'] ?? '') ?></h2>
    </div>
    <div class="grid grid-<?= min(max(count($values), 1), 5) ?>">
      <?php foreach ($values as $v): ?>
      <div class="card value-card"><div class="ic"><i class="bi <?= e($v['icon']) ?>"></i></div><h3 style="font-size:14.5px;"><?= e($v['title']) ?></h3></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- SASARAN STRATEGIS -->
<?php if ($goals): ?>
<section class="section" id="sasaran">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($s['sasaran']['eyebrow'] ?? 'Sasaran Strategis') ?></div>
      <h2><?= e($s['sasaran']['title'] ?? '') ?></h2>
    </div>
    <div class="grid grid-4">
      <?php foreach ($goals as $g): ?>
      <div class="card icon-card card-pad"><div class="ic"><i class="bi <?= e($g['icon']) ?>"></i></div><h3><?= e($g['title']) ?></h3><p><?= e($g['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- TUGAS POKOK -->
<?php if (!empty($s['tugas-pokok']['body'])): ?>
<section class="section section-alt" id="tugas-pokok">
  <div class="container" style="max-width:900px;">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($s['tugas-pokok']['eyebrow'] ?? 'Tugas Pokok') ?></div>
      <h2><?= e($s['tugas-pokok']['title'] ?? 'Tugas Direktorat Bandar Udara') ?></h2>
    </div>
    <div class="info-box">
      <h3><i class="bi bi-bullseye"></i>&nbsp; Tugas</h3>
      <?= paragraphs($s['tugas-pokok']['body']) ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- FUNGSI -->
<?php if ($funcs): ?>
<section class="section" id="fungsi">
  <div class="container" style="max-width:960px;">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center"><?= e($s['fungsi']['eyebrow'] ?? 'Fungsi') ?></div>
      <h2><?= e($s['fungsi']['title'] ?? 'Fungsi Direktorat Bandar Udara') ?></h2>
      <?php if (!empty($s['fungsi']['subtitle'])): ?><p><?= e($s['fungsi']['subtitle']) ?></p><?php endif; ?>
    </div>
    <ol class="fungsi-list">
      <?php foreach ($funcs as $i => $f): ?>
      <li class="fungsi-item">
        <span class="fungsi-num"><?= $i + 1 ?></span>
        <div class="fungsi-body">
          <?php if ($f['icon']): ?><i class="bi <?= e($f['icon']) ?>"></i><?php endif; ?>
          <p><?= e($f['body']) ?></p>
        </div>
      </li>
      <?php endforeach; ?>
    </ol>
  </div>
</section>
<?php endif; ?>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'profil', 'bawaan' => ['tentang','sejarah','visi','misi','nilai','sasaran','tugas-pokok','fungsi']]);
partial('footer');
?>
