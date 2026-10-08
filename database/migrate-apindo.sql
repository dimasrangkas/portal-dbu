-- ============================================================
--  MIGRASI: Penyeragaman nama pengelola pada peta wilayah beranda.
--  (AP I) dan (AP II) menjadi (APINDO) — Angkasa Pura Indonesia,
--  mengikuti penggabungan kedua BUMN itu menjadi satu perusahaan.
--  Kepanjangan APINDO ditampilkan sebagai keterangan pada panel
--  daftar bandar udara (frontend/partials/peta-wilayah.php).
--  Aman dijalankan berulang.
--  Jalankan: mysql -u root -h 127.0.0.1 dbu_cms < database/migrate-apindo.sql
-- ============================================================
SET NAMES utf8mb4;
-- USE `dbu_cms`;   <- nama basis data di server sering berbeda.
--                    Pilih basis datanya saat menjalankan, contoh:
--                    mysql -u USER -p NAMA_DB < migrate-apindo.sql

-- Tanda kurung ikut dicocokkan agar nama seperti "UPBU Tanjung Api, Ampana"
-- tidak ikut terubah.
UPDATE `region_airports` SET `name` = REPLACE(`name`, '(AP II)', '(APINDO)') WHERE `name` LIKE '%(AP II)%';
UPDATE `region_airports` SET `name` = REPLACE(`name`, '(AP I)',  '(APINDO)') WHERE `name` LIKE '%(AP I)%';

-- Bila ada penulisan panjang yang tersisa.
UPDATE `region_airports`
   SET `name` = REPLACE(REPLACE(`name`, '(Angkasa Pura I)', '(APINDO)'), '(Angkasa Pura II)', '(APINDO)')
 WHERE `name` LIKE '%(Angkasa Pura I)%' OR `name` LIKE '%(Angkasa Pura II)%';
