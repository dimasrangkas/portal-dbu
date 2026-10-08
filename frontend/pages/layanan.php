<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('layanan', ['breadcrumbs' => [['label' => 'Layanan']]]);

$services = konten('layanan', ['blueprint' => 'layanan']);
$genReq   = konten('layanan/persyaratan-umum');
$steps    = konten('layanan/alur');
$faqs     = konten('layanan/faq');
$s        = sections('layanan');

/* Pilihan filter diambil dari sub kategori yang benar-benar dipakai. */
$categories = [];
foreach ($services as $svc) {
    $categories[$svc['sub_slug']] = $svc['klasifikasi'] ?? $svc['sub_label'];
}

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<!-- DAFTAR LAYANAN -->
<section class="section">
  <div class="container">
    <div class="section-head">
      <?php if (!empty($s['daftar']['eyebrow'])): ?><div class="eyebrow"><?= e($s['daftar']['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($s['daftar']['title'] ?? '') ?></h2>
      <?php if (!empty($s['daftar']['subtitle'])): ?><p><?= e($s['daftar']['subtitle']) ?></p><?php endif; ?>
    </div>

    <div class="filter-bar">
      <div class="grow">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari nama layanan..." data-svc-search>
      </div>
      <select data-svc-cat>
        <option value="all">Semua Klasifikasi</option>
        <?php foreach ($categories as $slug => $label): ?>
        <option value="<?= e($slug) ?>"><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>

    <div class="table-wrap">
      <table class="reg-table" data-svc-table>
        <thead>
          <tr><th>Unit Pengelola Layanan</th><th>Nama Layanan</th><th>Sifat Layanan (Pemdi)</th><th>Klasifikasi Layanan</th><th>Aksi</th></tr>
        </thead>
        <tbody>
          <?php foreach ($services as $svc): ?>
          <tr data-cat="<?= e($svc['sub_slug']) ?>">
            <td><?= e($svc['unit_pengelola'] ?? '') ?></td>
            <td><a href="<?= e(url('pages/layanan-detail?slug=' . urlencode($svc['slug']))) ?>" style="color:var(--primary-dark);font-weight:600;"><?= e($svc['title']) ?></a></td>
            <td><span class="badge <?= e($svc['sifat_badge'] ?? '') ?>"><?= e($svc['sifat'] ?? '') ?></span></td>
            <td><span class="badge <?= e($svc['klasifikasi_badge'] ?? '') ?>"><?= e($svc['klasifikasi'] ?? '') ?></span></td>
            <td><button type="button" class="btn btn-primary btn-sm" data-svc-apply="<?= e($svc['title']) ?>">Ajukan</button></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <p data-svc-empty style="display:none; text-align:center; padding:40px; color:var(--text-500);">Tidak ada layanan yang sesuai dengan pencarian/filter Anda.</p>
    </div>
  </div>
</section>

<!-- MODAL AJUKAN LAYANAN -->
<div class="modal-overlay" data-apply-modal>
  <div class="modal-box" role="dialog" aria-modal="true" aria-labelledby="applyModalTitle">
    <button type="button" class="modal-close" data-apply-close aria-label="Tutup"><i class="bi bi-x-lg"></i></button>
    <span class="modal-eyebrow"><i class="bi bi-send-check"></i> Formulir Pengajuan Layanan</span>
    <h3 id="applyModalTitle" data-apply-service>Nama Layanan</h3>
    <p style="font-size:13.5px; color:var(--text-500); margin:6px 0 20px;">Lengkapi data berikut untuk mengajukan permohonan layanan ini. Tim kami akan menghubungi Anda setelah permohonan diterima.</p>
    <form data-apply-form method="post" action="<?= e(url('submit.php')) ?>" enctype="multipart/form-data">
      <input type="hidden" name="form" value="pengajuan">
      <input type="hidden" name="service_name" data-apply-service-input>
      <div class="form-grid">
        <div class="field"><label for="applyNama">Nama Pemohon</label><input type="text" id="applyNama" name="applicant_name" required placeholder="Masukkan nama lengkap"></div>
        <div class="field"><label for="applyInstansi">Instansi / Penyelenggara</label><input type="text" id="applyInstansi" name="institution" required placeholder="Nama instansi/perusahaan"></div>
        <div class="field"><label for="applyEmail">Alamat Email</label><input type="email" id="applyEmail" name="email" required placeholder="nama@email.com"></div>
        <div class="field"><label for="applyTelp">Nomor Telepon</label><input type="tel" id="applyTelp" name="phone" required placeholder="08xx-xxxx-xxxx"></div>
        <div class="field full"><label for="applyKet">Keterangan Permohonan</label><textarea id="applyKet" name="notes" placeholder="Uraikan kebutuhan atau catatan tambahan terkait permohonan..."></textarea></div>
        <div class="field full"><label for="applyFile">Unggah Dokumen Persyaratan</label><input type="file" id="applyFile" name="document"></div>
      </div>
      <button type="submit" class="btn btn-primary btn-block" style="margin-top:22px;">Kirim Permohonan <i class="bi bi-arrow-right"></i></button>
      <div class="form-note" data-apply-note><i class="bi bi-check-circle-fill"></i> Permohonan Anda berhasil diajukan. Tim kami akan segera menghubungi Anda.</div>
    </form>
  </div>
</div>

<!-- LAYANAN DARING -->
<section class="section section-alt" id="online">
  <div class="container">
    <div class="section-head center">
      <?php if (!empty($s['online']['eyebrow'])): ?><div class="eyebrow" style="justify-content:center"><?= e($s['online']['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($s['online']['title'] ?? '') ?></h2>
      <?php if (!empty($s['online']['subtitle'])): ?><p><?= e($s['online']['subtitle']) ?></p><?php endif; ?>
    </div>

    <div class="grid grid-2" style="align-items:start; margin-bottom:56px;">
      <div class="card card-pad">
        <h3 style="font-size:16.5px; margin-bottom:16px;"><i class="bi bi-clipboard-check" style="color:var(--primary)"></i>&nbsp; Persyaratan Umum</h3>
        <ul style="display:flex; flex-direction:column; gap:12px;">
          <?php foreach ($genReq as $req): ?>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-check-circle-fill" style="color:var(--success)"></i> <?= e($req['body']) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <div class="card card-pad" style="text-align:center;">
        <div class="ic" style="width:60px;height:60px;border-radius:50%;background:var(--sky-50);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:26px;margin:0 auto 18px;"><i class="bi bi-send-check"></i></div>
        <h3 style="font-size:16.5px; margin-bottom:10px;"><?= e($s['cta']['title'] ?? 'Siap Mengajukan Layanan?') ?></h3>
        <p style="font-size:14px; color:var(--text-500); margin-bottom:22px;"><?= e($s['cta']['subtitle'] ?? '') ?></p>
        <a href="<?= e(url('pages/layanan-detail?slug=' . urlencode($services[0]['slug'] ?? ''))) ?>" class="btn btn-primary btn-block">Ajukan Layanan Sekarang <i class="bi bi-arrow-right"></i></a>
      </div>
    </div>

    <?php if ($steps): ?>
    <div class="section-head center">
      <?php if (!empty($s['alur']['eyebrow'])): ?><div class="eyebrow" style="justify-content:center"><?= e($s['alur']['eyebrow']) ?></div><?php endif; ?>
      <h2 style="font-size:24px;"><?= e($s['alur']['title'] ?? '') ?></h2>
    </div>
    <div class="stepper">
      <?php foreach ($steps as $i => $step): ?>
      <div class="step"><div class="circ"><?= $i + 1 ?></div><h4><?= e($step['title']) ?></h4><p><?= e($step['body']) ?></p></div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- FAQ -->
<?php if ($faqs): ?>
<section class="section" id="faq">
  <div class="container" style="max-width:820px;">
    <div class="section-head center">
      <?php if (!empty($s['faq']['eyebrow'])): ?><div class="eyebrow" style="justify-content:center"><?= e($s['faq']['eyebrow']) ?></div><?php endif; ?>
      <h2><?= e($s['faq']['title'] ?? '') ?></h2>
    </div>
    <?php foreach ($faqs as $i => $faq): ?>
    <div class="faq-item<?= $i === 0 ? ' open' : '' ?>">
      <button class="faq-q"><?= e($faq['title']) ?><i class="bi bi-plus-lg"></i></button>
      <div class="faq-a"><?= paragraphs($faq['body']) ?></div>
    </div>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'layanan', 'bawaan' => ['daftar','online','cta','persyaratan-umum','alur','faq'], 'bawaan_bp' => ['layanan']]);
partial('footer');
?>
