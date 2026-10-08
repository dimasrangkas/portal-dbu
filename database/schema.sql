-- ============================================================
--  DIREKTORAT BANDAR UDARA — SKEMA CMS (SEDERHANA)
--  MySQL / MariaDB
--
--  Seluruh konten portal disimpan pada tiga sumbu saja:
--
--      categories     kategori   — satu baris per halaman portal
--      subcategories  sub kategori — satu baris per seksi di dalam halaman
--      tags           tag        — penanda lintas halaman
--
--  Isinya sendiri ada di `contents`; kolom yang hanya dipakai satu-dua
--  seksi (mis. nomor peraturan, teks tombol) disimpan sebagai JSON pada
--  kolom `extra`, bukan sebagai kolom tabel baru.
--
--  Skema lama dengan ~40 tabel konten ada di schema-lama.sql, dan
--  migrate-unifikasi-konten.php memindahkan datanya ke sini.
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `dbu_cms`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dbu_cms`;

-- ============================================================
--  KONTEN
-- ============================================================

-- ---------- Kategori: satu baris per halaman portal ----------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(60) NOT NULL UNIQUE COMMENT 'Kunci halaman, dipakai page_start() dan kategori()',
  `label` VARCHAR(120) NOT NULL COMMENT 'Nama kategori di panel CMS',
  `icon` VARCHAR(80) NULL,
  `page_file` VARCHAR(120) NULL COMMENT 'Berkas halaman portal, mis. pages/berita.php',
  `meta_title` VARCHAR(200) NULL,
  `meta_description` VARCHAR(255) NULL,
  `eyebrow` VARCHAR(160) NULL,
  `heading` VARCHAR(200) NULL,
  `subtitle` TEXT NULL,
  `breadcrumb` VARCHAR(120) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Sub kategori: satu baris per seksi halaman ----------
DROP TABLE IF EXISTS `subcategories`;
CREATE TABLE `subcategories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `slug` VARCHAR(80) NOT NULL,
  `label` VARCHAR(160) NOT NULL COMMENT 'Nama seksi di panel CMS',
  `icon` VARCHAR(80) NULL,
  `eyebrow` VARCHAR(160) NULL COMMENT 'Judul seksi sebagaimana tampil di portal',
  `title` VARCHAR(255) NULL,
  `subtitle` TEXT NULL,
  `body` TEXT NULL,
  `blueprint` VARCHAR(60) NOT NULL DEFAULT 'umum' COMMENT 'Kunci susunan kolom formulir di admin/config/blueprints.php',
  `is_child` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = daftar baris anak, disunting di dalam formulir induknya',
  `hint` VARCHAR(255) NULL COMMENT 'Keterangan singkat untuk editor',
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  UNIQUE KEY `uq_subcategory` (`category_id`,`slug`),
  KEY `idx_subcategory_sort` (`category_id`,`sort`),
  CONSTRAINT `fk_subcategory_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tag ----------
DROP TABLE IF EXISTS `tags`;
CREATE TABLE `tags` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(80) NOT NULL UNIQUE,
  `label` VARCHAR(120) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Konten ----------
DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT UNSIGNED NOT NULL,
  `subcategory_id` INT UNSIGNED NULL,
  `parent_id` INT UNSIGNED NULL COMMENT 'Baris anak: persyaratan layanan, foto dalam album, dsb.',
  `slug` VARCHAR(200) NULL,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(255) NULL,
  `subtitle` VARCHAR(255) NULL,
  `excerpt` VARCHAR(500) NULL,
  `body` LONGTEXT NULL,
  `image` VARCHAR(255) NULL,
  `file` VARCHAR(255) NULL,
  `url` VARCHAR(500) NULL,
  `icon` VARCHAR(80) NULL,
  `art_class` VARCHAR(20) NULL,
  `published_at` DATE NULL,
  `sort` INT NOT NULL DEFAULT 0 COMMENT 'Urutan tampil di portal — diatur dari CMS',
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `extra` LONGTEXT NULL COMMENT 'JSON — kolom khusus seksi tertentu',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `idx_content_list` (`category_id`,`subcategory_id`,`sort`),
  KEY `idx_content_parent` (`parent_id`,`sort`),
  KEY `idx_content_slug` (`slug`),
  KEY `idx_content_date` (`published_at`),
  CONSTRAINT `fk_content_category`    FOREIGN KEY (`category_id`)    REFERENCES `categories`(`id`)    ON DELETE CASCADE,
  CONSTRAINT `fk_content_subcategory` FOREIGN KEY (`subcategory_id`) REFERENCES `subcategories`(`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_content_parent`      FOREIGN KEY (`parent_id`)      REFERENCES `contents`(`id`)      ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Relasi konten ↔ tag ----------
DROP TABLE IF EXISTS `content_tags`;
CREATE TABLE `content_tags` (
  `content_id` INT UNSIGNED NOT NULL,
  `tag_id` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`content_id`,`tag_id`),
  KEY `idx_content_tag` (`tag_id`),
  CONSTRAINT `fk_ct_content` FOREIGN KEY (`content_id`) REFERENCES `contents`(`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_ct_tag`     FOREIGN KEY (`tag_id`)     REFERENCES `tags`(`id`)     ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
--  DI LUAR MODEL KONTEN
--  Bukan isi halaman: akun, pengaturan, navigasi, kotak masuk, dan
--  data bandar udara yang diperbarui lewat impor SQL.
-- ============================================================

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(120) NOT NULL,
  `username` VARCHAR(60) NOT NULL UNIQUE,
  `email` VARCHAR(160) NOT NULL,
  `password_hash` VARCHAR(255) NOT NULL,
  `role` ENUM('admin','editor') NOT NULL DEFAULT 'editor',
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `last_login_at` DATETIME NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `group_key` VARCHAR(60) NOT NULL DEFAULT 'umum',
  `setting_key` VARCHAR(80) NOT NULL UNIQUE,
  `label` VARCHAR(160) NOT NULL,
  `value` TEXT NULL,
  `type` ENUM('text','textarea','image','url','email','number') NOT NULL DEFAULT 'text',
  `hint` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `location` ENUM('navbar','footer_quick','footer_service','social') NOT NULL DEFAULT 'navbar',
  `parent_id` INT UNSIGNED NULL,
  `label` VARCHAR(120) NOT NULL,
  `group_label` VARCHAR(120) NULL,
  `description` VARCHAR(255) NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '#',
  `icon` VARCHAR(80) NULL,
  `style` ENUM('link','mega') NOT NULL DEFAULT 'link',
  `match_pages` VARCHAR(255) NULL,
  `is_external` TINYINT(1) NOT NULL DEFAULT 0,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_menu_loc` (`location`,`parent_id`,`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contact_messages`;
CREATE TABLE `contact_messages` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(160) NOT NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(60) NULL,
  `subject` VARCHAR(180) NULL,
  `message` TEXT NOT NULL,
  `is_read` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_applications`;
CREATE TABLE `service_applications` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_name` VARCHAR(255) NOT NULL,
  `applicant_name` VARCHAR(160) NOT NULL,
  `institution` VARCHAR(200) NULL,
  `email` VARCHAR(180) NOT NULL,
  `phone` VARCHAR(60) NULL,
  `notes` TEXT NULL,
  `file` VARCHAR(255) NULL,
  `status` ENUM('baru','diproses','selesai','ditolak') NOT NULL DEFAULT 'baru',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `newsletter_subscribers`;
CREATE TABLE `newsletter_subscribers` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `email` VARCHAR(180) NOT NULL UNIQUE,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daftar bandar udara — diperbarui lewat impor database/bandaras.sql
CREATE TABLE IF NOT EXISTS `bandaras` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nama` VARCHAR(200) NOT NULL,
  `kode_iata` VARCHAR(10) NULL,
  `kode_icao` VARCHAR(10) NULL,
  `alamat` VARCHAR(500) NULL,
  `email` VARCHAR(180) NULL,
  `fax` VARCHAR(60) NULL,
  `kelurahan_desa` VARCHAR(160) NULL,
  `kecamatan` VARCHAR(160) NULL,
  `kabupaten_kota` VARCHAR(160) NULL,
  `provinsi` VARCHAR(160) NULL,
  `kantor_otoritas` VARCHAR(160) NULL,
  `kelas` VARCHAR(60) NULL,
  `latitude` VARCHAR(40) NULL,
  `longitude` VARCHAR(40) NULL,
  `lokasi_arp` VARCHAR(160) NULL,
  `pengelola` VARCHAR(160) NULL,
  `jumlah_pesawat` INT NULL,
  `telepon` VARCHAR(60) NULL,
  `tipe` VARCHAR(60) NULL,
  `website` VARCHAR(200) NULL,
  `jumlah_penumpang` INT NULL,
  `jumlah_kargo` INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;
