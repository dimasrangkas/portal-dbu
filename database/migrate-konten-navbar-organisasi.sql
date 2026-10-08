-- ============================================================
--  MIGRASI: Isi navbar/mega menu, fungsi subdirektorat, dan
--           kaitan layanan -> subdirektorat
--
--  Konten di bawah ini sebelumnya hanya ada di basis data pengembangan
--  dan tidak pernah tercatat di seed.sql, sehingga pemasangan baru
--  maupun server yang di-seed dari seed.sql kehilangan seluruh struktur
--  mega menu, daftar fungsi subdirektorat, dan tugas tiap subdirektorat.
--
--  PRASYARAT: jalankan database/migrate-sinkron-skema.sql lebih dulu.
--
--  Aman dijalankan berulang (DELETE lalu INSERT dengan id tetap).
--
--  YANG DITIMPA:
--    - `menu_items`             -> SELURUH isi tabel diganti (31 baris)
--    - `org_unit_functions`     -> SELURUH isi tabel diganti (25 baris)
--    - `org_units`.`tugas`      -> 6 baris, dicocokkan lewat unit_key
--    - `services`.`org_unit_id` -> 29 baris, dicocokkan lewat slug
--  Perubahan pada keempat hal itu yang dibuat lewat CMS di server akan
--  hilang. Kolom dan tabel lain tidak disentuh.
--
--  Jalankan: mysql -u USER -p NAMA_DB < database/migrate-konten-navbar-organisasi.sql
-- ============================================================
SET NAMES utf8mb4;

-- ---------- 1. Navbar & mega menu ----------
DELETE FROM `menu_items`;
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (1,'navbar',NULL,'Beranda',NULL,NULL,'',NULL,'link','index.php',0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (2,'navbar',NULL,'Profil',NULL,NULL,'pages/profil',NULL,'link','profil.php',0,2,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (3,'navbar',2,'Organisasi','Struktur & Tugas','Bagan unit kerja, tugas, fungsi, dan layanannya','pages/organisasi','bi-diagram-3','link',NULL,0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (6,'navbar',NULL,'Informasi Publik',NULL,NULL,'pages/informasi-publik','bi-database','link','informasi-publik.php informasi-publik-detail.php',0,3,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (7,'navbar',NULL,'Regulasi',NULL,NULL,'pages/regulasi',NULL,'link','regulasi.php regulasi-detail.php',0,4,0);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (10,'navbar',NULL,'Kontak',NULL,NULL,'pages/kontak',NULL,'link','kontak.php',0,5,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (11,'navbar',NULL,'Portal AEMS',NULL,NULL,'https://dbu.edifly-dev.com/kemhub-ems-dev/',NULL,'link',NULL,1,6,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (12,'navbar',2,'Tentang Direktorat','Tentang Kami','Sekilas Direktorat Bandar Udara','pages/profil#tentang','bi-info-square','link',NULL,0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (13,'navbar',2,'Sejarah','Tentang Kami','Perjalanan dari masa ke masa','pages/profil#sejarah','bi-clock-history','link',NULL,0,2,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (14,'navbar',2,'Visi & Misi','Tentang Kami','Arah dan sasaran organisasi','pages/profil#visi-misi','bi-bullseye','link',NULL,0,3,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (15,'navbar',2,'Nilai Organisasi','Tentang Kami','Nilai yang kami junjung','pages/profil#nilai','bi-gem','link',NULL,0,4,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (20,'footer_quick',NULL,'Profil Direktorat',NULL,NULL,'pages/profil',NULL,'link',NULL,0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (21,'footer_quick',NULL,'Struktur Organisasi',NULL,NULL,'pages/organisasi',NULL,'link',NULL,0,2,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (22,'footer_quick',NULL,'Regulasi',NULL,NULL,'pages/regulasi',NULL,'link',NULL,0,3,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (23,'footer_quick',NULL,'Berita Terkini',NULL,NULL,'pages/berita',NULL,'link',NULL,0,4,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (24,'footer_quick',NULL,'Galeri',NULL,NULL,'pages/galeri',NULL,'link',NULL,0,5,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (30,'footer_service',NULL,'Sertifikasi Bandar Udara',NULL,NULL,'pages/layanan-detail?slug=sertifikasi-bandar-udara',NULL,'link',NULL,0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (31,'footer_service',NULL,'Standardisasi Keselamatan',NULL,NULL,'pages/layanan',NULL,'link',NULL,0,2,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (32,'footer_service',NULL,'Informasi Publik',NULL,NULL,'pages/informasi-publik',NULL,'link',NULL,0,3,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (33,'footer_service',NULL,'Pengaduan',NULL,NULL,'pages/kontak',NULL,'link',NULL,0,4,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (34,'footer_service',NULL,'FAQ',NULL,NULL,'pages/layanan#faq',NULL,'link',NULL,0,5,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (35,'navbar',6,'Daftar Bandar Udara','Data & Informasi','Peta dan tabel seluruh bandar udara di Indonesia','pages/informasi-publik','bi-search','link',NULL,0,1,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (36,'navbar',6,'Berita','Berita & Media','Kabar dan kegiatan terkini','pages/berita','bi-newspaper','link',NULL,0,2,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (37,'navbar',6,'Galeri','Berita & Media','Dokumentasi foto dan video','pages/galeri','bi-images','link',NULL,0,3,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (38,'navbar',6,'Regulasi','Berita & Media','Peraturan kebandarudaraan','pages/regulasi','bi-journal-text','link',NULL,0,4,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (39,'navbar',6,'Standardisasi Keselamatan Bandar Udara','Subdirektorat',NULL,'pages/informasi-publik-subdit?unit=std','bi-shield-check','link',NULL,0,5,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (40,'navbar',6,'Tatanan Kebandarudaraan & Lingkungan','Subdirektorat',NULL,'pages/informasi-publik-subdit?unit=tatanan','bi-tree','link',NULL,0,6,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (41,'navbar',6,'Prasarana Bandar Udara','Subdirektorat',NULL,'pages/informasi-publik-subdit?unit=prasarana','bi-signpost-split','link',NULL,0,7,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (42,'navbar',6,'Peralatan & Pelayanan Darurat','Subdirektorat',NULL,'pages/informasi-publik-subdit?unit=darurat','bi-life-preserver','link',NULL,0,8,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (43,'navbar',6,'Sistem Penyelenggaraan & Pengusahaan','Subdirektorat',NULL,'pages/informasi-publik-subdit?unit=sistem','bi-diagram-3','link',NULL,0,9,1);
INSERT INTO `menu_items` (`id`, `location`, `parent_id`, `label`, `group_label`, `description`, `url`, `icon`, `style`, `match_pages`, `is_external`, `sort`, `is_active`) VALUES (44,'navbar',6,'Dokumen Publik','Data & Informasi','Laporan tahunan, kinerja, dan rencana strategis','pages/dokumen-publik','bi-file-earmark-text','link',NULL,0,2,1);

-- ---------- 2. Fungsi per subdirektorat ----------
DELETE FROM `org_unit_functions`;
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (1,4,'Penyiapan perumusan kebijakan',1);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (2,4,'Penyiapan pelaksanaan kebijakan',2);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (3,4,'Penyiapan penyusunan norma, standar, prosedur, dan kriteria',3);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (4,4,'Penyiapan pemberian bimbingan teknis dan supervisi',4);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (5,4,'Penyiapan pelaksanaan pemantauan, analisis, evaluasi, dan pelaporan',5);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (6,3,'Penyiapan perumusan kebijakan',1);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (7,3,'Penyiapan pelaksanaan kebijakan',2);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (8,3,'Penyiapan penyusunan norma, standar, prosedur, dan kriteria',3);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (9,3,'Penyiapan pemberian bimbingan teknis dan supervisi',4);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (10,3,'Penyiapan pelaksanaan pemantauan, analisis, evaluasi, dan pelaporan',5);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (11,6,'Penyiapan perumusan kebijakan',1);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (12,6,'Penyiapan pelaksanaan kebijakan',2);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (13,6,'Penyiapan penyusunan norma, standar, prosedur, dan kriteria',3);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (14,6,'Penyiapan pemberian bimbingan teknis dan supervisi',4);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (15,6,'Penyiapan pelaksanaan pemantauan, analisis, evaluasi, dan pelaporan',5);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (16,1,'Penyiapan perumusan kebijakan',1);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (17,1,'Penyiapan pelaksanaan kebijakan',2);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (18,1,'Penyiapan penyusunan norma, standar, prosedur, dan kriteria',3);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (19,1,'Penyiapan pemberian bimbingan teknis dan supervisi',4);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (20,1,'Penyiapan pelaksanaan pemantauan, analisis, evaluasi, dan pelaporan',5);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (21,2,'Penyiapan perumusan kebijakan',1);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (22,2,'Penyiapan pelaksanaan kebijakan',2);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (23,2,'Penyiapan penyusunan norma, standar, prosedur, dan kriteria',3);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (24,2,'Penyiapan pemberian bimbingan teknis dan supervisi',4);
INSERT INTO `org_unit_functions` (`id`, `unit_id`, `content`, `sort`) VALUES (25,2,'Penyiapan pelaksanaan pemantauan, analisis, evaluasi, dan pelaporan',5);

-- ---------- 3. Tugas tiap subdirektorat ----------
UPDATE `org_units` SET `tugas` = 'Melaksanakan penyiapan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang standardisasi keselamatan bandar udara.' WHERE `unit_key` = 'std';
UPDATE `org_units` SET `tugas` = 'Melaksanakan penyiapan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang tatanan kebandarudaraan & lingkungan.' WHERE `unit_key` = 'tatanan';
UPDATE `org_units` SET `tugas` = 'Melaksanakan penyiapan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang prasarana bandar udara.' WHERE `unit_key` = 'prasarana';
UPDATE `org_units` SET `tugas` = 'Melaksanakan penyiapan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang peralatan & pelayanan darurat.' WHERE `unit_key` = 'darurat';
UPDATE `org_units` SET `tugas` = 'Melaksanakan urusan tata usaha, keuangan, sumber daya manusia, pengelolaan data dan informasi, serta rumah tangga direktorat.' WHERE `unit_key` = 'tu';
UPDATE `org_units` SET `tugas` = 'Melaksanakan penyiapan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang sistem penyelenggaraan & pengusahaan.' WHERE `unit_key` = 'sistem';

-- ---------- 4. Kaitan layanan -> subdirektorat ----------
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'sertifikasi-bandar-udara';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'sertifikasi-lembaga-pendidikan';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'sertifikasi-lembaga-inspeksi';
UPDATE `services` SET `org_unit_id` = 6 WHERE `slug` = 'pelayanan-jasa-kebandarudaraan';
UPDATE `services` SET `org_unit_id` = 6 WHERE `slug` = 'pelayanan-teknis-penanganan-pesawat';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'pengelolaan-emisi';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'pengawasan-lingkungan-hidup';
UPDATE `services` SET `org_unit_id` = 4 WHERE `slug` = 'penanggulangan-darurat-bencana';
UPDATE `services` SET `org_unit_id` = 6 WHERE `slug` = 'pengelolaan-kerjasama-badan-usaha';
UPDATE `services` SET `org_unit_id` = 4 WHERE `slug` = 'verifikasi-pkp-pk';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'bimtek-bandar-udara';
UPDATE `services` SET `org_unit_id` = 6 WHERE `slug` = 'pengawasan-level-of-service';
UPDATE `services` SET `org_unit_id` = 4 WHERE `slug` = 'pengesahan-aep';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'perumusan-kebijakan';
UPDATE `services` SET `org_unit_id` = 3 WHERE `slug` = 'pengawasan-pemeliharaan-prasarana';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'audit-keselamatan';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'sanksi-administratif';
UPDATE `services` SET `org_unit_id` = 1 WHERE `slug` = 'pengelolaan-pengawasan-bandara';
UPDATE `services` SET `org_unit_id` = 4 WHERE `slug` = 'pembangunan-peralatan-darurat';
UPDATE `services` SET `org_unit_id` = 3 WHERE `slug` = 'pengawasan-pembangunan-fasilitas';
UPDATE `services` SET `org_unit_id` = 3 WHERE `slug` = 'program-fasilitas-peralatan';
UPDATE `services` SET `org_unit_id` = 3 WHERE `slug` = 'pembangunan-prasarana';
UPDATE `services` SET `org_unit_id` = 3 WHERE `slug` = 'verifikasi-fasilitas';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'program-bandara-heliport-waterbase';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'penetapan-lokasi';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'penetapan-rencana-induk';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'peninjauan-tatanan';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'penetapan-bandara-internasional';
UPDATE `services` SET `org_unit_id` = 2 WHERE `slug` = 'penetapan-status-khusus-umum';

-- ---------- Verifikasi ----------
SELECT 'menu_items' AS tabel, COUNT(*) AS ada, 31 AS harus FROM `menu_items`
UNION ALL SELECT 'org_unit_functions', COUNT(*), 25 FROM `org_unit_functions`
UNION ALL SELECT 'org_units.tugas',    COUNT(*),  6 FROM `org_units` WHERE `tugas` IS NOT NULL AND `tugas` <> ''
UNION ALL SELECT 'services.org_unit_id', COUNT(*), 29 FROM `services` WHERE `org_unit_id` IS NOT NULL;
