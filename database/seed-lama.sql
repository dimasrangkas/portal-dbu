-- ============================================================
--  DIREKTORAT BANDAR UDARA — DATA AWAL (dari situs statis)
-- ============================================================
SET NAMES utf8mb4;
USE `dbu_cms`;

-- ---------- Pengguna admin (password: admin123) ----------
INSERT INTO `users` (`name`,`username`,`email`,`password_hash`,`role`) VALUES
('Administrator','admin','bandarudara@dephub.go.id','$2y$10$KipmWyYyrNi0Wbp35hX7euubCWsboBnSa.Kji.ap/kXFc2bK1cBMe','admin');

-- ---------- Pengaturan ----------
INSERT INTO `settings` (`group_key`,`setting_key`,`label`,`value`,`type`,`sort`) VALUES
('umum','site_name','Nama Situs','Direktorat Bandar Udara','text',1),
('umum','site_title','Judul Halaman (SEO)','Direktorat Bandar Udara — Ditjen Perhubungan Udara, Kementerian Perhubungan RI','text',2),
('umum','site_description','Deskripsi Situs (SEO)','Situs resmi Direktorat Bandar Udara — perumusan kebijakan, standar, dan pengawasan penyelenggaraan bandar udara di Indonesia.','textarea',3),
('umum','brand_kop','Kop Header','Kementerian Perhubungan Republik Indonesia','text',4),
('umum','brand_name','Nama Brand','Direktorat','text',5),
('umum','brand_name_accent','Nama Brand (Tebal)','Bandar Udara','text',6),
('umum','logo_primary','Logo Kementerian','assets/images/kemenhub.png','image',7),
('umum','logo_secondary','Logo Direktorat','assets/images/logo-dbu.png','image',8),
('kontak','contact_address','Alamat','Jl. Medan Merdeka Barat No. 8, Jakarta Pusat 10110','text',1),
('kontak','contact_city','Lokasi Topbar','Jakarta, Indonesia','text',2),
('kontak','contact_phone','Telepon','(021) 350-0000','text',3),
('kontak','contact_email','Email','bandarudara@dephub.go.id','email',4),
('kontak','contact_hours','Jam Layanan','Senin–Jumat, 08.00–16.00 WIB','text',5),
('kontak','contact_map','Embed Peta (URL)','https://maps.google.com/maps?q=Kementerian%20Perhubungan%20Republik%20Indonesia%2C%20Jakarta&t=&z=15&ie=UTF8&iwloc=&output=embed','url',6),
('kontak','whatsapp_url','Tautan WhatsApp','#','url',7),
('sosial','social_facebook','Facebook','#','url',1),
('sosial','social_instagram','Instagram','#','url',2),
('sosial','social_twitter','Twitter / X','#','url',3),
('sosial','social_youtube','YouTube','#','url',4),
('footer','footer_description','Deskripsi Footer','Direktorat Jenderal Perhubungan Udara, Kementerian Perhubungan Republik Indonesia. Mewujudkan penyelenggaraan bandar udara yang profesional, aman, dan berkelanjutan.','textarea',1),
('footer','footer_copyright','Teks Hak Cipta','Direktorat Bandar Udara — Direktorat Jenderal Perhubungan Udara. Hak cipta dilindungi.','text',2),
('footer','disclaimer_text','Teks Disclaimer','Disclaimer: Seluruh data, gambar, statistik, dan informasi pada website ini merupakan contoh (dummy data) untuk kebutuhan desain dan pengembangan sistem.','textarea',3),
('footer','disclaimer_active','Tampilkan Disclaimer (1/0)','1','text',4);

-- ---------- Menu ----------
INSERT INTO `menu_items` (`id`,`location`,`parent_id`,`label`,`url`,`match_pages`,`is_external`,`sort`) VALUES
(1,'navbar',NULL,'Beranda','index.php','index.php',0,1),
(2,'navbar',NULL,'Profil','pages/profil.php','profil.php',0,2),
(3,'navbar',NULL,'Organisasi','pages/organisasi.php','organisasi.php',0,3),
(4,'navbar',NULL,'Tugas & Fungsi','pages/tugas-fungsi.php','tugas-fungsi.php',0,4),
(5,'navbar',NULL,'Layanan','pages/layanan.php','layanan.php layanan-detail.php',0,5),
(6,'navbar',NULL,'Informasi Publik','pages/informasi-publik.php','informasi-publik.php informasi-publik-detail.php',0,6),
(7,'navbar',NULL,'Regulasi','pages/regulasi.php','regulasi.php regulasi-detail.php',0,7),
(8,'navbar',NULL,'Berita','pages/berita.php','berita.php berita-detail.php',0,8),
(9,'navbar',NULL,'Galeri','pages/galeri.php','galeri.php galeri-detail.php',0,9),
(10,'navbar',NULL,'Kontak','pages/kontak.php','kontak.php',0,10),
(11,'navbar',NULL,'Portal AEMS','https://dbu.edifly-dev.com/kemhub-ems-dev/',NULL,1,11),
(12,'navbar',2,'Tentang Direktorat','pages/profil.php#tentang',NULL,0,1),
(13,'navbar',2,'Sejarah','pages/profil.php#sejarah',NULL,0,2),
(14,'navbar',2,'Visi & Misi','pages/profil.php#visi-misi',NULL,0,3),
(15,'navbar',2,'Nilai Organisasi','pages/profil.php#nilai',NULL,0,4),
(16,'navbar',5,'Layanan Publik','pages/layanan.php',NULL,0,1),
(17,'navbar',5,'Layanan Daring','pages/layanan.php#online',NULL,0,2),
(18,'navbar',5,'FAQ','pages/layanan.php#faq',NULL,0,3),
(20,'footer_quick',NULL,'Profil Direktorat','pages/profil.php',NULL,0,1),
(21,'footer_quick',NULL,'Struktur Organisasi','pages/organisasi.php',NULL,0,2),
(22,'footer_quick',NULL,'Regulasi','pages/regulasi.php',NULL,0,3),
(23,'footer_quick',NULL,'Berita Terkini','pages/berita.php',NULL,0,4),
(24,'footer_quick',NULL,'Galeri','pages/galeri.php',NULL,0,5),
(30,'footer_service',NULL,'Sertifikasi Bandar Udara','pages/layanan-detail.php?slug=sertifikasi-bandar-udara',NULL,0,1),
(31,'footer_service',NULL,'Standardisasi Keselamatan','pages/layanan.php',NULL,0,2),
(32,'footer_service',NULL,'Informasi Publik','pages/informasi-publik.php',NULL,0,3),
(33,'footer_service',NULL,'Pengaduan','pages/kontak.php',NULL,0,4),
(34,'footer_service',NULL,'FAQ','pages/layanan.php#faq',NULL,0,5);

-- ---------- Meta halaman ----------
INSERT INTO `page_meta` (`page_key`,`page_label`,`meta_title`,`meta_description`,`eyebrow`,`heading`,`subtitle`,`breadcrumb`) VALUES
('home','Beranda','Direktorat Bandar Udara — Ditjen Perhubungan Udara, Kementerian Perhubungan RI','Situs resmi Direktorat Bandar Udara — perumusan kebijakan, standar, dan pengawasan penyelenggaraan bandar udara di Indonesia.',NULL,NULL,NULL,'Beranda'),
('profil','Profil','Profil Direktorat — Direktorat Bandar Udara','Profil, sejarah, visi misi, nilai organisasi, tugas dan fungsi Direktorat Bandar Udara.','Profil Direktorat','Profil Direktorat Bandar Udara','Sejarah, visi misi, nilai organisasi, tugas pokok, dan fungsi Direktorat Bandar Udara.','Profil'),
('organisasi','Organisasi','Struktur Organisasi — Direktorat Bandar Udara','Struktur organisasi Direktorat Bandar Udara: Direktur dan lima Subdirektorat teknis.','Struktur Organisasi','Struktur Organisasi Direktorat Bandar Udara','Direktorat Bandar Udara dipimpin oleh seorang Direktur yang membawahi lima Subdirektorat teknis dan Jabatan Fungsional & Jabatan Pelaksana.','Organisasi'),
('tugas-fungsi','Tugas & Fungsi','Tugas & Fungsi — Direktorat Bandar Udara','Tugas dan fungsi Direktorat Bandar Udara dalam perumusan kebijakan, penyusunan NSPK, bimbingan teknis, serta pemantauan dan evaluasi di bandar udara.','Tugas & Fungsi','Tugas & Fungsi Direktorat Bandar Udara','Tugas pokok Direktorat Bandar Udara beserta enam fungsi yang diselenggarakan dalam pelaksanaannya.','Tugas & Fungsi'),
('layanan','Layanan','Layanan — Direktorat Bandar Udara','Layanan publik Direktorat Bandar Udara: sertifikasi, standar keselamatan, prasarana, dan layanan daring lainnya.','Layanan Publik','Layanan Direktorat Bandar Udara','Layanan sertifikasi, standardisasi, dan perizinan kebandarudaraan yang dapat diajukan secara daring.','Layanan'),
('regulasi','Regulasi','Regulasi — Direktorat Bandar Udara','Daftar regulasi kebandarudaraan yang mengacu pada JDIH Kementerian Perhubungan — dapat dicari dan difilter berdasarkan tahun, kategori, dan status.','Regulasi','Regulasi Kebandarudaraan','Daftar peraturan bidang kebandarudaraan yang mengacu pada JDIH Kementerian Perhubungan. Klik ikon tautan untuk membuka dokumen resminya.','Regulasi'),
('berita','Berita','Berita — Direktorat Bandar Udara','Arsip berita dan kegiatan Direktorat Bandar Udara.','Berita','Berita Direktorat Bandar Udara','Kabar terbaru seputar kegiatan, sertifikasi, regulasi, dan kerja sama Direktorat Bandar Udara.','Berita'),
('galeri','Galeri','Galeri — Direktorat Bandar Udara','Galeri foto, video, dan album dokumentasi kegiatan Direktorat Bandar Udara.','Galeri','Galeri Direktorat Bandar Udara','Dokumentasi foto, video, dan album kegiatan Direktorat Bandar Udara.','Galeri'),
('kontak','Kontak','Kontak — Direktorat Bandar Udara','Hubungi Direktorat Bandar Udara: alamat kantor, telepon, email, jam layanan, dan formulir kontak.','Kontak','Hubungi Direktorat Bandar Udara','Kami siap membantu pertanyaan, layanan, dan pengaduan terkait kebandarudaraan.','Kontak');

-- ---------- Judul seksi ----------
INSERT INTO `sections` (`page`,`section_key`,`label`,`eyebrow`,`title`,`subtitle`,`sort`) VALUES
('home','about','Beranda — Tentang Kami','Tentang Kami','Mengenal Direktorat Bandar Udara','Direktorat Bandar Udara adalah unit kerja di bawah Direktorat Jenderal Perhubungan Udara yang melaksanakan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, bimbingan teknis, supervisi, serta pemantauan dan evaluasi di bidang kebandarudaraan.',1),
('home','peta','Beranda — Peta Wilayah','Wilayah Kerja','Peta Otoritas Bandar Udara Wilayah I–X','Arahkan kursor atau ketuk setiap wilayah pada peta untuk melihat kantor pusat dan cakupan provinsi Otoritas Bandar Udara di seluruh Indonesia.',2),
('home','rpjmn','Beranda — Program RPJMN','RPJMN 2025–2029','Indikasi Pembangunan & Pengembangan Bandar Udara','Arahkan kursor ke wilayah yang menyala pada peta untuk melihat bandar udara yang termasuk dalam setiap bagian program.',3),
('home','quick','Beranda — Akses Cepat','Akses Cepat','Layanan & Informasi Populer',NULL,4),
('home','news','Beranda — Berita','Berita & Pengumuman','Kabar Terbaru Direktorat',NULL,5),
('home','services','Beranda — Layanan Unggulan','Layanan Unggulan','Layanan Utama Direktorat Bandar Udara',NULL,6),
('home','gallery','Beranda — Galeri','Galeri','Dokumentasi Kegiatan',NULL,7),
('home','partners','Beranda — Mitra Kerja','Mitra Kerja','Bersinergi dengan Pemangku Kepentingan Penerbangan',NULL,8),
('home','newsletter','Beranda — Newsletter',NULL,'Dapatkan Info Terbaru','Berlangganan pembaruan regulasi, pengumuman, dan berita Direktorat Bandar Udara.',9),
('profil','sejarah','Profil — Sejarah','Sejarah','Perjalanan Direktorat Bandar Udara',NULL,1),
('profil','visi','Profil — Visi','Visi','Visi Direktorat',NULL,2),
('profil','misi','Profil — Misi','Misi','Misi Direktorat',NULL,3),
('profil','nilai','Profil — Nilai Organisasi','Nilai Organisasi','Nilai-Nilai yang Kami Junjung',NULL,4),
('profil','sasaran','Profil — Sasaran Strategis','Sasaran Strategis','Arah Pembangunan Kebandarudaraan',NULL,5),
('profil','tugas-pokok','Profil — Tugas Pokok','Tugas Pokok','Tugas Pokok Direktorat Bandar Udara',NULL,6),
('profil','fungsi','Profil — Fungsi','Fungsi','Fungsi Direktorat Bandar Udara',NULL,7),
('organisasi','intro','Organisasi — Pengantar',NULL,NULL,'Klik salah satu unit kerja di bawah untuk melihat deskripsi tugasnya.',1),
('organisasi','top','Organisasi — Node Puncak',NULL,'Direktur Bandar Udara','Direktorat Bandar Udara',2),
('layanan','daftar','Layanan — Daftar Layanan','Daftar Layanan','Daftar Layanan Direktorat Bandar Udara','Seluruh layanan yang diselenggarakan oleh Direktorat Bandar Udara beserta sifat dan klasifikasinya.',1),
('layanan','online','Layanan — Layanan Daring','Layanan Daring','Ajukan Layanan Secara Online','Seluruh layanan dapat diajukan melalui sistem informasi layanan Direktorat Bandar Udara tanpa perlu datang langsung.',2),
('layanan','alur','Layanan — Alur Proses','Alur Proses','Proses Layanan',NULL,3),
('layanan','faq','Layanan — FAQ','FAQ','Pertanyaan yang Sering Diajukan',NULL,4),
('layanan','cta','Layanan — Kartu Ajakan',NULL,'Siap Mengajukan Layanan?','Lengkapi persyaratan di atas lalu ajukan permohonan melalui sistem layanan digital kami.',5),
('tugas-fungsi','tugas','Tugas & Fungsi — Tugas','Tugas','Tugas Direktorat Bandar Udara',NULL,1),
('tugas-fungsi','fungsi','Tugas & Fungsi — Fungsi','Fungsi','Fungsi Direktorat Bandar Udara',NULL,2),
('galeri','tabs','Galeri — Tab',NULL,NULL,NULL,1);

UPDATE `sections`
   SET `body` = 'Seluruh data pada bagian ini bersumber dari Peraturan Presiden Nomor 12 Tahun 2025 Lampiran IV RPJMN 2025–2029.'
 WHERE `page` = 'home' AND `section_key` = 'rpjmn';

-- ---------- Hero ----------
INSERT INTO `hero_slides` (`eyebrow`,`title`,`tagline`,`art_class`,`icon`,`btn1_label`,`btn1_url`,`btn2_label`,`btn2_url`,`sort`) VALUES
('Landasan Pacu & Fasilitas Sisi Udara','Direktorat Bandar Udara','Profesional, Aman, dan Berkelanjutan dalam Pengembangan Bandar Udara — "Professional, Safe, Sustainable Airport Development"','art-1','bi-airplane-engines','Profil Direktorat','pages/profil.php','Layanan Digital','pages/layanan.php',1),
('Terminal & Prasarana Bandar Udara','Membangun Konektivitas Udara Nasional','Mendukung penyelenggaraan terminal dan prasarana bandar udara yang modern, inklusif, dan berstandar internasional di seluruh Indonesia.','art-2','bi-building','Profil Direktorat','pages/profil.php','Layanan Digital','pages/layanan.php',2),
('Pengawasan Lalu Lintas & Keselamatan','Keselamatan Udara adalah Prioritas Utama','Pengawasan operasional dan standardisasi keselamatan bandar udara dilakukan secara berkelanjutan bersama seluruh mitra penerbangan.','art-3','bi-broadcast-pin','Profil Direktorat','pages/profil.php','Layanan Digital','pages/layanan.php',3),
('Peralatan & Pelayanan Darurat','Siaga Darurat, Setiap Saat','Kesiapan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK) di setiap bandar udara di seluruh Indonesia.','art-5','bi-life-preserver','Profil Direktorat','pages/profil.php','Layanan Digital','pages/layanan.php',4);

-- ---------- Statistik ----------
INSERT INTO `stats` (`label`,`value`,`suffix`,`sort`) VALUES
('Bandar Udara',300,'+',1),
('Subdirektorat',5,NULL,2),
('Provinsi',34,NULL,3),
('Regulasi Aktif',100,'+',4),
('Sertifikasi Terbit',1000,'+',5);

-- ---------- Sambutan direktur ----------
INSERT INTO `director_message` (`eyebrow`,`title`,`body`,`director_name`,`director_position`,`art_class`,`icon`,`btn_label`,`btn_url`) VALUES
('Sambutan Direktur','Sambutan Direktur Bandar Udara','Selamat datang di laman resmi Direktorat Bandar Udara. Kami berkomitmen mewujudkan penyelenggaraan bandar udara yang profesional, aman, dan berkelanjutan melalui perumusan kebijakan, penyusunan standar, serta pengawasan yang konsisten di seluruh wilayah Indonesia. Transformasi digital layanan publik terus kami dorong agar masyarakat dan pemangku kepentingan mendapatkan kemudahan akses informasi dan layanan. Terima kasih atas kepercayaan dan dukungan seluruh mitra kerja dalam membangun konektivitas udara nasional.','Budhi Kurniawan Kresna, S.T., M.M.Tr.','Direktur Bandar Udara','art-2','bi-person-badge','Selengkapnya Tentang Direktorat','pages/profil.php');

-- ---------- Kartu tentang (beranda) ----------
INSERT INTO `about_cards` (`icon`,`title`,`description`,`url`,`sort`) VALUES
('bi-bullseye','Tugas Pokok','Perumusan & pelaksanaan kebijakan bidang bandar udara.','pages/tugas-fungsi.php',1),
('bi-gear-wide-connected','Fungsi','Standardisasi, bimbingan teknis, dan pengawasan.','pages/tugas-fungsi.php',2),
('bi-diagram-3','Struktur Organisasi','Direktur dan 5 Subdirektorat teknis.','pages/organisasi.php',3),
('bi-journal-text','Regulasi','Peraturan & standar kebandarudaraan.','pages/regulasi.php',4),
('bi-headset','Layanan','Sertifikasi & layanan publik digital.','pages/layanan.php',5);

-- ---------- Akses cepat ----------
INSERT INTO `quick_links` (`icon`,`label`,`url`,`sort`) VALUES
('bi-journal-richtext','Regulasi','pages/regulasi.php',1),
('bi-headset','Layanan Publik','pages/layanan.php',2),
('bi-megaphone','Pengaduan','pages/kontak.php',3),
('bi-info-circle','Informasi Publik','pages/informasi-publik.php',4),
('bi-bell','Pengumuman','pages/berita.php',5),
('bi-images','Galeri','pages/galeri.php',6),
('bi-telephone','Kontak','pages/kontak.php',7),
('bi-question-circle','FAQ','pages/layanan.php#faq',8);

-- ---------- Layanan unggulan (beranda) ----------
INSERT INTO `featured_services` (`icon`,`title`,`description`,`url`,`sort`) VALUES
('bi-patch-check-fill','Sertifikasi Bandar Udara','Penerbitan dan pembaruan sertifikat operasi bandar udara sesuai standar keselamatan.','pages/layanan.php',1),
('bi-shield-check','Standardisasi Keselamatan','Penyusunan norma, standar, dan prosedur keselamatan operasional bandar udara.','pages/layanan.php',2),
('bi-signpost-split','Prasarana Bandar Udara','Perencanaan dan pengembangan landasan pacu, taxiway, apron, dan terminal.','pages/layanan.php',3),
('bi-life-preserver','Pelayanan Darurat Bandar Udara','Standar peralatan dan kesiapan PKP-PK di seluruh bandar udara.','pages/layanan.php',4),
('bi-diagram-3-fill','Sistem Operasional Bandar Udara','Pengawasan sistem penyelenggaraan operasional bandar udara nasional.','pages/layanan.php',5),
('bi-briefcase-fill','Pengusahaan Bandar Udara','Bimbingan tata kelola bisnis dan kerja sama pengusahaan bandar udara.','pages/layanan.php',6);

-- ---------- Mitra ----------
INSERT INTO `partners` (`name`,`sort`) VALUES
('Kementerian Perhubungan RI',1),
('Angkasa Pura Indonesia',2),
('AirNav Indonesia',3),
('Garuda Indonesia',4),
('ICAO',5),
('Badan Usaha Bandar Udara',6);

-- ---------- Wilayah Otoritas Bandar Udara ----------
INSERT INTO `regions` (`region_code`,`numeral`,`title`,`hq`,`hq_short`,`coverage`,`coverage_short`,`sort`) VALUES
('1','I','Wilayah I','Bandara Internasional Soekarno–Hatta, Tangerang','Soekarno–Hatta, Tangerang','Lampung, DKI Jakarta, Jawa Barat, Banten, Kalimantan Barat','Lampung, DKI Jakarta, Jawa Barat, Banten, Kalimantan Barat',1),
('2','II','Wilayah II','Bandara Internasional Kualanamu, Deli Serdang','Kualanamu, Deli Serdang','Aceh, Sumatera Utara, Riau, Kepulauan Riau','Aceh, Sumatera Utara, Riau, Kepulauan Riau',2),
('3','III','Wilayah III','Bandara Internasional Juanda, Sidoarjo','Juanda, Sidoarjo','Jawa Tengah, DI Yogyakarta, Jawa Timur, Kalimantan Selatan','Jawa Tengah, DI Yogyakarta, Jawa Timur, Kalimantan Selatan',3),
('4','IV','Wilayah IV','Bandara Internasional I Gusti Ngurah Rai, Badung','I Gusti Ngurah Rai, Badung','Bali, Nusa Tenggara Barat, Nusa Tenggara Timur','Bali, Nusa Tenggara Barat, Nusa Tenggara Timur',4),
('5','V','Wilayah V','Bandara Internasional Sultan Hasanuddin, Maros','Sultan Hasanuddin, Maros','Sulawesi Tengah, Sulawesi Selatan, Sulawesi Tenggara, Sulawesi Barat','Sulawesi Tengah, Sulawesi Selatan, Sulawesi Tenggara, Sulawesi Barat',5),
('6','VI','Wilayah VI','Bandara Internasional Minangkabau, Padang Pariaman','Minangkabau, Padang Pariaman','Sumatera Barat, Jambi, Sumatera Selatan, Bengkulu, Kepulauan Bangka Belitung','Sumatera Barat, Jambi, Sumatera Selatan, Bengkulu, Kep. Bangka Belitung',6),
('7','VII','Wilayah VII','Bandara Internasional Sultan Aji Muhammad Sulaiman Sepinggan, Balikpapan','Sepinggan, Balikpapan','Kalimantan Tengah, Kalimantan Timur, Kalimantan Utara','Kalimantan Tengah, Kalimantan Timur, Kalimantan Utara',7),
('8','VIII','Wilayah VIII','Bandara Internasional Sam Ratulangi, Manado','Sam Ratulangi, Manado','Sulawesi Utara, Gorontalo, Maluku, Maluku Utara','Sulawesi Utara, Gorontalo, Maluku, Maluku Utara',8),
('9','IX','Wilayah IX','Bandara Rendani, Manokwari','Rendani, Manokwari','Papua Barat, Papua Barat Daya, Papua Tengah','Papua Barat, Papua Barat Daya, Papua Tengah',9),
('10','X','Wilayah X','Bandara Mopah, Merauke','Mopah, Merauke','Papua Selatan, Papua Pegunungan, Papua','Papua Selatan, Papua Pegunungan, Papua',10);


-- ---------- Bandar udara per wilayah OBU ----------
-- Kantor pusat wilayah mengikuti rekapitulasi PEROTBAN
UPDATE `regions` SET `hq_short` = 'Tangerang – Banten' WHERE `region_code` = '1';
UPDATE `regions` SET `hq_short` = 'Medan' WHERE `region_code` = '2';
UPDATE `regions` SET `hq_short` = 'Surabaya' WHERE `region_code` = '3';
UPDATE `regions` SET `hq_short` = 'Bali' WHERE `region_code` = '4';
UPDATE `regions` SET `hq_short` = 'Makassar' WHERE `region_code` = '5';
UPDATE `regions` SET `hq_short` = 'Padang' WHERE `region_code` = '6';
UPDATE `regions` SET `hq_short` = 'Balikpapan' WHERE `region_code` = '7';
UPDATE `regions` SET `hq_short` = 'Manado' WHERE `region_code` = '8';
UPDATE `regions` SET `hq_short` = 'Manokwari' WHERE `region_code` = '9';
UPDATE `regions` SET `hq_short` = 'Merauke' WHERE `region_code` = '10';

-- Bandar udara per wilayah (251 lokasi)
SET @r = (SELECT id FROM `regions` WHERE `region_code` = '1');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Budiarto, Curug', 1),
  (@r, 'UPBU Rahadi Oesman, Ketapang', 2),
  (@r, 'UPBU Cakrabhuwana, Cirebon', 3),
  (@r, 'UPBU Pangsuma, Putussibau', 4),
  (@r, 'Satpel Wiriadinata', 5),
  (@r, 'UPBU Tebelian, Sintang', 6),
  (@r, 'Satpel Taufiq Kiemas', 7),
  (@r, 'Gatot Subroto', 8),
  (@r, 'Soekarno–Hatta (AP II)', 9),
  (@r, 'Halim Perdanakusuma (AP II)', 10),
  (@r, 'Radin Inten II (AP II)', 11),
  (@r, 'Nusawiru (UPTD)', 12),
  (@r, 'Kertajati (AP II)', 13),
  (@r, 'Husein Sastranegara (AP II)', 14),
  (@r, 'Supadio', 15),
  (@r, 'Nangapinoh', 16);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '2');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'Sultan Iskandar Muda (AP II)', 1),
  (@r, 'UPBU Alas Leuser', 2),
  (@r, 'UPBU Lasikin', 3),
  (@r, 'UPBU Cut Nyak Dhien', 4),
  (@r, 'UPBU Maimun Saleh', 5),
  (@r, 'UPBU Rembele', 6),
  (@r, 'UPBU Malikussaleh', 7),
  (@r, 'Kuala Batu (UPTD)', 8),
  (@r, 'Satpel Blangkejeren', 9),
  (@r, 'Satpel Syekh Hamzah Fansuri', 10),
  (@r, 'Kualanamu (AP II)', 11),
  (@r, 'UPBU Binaka', 12),
  (@r, 'Satpel Sibisa', 13),
  (@r, 'UPBU Dr. Ferdinan L. Tobing', 14),
  (@r, 'UPBU Aek Godang', 15),
  (@r, 'Silangit / Raja Sisingamangaraja XII (AP II)', 16),
  (@r, 'UPBU Lasondre', 17),
  (@r, 'Sultan Syarif Kasim II (AP II)', 18),
  (@r, 'Tempuling (UPTD)', 19),
  (@r, 'UPBU Japura', 20),
  (@r, 'Tuanku Tambusai', 21),
  (@r, 'Pinang Kampai', 22),
  (@r, 'Hang Nadim (BUBU)', 23),
  (@r, 'Raja Haji Fisabilillah / RHF (AP II)', 24),
  (@r, 'UPBU Ranai', 25),
  (@r, 'UPBU Raja Haji Abdullah', 26),
  (@r, 'UPBU Dabo Singkep', 27),
  (@r, 'UPBU Letung – Anambas', 28),
  (@r, 'Matak (ConocoPhillips)', 29),
  (@r, 'UPBU Tambelan', 30),
  (@r, 'Teuku Cut Ali', 31);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '3');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Trunojoyo, Sumenep', 1),
  (@r, 'UPBU Dewadaru, Karimunjawa', 2),
  (@r, 'UPBU Harun Thohir, Bawean', 3),
  (@r, 'UPBU Tunggul Wulung, Cilacap', 4),
  (@r, 'UPBU Gusti Sjamsir Alam, Kotabaru', 5),
  (@r, 'Satpel Ngloram, Cepu', 6),
  (@r, 'Abdul Rachman Saleh (UPTD)', 7),
  (@r, 'Warukin', 8),
  (@r, 'Notohadinegoro (UPTD)', 9),
  (@r, 'Juanda (AP I)', 10),
  (@r, 'Yogyakarta International Airport (AP I)', 11),
  (@r, 'Adi Soemarmo (AP I)', 12),
  (@r, 'Jenderal Ahmad Yani (AP I)', 13),
  (@r, 'Syamsudin Noor (AP I)', 14),
  (@r, 'Banyuwangi (AP II)', 15),
  (@r, 'Adi Sutjipto (AP I)', 16),
  (@r, 'Jenderal Besar Soedirman (AP II)', 17);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '4');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Kelas III David Constantijn Saudale', 1),
  (@r, 'UPBU Kelas III Wunopito, Lewoleba', 2),
  (@r, 'UPBU H. Aroeboesman, Ende', 3),
  (@r, 'UPBU Kelas II Sultan M. Salahuddin, Bima', 4),
  (@r, 'UPBU Kelas III Sultan M. Kaharuddin, Sumbawa', 5),
  (@r, 'UPBU Kelas II Tambolaka, Sumba Barat', 6),
  (@r, 'UPBU Kelas II Umbu Mehang Kunda, Waingapu', 7),
  (@r, 'UPBU Kelas III Gewayantana, Larantuka', 8),
  (@r, 'UPBU Kelas II Fransiskus Xaverius Seda, Maumere', 9),
  (@r, 'UPBU Kelas III Soa, Bajawa', 10),
  (@r, 'UPBU Kelas II Komodo, Labuan Bajo', 11),
  (@r, 'UPBU Kelas III Frans Sales Lega, Ruteng', 12),
  (@r, 'UPBU Tardamu, Sabu', 13),
  (@r, 'UPBU Kelas III Mali, Alor', 14),
  (@r, 'UPBU Kelas III A.A. Bere Talo, Atambua', 15),
  (@r, 'Satpel Kabir', 16),
  (@r, 'I Gusti Ngurah Rai (AP I)', 17),
  (@r, 'Zainuddin Abdul Madjid (AP I)', 18),
  (@r, 'El Tari (AP I)', 19);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '5');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Haluoleo, Kendari', 1),
  (@r, 'UPBU Mutiara SIS Al-Jufri, Palu', 2),
  (@r, 'UPBU Syukuran Aminuddin Amir', 3),
  (@r, 'UPBU Kasiguncu, Poso', 4),
  (@r, 'UPBU Sultan Bantilan, Toli-Toli', 5),
  (@r, 'UPBU Lagaligo, Bua', 6),
  (@r, 'UPBU Matahora, Wakatobi', 7),
  (@r, 'UPBU H. Aroeppala, Selayar', 8),
  (@r, 'UPBU Tanjung Api, Ampana', 9),
  (@r, 'UPBU Betoambari, Bau-Bau', 10),
  (@r, 'UPBU Sangia Nibandera, Kolaka', 11),
  (@r, 'UPBU Tampa Padang, Mamuju', 12),
  (@r, 'UPBU Sumarorong, Mamasa', 13),
  (@r, 'UPBU Morowali, Morowali', 14),
  (@r, 'UPBU Pogogul, Buol', 15),
  (@r, 'UPBU Arung Palakka, Bone', 16),
  (@r, 'UPBU Rampi, Luwu Utara', 17),
  (@r, 'UPBU Seko, Luwu Utara', 18),
  (@r, 'UPBU Andi Jemma, Masamba', 19),
  (@r, 'UPBU Pongtiku, Tana Toraja', 20),
  (@r, 'UPBU Sugimanuru, Raha', 21),
  (@r, 'Sultan Hasanuddin (AP I)', 22),
  (@r, 'Bandar Udara Khusus Sorowako', 23);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '6');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Depati Parbo, Kerinci', 1),
  (@r, 'UPBU Muara Bungo', 2),
  (@r, 'UPBU Mukomuko', 3),
  (@r, 'UPBU Silampari, Lubuklinggau', 4),
  (@r, 'UPBU Rokot, Sipora', 5),
  (@r, 'Satpel Enggano', 6),
  (@r, 'Satpel Atung Bungsu, Pagar Alam', 7),
  (@r, 'Minangkabau (AP II)', 8),
  (@r, 'Sultan Mahmud Badaruddin II (AP II)', 9),
  (@r, 'H.A.S. Hanandjoeddin (AP II)', 10),
  (@r, 'Depati Amir (AP II)', 11),
  (@r, 'Fatmawati Soekarno (AP II)', 12),
  (@r, 'Sultan Thaha (AP II)', 13),
  (@r, 'Pusako Anak Negari, Pasaman Barat', 14);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '7');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Juwata', 1),
  (@r, 'UPBU Nunukan', 2),
  (@r, 'UPBU Yuvai Semaring, Long Bawan', 3),
  (@r, 'UPBU Long Apung', 4),
  (@r, 'UPBU Kol. Robert Atty Bessing', 5),
  (@r, 'UPBU Tanjung Harapan', 6),
  (@r, 'UPBU Maratua', 7),
  (@r, 'UPBU Kalimarau', 8),
  (@r, 'UPBU Aji Pangeran Tumenggung Pranoto', 9),
  (@r, 'UPBU Melalan, Melak', 10),
  (@r, 'UPBU Datah Dawai', 11),
  (@r, 'UPBU H. Asan, Sampit', 12),
  (@r, 'UPBU Kuala Kurun', 13),
  (@r, 'UPBU Kuala Pembuang', 14),
  (@r, 'UPBU Tumbang Samba', 15),
  (@r, 'UPBU Sanggu, Buntok', 16),
  (@r, 'UPBU Haji Muhammad Sidik', 17),
  (@r, 'UPBU Iskandar, Pangkalanbun', 18),
  (@r, 'Sultan Aji Muhammad Sulaiman (AP II)', 19),
  (@r, 'Tjilik Riwut (AP II)', 20),
  (@r, 'Tanjung Bara', 21),
  (@r, 'Long Layu', 22),
  (@r, 'Binuang', 23),
  (@r, 'Bersujud', 24),
  (@r, 'Singkawang', 25);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '8');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'Sultan Babullah, Ternate', 1),
  (@r, 'UPBU Djalaluddin Kelas I, Gorontalo', 2),
  (@r, 'Karel Sadsuitubun, Tual', 3),
  (@r, 'Pitu, Morotai', 4),
  (@r, 'Melonguane, Sangihe Talaud', 5),
  (@r, 'Naha, Tahuna', 6),
  (@r, 'Mathilda Batlayeri, Saumlaki', 7),
  (@r, 'Larat, Pulau Yamdena / Liwur Bunga', 8),
  (@r, 'Satpel Kuffar, Seram Bagian Timur', 9),
  (@r, 'Wahai', 10),
  (@r, 'Amahai, Pulau Seram', 11),
  (@r, 'Namniwel, Kab. Buru', 12),
  (@r, 'Namrole, Pulau Buru', 13),
  (@r, 'Bandaneira, Pulau Banda', 14),
  (@r, 'Buli, Maba', 15),
  (@r, 'Oesman Sadik, Labuha', 16),
  (@r, 'Gamar Malamo, Galela', 17),
  (@r, 'Kuabang, Kao', 18),
  (@r, 'Emalamo, Sanana', 19),
  (@r, 'Dobo, Pulau Aru', 20),
  (@r, 'Satpel Jos Orno Imsula', 21),
  (@r, 'Jhon Becker, Pulau Kisar', 22),
  (@r, 'Miangas', 23),
  (@r, 'Gebe', 24),
  (@r, 'Weda', 25),
  (@r, 'Sam Ratulangi (AP I)', 26),
  (@r, 'Pattimura (AP I)', 27),
  (@r, 'Dofa Benjina Falabisahaya', 28);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '9');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'Domine Eduard Osok (DEO)', 1),
  (@r, 'Rendani, Manokwari', 2),
  (@r, 'Douw Aturure, Nabire', 3),
  (@r, 'Utarom, Kaimana', 4),
  (@r, 'Torea, Fak-Fak', 5),
  (@r, 'Wasior', 6),
  (@r, 'Bintuni', 7),
  (@r, 'Babo', 8),
  (@r, 'Merdey / Ijahabra', 9),
  (@r, 'Kebar', 10),
  (@r, 'Inanwatan', 11),
  (@r, 'Ayawasi', 12),
  (@r, 'Kambuaya', 13),
  (@r, 'Teminabuan', 14),
  (@r, 'Marinda, Raja Ampat', 15),
  (@r, 'Frans Kaisiepo, Biak', 16),
  (@r, 'Anggi', 17),
  (@r, 'Werur', 18),
  (@r, 'Taive II', 19);

SET @r = (SELECT id FROM `regions` WHERE `region_code` = '10');
INSERT INTO `region_airports` (`region_id`,`name`,`sort`) VALUES
  (@r, 'UPBU Kelas III Bade', 1),
  (@r, 'UPBU Kelas III Moanamani', 2),
  (@r, 'UPBU Kelas III Kimam', 3),
  (@r, 'UPBU Kelas III Akimuga', 4),
  (@r, 'UPBU Kelas III Kepi', 5),
  (@r, 'UPBU Kelas III Ilaga, Puncak', 6),
  (@r, 'UPBU Kelas III Okaba', 7),
  (@r, 'UPBU Kelas III Kokonao', 8),
  (@r, 'UPBU Kelas I Mopah, Merauke', 9),
  (@r, 'UPBU Kelas III Elelim', 10),
  (@r, 'UPBU Kelas III Waghete', 11),
  (@r, 'UPBU Kelas III Kamur', 12),
  (@r, 'UPBU Kelas I Wamena', 13),
  (@r, 'UPBU Kelas III Numfor', 14),
  (@r, 'UPBU Kelas III Bomakia', 15),
  (@r, 'UPBU Kelas III Tanah Merah', 16),
  (@r, 'Sentani (AP I)', 17),
  (@r, 'UPBU Kelas II Mozes Kilangin', 18),
  (@r, 'UPBU Kelas III Tiom', 19),
  (@r, 'UPBU Kelas III Bilorai', 20),
  (@r, 'UPBU Kelas III Batom', 21),
  (@r, 'UPBU Kelas III Senggeh', 22),
  (@r, 'UPBU Kelas III Stevanus Rumbewas', 23),
  (@r, 'UPBU Kelas III Illu', 24),
  (@r, 'UPBU Kelas III Dabra', 25),
  (@r, 'UPBU Kelas III Kiwirok', 26),
  (@r, 'UPBU Kelas III Mararena, Sarmi', 27),
  (@r, 'UPBU Kelas III Bokondini', 28),
  (@r, 'UPBU Kelas III Karubaga', 29),
  (@r, 'UPBU Kelas II Nop Goliat Dekai', 30),
  (@r, 'UPBU Kelas III Mindiptana', 31),
  (@r, 'UPBU Kelas III Oksibil', 32),
  (@r, 'UPBU Kelas III Enarotali', 33),
  (@r, 'UPBU Kelas III Mulia', 34),
  (@r, 'Satpel Senggo', 35),
  (@r, 'UPBU Kelas III Ewer', 36),
  (@r, 'Molof', 37),
  (@r, 'Satpel Sinak', 38),
  (@r, 'Satpel Mangelum', 39),
  (@r, 'Kelila', 40),
  (@r, 'Kebo', 41),
  (@r, 'Aboyaga', 42),
  (@r, 'Aboy', 43),
  (@r, 'Yaniruma', 44),
  (@r, 'Kobakma', 45),
  (@r, 'Apalapsili', 46),
  (@r, 'Kenyam', 47),
  (@r, 'Mapenduma', 48),
  (@r, 'Paro', 49),
  (@r, 'Fawi', 50),
  (@r, 'Beoga', 51),
  (@r, 'Jila', 52),
  (@r, 'Jita', 53),
  (@r, 'Potowai', 54),
  (@r, 'Alama', 55),
  (@r, 'Wangbe', 56),
  (@r, 'Waris Baru / Towe Hitam', 57),
  (@r, 'Yuruf', 58),
  (@r, 'Tsinga', 59);


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

-- ---------- Video profil ----------
INSERT INTO `home_video` (`eyebrow`,`title`,`caption`,`art_class`,`icon`) VALUES
('Video Profil','Mengenal Lebih Dekat Direktorat Bandar Udara','Placeholder video profil — sematkan tautan YouTube resmi pada berkas produksi.','art-2','bi-camera-reels');

-- ---------- Profil ----------
INSERT INTO `profil_about` (`eyebrow`,`title`,`body`,`art_class`,`icon`,`visi`,`tugas_pokok`) VALUES
('Tentang Direktorat','Mewujudkan Kebandarudaraan Nasional yang Andal',
'Direktorat Bandar Udara merupakan salah satu unit teknis di bawah Direktorat Jenderal Perhubungan Udara, Kementerian Perhubungan Republik Indonesia. Direktorat ini bertanggung jawab atas perumusan kebijakan, penyusunan norma, standar, prosedur dan kriteria (NSPK), pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi, dan pelaporan di bidang kebandarudaraan.

Dengan cakupan lebih dari 300 bandar udara di 34 provinsi, Direktorat Bandar Udara berperan strategis dalam menjaga keselamatan, keamanan, dan kualitas layanan penerbangan sipil nasional, sekaligus mendorong konektivitas antarwilayah melalui pengembangan prasarana bandar udara yang berkelanjutan.',
'art-6','bi-building-check',
'"Terwujudnya penyelenggaraan bandar udara yang profesional, aman, dan berkelanjutan guna mendukung konektivitas nasional yang andal dan berdaya saing."',
'Direktorat Bandar Udara mempunyai tugas melaksanakan perumusan dan pelaksanaan kebijakan, penyusunan norma, standar, prosedur dan kriteria, pemberian bimbingan teknis dan supervisi, serta pemantauan, analisis, evaluasi dan pelaporan di bidang bandar udara.');

INSERT INTO `timeline_items` (`year`,`title`,`description`,`sort`) VALUES
('1975','Pembentukan Unit Kebandarudaraan','Cikal bakal unit yang menangani urusan bandar udara dibentuk di lingkungan Kementerian Perhubungan.',1),
('1990','Reorganisasi Direktorat Jenderal Perhubungan Udara','Fungsi kebandarudaraan diperkuat menjadi direktorat tersendiri dengan cakupan tugas yang lebih luas.',2),
('2009','Penguatan Regulasi Penerbangan Nasional','Penyesuaian tugas dan fungsi mengikuti pembaruan regulasi penerbangan nasional.',3),
('2015','Digitalisasi Layanan Sertifikasi','Direktorat mulai mengembangkan sistem informasi untuk layanan sertifikasi bandar udara.',4),
('2021','Penataan 5 Subdirektorat Teknis','Struktur organisasi ditata menjadi lima subdirektorat sesuai bidang keahlian teknis.',5),
('2026','Transformasi Layanan Digital Terpadu','Peluncuran portal layanan publik digital terpadu untuk mempermudah akses informasi dan layanan.',6);

INSERT INTO `missions` (`content`,`sort`) VALUES
('Merumuskan kebijakan dan standar kebandarudaraan yang adaptif terhadap perkembangan teknologi.',1),
('Meningkatkan kualitas prasarana dan fasilitas bandar udara di seluruh Indonesia.',2),
('Memperkuat pengawasan keselamatan dan kesiapsiagaan pelayanan darurat bandar udara.',3),
('Mendorong tata kelola pengusahaan bandar udara yang transparan dan akuntabel.',4),
('Memberikan layanan publik yang cepat, mudah, dan berbasis digital.',5);

INSERT INTO `value_cards` (`icon`,`title`,`sort`) VALUES
('bi-gem','Integritas',1),
('bi-award','Profesionalisme',2),
('bi-people','Sinergi',3),
('bi-lightbulb','Inovasi',4),
('bi-hand-thumbs-up','Pelayanan Prima',5);

INSERT INTO `strategic_goals` (`icon`,`title`,`description`,`sort`) VALUES
('bi-shield-check','Keselamatan Terjamin','Meningkatkan tingkat kepatuhan standar keselamatan bandar udara secara nasional.',1),
('bi-signpost-split','Prasarana Merata','Pemerataan pembangunan prasarana bandar udara di wilayah 3T (terdepan, terluar, tertinggal).',2),
('bi-graph-up-arrow','Layanan Optimal','Mempercepat waktu layanan sertifikasi dan perizinan melalui digitalisasi.',3),
('bi-tree','Berkelanjutan','Mendorong pembangunan bandar udara yang ramah lingkungan dan berkelanjutan.',4);

INSERT INTO `directorate_functions` (`icon`,`title`,`description`,`sort`) VALUES
('bi-shield-check','Standardisasi Keselamatan Bandar Udara','Penyusunan standar dan pengawasan keselamatan operasional bandar udara.',1),
('bi-tree','Tatanan Kebandarudaraan & Lingkungan','Penataan tatanan kebandarudaraan nasional serta aspek lingkungan.',2),
('bi-signpost-split','Prasarana Bandar Udara','Perencanaan dan pengembangan prasarana sisi udara dan sisi darat.',3),
('bi-life-preserver','Peralatan & Pelayanan Darurat','Standar peralatan dan kesiapsiagaan pelayanan darurat bandar udara.',4),
('bi-diagram-3','Sistem Penyelenggaraan & Pengusahaan','Pengawasan sistem penyelenggaraan dan pengusahaan bandar udara.',5),
('bi-archive','Tata Usaha & Pengelolaan Data','Pengelolaan administrasi, data, dan informasi kebandarudaraan.',6);

-- ---------- Organisasi ----------
INSERT INTO `org_units` (`unit_key`,`icon`,`title`,`description`,`chips`,`branch_class`,`sort`) VALUES
('std','bi-shield-check','Subdirektorat Standardisasi Keselamatan Bandar Udara','Menyusun norma, standar, prosedur, dan kriteria keselamatan operasional bandar udara, serta melakukan bimbingan teknis dan pengawasan kepatuhan standar keselamatan di seluruh bandar udara Indonesia.','Standar Keselamatan, Audit Kepatuhan, Bimbingan Teknis','org-branch',1),
('tatanan','bi-tree','Subdirektorat Tatanan Kebandarudaraan & Lingkungan','Menyusun tatanan kebandarudaraan nasional, rencana induk bandar udara, serta kajian dampak lingkungan atas pembangunan dan operasional bandar udara.','Tatanan Nasional, Rencana Induk, Kajian Lingkungan','org-branch',2),
('prasarana','bi-signpost-split','Subdirektorat Prasarana Bandar Udara','Merencanakan dan mengevaluasi pembangunan prasarana sisi udara (landasan pacu, taxiway, apron) dan sisi darat (terminal, akses) bandar udara di seluruh Indonesia.','Sisi Udara, Sisi Darat, Evaluasi Teknis','org-branch',3),
('darurat','bi-life-preserver','Subdirektorat Peralatan & Pelayanan Darurat','Menyusun standar peralatan dan kesiapsiagaan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK) serta fasilitas keselamatan darurat lainnya di bandar udara.','PKP-PK, Peralatan Darurat, Simulasi Tanggap Darurat','org-branch',4),
('tu','bi-archive','Jabatan Fungsional & Jabatan Pelaksana','Mengelola administrasi umum, kepegawaian, keuangan, serta data dan informasi di lingkungan Direktorat Bandar Udara.','Administrasi, Kepegawaian, Pengelolaan Data','org-branch2',5),
('sistem','bi-diagram-3','Subdirektorat Sistem Penyelenggaraan & Pengusahaan','Mengawasi sistem penyelenggaraan operasional bandar udara serta membina tata kelola pengusahaan dan kerja sama bandar udara oleh badan usaha bandar udara.','Operasional, Pengusahaan, Kerja Sama','org-branch',6);


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

-- ---------- Tugas & Fungsi (unit kerja — arsip) ----------
INSERT INTO `tf_units` (`id`,`number`,`title`,`intro`,`sort`) VALUES
(1,'01','Standardisasi Keselamatan Bandar Udara','Unit ini menyusun dan mengawasi penerapan standar keselamatan operasional bandar udara secara nasional.',1),
(2,'02','Tatanan Kebandarudaraan & Lingkungan','Unit ini menyusun tatanan kebandarudaraan nasional serta memastikan aspek lingkungan terjaga.',2),
(3,'03','Prasarana Bandar Udara','Unit ini merencanakan dan mengevaluasi pembangunan prasarana bandar udara di seluruh Indonesia.',3),
(4,'04','Peralatan & Pelayanan Darurat','Unit ini menjamin kesiapsiagaan peralatan dan pelayanan darurat di setiap bandar udara.',4),
(5,'05','Sistem Penyelenggaraan & Pengusahaan','Unit ini mengawasi sistem operasional dan tata kelola pengusahaan bandar udara.',5),
(6,'06','Tata Usaha & Pengelolaan Data','Unit ini mengelola administrasi umum serta data dan informasi kebandarudaraan.',6);

INSERT INTO `tf_details` (`unit_id`,`icon`,`label`,`content`,`sort`) VALUES
(1,'bi-eye','Overview','Mengelola aspek keselamatan sisi udara & sisi darat bandar udara.',1),
(1,'bi-list-check','Responsibilities','Audit kepatuhan standar keselamatan seluruh bandar udara.',2),
(1,'bi-journal-text','Policy','Kebijakan mitigasi risiko operasional bandar udara.',3),
(1,'bi-rulers','Standards','Standar keselamatan sisi udara mengacu regulasi penerbangan sipil.',4),
(1,'bi-mortarboard','Technical Guidance','Pelatihan & bimbingan teknis inspektur keselamatan.',5),
(1,'bi-binoculars','Monitoring','Pemantauan berkala terhadap kondisi fasilitas keselamatan.',6),
(1,'bi-clipboard-data','Evaluation','Evaluasi tahunan tingkat kepatuhan standar keselamatan.',7),
(1,'bi-file-earmark-bar-graph','Reporting','Laporan status keselamatan bandar udara ke Ditjen Hubud.',8),
(2,'bi-eye','Overview','Perencanaan tata ruang & lingkungan kawasan bandar udara.',1),
(2,'bi-list-check','Responsibilities','Menyusun rencana induk dan tatanan kebandarudaraan.',2),
(2,'bi-journal-text','Policy','Kebijakan zonasi kawasan keselamatan operasi penerbangan.',3),
(2,'bi-rulers','Standards','Standar baku mutu lingkungan kawasan bandar udara.',4),
(2,'bi-mortarboard','Technical Guidance','Bimbingan teknis kajian dampak lingkungan (AMDAL).',5),
(2,'bi-binoculars','Monitoring','Pemantauan kepatuhan zonasi kawasan sekitar bandar udara.',6),
(2,'bi-clipboard-data','Evaluation','Evaluasi rencana induk bandar udara secara berkala.',7),
(2,'bi-file-earmark-bar-graph','Reporting','Laporan tatanan kebandarudaraan & status lingkungan.',8),
(3,'bi-eye','Overview','Pengembangan landasan pacu, taxiway, apron, dan terminal.',1),
(3,'bi-list-check','Responsibilities','Perencanaan teknis pembangunan & rehabilitasi prasarana.',2),
(3,'bi-journal-text','Policy','Kebijakan prioritas pembangunan bandar udara perintis.',3),
(3,'bi-rulers','Standards','Standar teknis konstruksi sisi udara & sisi darat.',4),
(3,'bi-mortarboard','Technical Guidance','Bimbingan teknis desain & pengawasan konstruksi.',5),
(3,'bi-binoculars','Monitoring','Pemantauan progres pembangunan prasarana nasional.',6),
(3,'bi-clipboard-data','Evaluation','Evaluasi kelaikan fungsi prasarana pasca pembangunan.',7),
(3,'bi-file-earmark-bar-graph','Reporting','Laporan capaian pembangunan prasarana bandar udara.',8),
(4,'bi-eye','Overview','Standar & kesiapan PKP-PK serta fasilitas darurat lain.',1),
(4,'bi-list-check','Responsibilities','Menjamin ketersediaan peralatan pertolongan kecelakaan.',2),
(4,'bi-journal-text','Policy','Kebijakan kategori PKP-PK sesuai kelas bandar udara.',3),
(4,'bi-rulers','Standards','Standar respons waktu & jumlah personel PKP-PK.',4),
(4,'bi-mortarboard','Technical Guidance','Pelatihan simulasi tanggap darurat bandar udara.',5),
(4,'bi-binoculars','Monitoring','Pemantauan kesiapan armada & personel PKP-PK.',6),
(4,'bi-clipboard-data','Evaluation','Evaluasi hasil simulasi tanggap darurat tahunan.',7),
(4,'bi-file-earmark-bar-graph','Reporting','Laporan kesiapsiagaan pelayanan darurat bandar udara.',8),
(5,'bi-eye','Overview','Pengawasan sistem operasional & bisnis bandar udara.',1),
(5,'bi-list-check','Responsibilities','Pembinaan badan usaha bandar udara & kerja sama.',2),
(5,'bi-journal-text','Policy','Kebijakan tata kelola pengusahaan bandar udara.',3),
(5,'bi-rulers','Standards','Standar pelayanan operasional bandar udara komersial.',4),
(5,'bi-mortarboard','Technical Guidance','Bimbingan teknis tata kelola bisnis kebandarudaraan.',5),
(5,'bi-binoculars','Monitoring','Pemantauan kinerja operasional badan usaha bandar udara.',6),
(5,'bi-clipboard-data','Evaluation','Evaluasi kinerja penyelenggaraan & pengusahaan tahunan.',7),
(5,'bi-file-earmark-bar-graph','Reporting','Laporan kinerja sistem penyelenggaraan bandar udara.',8),
(6,'bi-eye','Overview','Administrasi umum, kepegawaian, dan keuangan direktorat.',1),
(6,'bi-list-check','Responsibilities','Pengelolaan surat, arsip, dan data kebandarudaraan.',2),
(6,'bi-journal-text','Policy','Kebijakan tata kelola data & informasi direktorat.',3),
(6,'bi-rulers','Standards','Standar pengarsipan & keamanan data instansi.',4),
(6,'bi-mortarboard','Technical Guidance','Bimbingan teknis sistem informasi kebandarudaraan.',5),
(6,'bi-binoculars','Monitoring','Pemantauan validitas & keterkinian basis data.',6),
(6,'bi-clipboard-data','Evaluation','Evaluasi tata kelola administrasi & data tahunan.',7),
(6,'bi-file-earmark-bar-graph','Reporting','Laporan kinerja administrasi & pengelolaan data.',8);

-- ---------- Layanan ----------
INSERT INTO `services` (`id`,`slug`,`name`,`category`,`sifat`,`sifat_badge`,`klasifikasi`,`klasifikasi_badge`,`icon`,`chip_label`,`description`,`time_process`,`cost`,`validity`,`method`,`cta_label`,`cta_url`,`sort`) VALUES
(1,'sertifikasi-bandar-udara','Sertifikasi Bandar Udara/ Registrasi Bandar Udara','sertifikasi','Layanan Publik','badge-success','Sertifikasi','badge-primary','bi-patch-check-fill','Layanan Publik','Layanan Sertifikasi Bandar Udara adalah proses penerbitan dan pembaruan Sertifikat Operasi Bandar Udara (SBU) yang menyatakan bahwa sebuah bandar udara telah memenuhi standar keselamatan, keamanan, dan kelaikan operasional sesuai peraturan perundang-undangan penerbangan sipil yang berlaku. Sertifikat ini wajib dimiliki oleh setiap penyelenggara bandar udara sebelum dan selama masa operasional.','14–30 hari kerja','Sesuai tarif PNBP','5 tahun','Daring & Tatap Muka','Ajukan Layanan Sekarang','kontak.php',1),
(2,'sertifikasi-lembaga-pendidikan','Sertifikasi Lembaga Pendidikan dan/ Atau Pelatihan Personel Bandar Udara','sertifikasi','Layanan Publik','badge-success','Sertifikasi','badge-primary','bi-mortarboard-fill','Layanan Publik','Sertifikasi bagi lembaga penyelenggara pendidikan dan/atau pelatihan personel bandar udara agar memenuhi standar kurikulum, instruktur, dan sarana pelatihan.','14–30 hari kerja','Sesuai tarif PNBP','3 tahun','Daring & Tatap Muka','Ajukan Layanan Sekarang','kontak.php',2),
(3,'sertifikasi-lembaga-inspeksi','Sertifikasi Lembaga Inspeksi Keselamatan','sertifikasi','Layanan Publik','badge-success','Sertifikasi','badge-primary','bi-clipboard2-check-fill','Layanan Publik','Sertifikasi lembaga inspeksi keselamatan yang melakukan pemeriksaan fasilitas dan peralatan bandar udara.','14–30 hari kerja','Sesuai tarif PNBP','3 tahun','Daring','Ajukan Layanan Sekarang','kontak.php',3),
(4,'pelayanan-jasa-kebandarudaraan','Pelayanan Jasa Kebandarudaraan','perizinan','Layanan Publik','badge-success','Perizinan','badge-accent','bi-briefcase-fill','Layanan Publik','Perizinan penyelenggaraan pelayanan jasa kebandarudaraan oleh badan usaha bandar udara maupun unit penyelenggara bandar udara.','14–30 hari kerja','Sesuai tarif PNBP','5 tahun','Daring','Ajukan Layanan Sekarang','kontak.php',4),
(5,'pelayanan-teknis-penanganan-pesawat','Pelayanan Teknis Penanganan Pesawat Udara di Darat, Pelayanan Penumpang dan Bagasi, Serta Penanganan Kargo dan Pos','perizinan','Layanan Publik','badge-success','Perizinan','badge-accent','bi-truck-front-fill','Layanan Publik','Perizinan bagi badan usaha yang menyelenggarakan ground handling, pelayanan penumpang dan bagasi, serta penanganan kargo dan pos.','14–30 hari kerja','Sesuai tarif PNBP','5 tahun','Daring','Ajukan Layanan Sekarang','kontak.php',5),
(6,'pengelolaan-emisi','Pengelolaan produksi emisi dan perubahan iklim','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-cloud-haze2-fill',NULL,'Pengelolaan pelaporan produksi emisi gas rumah kaca dan program mitigasi perubahan iklim di lingkungan bandar udara.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,6),
(7,'pengawasan-lingkungan-hidup','Pengawasan lingkungan hidup Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-tree-fill',NULL,'Pengawasan pemenuhan baku mutu lingkungan hidup pada kawasan bandar udara.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,7),
(8,'penanggulangan-darurat-bencana','Penanggulangan Darurat Akibat Bencana','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-life-preserver',NULL,'Dukungan penanggulangan keadaan darurat pada bandar udara akibat bencana alam maupun non-alam.','Sesuai kebutuhan','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,8),
(9,'pengelolaan-kerjasama-badan-usaha','Pengelolaan Kerjasama Pemerintah dengan Badan Usaha (Pemrakarsa Badan Usaha)','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-people-fill',NULL,'Pengelolaan usulan kerja sama pemerintah dengan badan usaha untuk pembangunan dan pengusahaan bandar udara.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,9),
(10,'verifikasi-pkp-pk','Permohonan Verifikasi atau Evaluasi Pelayanan PKP-PK','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-truck-front-fill',NULL,'Verifikasi dan evaluasi kesiapan pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran di bandar udara.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,10),
(11,'bimtek-bandar-udara','Pelaksanaan Bimtek bidang Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-mortarboard',NULL,'Penyelenggaraan bimbingan teknis bidang kebandarudaraan bagi personel dan penyelenggara bandar udara.','Sesuai jadwal','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,11),
(12,'pengawasan-level-of-service','Pengawasan Tingkat Pelayanan (Level of Service) di Bandar Udara','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-graph-up',NULL,'Pengawasan tingkat pelayanan bandar udara pada sisi udara, sisi darat, dan terminal.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,12),
(13,'pengesahan-aep','Pengesahan Initial dan amandemen Dokumen Airport Emergency Plan (AEP)','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-file-earmark-check-fill',NULL,'Pengesahan dokumen Airport Emergency Plan awal maupun amandemennya.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,13),
(14,'perumusan-kebijakan','Perumusan Kebijakan di Bidang Bandar Udara','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-journal-text',NULL,'Perumusan kebijakan, norma, standar, prosedur, dan kriteria di bidang bandar udara.','Menyesuaikan','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,14),
(15,'pengawasan-pemeliharaan-prasarana','Pengawasan Pemeliharaan Prasarana Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-tools',NULL,'Pengawasan kegiatan pemeliharaan prasarana sisi udara dan sisi darat bandar udara.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,15),
(16,'audit-keselamatan','Pengelolaan Audit Keselamatan Bandar Udara','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-shield-check',NULL,'Pengelolaan pelaksanaan audit keselamatan bandar udara secara berkala.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,16),
(17,'sanksi-administratif','Pengenaan Sanksi Administratif Kebandarudaraan','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-exclamation-octagon-fill',NULL,'Pengenaan sanksi administratif atas pelanggaran ketentuan kebandarudaraan.','Menyesuaikan','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,17),
(18,'pengelolaan-pengawasan-bandara','Pengelolaan pengawasan Bandar udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-binoculars-fill',NULL,'Pengelolaan kegiatan pengawasan penyelenggaraan bandar udara secara nasional.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,18),
(19,'pembangunan-peralatan-darurat','Pembangunan atau Pengembangan Peralatan dan Pelayanan Darurat Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-truck-front',NULL,'Pengelolaan program pembangunan atau pengembangan peralatan dan pelayanan darurat bandar udara.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,19),
(20,'pengawasan-pembangunan-fasilitas','Pengawasan pembangunan fasilitas bandar udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-cone-striped',NULL,'Pengawasan pelaksanaan pembangunan fasilitas bandar udara agar sesuai standar teknis.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,20),
(21,'program-fasilitas-peralatan','Pengelolaan Program Pembangunan/Pengembangan fasilitas peralatan Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-gear-wide-connected',NULL,'Pengelolaan program pembangunan dan pengembangan fasilitas peralatan bandar udara.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,21),
(22,'pembangunan-prasarana','Pembangunan atau Pengembangan Prasarana Bandar Udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-signpost-split',NULL,'Pengelolaan program pembangunan atau pengembangan prasarana sisi udara dan sisi darat.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,22),
(23,'verifikasi-fasilitas','Verifikasi Fasilitas bandar udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-clipboard-check',NULL,'Verifikasi kelaikan fasilitas bandar udara sebelum dioperasikan.','14 hari kerja','Tidak dipungut biaya','Menyesuaikan','Tatap Muka',NULL,NULL,23),
(24,'program-bandara-heliport-waterbase','Pengelolaan program pembangunan dan pengembangan bandar udara, heliport dan waterbase','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-airplane-fill',NULL,'Pengelolaan program pembangunan dan pengembangan bandar udara, heliport, dan waterbase.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,24),
(25,'penetapan-lokasi','Penetapan lokasi bandar udara','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-geo-alt-fill',NULL,'Penetapan lokasi bandar udara berdasarkan kajian teknis dan tatanan kebandarudaraan nasional.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,25),
(26,'penetapan-rencana-induk','Penetapan rencana induk bandar udara','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-map-fill',NULL,'Penetapan rencana induk bandar udara sebagai acuan pengembangan jangka panjang.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,26),
(27,'peninjauan-tatanan','Peninjauan Tatanan Kebandarudaraan Nasional','layanan','Layanan Administrasi Pemerintahan','badge-gray','Layanan','badge-gray','bi-diagram-3-fill',NULL,'Peninjauan dan pemutakhiran tatanan kebandarudaraan nasional.','Menyesuaikan','Tidak dipungut biaya','Menyesuaikan','Daring',NULL,NULL,27),
(28,'penetapan-bandara-internasional','Penetapan Bandar Udara Internasional','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-globe-americas',NULL,'Penetapan status bandar udara sebagai bandar udara internasional.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,28),
(29,'penetapan-status-khusus-umum','Penetapan status Bandar Udara Khusus menjadi Umum','layanan','Layanan Publik','badge-success','Layanan','badge-gray','bi-arrow-left-right',NULL,'Penetapan perubahan status bandar udara khusus menjadi bandar udara umum.','30 hari kerja','Tidak dipungut biaya','Menyesuaikan','Daring & Tatap Muka',NULL,NULL,29);

INSERT INTO `service_requirements` (`service_id`,`content`,`sort`) VALUES
(1,'Surat permohonan resmi dari penyelenggara bandar udara',1),
(1,'Manual Operasi Bandar Udara (Aerodrome Manual)',2),
(1,'Bukti kepemilikan/penguasaan lahan bandar udara',3),
(1,'Laporan hasil inspeksi keselamatan internal terbaru',4),
(1,'Bukti pembayaran Penerimaan Negara Bukan Pajak (PNBP)',5);

INSERT INTO `service_steps` (`service_id`,`title`,`description`,`sort`) VALUES
(1,'Pengajuan','Ajukan permohonan & unggah berkas.',1),
(1,'Verifikasi','Pemeriksaan kelengkapan dokumen.',2),
(1,'Inspeksi','Pemeriksaan lapangan oleh tim teknis.',3),
(1,'Evaluasi','Penilaian kesesuaian standar keselamatan.',4),
(1,'Penerbitan','Sertifikat diterbitkan & dapat diunduh.',5);

INSERT INTO `service_downloads` (`service_id`,`title`,`meta`,`sort`) VALUES
(1,'Formulir Permohonan SBU','PDF · 320 KB',1),
(1,'Panduan Pengisian Aerodrome Manual','PDF · 1.1 MB',2);

INSERT INTO `general_requirements` (`content`,`sort`) VALUES
('Surat permohonan resmi dari penyelenggara bandar udara',1),
('Dokumen teknis & data operasional bandar udara',2),
('Sertifikat sebelumnya (untuk pengajuan perpanjangan)',3),
('Bukti pembayaran PNBP sesuai ketentuan',4),
('Laporan hasil inspeksi/audit internal terbaru',5);

INSERT INTO `process_steps` (`title`,`description`,`sort`) VALUES
('Pengajuan Permohonan','Ajukan permohonan & unggah berkas persyaratan.',1),
('Verifikasi Berkas','Petugas memeriksa kelengkapan & validitas dokumen.',2),
('Inspeksi Lapangan','Tim teknis melakukan pemeriksaan lapangan bila diperlukan.',3),
('Evaluasi Teknis','Hasil inspeksi dievaluasi sesuai standar yang berlaku.',4),
('Penerbitan Sertifikat','Sertifikat/izin diterbitkan & dapat diunduh pemohon.',5);

INSERT INTO `faqs` (`question`,`answer`,`sort`) VALUES
('Berapa lama proses sertifikasi bandar udara?','Proses sertifikasi umumnya memakan waktu 14–30 hari kerja sejak berkas dinyatakan lengkap, tergantung kompleksitas bandar udara dan hasil inspeksi lapangan.',1),
('Apakah layanan dapat diajukan sepenuhnya secara daring?','Sebagian besar tahapan dapat dilakukan secara daring, kecuali inspeksi lapangan yang mengharuskan kehadiran tim teknis di lokasi bandar udara.',2),
('Bagaimana jika dokumen persyaratan belum lengkap?','Sistem akan menampilkan status "Perlu Perbaikan" beserta catatan dokumen yang perlu dilengkapi sebelum proses dapat dilanjutkan.',3),
('Ke mana saya dapat menghubungi jika mengalami kendala teknis?','Silakan hubungi kami melalui halaman Kontak atau email bandarudara@dephub.go.id untuk bantuan lebih lanjut.',4);

-- ---------- Regulasi ----------
INSERT INTO `regulation_categories` (`slug`,`label`,`sort`) VALUES
('keselamatan','Keselamatan',1),
('prasarana','Prasarana',2),
('darurat','Pelayanan Darurat',3),
('pengusahaan','Pengusahaan',4),
('lingkungan','Lingkungan',5),
('umum','Umum',6);

INSERT INTO `regulations` (`slug`,`number`,`title`,`year`,`category`,`status`,`date_set`,`source_url`,`sort`) VALUES
('pr-djpu-06-tahun-2026','PR-DJPU 06 Tahun 2026','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 (Manual of Standard CASR Part 139) Volume IV Pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2026,'darurat','berlaku','2026-07-01','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR-DJPU%2006%20Tahun%202026&pencarian-cepat-button=Cari',1),
('pr-djpu-4-tahun-2026','PR-DJPU 4 Tahun 2026','Pedoman Teknis Operasional Peraturan Keselamatan Penerbangan Sipil Bagian 139-30 (Advisory Circular Part 139-30) tentang Sistem Kelistrikan',2026,'prasarana','berlaku','2026-06-22','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR-DJPU%204%20Tahun%202026&pencarian-cepat-button=Cari',2),
('pr-djpu-3-tahun-2026','PR-DJPU 3 Tahun 2026','Pedoman Teknis Operasional Peraturan Keselamatan Penerbangan Sipil Bagian 139-23 tentang Program Pemeliharaan Perkerasan',2026,'prasarana','berlaku','2026-05-19','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR-DJPU%203%20Tahun%202026&pencarian-cepat-button=Cari',3),
('kp-djpu-140-tahun-2026','KP-DJPU 140 Tahun 2026','Penetapan Indikator Kinerja Kegiatan Kantor Unit Penyelenggara Bandar Udara',2026,'umum','berlaku','2026-06-11','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP-DJPU%20140%20Tahun%202026&pencarian-cepat-button=Cari',4),
('km-1038-tahun-2026','KM 1038 Tahun 2026','Penetapan Lokasi Bandar Udara Wanam Baru di Kabupaten Merauke Provinsi Papua Selatan',2026,'umum','berlaku','2026-05-13','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KM%201038%20Tahun%202026&pencarian-cepat-button=Cari',5),
('kp-djpu-74-tahun-2026','KP-DJPU 74 Tahun 2026','Spesifikasi Teknis Pekerjaan Fasilitas Sisi Udara Bandar Udara',2026,'prasarana','berlaku','2026-03-16','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP-DJPU%2074%20Tahun%202026&pencarian-cepat-button=Cari',6),
('kp-djpu-27-tahun-2026','KP-DJPU 27 Tahun 2026','Pengenaan Tarif Penerimaan Negara Bukan Pajak Sebesar 50% terhadap Pelayanan Jasa Kebandarudaraan pada Unit Penyelenggara Bandar Udara',2026,'pengusahaan','berlaku','2026-02-06','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP-DJPU%2027%20Tahun%202026&pencarian-cepat-button=Cari',7),
('km-51-tahun-2026','KM 51 Tahun 2026','Perubahan Nama Bandar Udara Kediri di Kabupaten Kediri Provinsi Jawa Timur Menjadi Bandar Udara Dhoho',2026,'umum','berlaku','2026-02-11','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KM%2051%20Tahun%202026&pencarian-cepat-button=Cari',8),
('kp-djpu-235-tahun-2025','KP-DJPU 235 Tahun 2025','Pengenaan Tarif Penerimaan Negara Bukan Pajak Sebesar 50% terhadap Pelayanan Jasa Kebandarudaraan selama Hari Raya Natal 2025 dan Tahun Baru 2026',2025,'pengusahaan','berlaku','2025-10-10','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP-DJPU%20235%20Tahun%202025&pencarian-cepat-button=Cari',9),
('pr-djpu-10-tahun-2025','PR-DJPU 10 Tahun 2025','Pedoman Pelaporan Perhitungan PCR Perkerasan Prasarana Bandar Udara',2025,'prasarana','berlaku','2025-07-15','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR-DJPU%2010%20Tahun%202025&pencarian-cepat-button=Cari',10),
('pr-djpu-03-tahun-2025','PR-DJPU 03 Tahun 2025','Tata Cara Pengukuran Tingkat Kepuasan Pengguna Jasa dalam Pelayanan Kebandarudaraan',2025,'pengusahaan','berlaku','2025-01-13','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR-DJPU%2003%20Tahun%202025&pencarian-cepat-button=Cari',11),
('km-33-tahun-2024','KM 33 Tahun 2024','Tatanan Kebandarudaraan Nasional',2024,'umum','berlaku','2024-04-02','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KM%2033%20Tahun%202024&pencarian-cepat-button=Cari',12),
('pr-7-djpu-tahun-2024','PR 7 DJPU Tahun 2024','Pedoman Surface Movement Guidance and Control System (SMGCS)',2024,'keselamatan','berlaku','2024-05-29','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR%207%20DJPU%20Tahun%202024&pencarian-cepat-button=Cari',13),
('pm-41-tahun-2023','PM 41 Tahun 2023','Pelayanan Jasa Kebandarudaraan di Bandar Udara',2023,'pengusahaan','berlaku','2023-08-01','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%2041%20Tahun%202023&pencarian-cepat-button=Cari',14),
('pm-40-tahun-2023','PM 40 Tahun 2023','Perubahan atas Peraturan Menteri Perhubungan Nomor PM 39 Tahun 2019 tentang Tatanan Kebandarudaraan Nasional',2023,'umum','berlaku','2023-08-01','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%2040%20Tahun%202023&pencarian-cepat-button=Cari',15),
('pr-21-tahun-2023','PR 21 Tahun 2023','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume I Aerodrome (Bandar Udara) Daratan',2023,'keselamatan','dicabut','2023-10-10','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR%2021%20Tahun%202023&pencarian-cepat-button=Cari',16),
('pr-30-tahun-2022','PR 30 Tahun 2022','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume IV Pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2022,'darurat','dicabut','2022-12-23','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR%2030%20Tahun%202022&pencarian-cepat-button=Cari',17),
('pr-26-tahun-2022','PR 26 Tahun 2022','Pedoman Pengoperasian Peralatan Bantu Pendaratan Visual',2022,'prasarana','berlaku','2022-11-29','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR%2026%20Tahun%202022&pencarian-cepat-button=Cari',18),
('pr-8-tahun-2022','PR 8 Tahun 2022','Panduan Desain Aerodrome — Alat Bantu Visual',2022,'prasarana','berlaku','2022-05-09','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PR%208%20Tahun%202022&pencarian-cepat-button=Cari',19),
('pm-95-tahun-2021','PM 95 Tahun 2021','Peraturan Keselamatan Penerbangan Sipil Bagian 139 tentang Aerodrome',2021,'keselamatan','dicabut','2021-12-24','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%2095%20Tahun%202021&pencarian-cepat-button=Cari',20),
('km-329-tahun-2020','KM 329 Tahun 2020','Pemberlakuan Standar Kompetensi Kerja Nasional Indonesia untuk Ground Operation and Service',2020,'umum','berlaku','2020-12-16','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KM%20329%20Tahun%202020&pencarian-cepat-button=Cari',21),
('kp-113-tahun-2020','KP 113 Tahun 2020','Penetapan Inspektur Keamanan Penerbangan Bidang Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK) dan Salvage Tahun 2020',2020,'darurat','berlaku','2020-06-15','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20113%20Tahun%202020&pencarian-cepat-button=Cari',22),
('kp-326-tahun-2019','KP 326 Tahun 2019','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume I Bandar Udara',2019,'keselamatan','berlaku','2019-12-12','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20326%20Tahun%202019&pencarian-cepat-button=Cari',23),
('km-166-tahun-2019','KM 166 Tahun 2019','Tatanan Kebandarudaraan Nasional',2019,'umum','berlaku','2019-08-26','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KM%20166%20Tahun%202019&pencarian-cepat-button=Cari',24),
('kp-215-tahun-2019','KP 215 Tahun 2019','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume II Tempat Pendaratan dan Lepas Landas Helikopter',2019,'keselamatan','berlaku','2019-08-12','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20215%20Tahun%202019&pencarian-cepat-button=Cari',25),
('kp-156-tahun-2019','KP 156 Tahun 2019','Perubahan Kedua atas Peraturan Direktur Jenderal tentang Kendaraan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2019,'darurat','berlaku','2019-06-10','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20156%20Tahun%202019&pencarian-cepat-button=Cari',26),
('pm-39-tahun-2019','PM 39 Tahun 2019','Tatanan Kebandarudaraan Nasional',2019,'umum','dicabut','2019-05-20','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%2039%20Tahun%202019&pencarian-cepat-button=Cari',27),
('kp-381-tahun-2018','KP 381 Tahun 2018','Perubahan atas Peraturan Direktur Jenderal tentang Kendaraan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2018,'darurat','berlaku','2018-12-05','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20381%20Tahun%202018&pencarian-cepat-button=Cari',28),
('kp-321-tahun-2018','KP 321 Tahun 2018','Perubahan atas Peraturan Direktur Jenderal tentang Petunjuk Teknis Pengawasan Rencana Penanggulangan Keadaan Darurat Bandar Udara dan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2018,'darurat','berlaku','2018-10-19','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20321%20Tahun%202018&pencarian-cepat-button=Cari',29),
('kp-172-tahun-2017','KP 172 Tahun 2017','Petunjuk Teknis Pengawasan Rencana Penanggulangan Keadaan Darurat Bandar Udara (Airport Emergency Plan) dan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2017,'darurat','berlaku','2017-07-18','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20172%20Tahun%202017&pencarian-cepat-button=Cari',30),
('pm-193-tahun-2015','PM 193 Tahun 2015','Konsesi dan Bentuk Kerja Sama Lainnya antara Pemerintah dengan Badan Usaha Bandar Udara untuk Pelayanan Jasa Kebandarudaraan',2015,'pengusahaan','berlaku','2015-12-14','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%20193%20Tahun%202015&pencarian-cepat-button=Cari',31),
('pm-179-tahun-2015','PM 179 Tahun 2015','Perubahan atas Peraturan Menteri tentang Tata Cara dan Prosedur Pengenaan Tarif Jasa Kebandarudaraan',2015,'pengusahaan','dicabut','2015-11-16','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=PM%20179%20Tahun%202015&pencarian-cepat-button=Cari',32),
('kp-547-tahun-2015','KP 547 Tahun 2015','Pedoman Teknis Operasional Peraturan Keselamatan Penerbangan Sipil Bagian 139-25 Kendaraan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2015,'darurat','berlaku','2015-09-14','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20547%20Tahun%202015&pencarian-cepat-button=Cari',33),
('kp-458-tahun-2015','KP 458 Tahun 2015','Sertifikasi Pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK) Bandar Udara',2015,'darurat','berlaku','2015-06-26','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20458%20Tahun%202015&pencarian-cepat-button=Cari',34),
('kp-14-tahun-2015','KP 14 Tahun 2015','Standar Teknis dan Operasi Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume IV Pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2015,'darurat','dicabut','2015-01-26','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%2014%20Tahun%202015&pencarian-cepat-button=Cari',35),
('kp-420-tahun-2011','KP 420 Tahun 2011','Persyaratan Standar Teknis dan Operasional Peraturan Keselamatan Penerbangan Sipil Bagian 139 Volume IV Pelayanan Pertolongan Kecelakaan Penerbangan dan Pemadam Kebakaran (PKP-PK)',2011,'darurat','dicabut','2011-08-24','https://jdih.kemenhub.go.id/peraturan/index?PencarianPeraturanForm%5BkataKunci%5D=KP%20420%20Tahun%202011&pencarian-cepat-button=Cari',36);


-- ---------- Berita ----------
INSERT INTO `news_categories` (`slug`,`label`,`sort`) VALUES
('sertifikasi','Sertifikasi',1),
('infrastruktur','Infrastruktur',2),
('kegiatan','Kegiatan',3),
('regulasi','Regulasi',4),
('kerjasama','Kerja Sama',5),
('pengumuman','Pengumuman',6);

INSERT INTO `news` (`slug`,`title`,`category`,`published_at`,`excerpt`,`content`,`author`,`art_class`,`icon`,`tags`,`is_featured`,`sort`) VALUES
('42-sertifikat-bandar-udara-baru','Direktorat Terbitkan 42 Sertifikat Bandar Udara Baru di Triwulan II','sertifikasi','2026-07-14','Proses sertifikasi dipercepat melalui digitalisasi berkas dan verifikasi lapangan terpadu.',
'Jakarta — Direktorat Bandar Udara mengumumkan penerbitan 42 sertifikat operasi bandar udara baru sepanjang triwulan II tahun 2026. Capaian ini merupakan hasil dari percepatan proses verifikasi berkas dan inspeksi lapangan yang kini didukung sistem informasi layanan digital terpadu.

Menurut catatan internal direktorat, rata-rata waktu penyelesaian layanan sertifikasi menurun dari sebelumnya lebih dari 30 hari kerja menjadi rata-rata 18 hari kerja. Penurunan waktu layanan ini didorong oleh digitalisasi pengajuan berkas serta koordinasi yang lebih efisien antara tim verifikasi dan tim inspeksi lapangan di lima subdirektorat teknis.

Sertifikat yang diterbitkan mencakup bandar udara di berbagai wilayah, termasuk sejumlah bandar udara perintis di kawasan timur Indonesia yang selama ini menghadapi tantangan aksesibilitas dalam proses pengajuan layanan. Direktorat menegaskan komitmennya untuk terus memperluas jangkauan layanan digital hingga ke bandar udara di wilayah terpencil.

Ke depan, direktorat berencana mengintegrasikan sistem pemantauan pascasertifikasi agar kepatuhan standar keselamatan dapat dipantau secara berkelanjutan, bukan hanya pada saat proses penerbitan sertifikat berlangsung.',
'Humas Direktorat Bandar Udara','art-4','bi-patch-check','Sertifikasi, LayananDigital, KeselamatanPenerbangan',1,1),
('perluasan-landasan-pacu-perintis','Perluasan Landasan Pacu di 6 Bandar Udara Perintis Dimulai','infrastruktur','2026-07-08','Program prasarana tahun ini menyasar bandar udara perintis di kawasan timur Indonesia.',
'Program perluasan landasan pacu di enam bandar udara perintis resmi dimulai pada awal Juli 2026. Kegiatan ini merupakan bagian dari prioritas pemerataan prasarana bandar udara di wilayah 3T.

Pekerjaan mencakup perpanjangan dan pelebaran landas pacu, perbaikan marka, serta peningkatan fasilitas alat bantu visual. Seluruh pekerjaan ditargetkan rampung sebelum akhir tahun anggaran berjalan.',
'Humas Direktorat Bandar Udara','art-1','bi-cone-striped','Infrastruktur, Prasarana',1,2),
('simulasi-tanggap-darurat-pkp-pk','Simulasi Tanggap Darurat PKP-PK Digelar Serentak','kegiatan','2026-07-02','Latihan gabungan bertujuan menguji kesiapan tim penyelamatan bandar udara.',
'Simulasi tanggap darurat PKP-PK digelar serentak di sejumlah bandar udara untuk menguji kesiapan personel, peralatan, dan koordinasi antarinstansi dalam menghadapi keadaan darurat penerbangan.

Latihan mencakup skenario kecelakaan pesawat udara di area bandar udara, evakuasi penumpang, serta pemadaman kebakaran sesuai standar waktu respons yang berlaku.',
'Humas Direktorat Bandar Udara','art-5','bi-life-preserver','PKPPK, TanggapDarurat',1,3),
('konsultasi-publik-standar-keselamatan','Konsultasi Publik Standar Keselamatan Landasan Pacu Digelar','regulasi','2026-06-28','Direktorat membuka masukan publik atas rancangan standar keselamatan terbaru.',
'Direktorat Bandar Udara menggelar konsultasi publik atas rancangan standar keselamatan operasional landasan pacu. Kegiatan ini melibatkan penyelenggara bandar udara, akademisi, dan asosiasi penerbangan.

Masukan yang diperoleh akan menjadi bahan penyempurnaan rancangan sebelum ditetapkan sebagai peraturan.',
'Humas Direktorat Bandar Udara','art-3','bi-journal-text','Regulasi, KonsultasiPublik',0,4),
('kerja-sama-teknis-airnav','Kerja Sama Teknis dengan AirNav Perkuat Keselamatan Operasional','kerjasama','2026-06-20','Kolaborasi difokuskan pada integrasi data pemantauan sisi udara dan sisi darat.',
'Direktorat Bandar Udara dan AirNav Indonesia menandatangani kerja sama teknis untuk memperkuat keselamatan operasional bandar udara melalui integrasi data pemantauan sisi udara dan sisi darat.

Kerja sama ini mencakup pertukaran data operasional, pelatihan bersama, serta pengembangan prosedur koordinasi dalam kondisi darurat.',
'Humas Direktorat Bandar Udara','art-7','bi-people-fill','KerjaSama, AirNav',0,5),
('seleksi-tenaga-teknis-2026','Pembukaan Seleksi Tenaga Teknis Bandar Udara 2026','pengumuman','2026-06-15','Pendaftaran dibuka untuk memperkuat kapasitas inspektur keselamatan bandar udara.',
'Direktorat Bandar Udara membuka seleksi tenaga teknis bandar udara tahun 2026. Formasi yang dibuka meliputi inspektur keselamatan, teknisi prasarana, dan analis data kebandarudaraan.

Pendaftaran dilakukan secara daring melalui portal resmi. Seluruh tahapan seleksi tidak dipungut biaya.',
'Humas Direktorat Bandar Udara','art-6','bi-megaphone','Kepegawaian, Pengumuman',0,6),
('rehabilitasi-terminal-wilayah-tengah','Rehabilitasi Terminal Bandar Udara Wilayah Tengah Rampung 70%','infrastruktur','2026-06-05','Renovasi terminal ditargetkan tuntas sebelum akhir tahun anggaran berjalan.',
'Progres rehabilitasi terminal penumpang di sejumlah bandar udara wilayah tengah telah mencapai 70 persen. Pekerjaan meliputi perluasan ruang tunggu, pembaruan sistem pendingin, serta peningkatan aksesibilitas bagi penyandang disabilitas.',
'Humas Direktorat Bandar Udara','art-2','bi-building','Infrastruktur, Terminal',0,7),
('inspeksi-terpadu-kawasan-barat','Inspeksi Terpadu Digelar di 12 Bandar Udara Kawasan Barat','kegiatan','2026-05-28','Inspeksi mencakup pemeriksaan prasarana, peralatan darurat, dan tata kelola operasional.',
'Tim gabungan Direktorat Bandar Udara melaksanakan inspeksi terpadu di 12 bandar udara kawasan barat Indonesia. Pemeriksaan mencakup kondisi prasarana sisi udara, kesiapan peralatan darurat, serta tata kelola operasional bandar udara.',
'Humas Direktorat Bandar Udara','art-8','bi-clipboard-check','Inspeksi, Pengawasan',0,8),
('waktu-layanan-sertifikasi-18-hari','Waktu Layanan Sertifikasi Dipangkas Menjadi Rata-Rata 18 Hari','sertifikasi','2026-05-19','Transformasi digital layanan mempercepat proses verifikasi dan penerbitan sertifikat.',
'Rata-rata waktu layanan sertifikasi bandar udara kini turun menjadi 18 hari kerja. Percepatan ini dicapai melalui digitalisasi pengajuan berkas, verifikasi paralel, serta penjadwalan inspeksi lapangan yang lebih efisien.',
'Humas Direktorat Bandar Udara','art-4','bi-award','Sertifikasi, LayananDigital',0,9);

INSERT INTO `announcements` (`title`,`category`,`published_at`,`url`,`sort`) VALUES
('Pengumuman Seleksi Tenaga Teknis Bandar Udara 2026','Kepegawaian','2026-07-22','pages/berita-detail.php?slug=seleksi-tenaga-teknis-2026',1),
('Undangan Konsultasi Publik Rancangan Standar Baru','Regulasi','2026-07-17','pages/berita-detail.php?slug=konsultasi-publik-standar-keselamatan',2),
('Pengumuman Lelang Pengadaan Peralatan PKP-PK','Pengadaan','2026-07-10','#',3),
('Jadwal Bimbingan Teknis Sertifikasi Bandar Udara','Layanan','2026-07-03','#',4);

-- ---------- Galeri ----------
INSERT INTO `gallery_albums` (`id`,`slug`,`title`,`description`,`art_class`,`icon`,`sort`) VALUES
(1,'peresmian-bandar-udara-baru','Peresmian Bandar Udara Baru','Dokumentasi kegiatan peresmian bandar udara baru yang dihadiri jajaran pimpinan Direktorat Jenderal Perhubungan Udara.','art-1','bi-collection',1),
(2,'rapat-kerja-nasional-2026','Rapat Kerja Nasional 2026','Dokumentasi Rapat Kerja Nasional Direktorat Bandar Udara tahun 2026.','art-7','bi-collection',2),
(3,'inspeksi-keselamatan-triwulan-ii','Inspeksi Keselamatan Triwulan II','Dokumentasi kegiatan inspeksi keselamatan bandar udara triwulan II.','art-6','bi-collection',3),
(4,'pelatihan-pkp-pk-terpadu','Pelatihan PKP-PK Terpadu','Dokumentasi pelatihan terpadu personel PKP-PK bandar udara.','art-5','bi-collection',4),
(5,'kunjungan-kerja-pimpinan','Kunjungan Kerja Pimpinan','Dokumentasi kunjungan kerja pimpinan ke sejumlah bandar udara.','art-2','bi-collection',5),
(6,'penandatanganan-kerja-sama-airnav','Penandatanganan Kerja Sama AirNav','Dokumentasi penandatanganan kerja sama teknis dengan AirNav Indonesia.','art-3','bi-collection',6);

INSERT INTO `gallery_photos` (`album_id`,`title`,`caption`,`art_class`,`icon`,`show_on_home`,`sort`) VALUES
(NULL,'Landasan Pacu','Landasan pacu bandar udara','art-1','bi-airplane-engines',1,1),
(NULL,'Terminal Penumpang','Terminal penumpang','art-2','bi-building',1,2),
(NULL,'Rapat Koordinasi','Rapat koordinasi','art-7','bi-people-fill',1,3),
(NULL,'Inspeksi Bandara','Inspeksi bandar udara','art-6','bi-clipboard-check',1,4),
(NULL,'Simulasi Darurat','Simulasi tanggap darurat','art-5','bi-life-preserver',1,5),
(NULL,'Menara ATC','Menara pengawas ATC','art-3','bi-broadcast-pin',1,6),
(NULL,'Fasilitas Bandara','Fasilitas bandar udara','art-8','bi-signpost-2',1,7),
(NULL,'Sertifikasi','Seremoni sertifikasi','art-4','bi-patch-check',1,8),
(NULL,'Armada PKP-PK','Armada PKP-PK','art-5','bi-truck-front',0,9),
(NULL,'Pelatihan Teknis','Pelatihan teknis','art-6','bi-mortarboard',0,10),
(NULL,'Apron','Apron bandar udara','art-2','bi-grid-3x3-gap',0,11),
(NULL,'Kunjungan Kerja','Kunjungan kerja','art-7','bi-briefcase',0,12),
(1,'Pengguntingan Pita','Prosesi pengguntingan pita peresmian','art-1','bi-scissors',0,1),
(1,'Sambutan Dirjen','Sambutan Direktur Jenderal','art-2','bi-mic',0,2),
(1,'Peninjauan Terminal','Peninjauan terminal baru','art-6','bi-building-check',0,3),
(1,'Penandatanganan Prasasti','Penandatanganan prasasti','art-7','bi-pen',0,4),
(1,'Foto Bersama','Foto bersama tamu undangan','art-4','bi-people-fill',0,5),
(1,'Penerbangan Perdana','Pendaratan penerbangan perdana','art-3','bi-airplane-engines',0,6),
(1,'Suasana Apron','Suasana apron bandar udara','art-8','bi-grid-3x3-gap',0,7),
(1,'Tur Fasilitas','Tur fasilitas bandar udara','art-5','bi-signpost-2',0,8),
(1,'Konferensi Pers','Konferensi pers','art-1','bi-camera',0,9),
(1,'Ruang Tunggu','Ruang tunggu terminal baru','art-2','bi-building',0,10),
(1,'Menara ATC','Menara ATC bandar udara baru','art-3','bi-broadcast-pin',0,11),
(1,'Penutupan Acara','Penutupan acara peresmian','art-6','bi-flag',0,12);

INSERT INTO `gallery_videos` (`title`,`art_class`,`icon`,`sort`) VALUES
('Profil Direktorat Bandar Udara 2026','art-2','bi-camera-reels',1),
('Simulasi Tanggap Darurat PKP-PK','art-5','bi-camera-reels',2),
('Proses Sertifikasi Bandar Udara','art-1','bi-camera-reels',3),
('Pembangunan Prasarana Bandar Udara Perintis','art-6','bi-camera-reels',4),
('Sistem Pengawasan Operasional Bandar Udara','art-3','bi-camera-reels',5),
('Testimoni Mitra Kerja Kebandarudaraan','art-7','bi-camera-reels',6);

-- ---------- Kontak ----------
INSERT INTO `contact_info` (`icon`,`label`,`value`,`sort`) VALUES
('bi-geo-alt-fill','Alamat Kantor','Jl. Medan Merdeka Barat No. 8, Jakarta Pusat 10110, Indonesia',1),
('bi-telephone-fill','Telepon','(021) 350-0000',2),
('bi-envelope-fill','Email','bandarudara@dephub.go.id',3),
('bi-clock-fill','Jam Layanan','Senin–Jumat, 08.00–16.00 WIB',4),
('bi-megaphone-fill','Pengaduan','pengaduan.hubud@dephub.go.id',5);

INSERT INTO `contact_subjects` (`label`,`sort`) VALUES
('Pertanyaan Umum',1),
('Layanan Sertifikasi',2),
('Permohonan Informasi Publik',3),
('Pengaduan',4),
('Kerja Sama',5);
