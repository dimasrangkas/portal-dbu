-- ============================================================
--  DATA CONTOH: Bandar Udara Mutiara SIS Al-Jufri, Palu (WAFF/PLW)
--
--  Mengisi seluruh tab halaman detail dengan data nyata yang
--  disalin dari hubud.kemenhub.go.id/bandara/27, supaya susunan
--  halaman bisa diperiksa dengan isi yang wajar sebelum operator
--  mengisi bandara lain lewat menu admin.
--
--  Aman dijalankan berulang: baris anak milik Palu dihapus dulu.
--  Jalankan: mysql -u root dbu_cms < database/seed-bandara-palu.sql
-- ============================================================
SET NAMES utf8mb4;

SET @b := (SELECT id FROM bandaras WHERE kode_icao = 'WAFF' LIMIT 1);

-- Tab "Data Umum"
UPDATE bandaras SET
  status_operasi    = 'Umum',
  critical_aircraft = 'BOEING 737-900ER',
  hierarki          = 'Pengumpul Skala Tersier',
  pkp_pk            = 'Kategori 7',
  klasifikasi       = '4D',
  tahun_data        = 2025,
  airnav            = 'Kantor Cabang Pembantu Palu',
  status_blu        = 'PM 15 Tahun 2018',
  status_blu_url    = 'https://peraturan.bpk.go.id/',
  jumlah_pesawat    = 3977,
  jumlah_penumpang  = 429591,
  jumlah_kargo      = 4726349,
  dokumen_pendukung = 'Renstra Bandara\nLAKIP\nRencana Kerja Tahunan\nPerjanjian Kinerja\nRencana Aksi\nASP\nAEP'
WHERE id = @b;

-- Bersihkan data anak lama milik Palu supaya berkas ini bisa diulang
DELETE FROM bandara_rute            WHERE bandara_id = @b;
DELETE FROM bandara_fasilitas_udara WHERE bandara_id = @b;
DELETE FROM bandara_fasilitas_darat WHERE bandara_id = @b;
DELETE FROM bandara_fasilitas_umum  WHERE bandara_id = @b;
DELETE FROM bandara_galeri          WHERE bandara_id = @b;

-- Tab "Rute Domestik"
INSERT INTO bandara_rute (bandara_id, tujuan, maskapai, pesawat, frekuensi, sort) VALUES
(@b, 'Soekarno Hatta',     'Super Air Jet', 'A320', '7x / minggu',  0),
(@b, 'Sultan Hasanuddin',  'Lion Air',      'B738', '21x / minggu', 1),
(@b, 'Morowali',           'Wings Air',     'AT76', '10x / minggu', 2),
(@b, 'Seko',               'Susi Air',      'C208', '2x / minggu',  3);

-- Tab "Fasilitas Udara" — `kategori` menentukan sub-tabnya
INSERT INTO bandara_fasilitas_udara
  (bandara_id, kategori, nama, dimensi_verifikasi, dimensi_terbangun, konstruksi, daya_dukung, sort) VALUES
(@b, 'runway',          'Runway 15-33',                      '2.500,00 x 45,00 m',  '2.500,00 x 45,00 m',  'Aspal Hotmix', 'PCR 510/F/C/X/T', 0),
(@b, 'runway-turn-pads', 'Turning Area 15',                   '60,00 x 15,00 m',     '60,00 x 15,00 m',     'Aspal Hotmix', NULL,              0),
(@b, 'taxiway',         'Taxiway A',                         '90,00 x 23,00 m',     '90,00 x 23,00 m',     'Aspal Hotmix', 'PCR 510/F/C/X/T', 0),
(@b, 'taxiway',         'Taxiway B',                         '90,00 x 23,00 m',     '90,00 x 23,00 m',     'Aspal Hotmix', 'PCR 510/F/C/X/U', 1),
(@b, 'apron',           'Aircraft Stand 01 - 07, R1 and R2', '373,00 x 110,00 m',   '373,00 x 110,00 m',   'Beton/Rigid',  'PCR 610/R/C/X/U', 0),
(@b, 'runway-strip',    'Strip',                             '2.680,00 x 300,00 m', '2.680,00 x 300,00 m', 'Rumput',       NULL,              0),
(@b, 'stop-way',        'SWY 15',                            '60,00 x 45,00 m',     '60,00 x 45,00 m',     'Tanah Padat',  NULL,              0),
(@b, 'runway-end-safety-area', 'RESA 15',                           '90,00 x 150,00 m',    '90,00 x 150,00 m',    'Tanah Padat',  NULL,              0),
(@b, 'clearway',        'CWY 33',                            '100,00 x 300,00 m',   '100,00 x 300,00 m',   'Tanah Padat',  NULL,              0);

-- Tab "Fasilitas Darat" — `kategori` menentukan sub-tabnya
INSERT INTO bandara_fasilitas_darat
  (bandara_id, kategori, nama, luas, kapasitas, keterangan, sort) VALUES
(@b, 'gedung-administrasi', 'Gedung Kantor Administrasi', 1600.00,  NULL,                        NULL,       0),
(@b, 'gedung-administrasi', 'Gedung Kantor Lama',          405.00,  NULL,                        NULL,       1),
(@b, 'gedung-terminal',     'Terminal Penumpang',        19476.00,  '1.738.929 Penumpang/Tahun', 'Domestik', 0),
(@b, 'gedung-kargo',        'Gedung Kargo Lini 1',        1656.00,  NULL,                        NULL,       0),
(@b, 'gapura-bandara',      'Main Gate',                  1039.24,  NULL,                        NULL,       0);

-- Tab "Fasilitas Umum" dan "Transportasi"
INSERT INTO bandara_fasilitas_umum (bandara_id, jenis, nama, icon, sort) VALUES
(@b, 'umum',         'Free Wifi',          'bi-wifi',              0),
(@b, 'umum',         'ATM Center',         'bi-cash-coin',         1),
(@b, 'umum',         'Ruang Menyusui',     'bi-heart',             2),
(@b, 'umum',         'Toilet Disabilitas', 'bi-person-wheelchair', 3),
(@b, 'umum',         'Musala',             'bi-moon-stars',        4),
(@b, 'umum',         'Ruang Kesehatan',    'bi-hospital',          5),
(@b, 'transportasi', 'Bus',                'bi-bus-front',         0),
(@b, 'transportasi', 'Taksi',              'bi-taxi-front',        1),
(@b, 'transportasi', 'Sewa Mobil',         'bi-car-front',         2);

-- Tab "Galeri" — kolom `image` dibiarkan kosong; unggah fotonya lewat menu admin
INSERT INTO bandara_galeri (bandara_id, image, keterangan, sort) VALUES
(@b, NULL, 'Gerbang Masuk Bandara', 0),
(@b, NULL, 'Apron',                 1),
(@b, NULL, 'Ruang Tunggu',          2);
