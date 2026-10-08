<?php
/* ============================================================
   CETAK BIRU KOLOM KONTEN

   Basis data hanya mengenal kategori, sub kategori, dan tag —
   seluruh konten portal ada di satu tabel `contents`. Berkas ini
   menentukan kolom mana yang ditawarkan panel CMS untuk tiap jenis
   seksi, supaya editor tidak dihadapkan pada 20 kolom kosong.

   Nama kolom yang termasuk KOLOM_KONTEN disimpan sebagai kolom tabel;
   selebihnya otomatis masuk ke kolom JSON `extra`.

   Sub kategori baru yang dibuat lewat CMS memakai cetak biru 'umum'.
   ============================================================ */
require_once __DIR__ . '/fields-umum.php';

/** Kolom asli tabel `contents` — sisanya disimpan di JSON `extra`. */
const KOLOM_KONTEN = [
    'slug', 'eyebrow', 'title', 'subtitle', 'excerpt', 'body', 'image', 'file', 'url',
    'icon', 'art_class', 'published_at', 'sort', 'is_featured', 'is_active', 'parent_id',
];

/* Kolom yang muncul di hampir semua cetak biru. */
$AKHIR = ['sort' => f_sort(), 'is_active' => f_active()];

return [

    /* ---------- Umum: dipakai sub kategori buatan sendiri ---------- */
    'umum' => [
        'list'   => ['title' => 'Judul', 'excerpt' => 'Ringkasan'],
        'fields' => [
            'title'   => ['label' => 'Judul', 'type' => 'text', 'required' => true],
            'excerpt' => ['label' => 'Ringkasan', 'type' => 'textarea', 'hint' => 'Teks pendek pada kartu atau daftar'],
            'body'    => ['label' => 'Isi', 'type' => 'richtext', 'hint' => 'Pisahkan paragraf dengan baris kosong'],
            'url'     => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
            'icon'    => f_icon(),
            'image'   => ['label' => 'Gambar', 'type' => 'image'],
        ] + $AKHIR,
    ],

    /* ---------- Judul seksi saja ---------- */
    'judul' => [
        'hanya_judul' => true,
        'list'   => ['title' => 'Judul'],
        'fields' => [
            'title' => ['label' => 'Judul', 'type' => 'text', 'required' => true],
            'body'  => ['label' => 'Isi', 'type' => 'richtext'],
        ] + $AKHIR,
    ],

    /* ---------- Beranda ---------- */
    'slide' => [
        'list'   => ['title' => 'Judul', 'eyebrow' => 'Label Atas'],
        'fields' => [
            'eyebrow' => ['label' => 'Label Atas (eyebrow)', 'type' => 'text', 'col' => 'half'],
            'title'   => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'body'    => ['label' => 'Deskripsi', 'type' => 'textarea'],
        ] + f_visual('art-1') + [
            'btn1_label' => ['label' => 'Tombol 1 — Teks', 'type' => 'text', 'col' => 'half'],
            'btn1_url'   => ['label' => 'Tombol 1 — Tautan', 'type' => 'text', 'col' => 'half'],
            'btn2_label' => ['label' => 'Tombol 2 — Teks', 'type' => 'text', 'col' => 'half'],
            'btn2_url'   => ['label' => 'Tombol 2 — Tautan', 'type' => 'text', 'col' => 'half'],
        ] + $AKHIR,
    ],

    'statistik' => [
        'list'   => ['title' => 'Label', 'value' => 'Nilai'],
        'fields' => [
            'title'  => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'value'  => ['label' => 'Nilai Angka', 'type' => 'number', 'required' => true, 'col' => 'half'],
            'suffix' => ['label' => 'Akhiran', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: +'],
        ] + $AKHIR,
    ],

    'sambutan' => [
        'tunggal' => true,
        'list'    => ['title' => 'Judul', 'director_name' => 'Nama Direktur'],
        'fields'  => [
            'eyebrow'           => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'             => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'body'              => ['label' => 'Isi Sambutan', 'type' => 'richtext', 'hint' => 'Pisahkan paragraf dengan baris kosong'],
            'director_name'     => ['label' => 'Nama Direktur', 'type' => 'text', 'col' => 'half'],
            'director_position' => ['label' => 'Jabatan', 'type' => 'text', 'col' => 'half'],
        ] + f_visual('art-2') + [
            'btn_label' => ['label' => 'Tombol — Teks', 'type' => 'text', 'col' => 'half'],
            'btn_url'   => ['label' => 'Tombol — Tautan', 'type' => 'text', 'col' => 'half'],
        ],
    ],

    'kartu' => [
        'list'   => ['title' => 'Judul', 'body' => 'Deskripsi'],
        'fields' => [
            'icon'  => f_icon(),
            'title' => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'body'  => ['label' => 'Deskripsi', 'type' => 'textarea'],
            'url'   => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
        ] + $AKHIR,
    ],

    'tautan' => [
        'list'   => ['title' => 'Label', 'url' => 'Tautan'],
        'fields' => [
            'icon'  => f_icon(),
            'title' => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'   => ['label' => 'Tautan', 'type' => 'text', 'required' => true, 'col' => 'half'],
        ] + $AKHIR,
    ],

    'wilayah' => [
        'anak'   => ['bandara-wilayah'],
        'list'   => ['numeral' => 'Wilayah', 'title' => 'Nama', 'hq' => 'Kantor Pusat'],
        'fields' => [
            'slug'           => ['label' => 'Kode Wilayah', 'type' => 'text', 'required' => true, 'col' => 'half',
                                 'hint'  => 'Terhubung ke bentuk peta SVG — jangan diubah'],
            'numeral'        => ['label' => 'Angka Romawi', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'title'          => ['label' => 'Nama Wilayah', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'hq'             => ['label' => 'Kantor Pusat (lengkap)', 'type' => 'text', 'col' => 'half'],
            'hq_short'       => ['label' => 'Kantor Pusat (ringkas)', 'type' => 'text', 'col' => 'half', 'hint' => 'Untuk daftar legenda peta'],
            'coverage'       => ['label' => 'Cakupan Provinsi', 'type' => 'textarea'],
            'coverage_short' => ['label' => 'Cakupan (ringkas)', 'type' => 'textarea', 'hint' => 'Untuk daftar legenda peta'],
        ] + $AKHIR,
    ],

    'rpjmn' => [
        'anak'   => ['area-rpjmn'],
        'list'   => ['title' => 'Bagian', 'eyebrow' => 'Label Atas'],
        'fields' => [
            'title'   => ['label' => 'Judul Bagian', 'type' => 'text', 'required' => true],
            'eyebrow' => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Prioritas Nasional 3'],
            'icon'    => f_icon(),
            'body'    => ['label' => 'Uraian', 'type' => 'textarea', 'rows' => 5,
                          'hint'  => 'Isi utama bagian: cakupan program dan daftar bandar udara'],
            'focus'   => ['label' => 'Catatan Fokus', 'type' => 'textarea',
                          'hint'  => 'Tampil dengan latar biru — mis. kawasan/DPP yang didukung'],
            'note'    => ['label' => 'Catatan Tambahan', 'type' => 'textarea',
                          'hint'  => 'Tampil dengan latar abu — mis. pertimbangan teknis atau status kesiapan'],
        ] + $AKHIR,
    ],

    'video' => [
        'tunggal' => true,
        'list'    => ['title' => 'Judul', 'url' => 'URL Video'],
        'fields'  => [
            'eyebrow' => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'   => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'url'     => ['label' => 'URL Video', 'type' => 'text', 'hint' => 'Tautan YouTube/Vimeo. Kosongkan untuk memakai gambar placeholder.'],
            'excerpt' => ['label' => 'Keterangan', 'type' => 'text'],
        ] + f_visual('art-2'),
    ],

    'mitra' => [
        'list'   => ['title' => 'Nama Mitra', 'url' => 'Tautan'],
        'fields' => [
            'title' => ['label' => 'Nama Mitra', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'   => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
            'image' => ['label' => 'Logo', 'type' => 'image', 'hint' => 'Kosongkan untuk menampilkan nama sebagai teks'],
        ] + $AKHIR,
    ],

    /* ---------- Profil ---------- */
    'tentang' => [
        'tunggal' => true,
        'list'    => ['title' => 'Judul'],
        'fields'  => [
            'eyebrow' => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'   => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'body'    => ['label' => 'Uraian Tentang Direktorat', 'type' => 'richtext'],
        ] + f_visual('art-6') + [
            'visi' => ['label' => 'Visi', 'type' => 'textarea', 'hint' => 'Tampil pada seksi Visi di halaman Profil'],
        ],
    ],

    'linimasa' => [
        'list'   => ['year' => 'Tahun', 'title' => 'Judul'],
        'fields' => [
            'year'  => ['label' => 'Tahun', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'title' => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'body'  => ['label' => 'Keterangan', 'type' => 'textarea'],
        ] + $AKHIR,
    ],

    'teks' => [
        'list'   => ['body' => 'Isi'],
        'fields' => [
            'icon' => f_icon(),
            'body' => ['label' => 'Isi', 'type' => 'textarea', 'rows' => 4, 'required' => true],
        ] + $AKHIR,
    ],

    'ikon' => [
        'list'   => ['title' => 'Judul'],
        'fields' => [
            'icon'  => f_icon(),
            'title' => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
        ] + $AKHIR,
    ],

    /* ---------- Organisasi ---------- */
    'unit' => [
        'anak'   => ['fungsi-unit'],
        'list'   => ['title' => 'Unit Kerja', 'slug' => 'Kode'],
        'fields' => [
            'slug'    => ['label' => 'Kode Unit', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half',
                          'hint'  => 'Huruf kecil tanpa spasi, harus unik'],
            'icon'    => f_icon(),
            'title'   => ['label' => 'Nama Unit', 'type' => 'text', 'required' => true],
            'excerpt' => ['label' => 'Deskripsi Singkat', 'type' => 'textarea'],
            'body'    => ['label' => 'Tugas', 'type' => 'richtext', 'rows' => 5,
                          'hint'  => 'Tampil pada kartu unit di halaman Organisasi'],
            'head_name'          => ['label' => 'Nama Kepala Subdirektorat', 'type' => 'text', 'col' => 'half',
                                     'hint'  => 'Kosongkan untuk unit tanpa pejabat tunggal.'],
            'head_position'      => ['label' => 'Jabatan Kepala Subdirektorat', 'type' => 'text', 'col' => 'half'],
            'head_photo'         => ['label' => 'Foto Kepala Subdirektorat', 'type' => 'image',
                                     'hint'  => 'Pasfoto. Bila kosong, kartu menampilkan ikon orang.'],
            'team_lead_name'      => ['label' => 'Nama Kepala Tim 1', 'type' => 'text', 'col' => 'half'],
            'team_lead_position'  => ['label' => 'Jabatan Kepala Tim 1', 'type' => 'text', 'col' => 'half'],
            'team_lead_photo'     => ['label' => 'Foto Kepala Tim 1', 'type' => 'image',
                                      'hint'  => 'Tampil di bawah kepala subdirektorat pada panel unit.'],
            'team_lead2_name'     => ['label' => 'Nama Kepala Tim 2', 'type' => 'text', 'col' => 'half'],
            'team_lead2_position' => ['label' => 'Jabatan Kepala Tim 2', 'type' => 'text', 'col' => 'half'],
            'team_lead2_photo'    => ['label' => 'Foto Kepala Tim 2', 'type' => 'image'],
            'chips'              => ['label' => 'Kata Kunci', 'type' => 'text', 'hint' => 'Pisahkan dengan koma'],
            'branch_class'       => ['label' => 'Posisi Bagan', 'type' => 'select', 'col' => 'half',
                                     'options' => ['org-branch' => 'Cabang Utama', 'org-branch2' => 'Cabang Kedua']],
        ] + $AKHIR,
    ],

    /* ---------- Layanan ---------- */
    'layanan' => [
        'anak'    => ['persyaratan', 'tahap', 'unduhan'],
        'pratinjau' => 'pages/layanan-detail.php?slug={slug}',
        'cari'    => ['title', 'slug'],
        'list'    => ['title' => 'Nama Layanan', 'klasifikasi' => 'Klasifikasi'],
        'fields'  => [
            'title'             => ['label' => 'Nama Layanan', 'type' => 'text', 'required' => true],
            'slug'              => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half'],
            'unit_pengelola'    => ['label' => 'Unit Pengelola', 'type' => 'text', 'col' => 'half', 'default' => 'Direktorat Bandar Udara'],
            'icon'              => f_icon(),
            'unit_slug'         => ['label' => 'Subdirektorat Pengelola', 'type' => 'select', 'col' => 'half', 'empty' => '— Tidak terhubung —',
                                    'options_konten' => 'organisasi/unit', 'options_nilai' => 'slug',
                                    'hint' => 'Dipakai halaman Informasi Publik per subdirektorat'],
            'sifat'             => ['label' => 'Sifat Layanan', 'type' => 'text', 'col' => 'half'],
            'sifat_badge'       => ['label' => 'Warna Label Sifat', 'type' => 'select', 'options' => BADGE_CLASSES, 'default' => 'badge-success', 'col' => 'half'],
            'klasifikasi'       => ['label' => 'Klasifikasi', 'type' => 'text', 'col' => 'half'],
            'klasifikasi_badge' => ['label' => 'Warna Label Klasifikasi', 'type' => 'select', 'options' => BADGE_CLASSES, 'default' => 'badge-gray', 'col' => 'half'],
            'chip_label'        => ['label' => 'Label Chip Halaman Detail', 'type' => 'text', 'col' => 'half'],
            'body'              => ['label' => 'Deskripsi Layanan', 'type' => 'richtext'],
            'time_process'      => ['label' => 'Waktu Proses', 'type' => 'text', 'col' => 'half'],
            'cost'              => ['label' => 'Biaya', 'type' => 'text', 'col' => 'half'],
            'validity'          => ['label' => 'Masa Berlaku', 'type' => 'text', 'col' => 'half'],
            'method'            => ['label' => 'Metode', 'type' => 'text', 'col' => 'half'],
            'cta_label'         => ['label' => 'Tombol CTA — Teks', 'type' => 'text', 'col' => 'half'],
            'cta_url'           => ['label' => 'Tombol CTA — Tautan', 'type' => 'text', 'col' => 'half'],
        ] + $AKHIR,
    ],

    'tahapan' => [
        'list'   => ['title' => 'Tahap', 'body' => 'Keterangan'],
        'fields' => [
            'title' => ['label' => 'Nama Tahap', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'body'  => ['label' => 'Keterangan', 'type' => 'textarea'],
        ] + $AKHIR,
    ],

    'faq' => [
        'list'   => ['title' => 'Pertanyaan'],
        'fields' => [
            'title' => ['label' => 'Pertanyaan', 'type' => 'text', 'required' => true],
            'body'  => ['label' => 'Jawaban', 'type' => 'richtext'],
        ] + $AKHIR,
    ],

    /* ---------- Regulasi ---------- */
    'regulasi' => [
        'anak'      => ['ruang-lingkup'],
        'pratinjau' => 'pages/regulasi-detail.php?slug={slug}',
        'cari'      => ['subtitle', 'title'],
        'urut'      => 'k.published_at DESC, k.sort, k.id',
        'list'      => ['subtitle' => 'Nomor', 'title' => 'Judul', 'year' => 'Tahun'],
        'fields'    => [
            'subtitle'       => ['label' => 'Nomor Peraturan', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'           => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'subtitle', 'required' => true, 'col' => 'half'],
            'title'          => ['label' => 'Judul Regulasi', 'type' => 'text', 'required' => true],
            'year'           => ['label' => 'Tahun', 'type' => 'number', 'required' => true, 'col' => 'half'],
            'status'         => ['label' => 'Status', 'type' => 'select', 'col' => 'half',
                                 'options' => ['berlaku' => 'Berlaku', 'dicabut' => 'Dicabut'], 'default' => 'berlaku'],
            'published_at'   => ['label' => 'Tanggal Ditetapkan', 'type' => 'date', 'col' => 'half'],
            'date_published' => ['label' => 'Tanggal Diundangkan', 'type' => 'date', 'col' => 'half'],
            'body'           => ['label' => 'Tentang Regulasi', 'type' => 'richtext'],
            'file'           => ['label' => 'Berkas PDF', 'type' => 'file'],
            'url'            => ['label' => 'Tautan Sumber (JDIH)', 'type' => 'text',
                                 'hint'  => 'Alamat dokumen pada jdih.kemenhub.go.id — tampil sebagai tombol "Lihat di JDIH Kemenhub"'],
        ] + $AKHIR,
    ],

    /* ---------- Berita ---------- */
    'berita' => [
        'pratinjau' => 'pages/berita-detail.php?slug={slug}',
        'cari'      => ['title', 'excerpt'],
        'urut'      => 'k.published_at DESC, k.id DESC',
        'list'      => ['title' => 'Judul', 'published_at' => 'Tanggal', 'views' => 'Dilihat'],
        'fields'    => [
            'title'        => ['label' => 'Judul Berita', 'type' => 'text', 'required' => true],
            'slug'         => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half'],
            'published_at' => ['label' => 'Tanggal Terbit', 'type' => 'date', 'required' => true, 'col' => 'half'],
            'author'       => ['label' => 'Penulis', 'type' => 'text', 'col' => 'half'],
            'excerpt'      => ['label' => 'Ringkasan', 'type' => 'textarea', 'hint' => 'Tampil pada kartu berita'],
            'body'         => ['label' => 'Isi Berita', 'type' => 'richtext', 'rows' => 16, 'hint' => 'Pisahkan paragraf dengan baris kosong'],
        ] + f_visual('art-1') + [
            'tags'        => ['label' => 'Tagar', 'type' => 'tags', 'hint' => 'Pisahkan dengan koma — tersimpan sebagai tag'],
            'is_featured' => ['label' => 'Sorot di beranda', 'type' => 'checkbox', 'col' => 'half'],
            'sort'        => f_sort(),
            'is_active'   => f_active('Terbitkan'),
        ],
    ],

    'pengumuman' => [
        'urut'   => 'k.published_at DESC, k.sort',
        'list'   => ['title' => 'Judul', 'category' => 'Kategori', 'published_at' => 'Tanggal'],
        'fields' => [
            'title'        => ['label' => 'Judul', 'type' => 'text', 'required' => true],
            'category'     => ['label' => 'Kategori', 'type' => 'text', 'col' => 'half'],
            'published_at' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true, 'col' => 'half'],
            'url'          => ['label' => 'Tautan', 'type' => 'text'],
        ] + $AKHIR,
    ],

    /* ---------- Galeri ---------- */
    'album' => [
        'pratinjau' => 'pages/galeri-detail.php?slug={slug}',
        'list'      => ['title' => 'Album', 'slug' => 'Slug'],
        'fields'    => [
            'title' => ['label' => 'Nama Album', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'  => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half'],
            'body'  => ['label' => 'Deskripsi', 'type' => 'textarea'],
        ] + f_visual('art-1') + $AKHIR,
    ],

    'foto' => [
        'cari'   => ['title'],
        'list'   => ['title' => 'Judul', 'parent_id' => 'Album', 'is_featured' => 'Sorot'],
        'fields' => [
            'title'       => ['label' => 'Judul Foto', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'excerpt'     => ['label' => 'Keterangan', 'type' => 'text', 'col' => 'half'],
            'parent_id'   => ['label' => 'Album', 'type' => 'select', 'col' => 'half', 'empty' => '— Tanpa album (tab Foto) —',
                              'options_konten' => 'galeri/album'],
            'is_featured' => ['label' => 'Tampil di beranda', 'type' => 'checkbox', 'col' => 'half'],
        ] + f_visual('art-1') + $AKHIR,
    ],

    'video-galeri' => [
        'list'   => ['title' => 'Judul', 'url' => 'URL'],
        'fields' => [
            'title' => ['label' => 'Judul Video', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'   => ['label' => 'URL Video', 'type' => 'text', 'col' => 'half', 'hint' => 'YouTube/Vimeo'],
        ] + f_visual('art-2') + $AKHIR,
    ],

    /* ---------- Kontak ---------- */
    'kontak-info' => [
        'list'   => ['title' => 'Label', 'body' => 'Isi'],
        'fields' => [
            'icon'  => f_icon(),
            'title' => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'body'  => ['label' => 'Isi', 'type' => 'textarea'],
        ] + $AKHIR,
    ],

    /* ---------- Baris anak (disunting di dalam formulir induk) ---------- */
    'baris' => [
        'list'   => ['body' => 'Isi'],
        'fields' => [
            'body' => ['label' => 'Isi', 'type' => 'text', 'required' => true],
        ] + $AKHIR,
    ],

    /* Baris satu nama — mis. bandar udara pada satu wilayah. */
    'nama' => [
        'list'   => ['title' => 'Nama'],
        'fields' => [
            'title' => ['label' => 'Nama', 'type' => 'text', 'required' => true],
        ] + $AKHIR,
    ],

    'area' => [
        'list'   => ['slug' => 'Wilayah', 'body' => 'Bandar Udara'],
        'fields' => [
            'slug' => ['label' => 'Wilayah OBU', 'type' => 'select', 'required' => true,
                       'options_konten' => 'home/peta', 'options_nilai' => 'slug'],
            'body' => ['label' => 'Bandar Udara / Lokasi', 'type' => 'text', 'required' => true,
                       'hint'  => 'Pisahkan dengan koma'],
        ],
    ],

    'tahap' => [
        'list'   => ['title' => 'Tahap', 'body' => 'Keterangan'],
        'fields' => [
            'title' => ['label' => 'Tahap', 'type' => 'text', 'required' => true],
            'body'  => ['label' => 'Keterangan', 'type' => 'text'],
        ],
    ],

    'unduhan' => [
        'list'   => ['title' => 'Nama Berkas', 'excerpt' => 'Keterangan'],
        'fields' => [
            'title'   => ['label' => 'Nama Berkas', 'type' => 'text', 'required' => true],
            'excerpt' => ['label' => 'Keterangan', 'type' => 'text', 'hint' => 'Contoh: PDF · 320 KB'],
            'file'    => ['label' => 'Berkas', 'type' => 'file'],
        ],
    ],
];
