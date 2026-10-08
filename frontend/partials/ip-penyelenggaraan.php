<?php /* Dasbor Informasi Publik — penyelenggaraan. Dipakai halaman per subdirektorat. */ ?>
<section class="section" id="penyelenggaraan" data-svc-section>
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Penyelenggaraan &amp; Pengusahaan</div>
      <h2>Level of Service &amp; Standar Pelayanan</h2>
      <p>Pemantauan tingkat pelayanan (Level of Service), hasil inspeksi fasilitas, dan dokumen standar pelayanan bandar udara. Data bersifat tayang (read-only).</p>
    </div>

    <!-- LOS charts -->
    <div class="pkp-row-2" style="grid-template-columns:1fr 1fr; margin-bottom:32px;">
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-graph-up"></i></div><h3>Level of Service Trend</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="los-trend"></div>
          <div class="emisi-legend" style="margin-top:12px;">
            <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#4a3aa7"></span>Sisi Udara</span>
            <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#1baf7a"></span>Sisi Darat</span>
            <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#eb6834"></span>Terminal</span>
          </div>
        </div>
      </div>
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-bar-chart-fill"></i></div><h3>Aggregate LOS Score</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="los-aggregate"></div>
        </div>
      </div>
    </div>

    <!-- Service Inspection List -->
    <div class="card card-pad" style="margin-bottom:32px;">
      <div class="svc-toolbar">
        <h3>Service Inspection List</h3>
        <label class="svc-toggle-wrap">
          <span class="svc-toggle">
            <input type="checkbox" data-svc-checklist>
            <span class="track"></span><span class="thumb"></span>
          </span>
          Checklist
        </label>
      </div>

      <div class="svc-filters">
        <div class="field"><label for="svcKategori">Kategori Pelayanan</label>
          <select id="svcKategori">
            <option>Pesawat Udara (PJP4U)</option>
            <option>Penumpang Pesawat Udara (PJP2U)</option>
            <option>Kargo dan Pos (PJPP)</option>
          </select>
        </div>
        <div class="field"><label for="svcPemeriksa">Pemeriksa</label>
          <select id="svcPemeriksa">
            <option>Regulator</option>
            <option>Operator Bandar Udara</option>
            <option>Auditor Internal</option>
          </select>
        </div>
        <div class="field"><label for="svcTahun">Inspection Year</label>
          <select id="svcTahun">
            <option>2026</option>
            <option>2025</option>
            <option>2024</option>
          </select>
        </div>
      </div>

      <div class="svc-panel">
        <div class="svc-panel-head">Pelayanan Pada Fasilitas Yang Digunakan Pada Proses Pendaratan, Lepas Landas dan Manuver Pesawat Udara</div>
        <div class="svc-panel-body">
          <div class="svc-subhead">Landas Pacu (Runway)</div>

          <div class="svc-crit">
            <div class="svc-crit-head">Kemampuan pelayanan dan memenuhi aspek keselamatan penerbangan</div>
            <div class="svc-def-row"><div class="label">Tolok Ukur</div><div class="value">Kondisi Landas Pacu (Runway)</div></div>
            <div class="svc-def-row"><div class="label">Uraian Tolok Ukur</div><div class="value">Hasil pengamatan langsung pada landas pacu (runway):
a. tidak ada kerusakan pada permukaan sesuai dengan ketentuan
b. tidak terdapat genangan air sesuai dengan ketentuan
c. tidak ada FOD</div></div>
            <div class="svc-def-row"><div class="label">Kesesuaian Tolok Ukur dan Nilai</div><div class="value">a. Kondisi Landas Pacu (Runway) tidak ada kerusakan pada permukaan sesuai ketentuan, tidak terdapat genangan air sesuai dengan ketentuan dan tidak ada FOD = 5.
b. Kondisi Landas Pacu (Runway) eksisting dibagi jumlah uraian tolok ukur dikali 5.</div></div>
            <div class="svc-def-row"><div class="label">Hasil Penilaian</div><div class="value">Kondisi Landas Pacu (Runway) tidak ada kerusakan pada permukaan sesuai ketentuan, tidak terdapat genangan air sesuai dengan ketentuan dan tidak ada FOD</div></div>
            <div class="svc-def-row"><div class="label">Nilai</div><div class="value" data-svc-nilai="9"><span class="svc-nilai-badge">9</span></div></div>
          </div>

          <div class="svc-crit">
            <div class="svc-crit-head">Kelengkapan dan visualisasi marka dan rambu sesuai dengan ketentuan</div>
            <div class="svc-def-row"><div class="label">Uraian Tolok Ukur</div><div class="value">a. Marka dan Rambu Lengkap
b. Marka dan Rambu terlihat jelas</div></div>
            <div class="svc-def-row"><div class="label">Kesesuaian Tolok Ukur dan Nilai</div><div class="value">a. Marka dan rambu lengkap dan terlihat jelas = 5. b. Kondisi marka dan rambu eksisting dibagi jumlah uraian tolok ukur dikali 5</div></div>
            <div class="svc-def-row"><div class="label">Hasil Penilaian</div><div class="value">Marka dan rambu lengkap dan terlihat jelas</div></div>
            <div class="svc-def-row"><div class="label">Nilai</div><div class="value" data-svc-nilai="4"><span class="svc-nilai-badge">4</span></div></div>
          </div>
        </div>
      </div>
    </div>

    <!-- Dokumen Standar Pelayanan -->
    <div class="card card-pad">
      <div class="pkp-head"><div class="ic"><i class="bi bi-file-earmark-text-fill"></i></div><h3>Dokumen Standar Pelayanan (DSP)</h3></div>

      <div class="svc-filters with-btn">
        <div class="field"><label for="dspAirport">Select Airport</label>
          <select id="dspAirport" data-dsp-airport>
            <option value="all">Semua Bandara</option>
          </select>
        </div>
        <div class="field"><label for="dspType">DSP Type</label>
          <select id="dspType" data-dsp-type>
            <option value="all">Semua Tipe</option>
            <option value="Main">Main</option>
            <option value="Addendum">Addendum</option>
          </select>
        </div>
        <div class="field"><label for="dspYear">Addendum Year</label>
          <select id="dspYear" data-dsp-year>
            <option value="all">Semua Tahun</option>
            <option value="2026">2026</option>
            <option value="2025">2025</option>
            <option value="2024">2024</option>
            <option value="2023">2023</option>
          </select>
        </div>
        <button type="button" class="btn btn-primary" data-dsp-search><i class="bi bi-search"></i> Cari</button>
      </div>

      <div class="table-wrap">
        <table class="reg-table" data-dsp-table>
          <thead><tr><th>Bandara</th><th>Type</th><th>Tahun</th><th>Nomor Lembar Penerimaan</th><th>Nomor / Perihal DSP</th><th>Aksi</th></tr></thead>
          <tbody></tbody>
        </table>
        <p data-dsp-empty style="display:none; text-align:center; padding:40px; color:var(--text-500);">Tidak ada dokumen yang sesuai dengan filter.</p>
      </div>
    </div>
  </div>
</section>
<div class="page-title-block">
  <div class="container">
    <div class="eyebrow">Informasi Publik</div>
    <h1>Informasi Publik Direktorat Bandar Udara</h1>
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
