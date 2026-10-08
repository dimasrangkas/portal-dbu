<?php
/* Blok judul halaman. $meta berasal dari page_start(). */
if (empty($meta['heading'])) {
    return;
}
?>
<div class="page-title-block">
  <div class="container">
    <?php if (!empty($meta['eyebrow'])): ?><div class="eyebrow"><?= e($meta['eyebrow']) ?></div><?php endif; ?>
    <h1><?= e($meta['heading']) ?></h1>
    <?php if (!empty($meta['subtitle'])): ?><p><?= e($meta['subtitle']) ?></p><?php endif; ?>
  </div>
</div>
