-- ============================================================
--  MIGRASI: Section "Indikasi Pembangunan/Pengembangan Bandar Udara"
--  (RPJMN 2025-2029) pada beranda + pembaruan halaman Tugas & Fungsi.
--  Aman dijalankan pada basis data yang sudah berisi data.
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-rpjmn-tugas-fungsi.sql
-- ============================================================
SET NAMES utf8mb4;
-- USE `dbu_cms`;   <- nama basis data di server sering berbeda.
--                    Pilih basis datanya saat menjalankan, contoh:
--                    mysql -u USER -p NAMA_DB < migrate-rpjmn-tugas-fungsi.sql

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


-- ---------- Program RPJMN 2025–2029 (indikasi pembangunan/pengembangan bandar udara) ----------
INSERT INTO `rpjmn_programs` (`id`,`title`,`eyebrow`,`icon`,`summary`,`focus`,`note`,`sort`) VALUES
(1,'Kawasan Pariwisata / Destinasi Pariwisata','Prioritas Nasional 3','bi-airplane-engines',
 'Indikasi pembangunan/pengembangan bandar udara pada kawasan pariwisata yang dikaitkan dengan RPJMN 2025–2029 PN 3 (pengembangan pariwisata, ekonomi kreatif, dan infrastruktur). Bandara: Kualanamu, Internasional Yogyakarta, Zainuddin Abdul Madjid, Komodo, Matahora, Perairan Misool (Raja Ampat), dan Domine Eduard Osok.',
 'Fokus mendukung DPP Borobudur–Yogyakarta–Prambanan, Danau Toba, Labuan Bajo, dan Lombok–Gili Tramena.',
 NULL,1),
(2,'Kawasan Perbatasan','Wilayah Terdepan','bi-signpost-2',
 'Indikasi pembangunan/pengembangan bandar udara di kawasan perbatasan. Bandara: Yuvai Semaring, Long Apung, Kol. Robert Atty Bessing, Kalimarau, dan Nunukan.',
 NULL,
 'Pertimbangan teknis: rencana induk bandar udara, RTT sisi udara dan sisi darat, serta kesiapan lahan.',2),
(3,'Bandar Udara Perairan Periode 2025–2029','Dukungan Kawasan Pariwisata','bi-water',
 'Indikasi pembangunan bandar udara perairan sebagai dukungan kawasan pengembangan pariwisata. Lokasi: Bandar Udara Perairan Sulawesi Selatan, pengembangan seaplane di Maluku dan Maluku Utara, serta Bandar Udara Perairan Misool Selatan.',
 NULL,
 'Belum ada readiness criteria. Lokasi masih dalam proses kelengkapan administrasi dan justifikasi luasan lahan.',3),
(4,'Kawasan Sentra Produksi Pangan / Food Estate','Fokus Wilayah Papua Selatan','bi-basket',
 'Indikasi pembangunan/pengembangan bandar udara pendukung kawasan sentra produksi pangan dengan fokus wilayah Papua Selatan. Bandara: Kimaam, Wanam, Kepi, Senggo, Bomakia, Korowai Batu, Senggeh, Manggelum, Mindiptanah, Tanah Merah, Bade, Okaba, dan Mopah.',
 NULL,
 'Pertimbangan: bandar udara UPBU Ditjen Perhubungan Udara dibandingkan bandar udara/lapangan terbang yang dikelola swasta (PT Djarmaru) atau aset pihak swasta terkait rute perintis Mopah–Wanam.',4),
(5,'Kawasan Hilirisasi','KEK & Kawasan Industri','bi-buildings',
 'Indikasi pembangunan/pengembangan bandar udara pendukung hilirisasi. Bandara: Raja Haji Abdullah (KEK Arun Lhokseumawe), Malikussaleh, Singkawang dan Pangsuma (KI Ketapang), serta Morowali (KI Morowali/Morowali Utara).',
 'Pertimbangan teknis pengembangan memperhatikan: (1) rencana induk bandar udara; (2) RTT sisi udara dan sisi darat; (3) kesiapan lahan.',
 'Telah dilakukan penandatanganan kesepakatan bersama pengembangan Bandar Udara Morowali antara Ditjen Perhubungan Udara dan PT Zhenshi Indonesia Industrial Park pada 13 November 2024. Rencana CSR sebesar Rp166.646.566.790 dengan rincian kegiatan: pekerjaan runway 300 m x 30 m; runway strip 260 m x 85 m; RESA 92,5 m x 65 m; serta pagar sisi udara sepanjang 720 meter.',5),
(6,'Pendukung Logistik (Penurunan Harga Logistik)','Jembatan Udara','bi-box-seam',
 'Indikasi pembangunan/pengembangan bandar udara pendukung logistik. Bandara: Long Apung, Yuvai Semaring, Juwata, Ilaga, Mozes Kilangin, Sinak, Elelim, Wamena, Sobaham, Tanah Merah, Nop Goliat Dekai, Mopah, dan Oksibil.',
 'Mendukung rute perintis kargo (jembatan udara) untuk menekan harga logistik di wilayah pedalaman.',
 NULL,6);

INSERT INTO `rpjmn_areas` (`program_id`,`region_code`,`airports`,`sort`) VALUES
(1,'2','Kualanamu — mendukung DPP Danau Toba',1),
(1,'3','Bandara Internasional Yogyakarta — mendukung DPP Borobudur–Yogyakarta–Prambanan',2),
(1,'4','Zainuddin Abdul Madjid (DPP Lombok–Gili Tramena), Komodo (DPP Labuan Bajo)',3),
(1,'5','Matahora, Wakatobi',4),
(1,'9','Perairan Misool (Raja Ampat), Domine Eduard Osok (Sorong)',5),
(2,'7','Yuvai Semaring, Long Apung, Kol. Robert Atty Bessing, Kalimarau, Nunukan',1),
(3,'5','Bandar Udara Perairan Sulawesi Selatan',1),
(3,'8','Pengembangan seaplane di Maluku dan Maluku Utara',2),
(3,'9','Bandar Udara Perairan Misool Selatan (Raja Ampat)',3),
(4,'10','Kimaam, Wanam, Kepi, Senggo, Bomakia, Korowai Batu, Senggeh, Manggelum, Mindiptanah, Tanah Merah, Bade, Okaba, Mopah',1),
(5,'1','Singkawang, Pangsuma (KI Ketapang)',1),
(5,'2','Raja Haji Abdullah (KEK Arun Lhokseumawe), Malikussaleh',2),
(5,'5','Morowali (KI Morowali / Morowali Utara)',3),
(6,'7','Long Apung, Yuvai Semaring, Juwata',1),
(6,'9','Ilaga, Mozes Kilangin, Sinak',2),
(6,'10','Elelim, Wamena, Sobaham, Tanah Merah, Nop Goliat Dekai, Mopah, Oksibil',3);

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


-- ---------- Tugas & Fungsi ----------
INSERT INTO `tf_overview` (`tugas_eyebrow`,`tugas_title`,`tugas`,`fungsi_eyebrow`,`fungsi_title`,`fungsi_intro`) VALUES
('Tugas','Tugas Direktorat Bandar Udara',
 'Direktorat Bandar Udara mempunyai tugas melaksanakan perumusan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bandar udara.',
 'Fungsi','Fungsi Direktorat Bandar Udara',
 'Dalam melaksanakan tugas tersebut, Direktorat Bandar Udara menyelenggarakan fungsi sebagai berikut.');

INSERT INTO `tf_functions` (`icon`,`content`,`sort`) VALUES
('bi-pencil-square','Penyiapan perumusan kebijakan di bidang standardisasi keselamatan bandar udara, tatanan kebandarudaraan dan lingkungan, prasarana bandar udara, peralatan dan pelayanan darurat bandar udara serta sistem penyelenggaraan dan pengusahaan bandar udara;',1),
('bi-play-circle','Penyiapan pelaksanaan kebijakan di bidang standardisasi keselamatan bandar udara, tatanan kebandarudaraan dan lingkungan, prasarana bandar udara, peralatan dan pelayanan darurat bandar udara serta sistem penyelenggaraan dan pengusahaan bandar udara;',2),
('bi-rulers','Penyiapan penyusunan, norma, standar, prosedur, dan kriteria di bidang standardisasi keselamatan bandar udara, tatanan kebandarudaraan dan lingkungan, prasarana bandar udara, peralatan dan pelayanan darurat bandar udara serta sistem penyelenggaraan dan pengusahaan bandar udara;',3),
('bi-mortarboard','Penyiapan pemberian bimbingan teknis dan supervisi di bidang standardisasi keselamatan bandar udara, tatanan kebandarudaraan dan lingkungan, prasarana bandar udara, peralatan dan pelayanan darurat bandar udara serta sistem penyelenggaraan dan pengusahaan bandar udara;',4),
('bi-clipboard-data','Penyiapan pelaksanaan pemantauan, analisis, evaluasi dan pelaporan di bidang standardisasi keselamatan bandar udara, tatanan kebandarudaraan dan lingkungan, prasarana bandar udara, peralatan dan pelayanan darurat bandar udara serta sistem penyelenggaraan dan pengusahaan bandar udara; dan',5),
('bi-archive','Penyiapan pelaksanaan urusan tata usaha, keuangan, sumber daya manusia, pengelolaan data dan informasi, dan rumah tangga direktorat.',6);

-- ---------- Judul seksi baru ----------
INSERT INTO `sections` (`page`,`section_key`,`label`,`eyebrow`,`title`,`subtitle`,`body`,`sort`) VALUES
('home','rpjmn','Beranda — Program RPJMN','RPJMN 2025–2029','Indikasi Pembangunan & Pengembangan Bandar Udara','Arahkan kursor ke wilayah yang menyala pada peta untuk melihat bandar udara yang termasuk dalam setiap bagian program.','Seluruh data pada bagian ini bersumber dari Peraturan Presiden Nomor 12 Tahun 2025 Lampiran IV RPJMN 2025–2029.',3),
('tugas-fungsi','tugas','Tugas & Fungsi — Tugas','Tugas','Tugas Direktorat Bandar Udara',NULL,NULL,1),
('tugas-fungsi','fungsi','Tugas & Fungsi — Fungsi','Fungsi','Fungsi Direktorat Bandar Udara',NULL,NULL,2)
ON DUPLICATE KEY UPDATE `label`=VALUES(`label`), `eyebrow`=VALUES(`eyebrow`), `title`=VALUES(`title`),
                        `subtitle`=VALUES(`subtitle`), `body`=VALUES(`body`), `sort`=VALUES(`sort`);

-- Geser urutan seksi beranda yang berada setelah peta wilayah
UPDATE `sections` SET `sort` = 4 WHERE `page` = 'home' AND `section_key` = 'quick';
UPDATE `sections` SET `sort` = 5 WHERE `page` = 'home' AND `section_key` = 'news';
UPDATE `sections` SET `sort` = 6 WHERE `page` = 'home' AND `section_key` = 'services';
UPDATE `sections` SET `sort` = 7 WHERE `page` = 'home' AND `section_key` = 'gallery';
UPDATE `sections` SET `sort` = 8 WHERE `page` = 'home' AND `section_key` = 'partners';
UPDATE `sections` SET `sort` = 9 WHERE `page` = 'home' AND `section_key` = 'newsletter';

-- ---------- Meta halaman Tugas & Fungsi ----------
UPDATE `page_meta` SET
  `meta_description` = 'Tugas dan fungsi Direktorat Bandar Udara dalam perumusan kebijakan, penyusunan NSPK, bimbingan teknis, serta pemantauan dan evaluasi di bandar udara.',
  `heading`          = 'Tugas & Fungsi Direktorat Bandar Udara',
  `subtitle`         = 'Tugas pokok Direktorat Bandar Udara beserta enam fungsi yang diselenggarakan dalam pelaksanaannya.'
 WHERE `page_key` = 'tugas-fungsi';

