-- ============================================================
--  DIREKTORAT BANDAR UDARA — CMS SCHEMA
--  MySQL / MariaDB
-- ============================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `dbu_cms`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `dbu_cms`;

-- ---------- Akun & pengaturan ----------
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

-- ---------- Navigasi ----------
DROP TABLE IF EXISTS `menu_items`;
CREATE TABLE `menu_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `location` ENUM('navbar','footer_quick','footer_service','social') NOT NULL DEFAULT 'navbar',
  `parent_id` INT UNSIGNED NULL,
  `label` VARCHAR(120) NOT NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '#',
  `icon` VARCHAR(80) NULL,
  `match_pages` VARCHAR(255) NULL,
  `is_external` TINYINT(1) NOT NULL DEFAULT 0,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_menu_loc` (`location`,`parent_id`,`sort`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Meta halaman & judul seksi ----------
DROP TABLE IF EXISTS `page_meta`;
CREATE TABLE `page_meta` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page_key` VARCHAR(60) NOT NULL UNIQUE,
  `page_label` VARCHAR(120) NOT NULL,
  `meta_title` VARCHAR(200) NULL,
  `meta_description` VARCHAR(255) NULL,
  `eyebrow` VARCHAR(160) NULL,
  `heading` VARCHAR(200) NULL,
  `subtitle` TEXT NULL,
  `breadcrumb` VARCHAR(120) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `page` VARCHAR(60) NOT NULL,
  `section_key` VARCHAR(80) NOT NULL,
  `label` VARCHAR(160) NOT NULL,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(255) NULL,
  `subtitle` TEXT NULL,
  `body` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `sort` INT NOT NULL DEFAULT 0,
  UNIQUE KEY `uq_section` (`page`,`section_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Beranda ----------
DROP TABLE IF EXISTS `hero_slides`;
CREATE TABLE `hero_slides` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(200) NOT NULL,
  `tagline` TEXT NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-1',
  `icon` VARCHAR(80) NULL,
  `image` VARCHAR(255) NULL,
  `btn1_label` VARCHAR(80) NULL,
  `btn1_url` VARCHAR(255) NULL,
  `btn2_label` VARCHAR(80) NULL,
  `btn2_url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `stats`;
CREATE TABLE `stats` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(120) NOT NULL,
  `value` INT NOT NULL DEFAULT 0,
  `suffix` VARCHAR(10) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `director_message`;
CREATE TABLE `director_message` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(200) NULL,
  `body` TEXT NULL,
  `director_name` VARCHAR(160) NULL,
  `director_position` VARCHAR(160) NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-2',
  `icon` VARCHAR(80) NULL,
  `btn_label` VARCHAR(120) NULL,
  `btn_url` VARCHAR(255) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `about_cards`;
CREATE TABLE `about_cards` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(160) NOT NULL,
  `description` VARCHAR(255) NULL,
  `url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `quick_links`;
CREATE TABLE `quick_links` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `label` VARCHAR(120) NOT NULL,
  `url` VARCHAR(255) NOT NULL DEFAULT '#',
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `featured_services`;
CREATE TABLE `featured_services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(160) NOT NULL,
  `description` VARCHAR(255) NULL,
  `url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `partners`;
CREATE TABLE `partners` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(160) NOT NULL,
  `logo` VARCHAR(255) NULL,
  `url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `regions`;
CREATE TABLE `regions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `region_code` VARCHAR(10) NOT NULL UNIQUE,
  `numeral` VARCHAR(10) NOT NULL,
  `title` VARCHAR(120) NOT NULL,
  `hq` VARCHAR(255) NULL,
  `hq_short` VARCHAR(160) NULL,
  `coverage` TEXT NULL,
  `coverage_short` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Daftar bandar udara di bawah tiap Otoritas Bandar Udara
DROP TABLE IF EXISTS `region_airports`;
CREATE TABLE `region_airports` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `region_id` INT UNSIGNED NOT NULL,
  `name` VARCHAR(200) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_region_airport` (`region_id`),
  CONSTRAINT `fk_region_airport` FOREIGN KEY (`region_id`) REFERENCES `regions`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Program indikasi pembangunan/pengembangan bandar udara (RPJMN 2025–2029)
DROP TABLE IF EXISTS `rpjmn_areas`;
DROP TABLE IF EXISTS `rpjmn_programs`;
CREATE TABLE `rpjmn_programs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `eyebrow` VARCHAR(160) NULL,
  `icon` VARCHAR(80) NULL,
  `summary` TEXT NULL,
  `focus` TEXT NULL,
  `note` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `rpjmn_areas` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `program_id` INT UNSIGNED NOT NULL,
  `region_code` VARCHAR(10) NOT NULL,
  `airports` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_rpjmn_area` (`program_id`),
  CONSTRAINT `fk_rpjmn_area` FOREIGN KEY (`program_id`) REFERENCES `rpjmn_programs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `home_video`;
CREATE TABLE `home_video` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(200) NULL,
  `video_url` VARCHAR(255) NULL,
  `caption` VARCHAR(255) NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-2',
  `icon` VARCHAR(80) NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Profil ----------
DROP TABLE IF EXISTS `profil_about`;
CREATE TABLE `profil_about` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `eyebrow` VARCHAR(160) NULL,
  `title` VARCHAR(200) NULL,
  `body` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-6',
  `icon` VARCHAR(80) NULL,
  `visi` TEXT NULL,
  `tugas_pokok` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `timeline_items`;
CREATE TABLE `timeline_items` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `year` VARCHAR(20) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `missions`;
CREATE TABLE `missions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `content` TEXT NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `value_cards`;
CREATE TABLE `value_cards` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(160) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `strategic_goals`;
CREATE TABLE `strategic_goals` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(160) NOT NULL,
  `description` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `directorate_functions`;
CREATE TABLE `directorate_functions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Organisasi ----------
DROP TABLE IF EXISTS `org_units`;
CREATE TABLE `org_units` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `unit_key` VARCHAR(40) NOT NULL UNIQUE,
  `icon` VARCHAR(80) NULL,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `chips` VARCHAR(255) NULL,
  `branch_class` ENUM('org-branch','org-branch2') NOT NULL DEFAULT 'org-branch',
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tugas & Fungsi ----------
DROP TABLE IF EXISTS `tf_overview`;
CREATE TABLE `tf_overview` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `tugas_eyebrow` VARCHAR(160) NULL,
  `tugas_title` VARCHAR(200) NULL,
  `tugas` TEXT NULL,
  `fungsi_eyebrow` VARCHAR(160) NULL,
  `fungsi_title` VARCHAR(200) NULL,
  `fungsi_intro` TEXT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tf_functions`;
CREATE TABLE `tf_functions` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `content` TEXT NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tf_units`;
CREATE TABLE `tf_units` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `number` VARCHAR(10) NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `intro` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `tf_details`;
CREATE TABLE `tf_details` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `unit_id` INT UNSIGNED NOT NULL,
  `icon` VARCHAR(80) NULL,
  `label` VARCHAR(120) NOT NULL,
  `content` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_tf_unit` (`unit_id`),
  CONSTRAINT `fk_tf_unit` FOREIGN KEY (`unit_id`) REFERENCES `tf_units`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Layanan ----------
DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `unit_pengelola` VARCHAR(160) NOT NULL DEFAULT 'Direktorat Bandar Udara',
  `name` VARCHAR(255) NOT NULL,
  `category` VARCHAR(40) NOT NULL DEFAULT 'layanan',
  `sifat` VARCHAR(120) NULL,
  `sifat_badge` VARCHAR(30) NOT NULL DEFAULT 'badge-success',
  `klasifikasi` VARCHAR(80) NULL,
  `klasifikasi_badge` VARCHAR(30) NOT NULL DEFAULT 'badge-gray',
  `icon` VARCHAR(80) NULL,
  `chip_label` VARCHAR(120) NULL,
  `description` TEXT NULL,
  `time_process` VARCHAR(120) NULL,
  `cost` VARCHAR(120) NULL,
  `validity` VARCHAR(120) NULL,
  `method` VARCHAR(120) NULL,
  `cta_label` VARCHAR(120) NULL,
  `cta_url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_requirements`;
CREATE TABLE `service_requirements` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT UNSIGNED NOT NULL,
  `content` VARCHAR(500) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_svc_req` (`service_id`),
  CONSTRAINT `fk_svc_req` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_steps`;
CREATE TABLE `service_steps` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(160) NOT NULL,
  `description` VARCHAR(500) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_svc_step` (`service_id`),
  CONSTRAINT `fk_svc_step` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_downloads`;
CREATE TABLE `service_downloads` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `service_id` INT UNSIGNED NOT NULL,
  `title` VARCHAR(200) NOT NULL,
  `meta` VARCHAR(80) NULL,
  `file` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_svc_dl` (`service_id`),
  CONSTRAINT `fk_svc_dl` FOREIGN KEY (`service_id`) REFERENCES `services`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `general_requirements`;
CREATE TABLE `general_requirements` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `content` VARCHAR(500) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `process_steps`;
CREATE TABLE `process_steps` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(160) NOT NULL,
  `description` VARCHAR(500) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `faqs`;
CREATE TABLE `faqs` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `question` VARCHAR(255) NOT NULL,
  `answer` TEXT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Regulasi ----------
DROP TABLE IF EXISTS `regulations`;
CREATE TABLE `regulations` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `number` VARCHAR(120) NOT NULL,
  `title` VARCHAR(255) NOT NULL,
  `year` SMALLINT NOT NULL,
  `category` VARCHAR(40) NOT NULL DEFAULT 'umum',
  `status` ENUM('berlaku','dicabut') NOT NULL DEFAULT 'berlaku',
  `about` TEXT NULL,
  `date_set` DATE NULL,
  `date_published` DATE NULL,
  `file` VARCHAR(255) NULL,
  `source_url` VARCHAR(500) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_reg` (`year`,`category`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `regulation_scopes`;
CREATE TABLE `regulation_scopes` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `regulation_id` INT UNSIGNED NOT NULL,
  `content` VARCHAR(500) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_reg_scope` (`regulation_id`),
  CONSTRAINT `fk_reg_scope` FOREIGN KEY (`regulation_id`) REFERENCES `regulations`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `regulation_categories`;
CREATE TABLE `regulation_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(40) NOT NULL UNIQUE,
  `label` VARCHAR(120) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Berita & pengumuman ----------
DROP TABLE IF EXISTS `news_categories`;
CREATE TABLE `news_categories` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(40) NOT NULL UNIQUE,
  `label` VARCHAR(120) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `news`;
CREATE TABLE `news` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(200) NOT NULL UNIQUE,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(40) NOT NULL DEFAULT 'kegiatan',
  `published_at` DATE NOT NULL,
  `excerpt` VARCHAR(500) NULL,
  `content` LONGTEXT NULL,
  `author` VARCHAR(160) NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-1',
  `icon` VARCHAR(80) NULL,
  `tags` VARCHAR(255) NULL,
  `is_featured` TINYINT(1) NOT NULL DEFAULT 0,
  `is_published` TINYINT(1) NOT NULL DEFAULT 1,
  `views` INT UNSIGNED NOT NULL DEFAULT 0,
  `sort` INT NOT NULL DEFAULT 0,
  KEY `idx_news` (`is_published`,`published_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `announcements`;
CREATE TABLE `announcements` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(255) NOT NULL,
  `category` VARCHAR(80) NULL,
  `published_at` DATE NOT NULL,
  `url` VARCHAR(255) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Galeri ----------
DROP TABLE IF EXISTS `gallery_albums`;
CREATE TABLE `gallery_albums` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `slug` VARCHAR(180) NOT NULL UNIQUE,
  `title` VARCHAR(200) NOT NULL,
  `description` TEXT NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-1',
  `icon` VARCHAR(80) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gallery_photos`;
CREATE TABLE `gallery_photos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `album_id` INT UNSIGNED NULL,
  `title` VARCHAR(200) NOT NULL,
  `caption` VARCHAR(255) NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-1',
  `icon` VARCHAR(80) NULL,
  `show_on_home` TINYINT(1) NOT NULL DEFAULT 0,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  KEY `idx_photo_album` (`album_id`),
  CONSTRAINT `fk_photo_album` FOREIGN KEY (`album_id`) REFERENCES `gallery_albums`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `gallery_videos`;
CREATE TABLE `gallery_videos` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `title` VARCHAR(200) NOT NULL,
  `video_url` VARCHAR(255) NULL,
  `image` VARCHAR(255) NULL,
  `art_class` VARCHAR(20) NOT NULL DEFAULT 'art-2',
  `icon` VARCHAR(80) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Kontak ----------
DROP TABLE IF EXISTS `contact_info`;
CREATE TABLE `contact_info` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `icon` VARCHAR(80) NULL,
  `label` VARCHAR(120) NOT NULL,
  `value` VARCHAR(500) NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `contact_subjects`;
CREATE TABLE `contact_subjects` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `label` VARCHAR(160) NOT NULL,
  `sort` INT NOT NULL DEFAULT 0,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Kotak masuk ----------
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

SET FOREIGN_KEY_CHECKS = 1;
