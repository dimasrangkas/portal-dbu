<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('organisasi', ['breadcrumbs' => [['label' => 'Organisasi']]]);

$units      = konten('organisasi/unit');
$unitFungsi = konten_anak(array_column($units, 'id'), 'fungsi-unit');
/* Blok "Layanan" pada tiap panel subdirektorat disembunyikan sementara.
   Ubah ke true untuk menampilkannya kembali — tidak ada perubahan lain yang
   perlu dibatalkan; bila false, $layanan selalu kosong sehingga blok
   rendernya di bawah tidak ikut tampil. */
const TAMPILKAN_LAYANAN_SUBDIT = false;

/* Layanan menyimpan unit pengelolanya sebagai slug unit pada kolom extra. */
$unitLayanan = [];
if (TAMPILKAN_LAYANAN_SUBDIT) {
    $slugUnit = array_column($units, 'id', 'slug');
    foreach (konten('layanan') as $sv) {
        if (!empty($sv['unit_slug']) && isset($slugUnit[$sv['unit_slug']])) {
            $unitLayanan[$slugUnit[$sv['unit_slug']]][] = $sv;
        }
    }
}
$s     = sections('organisasi');

/* Foto direktur dipakai bersama dengan seksi Sambutan Direktur di beranda,
   sehingga cukup diunggah sekali lewat CMS (Beranda > Sambutan Direktur). */
$direktur = konten_satu('home/sambutan');

/* Tiga pejabat per subdirektorat: satu kepala subdirektorat dengan dua
   kepala tim yang tampil sebaris di bawahnya. */
function unit_pejabat(array $unit): array {
    if ($unit['slug'] === 'tu') return [];
    return [
        'kepala' => ['name' => $unit['head_name'] ?? '', 'position' => $unit['head_position'] ?? 'Kepala Subdirektorat', 'photo' => $unit['head_photo'] ?? ''],
        'tim'    => [
            ['name' => $unit['team_lead_name']  ?? '', 'position' => $unit['team_lead_position']  ?? 'Kepala Tim', 'photo' => $unit['team_lead_photo']  ?? ''],
            ['name' => $unit['team_lead2_name'] ?? '', 'position' => $unit['team_lead2_position'] ?? 'Kepala Tim', 'photo' => $unit['team_lead2_photo'] ?? ''],
        ],
    ];
}

/* Kartu satu pejabat, dipakai kepala subdirektorat maupun kepala tim. */
function org_person(array $p): void { ?>
        <div class="org-person">
          <div class="org-detail-photo">
            <?php if ($p['photo']): ?>
            <img src="<?= e(asset_url($p['photo'])) ?>" alt="<?= e($p['name'] ?: $p['position']) ?>">
            <?php else: ?>
            <span><i class="bi bi-person-fill"></i></span>
            <?php endif; ?>
          </div>
          <div class="org-person-text">
            <?php if ($p['name']): ?>
            <b><?= e($p['name']) ?></b>
            <?php else: ?>
            <b class="is-empty">Nama belum diisi</b>
            <?php endif; ?>
            <span><?= e($p['position']) ?></span>
          </div>
        </div>
<?php }

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">
    <?php if (!empty($s['intro']['subtitle'])): ?>
    <p class="text-center" style="color:var(--text-500); font-size:13.5px; margin-bottom:36px;">
      <i class="bi bi-cursor"></i>&nbsp; <?= e($s['intro']['subtitle']) ?>
    </p>
    <?php endif; ?>

    <div class="org-wrap">
      <?php
        $dirNama    = $direktur['director_name'] ?? '';
        $dirJabatan = $direktur['director_position'] ?? ($s['top']['title'] ?? 'Direktur Bandar Udara');
      ?>
      <div class="org-node top">
        <div class="org-photo" title="<?= e($dirJabatan) ?><?= $dirNama ? ' — ' . $dirNama : '' ?>">
          <?php if (!empty($direktur['image'])): ?><img src="<?= e(asset_url($direktur['image'])) ?>" alt="<?= e($dirNama ?: $dirJabatan) ?>">
          <?php else: ?><span><i class="bi bi-person-fill"></i></span><?php endif; ?>
        </div>
        <b><?= e($s['top']['title'] ?? 'Direktur Bandar Udara') ?></b><span><?= e($s['top']['subtitle'] ?? '') ?></span>
      </div>
      <div class="org-connector"></div>
      <div class="org-children">
        <?php foreach ($units as $unit):
            /* Kartu bagan cukup menampilkan kepala subdirektorat;
               kepala tim baru muncul pada panel detail saat kartu diklik. */
            $kepala = unit_pejabat($unit)['kepala'] ?? null; ?>
        <div class="<?= e($unit['branch_class'] ?? 'org-branch') ?>">
          <div class="org-unit" data-unit="<?= e($unit['slug']) ?>">
            <?php if ($kepala): ?>
            <div class="org-photo" title="<?= e($kepala['position']) ?><?= $kepala['name'] ? ' — ' . $kepala['name'] : '' ?>">
              <?php if ($kepala['photo']): ?><img src="<?= e(asset_url($kepala['photo'])) ?>" alt="<?= e($kepala['name'] ?: $kepala['position']) ?>">
              <?php else: ?><span><i class="bi bi-person-fill"></i></span><?php endif; ?>
            </div>
            <?php else: ?>
            <div class="ic"><i class="bi <?= e($unit['icon']) ?>"></i></div>
            <?php endif; ?>
            <b><?= e($unit['title']) ?></b>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <?php foreach ($units as $unit):
        $chips   = split_list($unit['chips'] ?? '');
        $fungsi  = $unitFungsi[$unit['id']]  ?? [];
        $layanan = $unitLayanan[$unit['id']] ?? [];
        $pejabat = unit_pejabat($unit); ?>
    <div class="org-detail" data-detail="<?= e($unit['slug']) ?>">
      <div class="org-detail-head">
        <h3><i class="bi <?= e($unit['icon']) ?>"></i>&nbsp; <?= e($unit['title']) ?></h3>
      </div>

      <?php if ($pejabat): ?>
      <div class="org-pejabat">
        <?php org_person($pejabat['kepala']); ?>
        <div class="org-pejabat-tim">
          <?php foreach ($pejabat['tim'] as $p) org_person($p); ?>
        </div>
      </div>
      <?php endif; ?>

      <?php if ($unit['excerpt']): ?><p><?= e($unit['excerpt']) ?></p><?php endif; ?>

      <?php if ($unit['body']): ?>
      <h4 class="org-sub"><i class="bi bi-bullseye"></i> Tugas</h4>
      <div class="org-tugas"><?= paragraphs($unit['body']) ?></div>
      <?php endif; ?>

      <?php if ($fungsi): ?>
      <h4 class="org-sub"><i class="bi bi-list-check"></i> Fungsi</h4>
      <ol class="org-fungsi">
        <?php foreach ($fungsi as $fn): ?><li><?= e($fn['body']) ?></li><?php endforeach; ?>
      </ol>
      <?php endif; ?>

      <?php if ($layanan): ?>
      <h4 class="org-sub"><i class="bi bi-headset"></i> Layanan (<?= count($layanan) ?>)</h4>
      <div class="org-layanan">
        <?php foreach ($layanan as $svc): ?>
        <a class="org-layanan-item" href="<?= e(url('pages/layanan-detail?slug=' . urlencode($svc['slug']))) ?>">
          <i class="bi <?= e($svc['icon'] ?: 'bi-dot') ?>"></i><span><?= e($svc['title']) ?></span>
        </a>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>

      <?php if ($chips): ?>
      <div class="grid grid-3" style="margin-top:18px;">
        <?php foreach ($chips as $chip): ?>
        <div class="chip"><i class="bi bi-check2"></i> <?= e($chip) ?></div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
    <?php endforeach; ?>
  </div>
</section>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'organisasi', 'bawaan' => ['intro','top','unit']]);
partial('footer');
?>
