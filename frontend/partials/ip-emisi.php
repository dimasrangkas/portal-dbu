<?php /* Dasbor Informasi Publik — emisi. Dipakai halaman per subdirektorat. */ ?>
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

