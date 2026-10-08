/* ============================================================
   Daftar Bandar Udara — peta Leaflet + tabel, satu sumber data.
   Pencarian dan filter pengelola berlaku serentak untuk keduanya.
   ============================================================ */
(function () {
  'use strict';

  var sumber = document.getElementById('bandaraData');
  var petaEl = document.getElementById('bandaraPeta');
  if (!sumber || !petaEl || typeof L === 'undefined') { return; }

  var SEMUA = JSON.parse(sumber.textContent);
  var PER_HALAMAN = 20;

  /* Warna marker per pengelola. Nama yang tak terdaftar memakai warna cadangan. */
  var WARNA = {
    'BUMN'                       : '#2563eb',
    'Masyarakat'                 : '#16a34a',
    'Missionaris'                : '#db2777',
    'PT. Angkasa Pura Indonesia' : '#1d4ed8',
    'Swasta'                     : '#ea580c',
    'TNI'                        : '#7c3aed',
    'UPT Daerah / Pemda'         : '#dc2626',
    'UPT Ditjen Hubud'           : '#15803d'
  };
  var WARNA_CADANGAN = '#64748b';

  function warna(pengelola) {
    return WARNA[pengelola] || WARNA_CADANGAN;
  }

  /* Titik warna pada chip filter mengikuti warna markernya. */
  document.querySelectorAll('[data-pengelola]').forEach(function (dot) {
    var nama = dot.getAttribute('data-pengelola');
    dot.style.background = nama === '__semua' ? WARNA_CADANGAN : warna(nama);
  });

  function teks(nilai) {
    return (nilai === null || nilai === undefined || nilai === '') ? '—' : String(nilai);
  }

  function aman(nilai) {
    return teks(nilai).replace(/[&<>"]/g, function (c) {
      return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
    });
  }

  function angka(n) {
    return (n || 0).toLocaleString('id-ID');
  }

  /* Kartu berisi seluruh kolom bandara — dipakai untuk tooltip hover dan popup klik. */
  function kartu(b) {
    var baris = [
      ['Kode ICAO / IATA', aman(b.ic) + ' / ' + aman(b.ia)],
      ['Penggunaan', aman(b.tp)],
      ['Kelas', aman(b.kl)],
      ['Pengelola', aman(b.pg)],
      ['Kantor Otoritas', aman(b.ko)],
      ['Alamat', aman(b.al)],
      ['Kelurahan / Desa', aman(b.kd)],
      ['Kecamatan', aman(b.kc)],
      ['Kabupaten / Kota', aman(b.kk)],
      ['Provinsi', aman(b.pr)],
      ['Koordinat ARP', aman(b.ar)],
      ['Telepon', aman(b.tl)],
      ['Faksimile', aman(b.fx)],
      ['Surel', aman(b.em)],
      ['Situs Web', aman(b.ws)],
      ['Pergerakan Pesawat', angka(b.jp)],
      ['Jumlah Penumpang', angka(b.jn)],
      ['Jumlah Kargo', angka(b.jg)]
    ];
    var isi = baris.map(function (r) {
      return '<div class="peta-kartu-baris"><span>' + r[0] + '</span><b>' + r[1] + '</b></div>';
    }).join('');

    return '<div class="peta-kartu">' +
             '<div class="peta-kartu-kepala" style="border-color:' + warna(b.pg) + '">' +
               '<b>' + aman(b.n) + '</b>' +
               '<span>' + aman(b.pg) + '</span>' +
             '</div>' +
             '<div class="peta-kartu-isi">' + isi + '</div>' +
           '</div>';
  }

  /* ---------- Peta ---------- */
  var peta = L.map('bandaraPeta', { scrollWheelZoom: true }).setView([-2.5, 118], 5);

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(peta);

  var kelompok = L.markerClusterGroup({
    showCoverageOnHover: false,
    maxClusterRadius: 45,
    spiderfyOnMaxZoom: true
  });
  peta.addLayer(kelompok);

  /* Tombol kembali ke tampilan awal Indonesia. */
  var Reset = L.Control.extend({
    options: { position: 'bottomright' },
    onAdd: function () {
      var tombol = L.DomUtil.create('button', 'peta-reset');
      tombol.type = 'button';
      tombol.title = 'Kembalikan tampilan peta';
      tombol.innerHTML = '<i class="bi bi-arrow-clockwise"></i>';
      L.DomEvent.on(tombol, 'click', function (e) {
        L.DomEvent.stop(e);
        peta.setView([-2.5, 118], 5);
      });
      return tombol;
    }
  });
  peta.addControl(new Reset());

  function gambarMarker(daftar) {
    kelompok.clearLayers();
    var marker = [];

    daftar.forEach(function (b) {
      if (b.la === null || b.lo === null) { return; }

      var titik = L.circleMarker([b.la, b.lo], {
        radius: 7,
        color: '#fff',
        weight: 2,
        fillColor: warna(b.pg),
        fillOpacity: 1
      });

      /* Hover memunculkan seluruh data; klik menyematkannya sebagai popup. */
      titik.bindTooltip(kartu(b), {
        direction: 'auto',
        offset: [0, 0],
        opacity: 1,
        className: 'peta-tooltip'
      });
      titik.bindPopup(
        kartu(b) +
        '<a class="btn btn-primary btn-sm peta-kartu-tombol" href="' +
          window.BANDARA_DETAIL_URL + '?id=' + b.i + '">Halaman detail</a>',
        /* Harus >= lebar .peta-kartu; Leaflet memakai nilai ini sebagai
           max-width kaku pada .leaflet-popup-content, dan kartu yang lebih
           lebar akan meluber keluar gelembung. */
        { maxWidth: 460, minWidth: 260, className: 'peta-popup' }
      );

      marker.push(titik);
    });

    kelompok.addLayers(marker);
    return marker.length;
  }

  /* ---------- Tabel ---------- */
  var tbody      = document.querySelector('[data-bandara-tbody]');
  var pagination = document.querySelector('[data-bandara-pagination]');
  var halaman    = 1;

  function gambarTabel(daftar) {
    var halTotal = Math.max(1, Math.ceil(daftar.length / PER_HALAMAN));
    if (halaman > halTotal) { halaman = halTotal; }
    var mulai = (halaman - 1) * PER_HALAMAN;
    var iris  = daftar.slice(mulai, mulai + PER_HALAMAN);

    if (!iris.length) {
      tbody.innerHTML = '<tr><td colspan="10" style="text-align:center; padding:32px;">' +
                        'Tidak ada bandar udara yang cocok dengan pencarian.</td></tr>';
      pagination.innerHTML = '';
      return;
    }

    tbody.innerHTML = iris.map(function (b, i) {
      var lencana = b.tp === 'Internasional' ? 'badge-primary' : 'badge-gray';
      return '<tr>' +
        '<td>' + (mulai + i + 1) + '</td>' +
        '<td><b>' + aman(b.ic) + '</b></td>' +
        '<td>' + aman(b.ia) + '</td>' +
        '<td><b>' + aman(b.n) + '</b></td>' +
        '<td>' + aman(b.pr) + '</td>' +
        '<td>' + aman(b.kk) + '</td>' +
        '<td><span class="badge ' + lencana + '">' + aman(b.tp) + '</span></td>' +
        '<td>' + aman(b.kl) + '</td>' +
        '<td>' + aman(b.pg) + '</td>' +
        '<td><a class="more" href="' + window.BANDARA_DETAIL_URL + '?id=' + b.i + '">Detail <i class="bi bi-arrow-right"></i></a></td>' +
      '</tr>';
    }).join('');

    gambarPaginasi(halTotal);
  }

  function gambarPaginasi(halTotal) {
    if (halTotal < 2) { pagination.innerHTML = ''; return; }

    var tampil = [1, 2, halaman - 1, halaman, halaman + 1, halTotal - 1, halTotal]
      .filter(function (n, i, arr) { return n >= 1 && n <= halTotal && arr.indexOf(n) === i; })
      .sort(function (a, b) { return a - b; });

    var html = '';
    if (halaman > 1) {
      html += '<button type="button" class="page-btn" data-hal="' + (halaman - 1) + '"><i class="bi bi-chevron-left"></i></button>';
    }
    var sebelum = 0;
    tampil.forEach(function (n) {
      if (sebelum && n - sebelum > 1) { html += '<span class="page-gap">…</span>'; }
      html += '<button type="button" class="page-btn' + (n === halaman ? ' active' : '') + '" data-hal="' + n + '">' + n + '</button>';
      sebelum = n;
    });
    if (halaman < halTotal) {
      html += '<button type="button" class="page-btn" data-hal="' + (halaman + 1) + '"><i class="bi bi-chevron-right"></i></button>';
    }
    pagination.innerHTML = html;
  }

  pagination.addEventListener('click', function (e) {
    var tombol = e.target.closest('[data-hal]');
    if (!tombol) { return; }
    halaman = parseInt(tombol.getAttribute('data-hal'), 10);
    gambarTabel(saring());
    document.querySelector('[data-view-panel="tabel"]').scrollIntoView({ behavior: 'smooth', block: 'start' });
  });

  /* ---------- Pencarian & filter ---------- */
  var inputCari    = document.querySelector('[data-bandara-cari]');
  var grupPengelola = document.querySelector('[data-bandara-pengelola]');
  var hitungEl     = document.querySelectorAll('[data-bandara-hitung]');

  function saring() {
    var kata     = (inputCari.value || '').trim().toLowerCase();
    var terpilih = grupPengelola.querySelector('input:checked').value;

    return SEMUA.filter(function (b) {
      if (terpilih && (b.pg || 'Lainnya') !== terpilih) { return false; }
      if (!kata) { return true; }
      return (b.n || '').toLowerCase().indexOf(kata) !== -1 ||
             (b.ia || '').toLowerCase().indexOf(kata) !== -1 ||
             (b.ic || '').toLowerCase().indexOf(kata) !== -1;
    });
  }

  function perbarui(geserPeta) {
    var daftar   = saring();
    var terpasang = gambarMarker(daftar);
    gambarTabel(daftar);

    hitungEl.forEach(function (el) {
      el.textContent = 'Menampilkan ' + daftar.length + ' dari ' + SEMUA.length + ' Bandar Udara';
    });

    /* Setelah menyaring, dekatkan peta ke hasil yang tersisa. */
    if (geserPeta && terpasang) {
      var batas = kelompok.getBounds();
      if (batas.isValid()) { peta.fitBounds(batas, { padding: [40, 40], maxZoom: 12 }); }
    }
  }

  var jeda;
  inputCari.addEventListener('input', function () {
    clearTimeout(jeda);
    jeda = setTimeout(function () { halaman = 1; perbarui(true); }, 250);
  });

  grupPengelola.addEventListener('change', function () {
    grupPengelola.querySelectorAll('.chip-radio').forEach(function (chip) {
      chip.classList.toggle('active', chip.querySelector('input').checked);
    });
    halaman = 1;
    perbarui(true);
  });

  /* ---------- Alih tampilan Peta / Tabel ---------- */
  document.querySelectorAll('.view-switch button').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      var tampilan = tombol.getAttribute('data-view');

      document.querySelectorAll('.view-switch button').forEach(function (b) {
        b.classList.toggle('active', b === tombol);
      });
      document.querySelectorAll('[data-view-panel]').forEach(function (panel) {
        panel.hidden = panel.getAttribute('data-view-panel') !== tampilan;
      });

      /* Leaflet perlu diberi tahu ukuran wadahnya setelah panel ditampilkan lagi. */
      if (tampilan === 'peta') { setTimeout(function () { peta.invalidateSize(); }, 0); }
    });
  });

  perbarui(false);
})();
