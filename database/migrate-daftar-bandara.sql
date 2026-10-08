-- ============================================================
--  MIGRASI: Halaman Informasi Publik menjadi Daftar Bandar Udara.
--  1. Meta halaman baru "Dokumen Publik" (laporan & unduhan yang
--     sebelumnya menempel di halaman Informasi Publik).
--  2. Tautan "Dokumen Publik" pada mega menu Informasi Publik.
--  Tabel `bandaras` sendiri dibuat oleh database/bandaras.sql.
--  Aman dijalankan pada basis data yang sudah berisi data.
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-daftar-bandara.sql
-- ============================================================
SET NAMES utf8mb4;
-- USE `dbu_cms`;   <- nama basis data di server sering berbeda.
--                    Pilih basis datanya saat menjalankan, contoh:
--                    mysql -u USER -p NAMA_DB < migrate-daftar-bandara.sql

-- ---------- 1. Meta halaman Dokumen Publik ----------
INSERT INTO `page_meta` (`page_key`,`page_label`,`meta_title`,`meta_description`,`eyebrow`,`heading`,`subtitle`,`breadcrumb`)
VALUES ('dokumen-publik','Dokumen Publik',
        'Dokumen Publik — Direktorat Bandar Udara',
        'Laporan tahunan, laporan kinerja, rencana strategis, dan dokumen publik lain yang dapat diunduh.',
        'Informasi Publik','Dokumen Publik',
        'Laporan tahunan, laporan kinerja, rencana strategis, dan dokumen publik lain yang dapat diunduh.',
        'Dokumen Publik')
ON DUPLICATE KEY UPDATE
  `page_label` = VALUES(`page_label`),
  `meta_title` = VALUES(`meta_title`);

-- ---------- 2. Meta halaman Informasi Publik menyesuaikan isi barunya ----------
UPDATE `page_meta`
   SET `meta_title`       = 'Daftar Bandar Udara — Direktorat Bandar Udara',
       `meta_description` = 'Peta dan tabel bandar udara di Indonesia: kode ICAO/IATA, lokasi, kelas, penggunaan, dan pengelola.',
       `heading`          = 'Daftar Bandar Udara',
       `subtitle`         = 'Data bandar udara di seluruh Indonesia beserta kode ICAO/IATA, lokasi, kelas, penggunaan, dan pengelolanya.'
 WHERE `page_key` = 'informasi-publik';

-- ---------- 3. Tautan Dokumen Publik pada mega menu ----------
INSERT INTO `menu_items` (`location`,`parent_id`,`label`,`group_label`,`description`,`url`,`icon`,`style`,`is_external`,`sort`,`is_active`)
SELECT 'navbar', m.`id`, 'Dokumen Publik', 'Data & Informasi',
       'Laporan tahunan, kinerja, dan rencana strategis', 'pages/dokumen-publik',
       'bi-file-earmark-text', 'link', 0, 2, 1
  FROM `menu_items` m
 WHERE m.`location` = 'navbar' AND m.`parent_id` IS NULL AND m.`url` = 'pages/informasi-publik'
   AND NOT EXISTS (SELECT 1 FROM (SELECT * FROM `menu_items`) x
                    WHERE x.`location` = 'navbar' AND x.`url` = 'pages/dokumen-publik')
 LIMIT 1;

-- Nama tautan daftar bandara disesuaikan dengan isi halamannya.
UPDATE `menu_items`
   SET `label`       = 'Daftar Bandar Udara',
       `description`  = 'Peta dan tabel seluruh bandar udara di Indonesia'
 WHERE `location` = 'navbar' AND `url` = 'pages/informasi-publik' AND `parent_id` IS NOT NULL;
