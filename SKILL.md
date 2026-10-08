---
name: jalankan-dbu-portal
description: Menjalankan dan memeriksa Portal Direktorat Bandar Udara (PHP + MySQL di XAMPP) — menyalakan server, uji asap seluruh halaman, tangkapan layar headless, dan menjalankan migrasi basis data. Pakai saat diminta menjalankan aplikasi, membuka situs/panel admin, memastikan sebuah perubahan benar-benar bekerja, atau menerapkan perubahan basis data.
---

# Menjalankan Portal Direktorat Bandar Udara

Situs PHP 8 tanpa framework + MySQL/MariaDB, dijalankan di atas XAMPP.
Tidak ada langkah build, tidak ada package manager.

Semua perkakas dipanggil lewat jalur penuh XAMPP — `php` dan `mysql` **tidak
ada** di PATH:

```
/Applications/XAMPP/xamppfiles/bin/php
/Applications/XAMPP/xamppfiles/bin/mysql
```

## 1. Pastikan basis data hidup

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 -e "SHOW DATABASES;"
```

Harus ada `dbu_cms`. Bila MySQL mati, nyalakan lewat XAMPP Control Panel.
Sambungan bawaan: `root` tanpa kata sandi di `127.0.0.1:3306`.

## 2. Nyalakan server

```bash
/Applications/XAMPP/xamppfiles/bin/php -S localhost:8088 -t . router.php
```

Jalankan di latar belakang.

**`router.php` wajib disertakan.** Berkas itu menirukan aturan `.htaccess`
sehingga alamat tanpa akhiran `.php` (`/frontend/pages/profil`) tetap bekerja.
Tanpa itu semua tautan antar halaman rusak.

Bila muncul `Failed to listen on localhost:8088 (Address already in use)`,
server sudah menyala dari sesi lain — **jangan** langsung menyalakan ulang.
Periksa dulu apakah server yang ada memang melayani proyek ini:

```bash
lsof -nP -iTCP:8088 -sTCP:LISTEN          # dapatkan PID
lsof -a -p <PID> -d cwd -Fn               # pastikan cwd-nya folder proyek ini
```

Kalau ya, pakai saja. Kalau bukan, hentikan lalu nyalakan ulang.

| Alamat | Isi |
|---|---|
| http://localhost:8088/frontend/ | Situs publik |
| http://localhost:8088/admin/login.php | Panel CMS (`admin` / `admin123`) |

## 3. Uji asap

Setelah perubahan apa pun, jalankan ini — semuanya harus `200`, kecuali
`tugas-fungsi` yang memang `301`:

```bash
for u in /frontend/ /frontend/pages/profil /frontend/pages/organisasi \
         /frontend/pages/informasi-publik /frontend/pages/dokumen-publik \
         "/frontend/pages/bandara-detail?id=9" /frontend/pages/regulasi \
         /frontend/pages/berita /frontend/pages/galeri /frontend/pages/layanan \
         /frontend/pages/kontak /frontend/pages/tugas-fungsi /admin/login.php; do
  printf "%-46s %s\n" "$u" "$(curl -s -o /dev/null -w '%{http_code}' "http://localhost:8088$u")"
done
```

Halaman per subdirektorat butuh parameter `unit`; tanpa itu memang `404`:

```bash
for u in std tatanan prasarana darurat sistem; do
  curl -s -o /dev/null -w "$u %{http_code}\n" \
    "http://localhost:8088/frontend/pages/informasi-publik-subdit?unit=$u"
done
```

Periksa sintaks setiap berkas yang disunting:

```bash
/Applications/XAMPP/xamppfiles/bin/php -l frontend/pages/nama.php
```

## 4. Lihat hasilnya di peramban

HTTP 200 **tidak membuktikan** tampilannya benar. Untuk perubahan yang terlihat,
ambil tangkapan layar:

```bash
SHOT=/tmp/uji.png
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
  --headless --disable-gpu --hide-scrollbars \
  --window-size=1440,1700 --virtual-time-budget=12000 \
  --screenshot="$SHOT" "http://localhost:8088/frontend/pages/informasi-publik" 2>/dev/null
```

Lalu **buka berkas PNG-nya dan lihat**. Layar putih berarti gagal muat.

`--virtual-time-budget` wajib untuk halaman peta — Leaflet dan ubin OSM perlu
waktu memuat dari internet.

### Melihat bagian yang tersembunyi

Panel yang baru muncul setelah diklik (detail organisasi, panel wilayah, kartu
hover peta) tidak terlihat pada tangkapan biasa. Ambil HTML-nya, suntikkan CSS
atau skrip, simpan **di dalam folder proyek** supaya jalur aset relatifnya tetap
benar, lalu hapus setelah selesai:

```bash
OUT=frontend/_tmp-preview.html
curl -s 'http://localhost:8088/frontend/pages/organisasi' \
  | sed 's|</head>|<style>.org-detail{display:block!important}</style></head>|' > "$OUT"
# … ambil tangkapan layar dari http://localhost:8088/frontend/_tmp-preview.html …
rm -f "$OUT"
```

Untuk menguji hover pada marker peta, picu event DOM-nya:

```bash
sed 's|</body>|<script>setTimeout(function(){document.querySelector("path.leaflet-interactive").dispatchEvent(new MouseEvent("mouseover",{bubbles:true}));},5000);</script></body>|'
```

**Jangan** membuat berkas pratinjau `.php` yang mengambil URL dirinya sendiri —
server bawaan PHP hanya satu proses dan permintaan itu akan menggantung. Selalu
ambil HTML-nya lewat `curl` lebih dulu, simpan sebagai `.html`.

### Memeriksa galat JavaScript

```bash
"/Applications/Google Chrome.app/Contents/MacOS/Google Chrome" \
  --headless --disable-gpu --enable-logging=stderr --v=0 \
  --virtual-time-budget=10000 --dump-dom "http://localhost:8088/frontend/pages/informasi-publik" \
  2>err.log >dom.html
grep -i "error\|exception" err.log \
  | grep -vi "gcm\|registration\|cv_display\|web_applications\|DEPRECATED"
```

Pola yang disaring itu derau bawaan Chrome di macOS, bukan galat aplikasi.

## 5. Perubahan basis data

Jangan pernah mengubah basis data langsung tanpa jejak. Tulis berkas migrasi di
`database/`, jalankan, lalu daftarkan di `database/DEPLOY.md`.

```bash
/Applications/XAMPP/xamppfiles/bin/mysql -u root -h 127.0.0.1 dbu_cms \
  < database/migrate-nama.sql
```

Aturan migrasi:

- Aman dijalankan berulang — `ADD COLUMN IF NOT EXISTS`,
  `ON DUPLICATE KEY UPDATE`, `UPDATE … WHERE`.
- Baris `USE` dibiarkan sebagai komentar; basis data dipilih lewat perintah.
- Daftarkan di `database/DEPLOY.md`: perintah menjalankannya, kueri pemeriksa
  "sudah / BELUM", dan apa yang akan ditimpa.

**Jangan jalankan `database/schema.sql`** pada basis data berisi data — berkas
itu berisi `DROP TABLE` untuk setiap tabel.

Menambah seksi atau kolom konten **tidak** memerlukan migrasi: seksi dibuat dari
panel (*Halaman Portal → Tambah Seksi*), dan kolom khusus satu seksi cukup
ditambahkan sebagai cetak biru di `admin/config/blueprints.php` — nilainya
tersimpan pada kolom JSON `contents`.`extra`.

`database/bandaras.sql` juga diawali `DROP TABLE bandaras`. Aman dijalankan
ulang, tetapi seluruh isi tabel itu tergantikan.

## 6. Hal yang mudah menjebak

- `shared/config.php` dan `frontend/bootstrap.php` **tidak dilacak git**.
  Perubahan di sana tidak akan sampai ke server. Kait baru untuk halaman
  diletakkan di berkas yang dilacak, mis. `$GLOBALS['dbu_head']` yang dibaca
  `frontend/partials/header.php`.
- Berkas `.DS_Store` sempat terlacak git dan menghalangi rebase. Sekarang sudah
  masuk `.gitignore`.
- Setelah menyunting `frontend/css/style.css` atau `frontend/js/*.js`, muat
  ulang dengan cache dikosongkan bila tampilannya belum berubah.
- Selalu hapus berkas `frontend/_tmp-*` setelah selesai — jangan sampai ikut
  ter-commit.

## 7. Bacaan lanjutan

| Berkas | Isi |
|---|---|
| `README.md` | Pemasangan dan struktur folder |
| `IMPLEMENTATION.md` | Arsitektur dan cara kerja tiap bagian |
| `MEMORY.md` | Keputusan yang sudah diambil dan jebakan yang sudah ditemukan |
| `DESIGN.md` | Token warna dan komponen tampilan |
| `database/DEPLOY.md` | Menerapkan pembaruan ke server berjalan |
