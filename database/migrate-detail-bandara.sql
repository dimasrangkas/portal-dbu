-- ============================================================
--  MIGRASI: Data rinci halaman detail bandar udara.
--
--  Menambah data terlampir pada halaman detail bandara mengikuti
--  susunan tab: Data Umum, Bandar Udara Terdekat, Rute Domestik,
--  Fasilitas Udara, Fasilitas Darat, Fasilitas Umum, Transportasi,
--  dan Galeri.
--
--  1. Kolom tambahan pada tabel `bandaras` untuk tab Data Umum.
--  2. Lima tabel anak untuk data yang jumlahnya banyak per bandara.
--
--  Tab "Bandar Udara Terdekat" tidak butuh tabel: jaraknya dihitung
--  dari kolom `lokasi_arp` yang sudah ada (lihat bandara_terdekat()
--  pada shared/functions.php).
--
--  Aman dijalankan berulang pada basis data yang sudah berisi data.
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-detail-bandara.sql
-- ============================================================
SET NAMES utf8mb4;

-- ---------- 1. Kolom tambahan pada `bandaras` (tab Data Umum) ----------
-- ADD COLUMN IF NOT EXISTS tidak ada di MySQL 5.7/8.0, jadi daftar kolom
-- disaring dulu terhadap information_schema lalu dirangkai menjadi satu
-- ALTER TABLE. Kolom yang sudah ada dilewati, sehingga berkas ini aman
-- dijalankan lebih dari sekali.
--
-- Sengaja tanpa stored procedure: pada XAMPP yang tabel sistemnya belum
-- di-mysql_upgrade, CREATE PROCEDURE gagal dengan "Column count of
-- mysql.proc is wrong". Cara ini tidak menyentuh tabel sistem sama sekali.
SET SESSION group_concat_max_len = 8192;

SET @kolom := (
  SELECT GROUP_CONCAT(CONCAT('ADD COLUMN `', d.nama, '` ', d.definisi) SEPARATOR ', ')
    FROM (
              SELECT 'status_operasi'    AS nama, 'VARCHAR(60) NULL'  AS definisi
    UNION ALL SELECT 'critical_aircraft',        'VARCHAR(120) NULL'
    UNION ALL SELECT 'hierarki',                 'VARCHAR(60) NULL'
    UNION ALL SELECT 'pkp_pk',                   'VARCHAR(60) NULL'
    UNION ALL SELECT 'klasifikasi',              'VARCHAR(30) NULL'
    UNION ALL SELECT 'airnav',                   'VARCHAR(300) NULL'
    UNION ALL SELECT 'status_blu',               'VARCHAR(160) NULL'
    UNION ALL SELECT 'status_blu_url',           'VARCHAR(300) NULL'
    UNION ALL SELECT 'tahun_data',               'SMALLINT NULL'
    UNION ALL SELECT 'dokumen_pendukung',        'TEXT NULL'
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

-- ---------- 2. Samakan tipe kunci utama ----------
-- database/bandaras.sql membuat `id` sebagai INT(11) bertanda, sedangkan
-- database/schema.sql memakai INT UNSIGNED. Kunci asing menuntut tipe yang
-- persis sama, jadi disamakan dulu ke UNSIGNED (seluruh id bernilai positif,
-- tidak ada data yang hilang). Dilewati bila sudah unsigned, supaya tidak
-- menyusun ulang tabel setiap kali berkas ini dijalankan.
SET @sql := IF(
  (SELECT COLUMN_TYPE FROM information_schema.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'bandaras' AND COLUMN_NAME = 'id') LIKE '%unsigned%',
  'DO 0',
  'ALTER TABLE `bandaras` MODIFY `id` INT UNSIGNED NOT NULL AUTO_INCREMENT'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ---------- 3. Tabel anak ----------

-- Tab "Rute Domestik"
CREATE TABLE IF NOT EXISTS `bandara_rute` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bandara_id` INT UNSIGNED NOT NULL,
  `tujuan`     VARCHAR(160) NOT NULL,
  `maskapai`   VARCHAR(120) NULL,
  `pesawat`    VARCHAR(60)  NULL,
  `frekuensi`  VARCHAR(60)  NULL COMMENT 'Contoh: 7x / minggu',
  `sort`       INT NOT NULL DEFAULT 0,
  KEY `idx_rute_bandara` (`bandara_id`),
  CONSTRAINT `fk_rute_bandara` FOREIGN KEY (`bandara_id`) REFERENCES `bandaras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tab "Fasilitas Udara" — kategori mengisi sub-tab (runway, taxiway, apron, ...)
CREATE TABLE IF NOT EXISTS `bandara_fasilitas_udara` (
  `id`              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bandara_id`      INT UNSIGNED NOT NULL,
  `kategori`        VARCHAR(40)  NOT NULL COMMENT 'runway|runway-turn-pad|taxiway|apron|runway-strip|stop-way|resa|clearway',
  `nama`            VARCHAR(160) NOT NULL,
  `dimensi_verifikasi` VARCHAR(80) NULL COMMENT 'Contoh: 2.500,00 x 45,00 m',
  `dimensi_terbangun`  VARCHAR(80) NULL,
  `konstruksi`      VARCHAR(120) NULL,
  `daya_dukung`     VARCHAR(120) NULL COMMENT 'Contoh: PCR 510/F/C/X/T',
  `sort`            INT NOT NULL DEFAULT 0,
  KEY `idx_fu_bandara` (`bandara_id`, `kategori`),
  CONSTRAINT `fk_fu_bandara` FOREIGN KEY (`bandara_id`) REFERENCES `bandaras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tab "Fasilitas Darat" — kategori mengisi sub-tab (gedung terminal, kargo, ...)
CREATE TABLE IF NOT EXISTS `bandara_fasilitas_darat` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bandara_id` INT UNSIGNED NOT NULL,
  `kategori`   VARCHAR(40)  NOT NULL COMMENT 'gedung-administrasi|gedung-terminal|gedung-kargo|...',
  `nama`       VARCHAR(160) NOT NULL,
  `luas`       DECIMAL(12,2) NULL COMMENT 'Meter persegi',
  `kapasitas`  VARCHAR(120) NULL COMMENT 'Khusus terminal, mis. 1.738.929 Penumpang/Tahun',
  `keterangan` VARCHAR(120) NULL COMMENT 'Khusus terminal, mis. Domestik',
  `sort`       INT NOT NULL DEFAULT 0,
  KEY `idx_fd_bandara` (`bandara_id`, `kategori`),
  CONSTRAINT `fk_fd_bandara` FOREIGN KEY (`bandara_id`) REFERENCES `bandaras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tab "Fasilitas Umum" dan "Transportasi" — satu tabel, dibedakan kolom `jenis`
CREATE TABLE IF NOT EXISTS `bandara_fasilitas_umum` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bandara_id` INT UNSIGNED NOT NULL,
  `jenis`      VARCHAR(20)  NOT NULL DEFAULT 'umum' COMMENT 'umum|transportasi',
  `nama`       VARCHAR(120) NOT NULL,
  `icon`       VARCHAR(60)  NULL COMMENT 'Nama ikon Bootstrap Icons, mis. bi-wifi',
  `sort`       INT NOT NULL DEFAULT 0,
  KEY `idx_fum_bandara` (`bandara_id`, `jenis`),
  CONSTRAINT `fk_fum_bandara` FOREIGN KEY (`bandara_id`) REFERENCES `bandaras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tab "Galeri"
CREATE TABLE IF NOT EXISTS `bandara_galeri` (
  `id`         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `bandara_id` INT UNSIGNED NOT NULL,
  `image`      VARCHAR(300) NULL,
  `keterangan` VARCHAR(160) NULL,
  `sort`       INT NOT NULL DEFAULT 0,
  KEY `idx_gal_bandara` (`bandara_id`),
  CONSTRAINT `fk_gal_bandara` FOREIGN KEY (`bandara_id`) REFERENCES `bandaras` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
