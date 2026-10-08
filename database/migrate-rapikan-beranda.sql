-- ============================================================
--  Merapikan beranda
--
--  1. Tiga kartu Akses Cepat dinonaktifkan: Layanan Publik,
--     Pengaduan, dan Pengumuman. Seksinya tetap ada dengan lima
--     kartu sisanya.
--  2. Tiga seksi dinonaktifkan seluruhnya: Layanan Unggulan,
--     Mitra Kerja, dan Dapatkan Info Terbaru (newsletter).
--
--  Barisnya sengaja dinonaktifkan, bukan dihapus — operator masih
--  bisa menyalakannya kembali lewat panel bila suatu saat
--  diperlukan, dan tidak ada isi yang hilang.
--
--  Aman dijalankan ulang.
-- ============================================================

-- USE `dbu_cms`;

/* ---- 1. Kartu Akses Cepat ---- */
UPDATE `contents` k
  JOIN `subcategories` s ON s.id = k.subcategory_id
  JOIN `categories`    c ON c.id = s.category_id
   SET k.is_active = 0
 WHERE c.slug = 'home'
   AND s.slug = 'quick'
   AND k.title IN ('Layanan Publik', 'Pengaduan', 'Pengumuman');

/* ---- 2. Seksi beranda ---- */
UPDATE `subcategories` s
  JOIN `categories` c ON c.id = s.category_id
   SET s.is_active = 0
 WHERE c.slug = 'home'
   AND s.slug IN ('services', 'partners', 'newsletter');

/* ---- Laporan ---- */
SELECT 'kartu akses cepat nonaktif' AS bagian, COUNT(*) AS jumlah
  FROM `contents` k
  JOIN `subcategories` s ON s.id = k.subcategory_id
  JOIN `categories`    c ON c.id = s.category_id
 WHERE c.slug = 'home' AND s.slug = 'quick' AND k.is_active = 0
UNION ALL
SELECT 'kartu akses cepat tampil', COUNT(*)
  FROM `contents` k
  JOIN `subcategories` s ON s.id = k.subcategory_id
  JOIN `categories`    c ON c.id = s.category_id
 WHERE c.slug = 'home' AND s.slug = 'quick' AND k.is_active = 1
UNION ALL
SELECT 'seksi beranda nonaktif', COUNT(*)
  FROM `subcategories` s
  JOIN `categories` c ON c.id = s.category_id
 WHERE c.slug = 'home' AND s.is_active = 0;
