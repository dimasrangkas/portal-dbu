<?php
require_once dirname(__DIR__) . '/bootstrap.php';
$meta = page_start('kontak', ['breadcrumbs' => [['label' => 'Kontak']]]);

$infos    = konten('kontak/informasi');
$subjects = konten('kontak/subjek');
$mapUrl   = setting('contact_map');

partial('header');
partial('page-title', ['meta' => $meta]);
?>

<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:1fr 1.3fr; align-items:start; gap:40px;">

      <div>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h3 style="font-size:16.5px; margin-bottom:6px;">Informasi Kontak</h3>
          <?php foreach ($infos as $info): ?>
          <div class="contact-info-item">
            <div class="ic"><i class="bi <?= e($info['icon']) ?>"></i></div>
            <div><h4><?= e($info['title']) ?></h4><p><?= e($info['body']) ?></p></div>
          </div>
          <?php endforeach; ?>
        </div>

        <?php if ($mapUrl): ?>
        <div class="map-frame" style="aspect-ratio:4/3.4;">
          <iframe src="<?= e($mapUrl) ?>" loading="lazy" title="Peta lokasi kantor" allowfullscreen></iframe>
        </div>
        <?php endif; ?>
      </div>

      <div class="card card-pad">
        <h3 style="font-size:16.5px; margin-bottom:6px;">Kirim Pesan</h3>
        <p style="font-size:13.5px; color:var(--text-500); margin-bottom:24px;">Isi formulir berikut dan tim kami akan merespons dalam 1–3 hari kerja.</p>
        <form data-contact-form method="post" action="<?= e(url('submit.php')) ?>">
          <input type="hidden" name="form" value="kontak">
          <div class="form-grid">
            <div class="field"><label for="nama">Nama Lengkap</label><input type="text" id="nama" name="name" required placeholder="Masukkan nama lengkap"></div>
            <div class="field"><label for="email">Alamat Email</label><input type="email" id="email" name="email" required placeholder="nama@email.com"></div>
            <div class="field"><label for="telp">Nomor Telepon</label><input type="tel" id="telp" name="phone" placeholder="08xx-xxxx-xxxx"></div>
            <div class="field"><label for="subjek">Subjek</label>
              <select id="subjek" name="subject">
                <?php foreach ($subjects as $subject): ?>
                <option><?= e($subject['title']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="field full"><label for="pesan">Pesan</label><textarea id="pesan" name="message" required placeholder="Tuliskan pesan Anda di sini..."></textarea></div>
          </div>
          <button type="submit" class="btn btn-primary" style="margin-top:20px;">Kirim Pesan <i class="bi bi-send"></i></button>
          <div class="form-note" data-form-note><i class="bi bi-check-circle-fill"></i> Pesan Anda berhasil terkirim. Terima kasih!</div>
        </form>
      </div>

    </div>
  </div>
</section>

<?php
/* Seksi tambahan buatan CMS — tampil mengikuti urutan seksi di panel. */
partial('seksi-tambahan', ['kategori' => 'kontak', 'bawaan' => ['informasi','subjek']]);
partial('footer');
?>
