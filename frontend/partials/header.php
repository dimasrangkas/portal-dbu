<?php
/* Kepala dokumen + topbar + header + navbar + disclaimer */
$ctx  = page_ctx();
$navs = menu_items('navbar');
$currentFile = $ctx['file'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($ctx['title']) ?></title>
<meta name="description" content="<?= e($ctx['description']) ?>">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= e(asset_version(url('css/style.css'), FRONTEND_PATH, 'css/style.css')) ?>">
<?php /* Aset tambahan khusus satu halaman: isi $GLOBALS['dbu_head'] sebelum memanggil partial ini. */ ?>
<?= $GLOBALS['dbu_head'] ?? '' ?>
</head>
<body>

<!-- ===================== TOP BAR ===================== -->
<div class="topbar">
  <div class="container">
    <div class="topbar-left">
      <span class="hide-sm"><i class="bi bi-geo-alt"></i> <?= e(setting('contact_city')) ?></span>
      <span class="hide-sm"><i class="bi bi-envelope"></i> <?= e(setting('contact_email')) ?></span>
    </div>
    <div class="topbar-right">
      <div class="social-mini">
        <?php foreach (social_links() as $s): ?>
        <a href="<?= eurl($s['url']) ?>" aria-label="<?= e($s['label']) ?>"><i class="bi <?= e($s['icon']) ?>"></i></a>
        <?php endforeach; ?>
      </div>
      <div class="lang-switch"><button class="active">ID</button><span>/</span><button>EN</button></div>
    </div>
  </div>
</div>

<div class="sticky-stack">

  <!-- ===================== HEADER ===================== -->
  <header class="header">
    <div class="container brandrow">
      <a href="<?= e(url('')) ?>" class="brand">
        <div class="brand-logos">
          <div class="logo-badge">
            <img src="<?= e(asset_url(setting('logo_primary', 'assets/images/kemenhub.png'))) ?>" alt="Logo Kementerian Perhubungan">
          </div>
          <div class="brand-div"></div>
          <div class="logo-badge small">
            <img src="<?= e(asset_url(setting('logo_secondary', 'assets/images/logo-dbu.png'))) ?>" alt="Logo Direktorat Bandar Udara">
          </div>
        </div>
        <div class="brand-text">
          <div class="kop"><?= e(setting('brand_kop')) ?></div>
          <div class="name"><?= e(setting('brand_name')) ?> <span><?= e(setting('brand_name_accent')) ?></span></div>
        </div>
      </a>
      <div class="header-actions">
        <div class="search-box">
          <input type="text" name="q" placeholder="Cari informasi...">
        </div>
        <button class="icon-btn" data-search-toggle aria-label="Cari"><i class="bi bi-search"></i></button>
        <button class="icon-btn burger" aria-label="Menu"><i class="bi bi-list"></i></button>
      </div>
    </div>
  </header>

  <!-- ===================== NAVBAR ===================== -->
  <nav class="navbar">
    <div class="container">
      <ul class="navlist">
        <?php foreach ($navs as $item):
            $match  = split_list($item['match_pages'], ' ');
            $active = $match && in_array($currentFile, $match, true);
        ?>
        <li<?= $active ? ' class="active"' : '' ?>>
          <a href="<?= e($item['is_external'] ? $item['url'] : url($item['url'])) ?>"<?= $item['is_external'] ? ' target="_blank" rel="noopener"' : '' ?>>
            <?= e($item['label']) ?>
          </a>
          <?php if ($item['children']): ?>
          <?php /* Panel dibuka dengan diklik, bukan saat kursor lewat. Label dan panah
                    sama-sama jadi pemicunya (lihat main.js); panahnya tetap tombol
                    tersendiri agar terbaca pembaca layar sebagai kontrol buka-tutup. */ ?>
          <button class="nav-toggle" type="button" aria-expanded="false"
                  aria-label="Buka submenu <?= e($item['label']) ?>"><i class="bi bi-chevron-down"></i></button>
          <?php endif; ?>
          <?php if ($item['children']):
              /* Anak menu dikelompokkan per group_label menjadi kolom mega menu. */
              $kolom = [];
              foreach ($item['children'] as $child) {
                  $kolom[$child['group_label'] ?: ''][] = $child;
              }
          ?>
          <div class="megamenu">
            <div class="container">
              <div class="megamenu-grid" style="--kolom:<?= count($kolom) ?>">
                <?php foreach ($kolom as $judul => $anak): ?>
                <div class="megamenu-col">
                  <?php if ($judul !== ''): ?><h4><?= e($judul) ?></h4><?php endif; ?>
                  <?php foreach ($anak as $child):
                      $href  = $child['is_external'] ? $child['url'] : url($child['url']);
                      $gaya  = $child['style'] ?? 'link';
                      $extra = $child['is_external'] ? ' target="_blank" rel="noopener"' : ''; ?>
                  <?php if ($gaya === 'link'): ?>
                  <a class="megamenu-item" href="<?= e($href) ?>"<?= $extra ?>>
                    <?php if ($child['icon']): ?><span class="megamenu-ic"><i class="bi <?= e($child['icon']) ?>"></i></span><?php endif; ?>
                    <span class="megamenu-text">
                      <b><?= e($child['label']) ?></b>
                      <?php if ($child['description']): ?><small><?= e($child['description']) ?></small><?php endif; ?>
                    </span>
                  </a>
                  <?php else: ?>
                  <a class="megamenu-btn<?= $gaya === 'button-primary' ? ' is-primary' : '' ?>" href="<?= e($href) ?>"<?= $extra ?>>
                    <span><?= e($child['label']) ?></span><i class="bi bi-chevron-right"></i>
                  </a>
                  <?php endif; ?>
                  <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
              </div>
            </div>
          </div>
          <?php endif; ?>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </nav>

  <!-- ===================== DISCLAIMER ===================== -->
  <?php if (setting('disclaimer_active', '1') === '1' && setting('disclaimer_text') !== ''): ?>
  <div class="disclaimer">
    <div class="container">
      <i class="bi bi-info-circle-fill"></i>
      <span><?= e(setting('disclaimer_text')) ?></span>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php if (!empty($ctx['breadcrumbs'])): ?>
<div class="breadcrumb-bar">
  <div class="container breadcrumb">
    <a href="<?= e(url('')) ?>">Beranda</a>
    <?php foreach ($ctx['breadcrumbs'] as $i => $crumb):
        $last = $i === count($ctx['breadcrumbs']) - 1; ?>
      <span class="sep">/</span>
      <?php if (!$last && !empty($crumb['url'])): ?>
        <a href="<?= e(url($crumb['url'])) ?>"><?= e($crumb['label']) ?></a>
      <?php else: ?>
        <span class="current"><?= e($crumb['label']) ?></span>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>
</div>
<?php endif; ?>
