<?php /* Dasbor Informasi Publik — register-bandara. Dipakai halaman per subdirektorat. */ ?>
<section class="section" id="register-bandara">
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

