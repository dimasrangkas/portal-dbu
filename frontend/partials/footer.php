<?php
$quick    = menu_items('footer_quick');
$svcLinks = menu_items('footer_service');
$socials  = social_links();
?>
<footer class="footer">
  <div class="container footer-top">
    <div class="footer-brand">
      <div class="footer-logos">
        <div class="logo-badge">
          <img src="<?= e(asset_url(setting('logo_primary', 'assets/images/kemenhub.png'))) ?>" alt="Logo Kementerian Perhubungan">
        </div>
        <div class="brand-div"></div>
        <div class="logo-badge small">
          <img src="<?= e(asset_url(setting('logo_secondary', 'assets/images/logo-dbu.png'))) ?>" alt="Logo Direktorat Bandar Udara">
        </div>
      </div>
      <p><?= e(setting('footer_description')) ?></p>
      <div class="footer-social">
        <?php foreach ($socials as $s): ?>
        <a href="<?= eurl($s['url']) ?>" aria-label="<?= e($s['label']) ?>"><i class="bi <?= e($s['icon']) ?>"></i></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div>
      <h4>Tautan Cepat</h4>
      <ul class="footer-links">
        <?php foreach ($quick as $item): ?>
        <li><a href="<?= e($item['is_external'] ? $item['url'] : url($item['url'])) ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h4>Layanan</h4>
      <ul class="footer-links">
        <?php foreach ($svcLinks as $item): ?>
        <li><a href="<?= e($item['is_external'] ? $item['url'] : url($item['url'])) ?>"><?= e($item['label']) ?></a></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div>
      <h4>Kontak Kami</h4>
      <ul class="footer-contact">
        <li><i class="bi bi-geo-alt-fill"></i><span><?= e(setting('contact_address')) ?></span></li>
        <li><i class="bi bi-telephone-fill"></i><span><?= e(setting('contact_phone')) ?></span></li>
        <li><i class="bi bi-envelope-fill"></i><span><?= e(setting('contact_email')) ?></span></li>
        <li><i class="bi bi-clock-fill"></i><span><?= e(setting('contact_hours')) ?></span></li>
      </ul>
    </div>
  </div>
  <div class="container footer-bottom">
    <span>&copy; <?= date('Y') ?> <?= e(setting('footer_copyright')) ?></span>
    <span><a href="#">Kebijakan Privasi</a> &middot; <a href="<?= e(url('pages/kontak')) ?>">Peta Situs</a></span>
  </div>
</footer>

<div class="floating-col">
  <a class="fab fab-wa" href="<?= eurl(setting('whatsapp_url', '#')) ?>" aria-label="WhatsApp"><i class="bi bi-whatsapp"></i></a>
  <button class="fab fab-top" aria-label="Kembali ke atas"><i class="bi bi-arrow-up"></i></button>
</div>

<script src="<?= e(asset_version(url('js/main.js'), FRONTEND_PATH, 'js/main.js')) ?>"></script>
</body>
</html>
