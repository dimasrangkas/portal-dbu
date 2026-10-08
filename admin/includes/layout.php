<?php
/* ============================================================
   Kerangka tampilan panel admin (sidebar, topbar, notifikasi)
   ============================================================ */

function admin_resources(): array
{
    static $res = null;
    if ($res === null) {
        $res = require dirname(__DIR__) . '/config/resources.php';
    }
    return $res;
}

function admin_resource(string $key): ?array
{
    $all = admin_resources();
    if (!isset($all[$key])) {
        return null;
    }
    return $all[$key] + ['key' => $key];
}

/** Halaman portal untuk menu samping — satu baris per kategori. */
function admin_kategori(): array
{
    static $rows = null;
    if ($rows === null) {
        $rows = db_all('SELECT slug, label, icon FROM categories ORDER BY sort, id');
    }
    return $rows;
}

/** Menu samping dikelompokkan sesuai kunci `group`. */
function admin_menu(): array
{
    $groups = [];
    foreach (admin_resources() as $key => $res) {
        if (!empty($res['admin_only']) && (current_user()['role'] ?? '') !== 'admin') {
            continue;
        }
        $groups[$res['group']][] = ['key' => $key, 'label' => $res['label'], 'icon' => $res['icon'] ?? 'bi-dot'];
    }
    return $groups;
}

function unread_messages(): int
{
    return (int) db_value('SELECT COUNT(*) FROM contact_messages WHERE is_read = 0', [], 0);
}

function new_applications(): int
{
    return (int) db_value('SELECT COUNT(*) FROM service_applications WHERE status = "baru"', [], 0);
}

function admin_head(string $title, array $crumbs = []): void
{
    $user   = current_user();
    $menu   = admin_menu();
    $unread = unread_messages() + new_applications();
    ?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($title) ?> — CMS Direktorat Bandar Udara</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="<?= e(asset_version(admin_url('assets/admin.css'), ADMIN_PATH, 'assets/admin.css')) ?>">
</head>
<body>
<div class="app">

  <aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
      <div class="logo"><i class="bi bi-airplane-fill"></i></div>
      <div>
        <b>CMS Bandar Udara</b>
        <span>Panel Administrator</span>
      </div>
    </div>

    <nav class="sidebar-nav">
      <a class="nav-link<?= is_active_nav('index.php') ? ' active' : '' ?>" href="<?= e(admin_url('index.php')) ?>">
        <i class="bi bi-speedometer2"></i> Dasbor
      </a>
      <a class="nav-link<?= is_active_nav('inbox.php') ? ' active' : '' ?>" href="<?= e(admin_url('inbox.php')) ?>">
        <i class="bi bi-inbox"></i> Kotak Masuk
        <?php if ($unread): ?><span class="badge-count"><?= $unread ?></span><?php endif; ?>
      </a>
      <a class="nav-link<?= is_active_nav('settings.php') ? ' active' : '' ?>" href="<?= e(admin_url('settings.php')) ?>">
        <i class="bi bi-sliders"></i> Pengaturan Situs
      </a>

      <div class="nav-group">Halaman Portal</div>
      <?php foreach (admin_kategori() as $kat): ?>
      <a class="nav-link<?= is_active_nav('konten.php') && ($_GET['kategori'] ?? '') === $kat['slug'] ? ' active' : '' ?>"
         href="<?= e(admin_url('konten.php?kategori=' . urlencode($kat['slug']))) ?>">
        <i class="bi <?= e($kat['icon'] ?: 'bi-file-earmark') ?>"></i> <?= e($kat['label']) ?>
      </a>
      <?php endforeach; ?>
      <a class="nav-link" href="<?= e(admin_url('konten.php?a=kategori-form')) ?>">
        <i class="bi bi-plus-lg"></i> Tambah Halaman
      </a>

      <?php foreach ($menu as $group => $items): ?>
      <div class="nav-group"><?= e($group) ?></div>
      <?php foreach ($items as $item): ?>
      <a class="nav-link<?= is_active_nav('resource.php', $item['key']) ? ' active' : '' ?>"
         href="<?= e(admin_url('resource.php?r=' . $item['key'])) ?>">
        <i class="bi <?= e($item['icon']) ?>"></i> <?= e($item['label']) ?>
      </a>
      <?php endforeach; ?>
      <?php endforeach; ?>
    </nav>
  </aside>

  <div class="main">
    <header class="topbar">
      <button class="icon-btn only-mobile" id="sidebarToggle" aria-label="Menu"><i class="bi bi-list"></i></button>
      <div class="crumbs">
        <a href="<?= e(admin_url('index.php')) ?>">Dasbor</a>
        <?php foreach ($crumbs as $crumb): ?>
          <i class="bi bi-chevron-right"></i>
          <?php if (!empty($crumb['url'])): ?>
            <a href="<?= e($crumb['url']) ?>"><?= e($crumb['label']) ?></a>
          <?php else: ?>
            <span><?= e($crumb['label']) ?></span>
          <?php endif; ?>
        <?php endforeach; ?>
      </div>
      <div class="topbar-actions">
        <a class="btn btn-ghost btn-sm" href="<?= e(site_url('index.php')) ?>" target="_blank" rel="noopener">
          <i class="bi bi-box-arrow-up-right"></i> Lihat Situs
        </a>
        <div class="user-chip">
          <span class="avatar"><?= e(mb_strtoupper(mb_substr($user['name'], 0, 1))) ?></span>
          <div>
            <b><?= e($user['name']) ?></b>
            <span><?= $user['role'] === 'admin' ? 'Administrator' : 'Editor' ?></span>
          </div>
        </div>
        <a class="icon-btn" href="<?= e(admin_url('logout.php')) ?>" title="Keluar"><i class="bi bi-box-arrow-right"></i></a>
      </div>
    </header>

    <main class="content">
      <?php foreach (take_flashes() as $f): ?>
      <div class="alert alert-<?= e($f['type']) ?>">
        <i class="bi bi-<?= $f['type'] === 'success' ? 'check-circle-fill' : ($f['type'] === 'danger' ? 'exclamation-triangle-fill' : 'info-circle-fill') ?>"></i>
        <span><?= e($f['message']) ?></span>
      </div>
      <?php endforeach; ?>
    <?php
}

function admin_foot(): void
{
    ?>
    </main>
  </div>
</div>
<script src="<?= e(asset_version(admin_url('assets/admin.js'), ADMIN_PATH, 'assets/admin.js')) ?>"></script>
</body>
</html>
    <?php
}
