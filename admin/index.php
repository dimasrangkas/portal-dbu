<?php
/* Dasbor CMS */
require_once __DIR__ . '/includes/bootstrap.php';
require_once __DIR__ . '/includes/layout.php';

$user = require_login();

/** Jumlah konten satu kategori, dibatasi jenis seksinya. */
function hitung_konten(string $kategori, string $blueprint): int
{
    return (int) db_value(
        'SELECT COUNT(*) FROM contents k
           JOIN categories c    ON c.id = k.category_id
           JOIN subcategories s ON s.id = k.subcategory_id
          WHERE c.slug = ? AND s.blueprint = ?',
        [$kategori, $blueprint],
        0
    );
}

$counts = [
    ['label' => 'Berita',      'value' => hitung_konten('berita', 'berita'),     'icon' => 'bi-newspaper',    'kat' => 'berita'],
    ['label' => 'Regulasi',    'value' => hitung_konten('regulasi', 'regulasi'), 'icon' => 'bi-journal-text', 'kat' => 'regulasi'],
    ['label' => 'Layanan',     'value' => hitung_konten('layanan', 'layanan'),   'icon' => 'bi-headset',      'kat' => 'layanan'],
    ['label' => 'Foto Galeri', 'value' => hitung_konten('galeri', 'foto'),       'icon' => 'bi-image',        'kat' => 'galeri'],
];

/* Seksi berita pertama — tujuan tombol "Tulis Berita". */
$subBerita = db_value(
    'SELECT s.slug FROM subcategories s JOIN categories c ON c.id = s.category_id
      WHERE c.slug = "berita" AND s.blueprint = "berita" ORDER BY s.sort LIMIT 1',
    [],
    ''
);

$unreadMsg   = unread_messages();
$newApps     = new_applications();
$subscribers = (int) db_value('SELECT COUNT(*) FROM newsletter_subscribers WHERE is_active = 1', [], 0);

$latestNews = db_all(
    'SELECT k.id, k.title, k.published_at, k.is_active AS is_published, s.slug AS sub
       FROM contents k
       JOIN categories c    ON c.id = k.category_id
       JOIN subcategories s ON s.id = k.subcategory_id
      WHERE c.slug = "berita" AND s.blueprint = "berita"
      ORDER BY k.published_at DESC, k.id DESC LIMIT 5'
);
$latestMsg  = db_all('SELECT id, name, subject, created_at, is_read FROM contact_messages ORDER BY created_at DESC LIMIT 5');
$topNews    = db_all(
    'SELECT k.title, k.views FROM contents k
       JOIN categories c ON c.id = k.category_id
      WHERE c.slug = "berita" AND k.views > 0
      ORDER BY k.views DESC LIMIT 5'
);

admin_head('Dasbor');
?>

<div class="page-head">
  <div>
    <h1>Selamat datang, <?= e(explode(' ', $user['name'])[0]) ?></h1>
    <p>Ringkasan konten dan aktivitas situs Direktorat Bandar Udara.</p>
  </div>
  <div class="page-head-actions">
    <a class="btn btn-primary" href="<?= e(admin_url('konten.php?kategori=berita&sub=' . urlencode($subBerita) . '&a=form')) ?>"><i class="bi bi-plus-lg"></i> Tulis Berita</a>
  </div>
</div>

<div class="stat-grid">
  <?php foreach ($counts as $c): ?>
  <a class="stat-card" href="<?= e(admin_url('konten.php?kategori=' . $c['kat'])) ?>">
    <div class="stat-ic"><i class="bi <?= e($c['icon']) ?>"></i></div>
    <div>
      <b><?= (int) $c['value'] ?></b>
      <span><?= e($c['label']) ?></span>
    </div>
  </a>
  <?php endforeach; ?>
  <a class="stat-card <?= $unreadMsg ? 'accent' : '' ?>" href="<?= e(admin_url('inbox.php')) ?>">
    <div class="stat-ic"><i class="bi bi-envelope"></i></div>
    <div><b><?= $unreadMsg ?></b><span>Pesan Belum Dibaca</span></div>
  </a>
  <a class="stat-card <?= $newApps ? 'accent' : '' ?>" href="<?= e(admin_url('inbox.php?tab=permohonan')) ?>">
    <div class="stat-ic"><i class="bi bi-file-earmark-text"></i></div>
    <div><b><?= $newApps ?></b><span>Permohonan Baru</span></div>
  </a>
  <a class="stat-card" href="<?= e(admin_url('inbox.php?tab=langganan')) ?>">
    <div class="stat-ic"><i class="bi bi-bell"></i></div>
    <div><b><?= $subscribers ?></b><span>Pelanggan Newsletter</span></div>
  </a>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-head">
      <h2>Berita Terbaru</h2>
      <a href="<?= e(admin_url('konten.php?kategori=berita')) ?>">Kelola</a>
    </div>
    <?php if (!$latestNews): ?>
    <div class="empty-state"><i class="bi bi-newspaper"></i><p>Belum ada berita.</p></div>
    <?php else: ?>
    <ul class="list">
      <?php foreach ($latestNews as $n): ?>
      <li>
        <a href="<?= e(admin_url('konten.php?kategori=berita&sub=' . urlencode($n['sub']) . '&a=form&id=' . (int) $n['id'])) ?>"><?= e($n['title']) ?></a>
        <span><?= e(tanggal_id($n['published_at'])) ?>
          <?php if (!(int) $n['is_published']): ?><em class="pill pill-off">Draf</em><?php endif; ?>
        </span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-head">
      <h2>Pesan Masuk Terbaru</h2>
      <a href="<?= e(admin_url('inbox.php')) ?>">Lihat semua</a>
    </div>
    <?php if (!$latestMsg): ?>
    <div class="empty-state"><i class="bi bi-inbox"></i><p>Belum ada pesan masuk.</p></div>
    <?php else: ?>
    <ul class="list">
      <?php foreach ($latestMsg as $m): ?>
      <li>
        <a href="<?= e(admin_url('inbox.php#pesan-' . (int) $m['id'])) ?>">
          <?= e($m['name']) ?><?= $m['subject'] ? ' — ' . e($m['subject']) : '' ?>
        </a>
        <span><?= e(tanggal_id($m['created_at'])) ?>
          <?php if (!(int) $m['is_read']): ?><em class="pill pill-on">Baru</em><?php endif; ?>
        </span>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</div>

<?php if ($topNews): ?>
<div class="card">
  <div class="card-head"><h2>Berita Paling Banyak Dibaca</h2></div>
  <ul class="list">
    <?php foreach ($topNews as $n): ?>
    <li><span><?= e($n['title']) ?></span><span><?= (int) $n['views'] ?>×</span></li>
    <?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<?php admin_foot(); ?>
