<?php
/* Dokumen Publik — laporan, rencana strategis, dan unduhan lain.
   Dipisah dari halaman Informasi Publik yang kini berisi Daftar Bandar Udara. */
require_once dirname(__DIR__) . '/bootstrap.php';
page_start('dokumen-publik', [
    'title'       => 'Dokumen Publik — Direktorat Bandar Udara',
    'description' => 'Laporan tahunan, laporan kinerja, rencana strategis, dan dokumen publik lain yang dapat diunduh.',
    'breadcrumbs' => [['label' => 'Dokumen Publik']],
]);
partial('header');
?>
<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Informasi Publik</div>
    <h1>Dokumen Publik</h1>
    <p>Laporan tahunan, laporan kinerja, rencana strategis, dan dokumen publik lain yang dapat diunduh.</p>
  </div>
</div>

<section class="section">
  <div class="container">

    <div class="tabs-nav" data-tabs="ip">
      <button class="active" data-tab="semua">Semua</button>
      <button data-tab="tahunan">Laporan Tahunan</button>
      <button data-tab="kinerja">Laporan Kinerja</button>
      <button data-tab="strategis">Rencana Strategis</button>
      <button data-tab="lain">Unduhan Lain</button>
    </div>

    <div class="tab-panel active" data-tab-panel="ip" data-panel-id="semua">
      <div class="grid grid-3">
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div><div><h4>Laporan Tahunan 2025</h4><span>PDF · 4.2 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div><div><h4>Laporan Tahunan 2024</h4><span>PDF · 3.9 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-bar-graph-fill"></i></div><div><h4>Laporan Kinerja Triwulan II 2026</h4><span>PDF · 2.1 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-bar-graph-fill"></i></div><div><h4>Laporan Kinerja Tahunan 2025</h4><span>PDF · 3.0 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-diagram-3-fill"></i></div><div><h4>Rencana Strategis 2025–2029</h4><span>PDF · 5.6 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-diagram-3-fill"></i></div><div><h4>Rencana Strategis 2020–2024</h4><span>PDF · 5.1 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Standar Pelayanan Direktorat</h4><span>PDF · 900 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Maklumat Pelayanan Publik</h4><span>PDF · 450 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Formulir Permohonan Informasi (PPID)</h4><span>PDF · 210 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
      </div>
    </div>

    <div class="tab-panel" data-tab-panel="ip" data-panel-id="tahunan">
      <div class="grid grid-3">
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div><div><h4>Laporan Tahunan 2025</h4><span>PDF · 4.2 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div><div><h4>Laporan Tahunan 2024</h4><span>PDF · 3.9 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-pdf-fill"></i></div><div><h4>Laporan Tahunan 2023</h4><span>PDF · 3.7 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
      </div>
    </div>

    <div class="tab-panel" data-tab-panel="ip" data-panel-id="kinerja">
      <div class="grid grid-3">
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-bar-graph-fill"></i></div><div><h4>Laporan Kinerja Triwulan II 2026</h4><span>PDF · 2.1 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-bar-graph-fill"></i></div><div><h4>Laporan Kinerja Tahunan 2025</h4><span>PDF · 3.0 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
      </div>
    </div>

    <div class="tab-panel" data-tab-panel="ip" data-panel-id="strategis">
      <div class="grid grid-3">
        <div class="card download-card"><div class="ic"><i class="bi bi-diagram-3-fill"></i></div><div><h4>Rencana Strategis 2025–2029</h4><span>PDF · 5.6 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-diagram-3-fill"></i></div><div><h4>Rencana Strategis 2020–2024</h4><span>PDF · 5.1 MB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
      </div>
    </div>

    <div class="tab-panel" data-tab-panel="ip" data-panel-id="lain">
      <div class="grid grid-3">
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Standar Pelayanan Direktorat</h4><span>PDF · 900 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Maklumat Pelayanan Publik</h4><span>PDF · 450 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
        <div class="card download-card"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><div><h4>Formulir Permohonan Informasi (PPID)</h4><span>PDF · 210 KB</span></div><a href="informasi-publik-detail.php" class="go"><i class="bi bi-arrow-right-circle"></i></a></div>
      </div>
    </div>

  </div>
</section>

<!-- PERMOHONAN INFORMASI PUBLIK -->
<section class="section section-alt">
  <div class="container">
    <div class="newsletter" style="background:linear-gradient(120deg,var(--primary-darker),var(--primary-dark));">
      <div>
        <h3>Permohonan Informasi Publik</h3>
        <p>Ajukan permintaan informasi yang belum tersedia pada laman ini melalui Pejabat Pengelola Informasi dan Dokumentasi (PPID).</p>
      </div>
      <a href="kontak.php" class="btn btn-white">Ajukan Permohonan <i class="bi bi-arrow-right"></i></a>
    </div>
  </div>
</section>
<?php partial('footer'); ?>
