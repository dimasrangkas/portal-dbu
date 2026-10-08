-- ============================================================
--  MIGRASI: daftar bandar udara per Otoritas Bandar Udara
--  + penyesuaian kantor pusat wilayah (sumber: rekapitulasi PEROTBAN).
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-region-airports.sql
-- ============================================================
SET NAMES utf8mb4;
-- USE `dbu_cms`;   <- nama basis data di server sering berbeda.
--                    Pilih basis datanya saat menjalankan, contoh:
--                    mysql -u USER -p NAMA_DB < migrate-region-airports.sql

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

