-- ============================================================
--  MIGRASI: Selaraskan skema server dengan skema aplikasi
--
--  Beberapa kolom dipakai oleh kode tetapi tidak pernah dibuat oleh
--  schema.sql maupun migrasi mana pun, sehingga basis data yang dipasang
--  dari schema.sql kekurangan 15 kolom dan 1 tabel. Akibatnya halaman
--  Organisasi gagal (HTTP 500) dan migrate-daftar-bandara.sql berhenti
--  dengan "Unknown column 'group_label'".
--
--  Jalankan migrasi ini LEBIH DULU, sebelum migrate-organisasi-navbar.sql
--  dan migrate-daftar-bandara.sql.
--
--  Aman dijalankan berulang: setiap kolom diperiksa dulu keberadaannya.
--  Tidak ada DROP dan tidak ada data yang ditimpa — hanya penambahan.
--
--  Kompatibilitas: memakai prepared statement tingkat atas, bukan stored
--  procedure. "ADD COLUMN IF NOT EXISTS" sengaja dihindari karena itu
--  sintaks khusus MariaDB — MySQL 8 menolaknya dengan galat 1064.
--
--  Jalankan: mysql -u USER -p NAMA_DB < database/migrate-sinkron-skema.sql
-- ============================================================
SET NAMES utf8mb4;

-- ---------- 1. menu_items: mega menu ----------
-- group_label mengelompokkan item di mega menu, description jadi teks
-- pendamping, style menentukan tampilan tautan/tombol.

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='menu_items' AND COLUMN_NAME='group_label'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `menu_items` ADD COLUMN `group_label` VARCHAR(120) NULL AFTER `label`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='menu_items' AND COLUMN_NAME='description'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `menu_items` ADD COLUMN `description` VARCHAR(255) NULL AFTER `group_label`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='menu_items' AND COLUMN_NAME='style'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `menu_items` ADD COLUMN `style` ENUM(''link'',''button'',''button-primary'') NOT NULL DEFAULT ''link'' AFTER `icon`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------- 2. org_units: tugas & dua pejabat ----------

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='tugas'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `tugas` TEXT NULL AFTER `description`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='head_name'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `head_name` VARCHAR(160) NULL AFTER `tugas`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='head_position'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `head_position` VARCHAR(160) NULL AFTER `head_name`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='head_photo'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `head_photo` VARCHAR(255) NULL AFTER `head_position`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='team_lead_name'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `team_lead_name` VARCHAR(160) NULL AFTER `head_photo`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='team_lead_position'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `team_lead_position` VARCHAR(160) NULL AFTER `team_lead_name`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='org_units' AND COLUMN_NAME='team_lead_photo'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `org_units` ADD COLUMN `team_lead_photo` VARCHAR(255) NULL AFTER `team_lead_position`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------- 3. services: kaitan ke subdirektorat ----------

SET @s = IF(EXISTS(SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
            AND TABLE_NAME='services' AND COLUMN_NAME='org_unit_id'),
  'SELECT 1 INTO @skip',
  'ALTER TABLE `services` ADD COLUMN `org_unit_id` INT UNSIGNED NULL AFTER `unit_pengelola`');
PREPARE st FROM @s; EXECUTE st; DEALLOCATE PREPARE st;

-- ---------- 4. org_unit_functions: rincian fungsi per subdirektorat ----------
CREATE TABLE IF NOT EXISTS `org_unit_functions` (
  `id`      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `unit_id` INT UNSIGNED NOT NULL,
  `content` TEXT NOT NULL,
  `sort`    INT NOT NULL DEFAULT 0,
  KEY `idx_org_fn` (`unit_id`),
  CONSTRAINT `fk_org_fn` FOREIGN KEY (`unit_id`)
      REFERENCES `org_units` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Verifikasi ----------
SELECT 'menu_items' AS tabel, COUNT(*) AS ada, 3 AS harus FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'menu_items'
   AND COLUMN_NAME IN ('group_label','description','style')
UNION ALL
SELECT 'org_units', COUNT(*), 7 FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'org_units'
   AND COLUMN_NAME IN ('tugas','head_name','head_position','head_photo',
                       'team_lead_name','team_lead_position','team_lead_photo')
UNION ALL
SELECT 'services', COUNT(*), 1 FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'services'
   AND COLUMN_NAME = 'org_unit_id'
UNION ALL
SELECT 'org_unit_functions', COUNT(*), 1 FROM information_schema.TABLES
 WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'org_unit_functions';
