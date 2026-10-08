/* ============================================================
   Detail bandar udara — carousel hero, tiga peta Leaflet,
   dan pembesar foto.

   Data peta dikirim halaman lewat <script id="bdrPetaData">, isinya:
   { ini: {nama, icao, lat, lng}, terdekat: [...], rute: [...], lapis }.
   `lapis` adalah alamat berkas GeoJSON kawasan KKOP dan cakupan bandara
   (data/peta/<ICAO>.json) yang baru diambil saat lapisannya dinyalakan,
   atau null bila bandara itu belum punya datanya.
   Bandara tanpa koordinat ARP yang terbaca mengirim ini = null;
   dalam hal itu tidak ada peta yang dibuat sama sekali.
   ============================================================ */
(function () {
  'use strict';

  function aman(nilai) {
    return String(nilai === null || nilai === undefined ? '' : nilai)
      .replace(/[&<>"]/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c];
      });
  }

  /* ---------- Carousel foto pada hero ---------- */
  document.querySelectorAll('[data-carousel]').forEach(function (wadah) {
    var slide = wadah.querySelectorAll('.bdr-slide');
    if (slide.length < 2) {
      /* Satu foto tidak butuh panah. */
      wadah.querySelectorAll('.bdr-carousel-nav').forEach(function (b) { b.remove(); });
      return;
    }

    var kini = 0;
    function tampilkan(i) {
      slide[kini].classList.remove('active');
      kini = (i + slide.length) % slide.length;
      slide[kini].classList.add('active');
    }

    wadah.querySelectorAll('.bdr-carousel-nav').forEach(function (tombol) {
      tombol.addEventListener('click', function () {
        tampilkan(kini + parseInt(tombol.getAttribute('data-arah'), 10));
      });
    });
  });

  /* ---------- Pembesar foto ---------- */
  document.addEventListener('click', function (ev) {
    var tautan = ev.target.closest('[data-lightbox]');
    if (!tautan) { return; }
    ev.preventDefault();

    var lapis = document.createElement('div');
    lapis.className = 'bdr-lightbox';
    lapis.innerHTML = '<button type="button" aria-label="Tutup"><i class="bi bi-x-lg"></i></button>'
                    + '<img src="' + aman(tautan.getAttribute('href')) + '" alt="">';
    document.body.appendChild(lapis);

    function tutup() {
      lapis.remove();
      document.removeEventListener('keydown', padaTombol);
    }
    function padaTombol(e) { if (e.key === 'Escape') { tutup(); } }

    /* Klik di mana pun selain gambar menutup lapisan. */
    lapis.addEventListener('click', function (e) {
      if (e.target.tagName !== 'IMG') { tutup(); }
    });
    document.addEventListener('keydown', padaTombol);
  });

  /* ---------- Peta ---------- */
  var sumber = document.getElementById('bdrPetaData');
  if (!sumber || typeof L === 'undefined') { return; }

  var DATA = JSON.parse(sumber.textContent);
  if (!DATA.ini) { return; }

  var UBIN     = 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png';
  var ATRIBUSI = '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>';

  /* Peta yang lahir di dalam tab tersembunyi salah menghitung ukurannya;
     dikumpulkan di sini lalu disegarkan saat tabnya dibuka. */
  var semuaPeta = [];

  function buatPeta(elId, opsi) {
    var el = document.getElementById(elId);
    if (!el) { return null; }
    var peta = L.map(el, Object.assign({ scrollWheelZoom: false }, opsi || {}));
    L.tileLayer(UBIN, { attribution: ATRIBUSI, maxZoom: 18 }).addTo(peta);
    semuaPeta.push(peta);
    return peta;
  }

  /* Penanda bandara ini: biru tua dan sedikit lebih besar dari yang lain. */
  function penandaUtama(peta) {
    return L.circleMarker([DATA.ini.lat, DATA.ini.lng], {
      radius: 9, color: '#fff', weight: 2.5, fillColor: '#005BAC', fillOpacity: 1
    }).addTo(peta).bindPopup(
      '<div class="bdr-peta-nama">' + aman(DATA.ini.nama) + '</div>'
      + (DATA.ini.icao ? '<div class="bdr-peta-info">ICAO ' + aman(DATA.ini.icao) + '</div>' : '')
    );
  }

  /* 1. Peta lokasi pada kartu ringkasan. */
  var petaLokasi = buatPeta('bdrPetaLokasi', { zoomControl: true });
  if (petaLokasi) {
    petaLokasi.setView([DATA.ini.lat, DATA.ini.lng], 13);
    penandaUtama(petaLokasi);
  }

  /* ---------- Lapisan KKOP dan cakupan pada peta lokasi ----------
     Gambarnya tidak ikut dimuat bersama halaman: berkas
     data/peta/<ICAO>.json baru diambil saat tombolnya ditekan pertama
     kali, lalu disimpan supaya penekanan berikutnya langsung tampil.
     DATA.lapis bernilai null untuk bandara yang datanya belum ada. */
  (function () {
    var wadah = document.querySelector('.peta-lapis');
    if (!wadah || !petaLokasi) { return; }

    /* Warna kawasan KKOP: panas di tengah, dingin ke luar, mengikuti
       urutan BANDARA_KKOP_ZONA pada shared/functions.php. Padanan
       keterangan warnanya ada di .peta-lapis-legenda (style.css). */
    var WARNA = {
      'utama':            '#B3261E',
      'bahaya':           '#E2622C',
      'ancangan':         '#D89B12',
      'transisi':         '#1B8A5A',
      'horizontal-dalam': '#0E8C86',
      'kerucut':          '#2E86D8',
      'horizontal-luar':  '#3F51A8',
      'cakupan':          '#8E44AD'
    };
    var NAMA = { kkop: 'KKOP', cakupan: 'cakupan bandara' };

    var nota    = wadah.querySelector('[data-lapis-nota]');
    var legenda = wadah.querySelector('[data-lapis-legenda]');
    var tombol  = Array.prototype.slice.call(wadah.querySelectorAll('.peta-lapis-btn'));
    var lapisan = {};   /* nama lapisan -> L.GeoJSON yang sudah jadi */
    var data    = null; /* isi berkas, diambil sekali saja */
    var sedang  = null; /* janji pengambilan yang masih berjalan */

    /* Bingkai awal peta, untuk dikembalikan saat semua lapisan dipadamkan. */
    var awalPusat = petaLokasi.getCenter();
    var awalZoom  = petaLokasi.getZoom();

    function nyala(nama) {
      var b = wadah.querySelector('.peta-lapis-btn[data-lapis="' + nama + '"]');
      return b && b.getAttribute('aria-pressed') === 'true';
    }

    function setNota(teks) {
      nota.textContent = teks || '';
      nota.hidden = !teks;
    }

    /* Keterangan warna hanya memuat zona yang benar-benar ada pada bandara
       ini; nama zonanya ikut di dalam berkas, tidak disalin ke sini. */
    function gambarLegenda() {
      var item = [];
      if (nyala('kkop') && data && data.zona) {
        data.zona.forEach(function (z) {
          item.push({ warna: WARNA[z.kode] || '#6B7A88', nama: z.nama, resmi: z.resmi });
        });
      }
      if (nyala('cakupan') && lapisan.cakupan) {
        item.push({ warna: WARNA.cakupan, nama: 'Cakupan Bandara', resmi: 'Cakupan pelayanan bandar udara' });
      }
      legenda.innerHTML = item.map(function (i) {
        return '<span class="peta-lapis-zona" title="' + aman(i.resmi) + '">'
             + '<i style="background:' + aman(i.warna) + '"></i>' + aman(i.nama) + '</span>';
      }).join('');
      legenda.hidden = item.length === 0;
    }

    /* Peta lokasi bersandar pada satu titik dengan zoom 13; kawasan KKOP
       maupun cakupan jauh lebih lebar dari itu, jadi bingkainya disesuaikan
       dengan lapisan yang sedang menyala — dan dikembalikan bila semuanya
       dipadamkan. */
    function sesuaikanBingkai() {
      var batas = null;
      Object.keys(lapisan).forEach(function (nama) {
        if (!nyala(nama)) { return; }
        batas = batas ? batas.extend(lapisan[nama].getBounds()) : lapisan[nama].getBounds();
      });
      if (batas) {
        petaLokasi.fitBounds(batas, { padding: [20, 20] });
      } else {
        petaLokasi.setView(awalPusat, awalZoom);
      }
    }

    function buatLapisan(nama) {
      if (lapisan[nama] || !data || !data[nama]) { return; }

      lapisan[nama] = L.geoJSON(data[nama], {
        style: function (fitur) {
          var kode  = (fitur.properties && fitur.properties.zona) || 'cakupan';
          var warna = WARNA[kode] || '#6B7A88';
          return {
            color: warna, weight: 1.4, opacity: .9,
            fillColor: warna, fillOpacity: nama === 'cakupan' ? .12 : .18,
            dashArray: nama === 'cakupan' ? '6 4' : null
          };
        },
        onEachFeature: function (fitur, lapis) {
          var kode  = (fitur.properties && fitur.properties.zona) || null;
          var zona  = kode && (data.zona || []).filter(function (z) { return z.kode === kode; })[0];
          var judul = zona ? zona.nama : 'Cakupan Bandara';
          var isi   = zona
            ? zona.resmi
            : 'Wilayah cakupan pelayanan pada tatanan kebandarudaraan. Gambarnya sudah '
              + 'tergabung, jadi satu wilayah dapat memuat beberapa bandar udara.';

          /* Sebagian zona bernama sama panjang dan pendeknya (Permukaan Utama);
             barisnya tidak perlu diulang. */
          lapis.bindPopup(
            '<div class="bdr-peta-nama">' + aman(judul) + '</div>'
            + (isi.toUpperCase() === judul.toUpperCase()
                ? ''
                : '<div class="bdr-peta-info">' + aman(isi) + '</div>')
          );
        }
      });
    }

    /* Satu berkas memuat kedua lapisan, jadi cukup diambil sekali. */
    function ambilData() {
      if (data) { return Promise.resolve(data); }
      if (sedang) { return sedang; }
      sedang = fetch(DATA.lapis)
        .then(function (r) {
          if (!r.ok) { throw new Error('HTTP ' + r.status); }
          return r.json();
        })
        .then(function (isi) { data = isi; return isi; });
      return sedang;
    }

    function perbarui() {
      var aktif = tombol.filter(function (b) { return b.getAttribute('aria-pressed') === 'true'; })
                        .map(function (b) { return b.getAttribute('data-lapis'); });

      if (!aktif.length) {
        Object.keys(lapisan).forEach(function (n) { petaLokasi.removeLayer(lapisan[n]); });
        setNota('');
        gambarLegenda();
        sesuaikanBingkai();
        return;
      }

      if (!DATA.lapis) {
        setNota('Data ' + aktif.map(function (n) { return NAMA[n]; }).join(' dan ')
              + ' untuk bandar udara ini belum tersedia, sehingga lapisannya belum tampil di peta.');
        return;
      }

      setNota(data ? '' : 'Memuat lapisan peta…');

      ambilData().then(function () {
        var kosong = [];
        ['kkop', 'cakupan'].forEach(function (nama) {
          buatLapisan(nama);
          if (!lapisan[nama]) {
            if (nyala(nama)) { kosong.push(NAMA[nama]); }
            return;
          }
          if (nyala(nama)) {
            lapisan[nama].addTo(petaLokasi);
          } else {
            petaLokasi.removeLayer(lapisan[nama]);
          }
        });
        setNota(kosong.length
          ? 'Data ' + kosong.join(' dan ') + ' untuk bandar udara ini belum tersedia.'
          : '');
        gambarLegenda();
        sesuaikanBingkai();
      }).catch(function () {
        setNota('Lapisan peta gagal dimuat. Coba muat ulang halaman ini.');
      });
    }

    tombol.forEach(function (b) {
      b.addEventListener('click', function () {
        b.setAttribute('aria-pressed', b.getAttribute('aria-pressed') === 'true' ? 'false' : 'true');
        perbarui();
      });
    });
  })();

  /* 2. Peta bandar udara terdekat. */
  var petaDekat = DATA.terdekat.length ? buatPeta('bdrPetaTerdekat') : null;
  if (petaDekat) {
    var titikDekat = [[DATA.ini.lat, DATA.ini.lng]];
    penandaUtama(petaDekat);

    DATA.terdekat.forEach(function (b) {
      titikDekat.push([b.lat, b.lng]);
      L.circleMarker([b.lat, b.lng], {
        radius: 6, color: '#fff', weight: 2, fillColor: '#6B7A88', fillOpacity: 1
      }).addTo(petaDekat).bindPopup(
        '<div class="bdr-peta-nama">' + aman(b.nama) + '</div>'
        + '<div class="bdr-peta-info">' + aman(b.jarak.toFixed(1).replace('.', ',')) + ' km dari sini</div>'
        + '<a class="bdr-peta-tautan" href="' + aman(b.url) + '">Lihat detail</a>'
      );
    });

    petaDekat.fitBounds(L.latLngBounds(titikDekat), { padding: [36, 36] });
  }

  /* 3. Peta rute: garis dari bandara ini ke tiap tujuan yang koordinatnya
        ditemukan. Tujuan yang namanya tak cocok dengan tabel `bandaras`
        tetap ada di daftar sebelah, hanya tanpa garis di peta. */
  var petaRute = DATA.rute.length ? buatPeta('bdrPetaRute') : null;
  if (petaRute) {
    var titikRute = [[DATA.ini.lat, DATA.ini.lng]];
    penandaUtama(petaRute);

    DATA.rute.forEach(function (b) {
      titikRute.push([b.lat, b.lng]);

      L.polyline([[DATA.ini.lat, DATA.ini.lng], [b.lat, b.lng]], {
        color: '#005BAC', weight: 1.6, opacity: .5
      }).addTo(petaRute);

      L.circleMarker([b.lat, b.lng], {
        radius: 6, color: '#fff', weight: 2, fillColor: '#1B8A5A', fillOpacity: 1
      }).addTo(petaRute).bindPopup(
        '<div class="bdr-peta-nama">' + aman(b.nama) + '</div>'
        + (b.maskapai.length
            ? '<div class="bdr-peta-info">' + aman(b.maskapai.join(', ')) + '</div>'
            : '')
        + '<a class="bdr-peta-tautan" href="' + aman(b.url) + '">Lihat detail</a>'
      );
    });

    petaRute.fitBounds(L.latLngBounds(titikRute), { padding: [36, 36] });
  }

  /* Leaflet perlu diberi tahu ukuran wadahnya setelah panelnya ditampilkan. */
  document.querySelectorAll('.tabs-nav[data-tabs="bdr"] button').forEach(function (tombol) {
    tombol.addEventListener('click', function () {
      setTimeout(function () {
        semuaPeta.forEach(function (peta) { peta.invalidateSize(); });
      }, 60);
    });
  });
})();
