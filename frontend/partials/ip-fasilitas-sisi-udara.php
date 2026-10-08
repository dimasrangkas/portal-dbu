<?php /* Dasbor Informasi Publik — fasilitas-sisi-udara. Dipakai halaman per subdirektorat. */ ?>
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

