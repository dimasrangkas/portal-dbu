-- ============================================================
--  MIGRASI: penyesuaian halaman detail bandar udara dengan
--  susunan hubud.kemenhub.go.id/bandara/<id>.
--
--  Dua hal yang dibutuhkan tampilan baru:
--
--  1. Kolom cuaca dan microsite pada `bandaras`. Pada situs resmi,
--     cuaca diambil langsung dari API BMKG dan microsite menunjuk ke
--     halaman OBU/UPBU. Keduanya tidak ada di berkas
--     bandara_part_*.json, jadi di sini disediakan kolomnya saja —
--     diisi operator lewat menu admin, atau kelak oleh penyelaras
--     otomatis. Selama kosong, halaman menampilkan "—".
--
--  2. Perbaikan nama ikon `bi-smog`. Ikon itu tidak ada di Bootstrap
--     Icons 1.11.3, sehingga ubin "Area Merokok" pada tab Fasilitas
--     Umum tampil sebagai lingkaran kosong. Diganti `bi-cloud-haze2`.
--     database/import-bandara-json.php sudah memakai nama yang benar,
--     jadi impor ulang tidak mengembalikan nama lama.
--
--  Aman dijalankan berulang. Tanpa stored procedure — lihat catatan
--  pada migrate-detail-bandara.sql soal mysql.proc di XAMPP.
--
--  Jalankan setelah migrate-detail-bandara-lanjutan.sql:
--    mysql -u USER -p NAMA_DB < database/migrate-bandara-detail-ui.sql
-- ============================================================
SET NAMES utf8mb4;
SET SESSION group_concat_max_len = 8192;

-- ---------- 1. Kolom cuaca & microsite pada `bandaras` ----------
SET @kolom := (
  SELECT GROUP_CONCAT(CONCAT('ADD COLUMN `', d.nama, '` ', d.definisi) SEPARATOR ', ')
    FROM (
              SELECT 'microsite_url'    AS nama, 'VARCHAR(300) NULL COMMENT ''Tautan microsite OBU/UPBU''' AS definisi
    UNION ALL SELECT 'cuaca_suhu',              'SMALLINT NULL COMMENT ''Derajat Celsius, mis. 32'''
    UNION ALL SELECT 'cuaca_kondisi',           'VARCHAR(60) NULL COMMENT ''Mis. Cerah, Berawan, Hujan Ringan'''
    UNION ALL SELECT 'cuaca_diperbarui',        'DATETIME NULL COMMENT ''Kapan angka cuaca terakhir diperbarui'''
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

-- ---------- 2. Ikon fasilitas umum yang tidak ada di Bootstrap Icons ----------
-- Hanya menyentuh baris yang masih memakai nama lama, jadi ikon yang
-- sudah disunting operator tidak ikut tertimpa.
UPDATE `bandara_fasilitas_umum` SET `icon` = 'bi-cloud-haze2' WHERE `icon` = 'bi-smog';
