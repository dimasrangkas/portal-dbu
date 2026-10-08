-- ============================================================
--  MIGRASI LANJUTAN: menampung data lengkap 608 bandar udara.
--
--  database/migrate-detail-bandara.sql dibuat berdasarkan satu
--  contoh halaman (Palu). Setelah seluruh data resmi diterima
--  dalam bandara_part_01..05.json, ada beberapa medan yang belum
--  punya tempat. Berkas ini menutup kekurangannya:
--
--  1. `bandaras.pesawat_beroperasi` — medan "Pesawat Beroperasi"
--     pada tab Data Umum (ada di 195 bandara).
--  2. `bandara_rute.keberangkatan` — data resmi mencantumkan
--     bandara asal DAN tujuan; sebelumnya hanya `tujuan`.
--  3. Tambahan spesifikasi fasilitas udara: azimuth, tipe runway,
--     critical aircraft, pesawat beroperasi, dan declare distance
--     (TORA/TODA/ASDA/LDA per ujung runway, disimpan sebagai JSON
--     karena jumlah ujungnya berbeda-beda tiap runway).
--  4. Pelebaran kolom yang ternyata kurang panjang untuk data asli
--     (mis. `daya_dukung` sampai 86 huruf, `nama` fasilitas darat
--     sampai 100 huruf).
--  5. `kategori` dilebarkan: kategori fasilitas darat ada 40 macam,
--     siputnya bisa lebih panjang dari 40 huruf.
--
--  Aman dijalankan berulang. Tanpa stored procedure — lihat catatan
--  pada migrate-detail-bandara.sql soal mysql.proc di XAMPP.
--
--  Jalankan setelah migrate-detail-bandara.sql:
--    mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-detail-bandara-lanjutan.sql
-- ============================================================
SET NAMES utf8mb4;
SET SESSION group_concat_max_len = 8192;

-- ---------- 1. Kolom baru pada `bandaras` ----------
SET @kolom := (
  SELECT GROUP_CONCAT(CONCAT('ADD COLUMN `', d.nama, '` ', d.definisi) SEPARATOR ', ')
    FROM (
      SELECT 'pesawat_beroperasi' AS nama, 'VARCHAR(120) NULL' AS definisi
    ) AS d
   WHERE NOT EXISTS (
     SELECT 1 FROM information_schema.COLUMNS c
      WHERE c.TABLE_SCHEMA = DATABASE()
        AND c.TABLE_NAME   = 'bandaras'
        AND c.COLUMN_NAME  = d.nama
   )
);
SET @sql := IF(@kolom IS NULL, 'DO 0', CONCAT('ALTER TABLE `bandaras` ', @kolom));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- 2. Kolom baru pada `bandara_rute` ----------
SET @kolom := (
  SELECT GROUP_CONCAT(CONCAT('ADD COLUMN `', d.nama, '` ', d.definisi) SEPARATOR ', ')
    FROM (
      SELECT 'keberangkatan' AS nama, 'VARCHAR(160) NULL AFTER `bandara_id`' AS definisi
    ) AS d
   WHERE NOT EXISTS (
     SELECT 1 FROM information_schema.COLUMNS c
      WHERE c.TABLE_SCHEMA = DATABASE()
        AND c.TABLE_NAME   = 'bandara_rute'
        AND c.COLUMN_NAME  = d.nama
   )
);
SET @sql := IF(@kolom IS NULL, 'DO 0', CONCAT('ALTER TABLE `bandara_rute` ', @kolom));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- 3. Kolom baru pada `bandara_fasilitas_udara` ----------
-- `declare_distance` memuat TORA/TODA/ASDA/LDA untuk tiap ujung runway.
-- Bentuknya {"Runway 02": {"TORA": "2.220 m", ...}, "Runway 20": {...}}.
-- Disimpan sebagai teks JSON: jumlah ujung berbeda-beda dan datanya hanya
-- ditampilkan apa adanya, tidak pernah dicari atau dijumlahkan.
SET @kolom := (
  SELECT GROUP_CONCAT(CONCAT('ADD COLUMN `', d.nama, '` ', d.definisi) SEPARATOR ', ')
    FROM (
              SELECT 'critical_aircraft'  AS nama, 'VARCHAR(120) NULL' AS definisi
    UNION ALL SELECT 'pesawat_beroperasi',        'VARCHAR(120) NULL'
    UNION ALL SELECT 'azimuth',                   'VARCHAR(30) NULL'
    UNION ALL SELECT 'tipe_runway',               'VARCHAR(255) NULL'
    UNION ALL SELECT 'declare_distance',          'TEXT NULL COMMENT ''JSON: {"Runway 02":{"TORA":"2.220 m",...}}'''
    ) AS d
   WHERE NOT EXISTS (
     SELECT 1 FROM information_schema.COLUMNS c
      WHERE c.TABLE_SCHEMA = DATABASE()
        AND c.TABLE_NAME   = 'bandara_fasilitas_udara'
        AND c.COLUMN_NAME  = d.nama
   )
);
SET @sql := IF(@kolom IS NULL, 'DO 0', CONCAT('ALTER TABLE `bandara_fasilitas_udara` ', @kolom));
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- 4. Pelebaran kolom yang kurang panjang ----------
-- MODIFY COLUMN tidak punya bentuk "IF NEEDED", tetapi menyetel tipe yang
-- sama persis dengan tipe sekarang tidak mengubah apa pun. Supaya berkas ini
-- tetap murah saat diulang, tiap kolom hanya diubah bila panjangnya masih
-- kurang dari sasaran.

-- bandara_fasilitas_udara.kategori: siput kategori terpanjang
-- (runway-end-safety-area) 23 huruf — 40 sudah cukup, dibiarkan.
-- bandara_fasilitas_udara.nama: "Runway End Safety Area (RESA) (...)" 66 huruf.
SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_udara'
      AND COLUMN_NAME = 'daya_dukung') >= 255,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_udara` MODIFY `daya_dukung` VARCHAR(255) NULL COMMENT ''Contoh: PCR 510/F/C/X/T'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Dimensi terpanjang 23 huruf, tetapi kolom ini juga menampung
-- "Luas Terverifikasi"/"Luas Terbangun" (mis. 125.764,00 m2) — 120 aman.
SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_udara'
      AND COLUMN_NAME = 'dimensi_verifikasi') >= 120,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_udara` MODIFY `dimensi_verifikasi` VARCHAR(120) NULL COMMENT ''Dimensi atau luas terverifikasi'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_udara'
      AND COLUMN_NAME = 'dimensi_terbangun') >= 120,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_udara` MODIFY `dimensi_terbangun` VARCHAR(120) NULL COMMENT ''Dimensi atau luas terbangun'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- bandara_fasilitas_darat.nama: terpanjang 100 huruf, beri ruang lebih.
SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_darat'
      AND COLUMN_NAME = 'nama') >= 255,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_darat` MODIFY `nama` VARCHAR(255) NOT NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Siput kategori darat terpanjang: lahan-parkir-gedung-terminal-dan-landscape (46).
SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_darat'
      AND COLUMN_NAME = 'kategori') >= 60,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_darat` MODIFY `kategori` VARCHAR(60) NOT NULL COMMENT ''Siput kategori, lihat BANDARA_FASILITAS_DARAT'''
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Nama fasilitas umum ada yang panjang ("Flight Information Display System (FIDS)").
SET @sql := IF(
  (SELECT CHARACTER_MAXIMUM_LENGTH FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandara_fasilitas_umum'
      AND COLUMN_NAME = 'nama') >= 160,
  'DO 0',
  'ALTER TABLE `bandara_fasilitas_umum` MODIFY `nama` VARCHAR(160) NOT NULL'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- bandaras.status_blu: teks resmi sampai 121 huruf, 160 masih muat — dibiarkan.
-- bandaras.airnav: sampai 258 huruf, kolomnya sudah 300 — dibiarkan.
