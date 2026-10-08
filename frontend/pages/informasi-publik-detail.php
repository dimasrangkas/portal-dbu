<?php
/* Informasi Publik — DI LUAR CAKUPAN CMS (konten statis, sesuai permintaan).
   Hanya kerangka situs (header/navbar/footer) yang mengikuti data CMS. */
require_once dirname(__DIR__) . '/bootstrap.php';
page_start('informasi-publik', [
    'title'       => 'Laporan Tahunan 2025 — Informasi Publik — Direktorat Bandar Udara',
    'description' => 'Detail dokumen Laporan Tahunan 2025 Direktorat Bandar Udara.',
    'breadcrumbs' => [['label' => 'Informasi Publik', 'url' => 'pages/informasi-publik'], ['label' => 'Laporan Tahunan 2025']],
]);
partial('header');
?>
<section class="section">
  <div class="container">
    <div class="grid" style="grid-template-columns:2fr 1fr; align-items:start; gap:40px;">

      <div>
        <span class="chip"><i class="bi bi-file-earmark-pdf-fill"></i> Laporan Tahunan</span>
        <h1 style="font-size:clamp(22px,2.8vw,30px); margin:14px 0 22px;">Laporan Tahunan Direktorat Bandar Udara 2025</h1>

        <div class="director-photo" style="aspect-ratio:16/8; margin-bottom:28px;">
          <div class="art art-7"><div class="art-inner"><i class="bi bi-file-earmark-richtext"></i></div></div>
        </div>

        <p style="font-size:15px; color:var(--text-700); margin-bottom:18px;">
          Dokumen ini memuat ringkasan capaian kinerja, realisasi anggaran, serta perkembangan program kerja
          Direktorat Bandar Udara sepanjang tahun 2025, termasuk pencapaian sertifikasi bandar udara, pembangunan
          prasarana, dan penguatan standar keselamatan operasional.
        </p>
        <p style="font-size:15px; color:var(--text-700); margin-bottom:28px;">
          Laporan disusun sesuai dengan ketentuan akuntabilitas kinerja instansi pemerintah dan dapat diakses
          secara terbuka oleh publik sebagai bentuk transparansi penyelenggaraan tugas pemerintahan di bidang
          kebandarudaraan.
        </p>

        <h3 style="font-size:16px; margin-bottom:14px;">Daftar Isi</h3>
        <ul style="display:flex; flex-direction:column; gap:10px; margin-bottom:10px;">
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-file-earmark"></i> Ikhtisar Eksekutif</li>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-file-earmark"></i> Capaian Kinerja Program</li>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-file-earmark"></i> Realisasi Anggaran</li>
          <li style="display:flex; gap:10px; font-size:14px; color:var(--text-700);"><i class="bi bi-file-earmark"></i> Tantangan &amp; Rencana Tindak Lanjut</li>
        </ul>
      </div>

      <div>
        <div class="card card-pad" style="margin-bottom:24px;">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Detail Dokumen</h4>
          <div style="display:flex; flex-direction:column; gap:14px; font-size:13.5px;">
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Kategori</span><b>Laporan Tahunan</b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tahun</span><b>2025</b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Format</span><b>PDF</b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Ukuran File</span><b>4.2 MB</b></div>
            <div class="flex" style="justify-content:space-between;"><span style="color:var(--text-500);">Tanggal Terbit</span><b>15 Maret 2026</b></div>
          </div>
          <a href="#" class="btn btn-primary btn-block" style="margin-top:22px;"><i class="bi bi-download"></i> Unduh Dokumen</a>
        </div>
        <div class="card card-pad">
          <h4 style="font-size:14.5px; margin-bottom:16px;">Dokumen Terkait</h4>
          <ul style="display:flex; flex-direction:column; gap:12px;">
            <li><a href="informasi-publik.php" style="color:var(--primary-dark); font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:8px;"><i class="bi bi-file-earmark-pdf-fill"></i> Laporan Tahunan 2024</a></li>
            <li><a href="informasi-publik.php" style="color:var(--primary-dark); font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:8px;"><i class="bi bi-file-earmark-bar-graph-fill"></i> Laporan Kinerja Tahunan 2025</a></li>
            <li><a href="informasi-publik.php" style="color:var(--primary-dark); font-weight:600; font-size:13.5px; display:flex; align-items:center; gap:8px;"><i class="bi bi-diagram-3-fill"></i> Rencana Strategis 2025–2029</a></li>
          </ul>
        </div>
      </div>

    </div>
    <div style="margin-top:40px;">
      <a href="informasi-publik.php" class="btn btn-outline-blue btn-sm"><i class="bi bi-arrow-left"></i> Kembali ke Informasi Publik</a>
    </div>
  </div>
</section>

<?php partial('footer'); ?>
