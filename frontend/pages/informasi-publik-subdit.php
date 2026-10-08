<?php
/* Informasi publik per subdirektorat — dasbor data dipisah mengikuti unit kerjanya. */
require_once dirname(__DIR__) . '/bootstrap.php';

/* Dasbor mana milik unit mana. */
const SUBDIT_DASBOR = [
    'std'       => ['ip-register-bandara'],
    'tatanan'   => ['ip-emisi'],
    'prasarana' => ['ip-fasilitas-sisi-udara'],
    'darurat'   => ['ip-peralatan-darurat'],
    'sistem'    => ['ip-penyelenggaraan'],
];

$unitKey = trim($_GET['unit'] ?? '');
$unit    = $unitKey !== ''
    ? konten_slug('organisasi', $unitKey, ['sub' => 'unit'])
    : [];

if (!$unit || !isset(SUBDIT_DASBOR[$unitKey])) {
    http_response_code(404);
    page_start('informasi-publik', [
        'title'       => 'Data subdirektorat tidak ditemukan',
        'breadcrumbs' => [['label' => 'Informasi Publik', 'url' => 'pages/informasi-publik'], ['label' => 'Tidak ditemukan']],
    ]);
    partial('header');
    echo '<section class="section"><div class="container" style="text-align:center;padding:60px 0;">'
       . '<h1 style="font-size:24px;margin-bottom:12px;">Data subdirektorat tidak ditemukan</h1>'
       . '<a href="' . e(url('pages/informasi-publik')) . '" class="btn btn-primary btn-sm">Kembali ke Informasi Publik</a>'
       . '</div></section>';
    partial('footer');
    exit;
}

$meta = page_start('informasi-publik', [
    'title'       => $unit['title'] . ' — Informasi Publik — ' . setting('site_name'),
    'description' => excerpt($unit['excerpt'], 200),
    'breadcrumbs' => [['label' => 'Informasi Publik', 'url' => 'pages/informasi-publik'], ['label' => $unit['title']]],
]);

$dasbor   = SUBDIT_DASBOR[$unitKey];
/* Layanan menandai unit pengelolanya lewat slug unit pada kolom extra. */
$services = array_values(array_filter(
    konten('layanan', ['blueprint' => 'layanan']),
    fn($sv) => ($sv['unit_slug'] ?? '') === $unitKey
));

partial('header');
?>

<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Informasi Publik</div>
    <h1><?= e($unit['title']) ?></h1>
    <?php if ($unit['excerpt']): ?><p><?= e($unit['excerpt']) ?></p><?php endif; ?>
  </div>
</div>

<?php foreach ($dasbor as $partial): ?>
<?php partial($partial); ?>
<?php endforeach; ?>

<?php if (!$dasbor): ?>
<section class="section">
  <div class="container">
    <div class="info-box">
      <h3><i class="bi bi-info-circle"></i>&nbsp; Belum ada data tayang</h3>
      <p>Data informasi publik untuk <?= e($unit['title']) ?> belum tersedia. Isi dapat ditambahkan melalui panel CMS.</p>
    </div>
  </div>
</section>
<?php endif; ?>


<?php partial('footer'); ?>
