<?php
/* Dasbor Data Kebandarudaraan — DI LUAR CAKUPAN CMS (markup & data statis, sesuai permintaan). */
?>
<section class="section section-alt" id="dashboard" data-dash-carousel>
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Pusat Data</div>
      <h2>Dasbor Data Kebandarudaraan</h2>
      <p>Ringkasan data registrasi, emisi, fasilitas sisi udara, pelayanan darurat, dan penyelenggaraan bandar udara.</p>
    </div>
    <div class="dash-tabs" role="tablist" aria-label="Pilih dasbor">
        <button type="button" class="dash-tab active" data-dash-tab="0" role="tab" aria-selected="true"><i class="bi bi-journal-check"></i> Register Bandar Udara</button>
        <button type="button" class="dash-tab" data-dash-tab="1" role="tab" aria-selected="false"><i class="bi bi-cloud-haze2-fill"></i> Emisi Karbon</button>
        <button type="button" class="dash-tab" data-dash-tab="2" role="tab" aria-selected="false"><i class="bi bi-airplane-fill"></i> Runway, Taxiway & Apron</button>
        <button type="button" class="dash-tab" data-dash-tab="3" role="tab" aria-selected="false"><i class="bi bi-truck-front-fill"></i> Peralatan & Pelayanan Darurat</button>
        <button type="button" class="dash-tab" data-dash-tab="4" role="tab" aria-selected="false"><i class="bi bi-graph-up"></i> Penyelenggaraan & Pengusahaan</button>
    </div>
  </div>

  <div class="dash-viewport">
    <div class="dash-slide active" data-dash-slide="0" role="tabpanel">
<section class="section section-alt" id="register-bandara">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Registrasi</div>
      <h2>Register Bandar Udara</h2>
      <p>Cari data sertifikasi dan registrasi bandar udara/heliport berdasarkan basis dan jenis dokumen.</p>
    </div>

    <div class="card card-pad" style="margin-bottom:20px;">
      <div class="form-grid">
        <div class="field">
          <label for="abuBase">Base</label>
          <select id="abuBase" data-abu-base>
            <option value="bandar-udara">Bandar Udara</option>
            <option value="heliport">Heliport</option>
          </select>
        </div>
        <div class="field">
          <label for="abuCert">SBU - RBU</label>
          <select id="abuCert" data-abu-cert>
            <option value="sbu">Sertifikat Bandar Udara</option>
            <option value="rbu">Register Bandar Udara</option>
            <option value="rwb">Register Waterbase</option>
          </select>
        </div>
      </div>
    </div>

    <div class="filter-bar">
      <div class="grow">
        <i class="bi bi-search"></i>
        <input type="text" placeholder="Cari nomor atau nama bandara..." data-abu-search>
      </div>
    </div>

    <div class="table-wrap abu-table-wrap">
      <table class="reg-table" data-abu-table>
        <thead>
          <tr><th>No.</th><th>Nomor</th><th>Bandara</th><th>Masa Berlaku</th></tr>
        </thead>
        <tbody>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2025-03-01"><td data-abu-no>1</td><td>001/SBU/III/2025</td><td>I Gusti Ngurah Rai</td><td>March 1, 2025</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2025-06-15"><td data-abu-no>2</td><td>002/SBU/III/2025</td><td>Juanda</td><td>June 15, 2025</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2026-01-10"><td data-abu-no>3</td><td>003/SBU/III/2025</td><td>Kualanamu</td><td>January 10, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2026-08-20"><td data-abu-no>4</td><td>004/SBU/III/2025</td><td>Sultan Aji Muhammad Sulaiman</td><td>August 20, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2026-09-05"><td data-abu-no>5</td><td>005/SBU/III/2025</td><td>Adisutjipto</td><td>September 5, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2029-04-12"><td data-abu-no>6</td><td>006/SBU/III/2025</td><td>Jenderal Ahmad Yani</td><td>April 12, 2029</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2026-07-01"><td data-abu-no>7</td><td>007/SBU/III/2025</td><td>Sam Ratulangi</td><td>July 1, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2030-11-30"><td data-abu-no>8</td><td>008/SBU/III/2025</td><td>Sultan Hasanuddin</td><td>November 30, 2030</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2026-10-01"><td data-abu-no>9</td><td>009/SBU/III/2025</td><td>Halim Perdanakusuma</td><td>October 1, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2028-02-14"><td data-abu-no>10</td><td>010/SBU/III/2025</td><td>Sultan Iskandar Muda</td><td>February 14, 2028</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2025-12-20"><td data-abu-no>11</td><td>011/SBU/III/2025</td><td>Hang Nadim</td><td>December 20, 2025</td></tr>
          <tr data-base="bandar-udara" data-cert="sbu" data-expiry="2027-05-18"><td data-abu-no>12</td><td>012/SBU/III/2025</td><td>Minangkabau</td><td>May 18, 2027</td></tr>

          <tr data-base="bandar-udara" data-cert="rbu" data-expiry="2024-11-01"><td data-abu-no>1</td><td>001/RBU/II/2024</td><td>Radin Inten II</td><td>November 1, 2024</td></tr>
          <tr data-base="bandar-udara" data-cert="rbu" data-expiry="2026-08-25"><td data-abu-no>2</td><td>002/RBU/IV/2025</td><td>Fatmawati Soekarno</td><td>August 25, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="rbu" data-expiry="2027-03-10"><td data-abu-no>3</td><td>003/RBU/I/2026</td><td>Depati Amir</td><td>March 10, 2027</td></tr>
          <tr data-base="bandar-udara" data-cert="rbu" data-expiry="2026-09-15"><td data-abu-no>4</td><td>004/RBU/V/2026</td><td>Mopah</td><td>September 15, 2026</td></tr>

          <tr data-base="bandar-udara" data-cert="rwb" data-expiry="2025-09-01"><td data-abu-no>1</td><td>001/RWB/I/2025</td><td>Waterbase Kaimana</td><td>September 1, 2025</td></tr>
          <tr data-base="bandar-udara" data-cert="rwb" data-expiry="2026-08-15"><td data-abu-no>2</td><td>002/RWB/II/2026</td><td>Waterbase Nabire</td><td>August 15, 2026</td></tr>
          <tr data-base="bandar-udara" data-cert="rwb" data-expiry="2028-01-20"><td data-abu-no>3</td><td>003/RWB/III/2026</td><td>Waterbase Sorong</td><td>January 20, 2028</td></tr>

          <tr data-base="heliport" data-cert="sbu" data-expiry="2025-05-01"><td data-abu-no>1</td><td>001/SBU-H/I/2025</td><td>Helipad Senayan</td><td>May 1, 2025</td></tr>
          <tr data-base="heliport" data-cert="sbu" data-expiry="2026-08-30"><td data-abu-no>2</td><td>002/SBU-H/II/2026</td><td>Helipad Monas</td><td>August 30, 2026</td></tr>
          <tr data-base="heliport" data-cert="sbu" data-expiry="2029-06-01"><td data-abu-no>3</td><td>003/SBU-H/III/2026</td><td>Helipad Ancol</td><td>June 1, 2029</td></tr>

          <tr data-base="heliport" data-cert="rbu" data-expiry="2026-07-20"><td data-abu-no>1</td><td>001/RBU-H/I/2026</td><td>Helipad RSCM</td><td>July 20, 2026</td></tr>
          <tr data-base="heliport" data-cert="rbu" data-expiry="2027-11-11"><td data-abu-no>2</td><td>002/RBU-H/II/2026</td><td>Helipad BNN</td><td>November 11, 2027</td></tr>

          <tr data-base="heliport" data-cert="rwb" data-expiry="2025-08-01"><td data-abu-no>1</td><td>001/RWB-H/I/2025</td><td>Waterbase Helipad Danau Toba</td><td>August 1, 2025</td></tr>
          <tr data-base="heliport" data-cert="rwb" data-expiry="2026-08-18"><td data-abu-no>2</td><td>002/RWB-H/II/2026</td><td>Waterbase Helipad Raja Ampat</td><td>August 18, 2026</td></tr>
        </tbody>
      </table>
      <p data-abu-empty style="display:none; text-align:center; padding:40px; color:var(--text-500);">Tidak ada data yang sesuai dengan pencarian/filter Anda.</p>
    </div>
    <div style="display:flex; gap:20px; flex-wrap:wrap; margin-top:14px; font-size:12.5px; color:var(--text-500);">
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:11px;height:11px;border-radius:3px;background:var(--danger-bg);border:1px solid var(--danger);display:inline-block;"></span> Masa berlaku telah habis</span>
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:11px;height:11px;border-radius:3px;background:var(--warning-bg);border:1px solid var(--warning);display:inline-block;"></span> Akan habis (&le; 90 hari)</span>
      <span style="display:flex; align-items:center; gap:6px;"><span style="width:11px;height:11px;border-radius:3px;background:#fff;border:1px solid var(--border);display:inline-block;"></span> Masih berlaku</span>
    </div>
  </div>
</section>
    </div>
    <div class="dash-slide" data-dash-slide="1" role="tabpanel" hidden>
<section class="section section-alt" id="emisi">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Lingkungan</div>
      <h2>Emisi Karbon Bandar Udara</h2>
      <p>Ringkasan pemantauan emisi gas rumah kaca dari operasional kebandarudaraan. Arahkan kursor ke batang grafik untuk melihat rincian.</p>
    </div>

    <div class="emisi-grid">
      <div class="card card-pad emisi-card">
        <h3>Emissions by Scope</h3>
        <div class="emisi-legend" data-emisi-legend="scope">
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#00396B"></span>Scope 1</span>
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#005BAC"></span>Scope 2</span>
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#2E86D8"></span>Scope 3</span>
        </div>
        <div class="emisi-chart-wrap" data-emisi-chart="scope"></div>
      </div>

      <div class="card card-pad emisi-card">
        <h3>Quarterly Comparison of Emissions</h3>
        <div class="emisi-legend" data-emisi-legend="year">
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#a8440f"></span>2023</span>
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#e07830"></span>2024</span>
          <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#eb9a63"></span>2025</span>
        </div>
        <div class="emisi-chart-wrap" data-emisi-chart="quarterly"></div>
      </div>

      <div class="card card-pad emisi-card">
        <h3>Emissions Insights</h3>
        <div class="emisi-total-badge"><b>14,466</b><span>tCO2e</span></div>
        <div class="emisi-scope-list">
          <div class="emisi-scope-row"><span>Scope 1</span><b>10.232,7 tCO2e</b></div>
          <div class="emisi-scope-row"><span>Scope 2</span><b>2.100,7 tCO2e</b></div>
          <div class="emisi-scope-row"><span>Scope 3</span><b>2.132,6 tCO2e</b></div>
        </div>
        <div class="emisi-activities">
          <div class="emisi-activities-head"><span>Activities</span><span>% Emissions tCO2e</span></div>
          <div class="emisi-activity-row">
            <div class="emisi-activity-left"><div class="emisi-activity-ic" style="background:var(--success-bg); color:var(--success);"><i class="bi bi-car-front-fill"></i></div><span>Transportation</span></div>
            <b>41%</b>
          </div>
          <div class="emisi-activity-row">
            <div class="emisi-activity-left"><div class="emisi-activity-ic" style="background:var(--danger-bg); color:var(--danger);"><i class="bi bi-fuel-pump-fill"></i></div><span>Fuel &amp; Energy</span></div>
            <b>26%</b>
          </div>
          <div class="emisi-activity-row">
            <div class="emisi-activity-left"><div class="emisi-activity-ic" style="background:var(--violet-bg); color:var(--violet);"><i class="bi bi-lightbulb-fill"></i></div><span>Electricity</span></div>
            <b>26%</b>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
    </div>
    <div class="dash-slide" data-dash-slide="2" role="tabpanel" hidden>
<section class="section" id="fasilitas-sisi-udara">
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Fasilitas Sisi Udara</div>
      <h2>Status Runway, Taxiway &amp; Apron</h2>
      <p>Data kondisi dan spesifikasi teknis fasilitas sisi udara bandar udara.</p>
    </div>

    <div class="card card-pad spec-picker">
      <div class="field">
        <label for="specAirport"><i class="bi bi-geo-alt-fill" style="color:var(--primary)"></i> Pilih Bandara</label>
        <select id="specAirport" data-spec-airport></select>
      </div>
    </div>

    <div class="grid grid-3">
      <div class="card card-pad spec-card">
        <div class="spec-head">
          <div class="ic"><i class="bi bi-airplane-fill"></i></div>
          <div>
            <h3>Runway</h3>
            <span class="spec-count" data-spec-count="runway"></span>
          </div>
        </div>
        <div class="spec-body" data-spec-body="runway"></div>
      </div>

      <div class="card card-pad spec-card">
        <div class="spec-head">
          <div class="ic"><i class="bi bi-signpost-2-fill"></i></div>
          <div>
            <h3>Taxiway</h3>
            <span class="spec-count" data-spec-count="taxiway"></span>
          </div>
        </div>
        <div class="spec-body" data-spec-body="taxiway"></div>
      </div>

      <div class="card card-pad spec-card">
        <div class="spec-head">
          <div class="ic"><i class="bi bi-grid-3x3-gap-fill"></i></div>
          <div>
            <h3>Apron</h3>
            <span class="spec-count" data-spec-count="apron"></span>
          </div>
        </div>
        <div class="spec-body" data-spec-body="apron"></div>
      </div>
    </div>
  </div>
</section>
    </div>
    <div class="dash-slide" data-dash-slide="3" role="tabpanel" hidden>
<section class="section section-alt" id="peralatan-darurat" data-pkp-section>
  <div class="container">
    <div class="section-head center">
      <div class="eyebrow" style="justify-content:center">Pelayanan Darurat</div>
      <h2>Peralatan &amp; Pelayanan Darurat Bandar Udara</h2>
      <p>Rekap data pengelola bandar udara, kategori PKP-PK, status verifikasi, dan peralatan visual approach. Arahkan kursor ke grafik untuk melihat rincian.</p>
    </div>

    <div class="pkp-row-2">
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-airplane-fill"></i></div><h3>Data Pengelola &amp; Kelas Bandara</h3></div>
        <div class="pkp-table-wrap">
          <table class="pkp-table">
            <thead><tr><th>Pengelola Bandar Udara</th><th>Kelas Bandara</th><th style="text-align:right">Jumlah</th></tr></thead>
            <tbody>
              <tr><td class="grp" rowspan="6">UPT Ditjen Hubud</td><td>Kelas I (BLU)</td><td class="num"><span class="badge badge-primary">20</span></td></tr>
              <tr><td>Kelas I</td><td class="num"><span class="badge badge-primary">15</span></td></tr>
              <tr><td>Kelas II (BLU)</td><td class="num"><span class="badge badge-primary">13</span></td></tr>
              <tr><td>Kelas II</td><td class="num"><span class="badge badge-primary">12</span></td></tr>
              <tr><td>Kelas III</td><td class="num"><span class="badge badge-primary">8</span></td></tr>
              <tr><td>Satpel BU</td><td class="num"><span class="badge badge-primary">5</span></td></tr>
              <tr><td class="grp">Angkasa Pura Indonesia</td><td>Non Kelas</td><td class="num"><span class="badge badge-primary">99</span></td></tr>
              <tr><td class="grp">Bandar Udara UPTD/Pemda</td><td>Non Kelas</td><td class="num"><span class="badge badge-primary">37</span></td></tr>
              <tr><td class="grp">Bandar Udara Bubu Lain</td><td>Non Kelas</td><td class="num"><span class="badge badge-primary">157</span></td></tr>
            </tbody>
          </table>
        </div>
      </div>

      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-truck-front-fill"></i></div><h3>Kategori Pelayanan PKP-PK</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="kategori"></div>
        </div>
      </div>
    </div>

    <div class="grid grid-3" style="margin-bottom:20px;">
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-clipboard-check-fill"></i></div><h3>Status Tindak Lanjut Verifikasi PKP-PK</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="tindak-lanjut"></div>
        </div>
      </div>

      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-patch-check-fill"></i></div><h3>Status Verifikasi PKP-PK</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="verifikasi"></div>
        </div>
      </div>

      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-file-earmark-check-fill"></i></div><h3>Status Dokumen AEP</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="aep"></div>
        </div>
      </div>
    </div>

    <div class="grid grid-3">
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-bullseye"></i></div><h3>Vasis Light Breakdown</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="vasis-breakdown"></div>
        </div>
      </div>

      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-activity"></i></div><h3>Status Kalibrasi VASIS</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="vasis-kalibrasi"></div>
          <div class="emisi-legend" style="margin-top:10px;">
            <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#4a3aa7"></span>PAPI</span>
            <span class="emisi-legend-item"><span class="emisi-legend-dot" style="background:#1baf7a"></span>A-PAPI</span>
          </div>
        </div>
      </div>

      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-sun-fill"></i></div><h3>Approach Light Assets</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="approach-light"></div>
        </div>
      </div>
    </div>
  </div>
</section>
    </div>
    <div class="dash-slide" data-dash-slide="4" role="tabpanel" hidden>
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
    </div>
  </div>

  <div class="container dash-nav">
    <button type="button" class="dash-arrow" data-dash-prev aria-label="Dasbor sebelumnya"><i class="bi bi-chevron-left"></i></button>
    <div class="dash-dots" data-dash-dots></div>
    <button type="button" class="dash-arrow" data-dash-next aria-label="Dasbor berikutnya"><i class="bi bi-chevron-right"></i></button>
  </div>
</section>
