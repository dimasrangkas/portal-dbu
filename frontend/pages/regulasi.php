<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('regulasi', ['breadcrumbs' => [['label' => 'Regulasi']]]);

$regs = konten('regulasi', ['blueprint' => 'regulasi']);
/* Tahun regulasi disimpan pada kolom extra, jadi urutannya disusun di sini. */
usort($regs, fn($a, $b) => [(int) $b['year'], (int) $a['sort'], (int) $a['id']]
                       <=> [(int) $a['year'], (int) $b['sort'], (int) $b['id']]);

/* Kategori regulasi kini berupa sub kategori. */
$cats   = array_values(array_filter(subkategori_semua('regulasi'), fn($c) => $c['blueprint'] === 'regulasi'));

$years = array_values(array_unique(array_map(fn($r) => $r['year'], $regs)));
rsort($years);

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">

    <div class="filter-bar">
      <div class="grow">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari nomor atau judul regulasi..." data-reg-search>
      </div>
      <select data-reg-year>
        <option value="all">Semua Tahun</option>
        <?php foreach ($years as $y): ?><option value="<?= (int) $y ?>"><?= (int) $y ?></option><?php endforeach; ?>
      </select>
      <select data-reg-cat>
        <option value="all">Semua Kategori</option>
        <?php foreach ($cats as $c): ?><option value="<?= e($c['slug']) ?>"><?= e($c['label']) ?></option><?php endforeach; ?>
      </select>
    </div>

    <div class="table-wrap">
      <table class="reg-table" data-reg-table>
        <thead>
          <tr><th>Nomor</th><th>Judul Regulasi</th><th>Tahun</th><th>Kategori</th><th>Status</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($regs as $r):
              $detail = url('pages/regulasi-detail?slug=' . urlencode($r['slug'])); ?>
          <tr data-year="<?= (int) $r['year'] ?>" data-cat="<?= e($r['sub_slug']) ?>">
            <td><?= e($r['subtitle']) ?></td>
            <td><?= e($r['title']) ?></td>
            <td><?= (int) $r['year'] ?></td>
            <td><span class="badge badge-gray"><?= e($r['sub_label'] ?? $r['sub_slug']) ?></span></td>
            <td><span class="badge <?= ($r['status'] ?? '') === 'berlaku' ? 'badge-success' : 'badge-gray' ?>"><?= ($r['status'] ?? '') === 'berlaku' ? 'Berlaku' : 'Dicabut' ?></span></td>
            <td>
              <a href="<?= e($detail) ?>" class="dl-link">Detail</a>
              <?php if ($r['file']): ?> &nbsp;<a href="<?= e(asset_url($r['file'])) ?>" class="dl-link" download><i class="bi bi-download"></i></a><?php endif; ?>
              <?php if (!empty($r['url'])): ?> &nbsp;<a href="<?= e($r['url']) ?>" class="dl-link" target="_blank" rel="noopener" title="Buka di JDIH Kemenhub"><i class="bi bi-box-arrow-up-right"></i></a><?php endif; ?>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p data-reg-empty style="display:none; text-align:center; padding:40px; color:var(--text-500);">Tidak ada regulasi yang sesuai dengan pencarian/filter Anda.</p>
    </div>

  </div>
</section>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'regulasi', 'bawaan_bp' => ['regulasi']]);
partial('footer');
?>
