# Portal Direktorat Bandar Udara

Situs publik + panel CMS untuk mengelola seluruh konten situs Direktorat Bandar
Udara, Ditjen Perhubungan Udara, Kementerian Perhubungan RI.

PHP 8 tanpa framework, basis data MySQL/MariaDB, tanpa langkah build.

| Dokumen | Isi |
|---|---|
| [PRD.md](PRD.md) | Tujuan produk, pengguna, cakupan, dan kriteria selesai |
| [IMPLEMENTATION.md](IMPLEMENTATION.md) | Arsitektur, alur data, dan cara kerja tiap bagian |
| [DESIGN.md](DESIGN.md) | Token warna, komponen, dan aturan tampilan |
| [MEMORY.md](MEMORY.md) | Keputusan yang sudah diambil dan jebakan yang sudah ditemukan |
| [database/DEPLOY.md](database/DEPLOY.md) | Langkah menerapkan pembaruan ke server berjalan |

---

## Struktur Folder

```
dbu-portal/
├── frontend/              Situs publik
│   ├── index.php              Beranda
│   ├── pages/                 17 halaman: profil, organisasi, layanan, regulasi,
│   │                          berita, galeri, kontak, daftar bandar udara …
│   ├── partials/              Header, navbar, footer, peta wilayah, dasbor data
│   ├── submit.php             Penerima formulir kontak / pengajuan / newsletter
│   ├── css/ js/ assets/       Aset tampilan
│   ├── data/peta/             Kawasan KKOP & cakupan per bandara (<ICAO>.json),
│   │                          dibuat database/import-peta-kkop.php
│   └── bootstrap.php          Pemuat awal situs publik  (tidak dilacak git)
│
├── admin/                 Panel CMS
│   ├── login.php              Halaman masuk
│   ├── index.php              Dasbor
│   ├── konten.php             Seluruh isi portal: kategori → sub kategori → konten
│   ├── resource.php           CRUD generik untuk modul di luar konten
│   ├── settings.php           Pengaturan situs
│   ├── inbox.php              Pesan kontak, permohonan layanan, newsletter
│   ├── config/blueprints.php  Susunan kolom formulir tiap jenis seksi
│   ├── config/resources.php   Modul di luar konten: tag, menu, pengguna
│   ├── includes/              Sesi, tata letak, kolom formulir, unggahan
│   └── assets/                CSS & JS panel admin
│
├── shared/                config.php (tidak dilacak git), db.php, content.php, functions.php
├── database/              schema.sql, seed.sql, migrasi, bandaras.sql
├── uploads/               Berkas unggahan
├── legacy/                Arsip situs statis HTML sebelum jadi CMS
├── router.php             Router untuk server bawaan PHP
└── .htaccess              Aturan URL tanpa akhiran .php untuk Apache
```

## Menjalankan (lokal, XAMPP)

Prasyarat: XAMPP dengan PHP 8.x dan MySQL/MariaDB berjalan.

**1. Basis data**

```bash
cd /Applications/XAMPP/xamppfiles/htdocs/dbu-portal
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/schema.sql
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 < database/seed.sql
```

Lalu impor data bandar udara:

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 dbu_cms < database/bandaras.sql
```

Untuk server yang sudah berisi data, jangan jalankan `schema.sql` — ikuti
[database/DEPLOY.md](database/DEPLOY.md).

**2. Server**

```bash
/Applications/XAMPP/xamppfiles/bin/php -S localhost:8088 -t . router.php
```

`router.php` menirukan aturan `.htaccess` sehingga alamat tanpa `.php`
(mis. `/frontend/pages/profil`) tetap bekerja di server bawaan PHP. Tanpa berkas
itu, halaman hanya bisa dibuka dengan akhiran `.php`.

**3. Buka**

| Alamat | Keterangan |
|---|---|
| http://localhost:8088/frontend/ | Situs publik |
| http://localhost:8088/admin/ | Panel CMS |

**Akun awal:** `admin` / `admin123` — **ganti kata sandi ini** lewat menu
*Sistem → Pengguna* setelah masuk pertama kali.

## Konfigurasi

`shared/config.php` membaca variabel lingkungan lebih dulu, jadi tidak perlu
menyunting berkasnya:

| Variabel | Bawaan |
|---|---|
| `DBU_DB_HOST` | `127.0.0.1` |
| `DBU_DB_PORT` | `3306` |
| `DBU_DB_NAME` | `dbu_cms` |
| `DBU_DB_USER` | `root` |
| `DBU_DB_PASS` | *(kosong)* |
| `DBU_BASE_URL` | *(kosong)* — awalan URL bila proyek berada di sub-folder |
| `DBU_DEBUG` | `0` — set `1` untuk menampilkan galat saat pengembangan |

Bila situs dipasang di sub-folder (mis. `http://localhost/dbu/`), jalankan dengan
`DBU_BASE_URL=/dbu`.

`shared/config.php` dan `frontend/bootstrap.php` **tidak dilacak git** karena
berisi penyesuaian per server. Keduanya harus dibuat manual di server baru.

## Cakupan CMS

Seluruh isi halaman portal dikelola lewat satu menu **Halaman Portal** di panel,
dengan susunan yang sama seperti basis datanya:

| Lapis | Tabel | Isi |
|---|---|---|
| Kategori | `categories` | Satu halaman portal — Beranda, Profil, Layanan, … (11) |
| Sub kategori | `subcategories` | Satu seksi di dalam halaman — Slider Utama, Statistik, … (61) |
| Konten | `contents` | Isi seksi, beserta urutan tampilnya (525) |
| Tag | `tags` | Penanda lintas halaman |

Tiap seksi punya tombol **Tambah Konten**, dan urutan tampil di portal — baik
urutan seksi maupun urutan konten di dalamnya — diatur dari daftar yang sama.
Seksi dan halaman baru pun dapat dibuat sendiri; seksi yang tidak dikenal templat
tetap tampil di portal lewat `frontend/partials/seksi-tambahan.php`.

Kolom formulir tiap jenis seksi diatur `admin/config/blueprints.php`. Di luar
konten hanya tersisa tiga modul: **Tag**, **Menu Navigasi**, dan **Pengguna**.

**Di luar cakupan CMS** — markup dan datanya tetap di berkas:

- **Daftar Bandar Udara** (`frontend/pages/informasi-publik.php`) — datanya dari
  tabel `bandaras`, diperbarui lewat impor SQL, bukan lewat panel.
- **Lapisan KKOP & cakupan** pada peta halaman detail bandar udara — GeoJSON di
  `frontend/data/peta/`, dibuat ulang dengan `database/import-peta-kkop.php`.
- **Dasbor Data Kebandarudaraan** di beranda dan partial `ip-*.php` — grafiknya
  digambar oleh `frontend/js/main.js`.

## Ketergantungan Luar

Situs tidak memakai package manager. Tiga aset diambil dari CDN saat halaman
dibuka:

| Aset | Dipakai di |
|---|---|
| Bootstrap Icons 1.11.3 | seluruh halaman |
| Leaflet 1.9.4 | Daftar Bandar Udara |
| Leaflet.markercluster 1.5.3 | Daftar Bandar Udara |

Ubin peta diambil dari `tile.openstreetmap.org`. Server yang tertutup dari
internet perlu meng-host sendiri berkas-berkas ini.

## Menambah Konten Baru

Lewat panel: **Halaman Portal → pilih halaman → Tambah Seksi**, lalu **Tambah
Konten** di seksi itu. Tidak ada perubahan basis data maupun kode yang
diperlukan — seksi baru langsung tampil di portal mengikuti urutan yang diatur
di panel.

Bila seksi barunya butuh kolom khusus (mis. dua tombol, atau label berwarna),
tambahkan satu cetak biru di `admin/config/blueprints.php`; kolom di luar
`KOLOM_KONTEN` tersimpan otomatis sebagai JSON pada kolom `extra`, tanpa
mengubah tabel. Rinciannya di [IMPLEMENTATION.md](IMPLEMENTATION.md).
