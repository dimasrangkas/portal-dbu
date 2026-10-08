<?php /* Dasbor Informasi Publik — peralatan-darurat. Dipakai halaman per subdirektorat. */ ?>
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

    <div class="grid grid-2">
      <div class="card card-pad pkp-card">
        <div class="pkp-head"><div class="ic"><i class="bi bi-bullseye"></i></div><h3>Vasis Breakdown</h3></div>
        <div class="pkp-chart-body">
          <div class="pkp-chart-wrap" data-pkp-chart="vasis-breakdown"></div>
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

