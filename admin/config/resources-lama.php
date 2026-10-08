<?php
/* ============================================================
   ARSIP — definisi 36 modul CMS sebelum unifikasi konten.

   Tidak lagi dimuat oleh panel. Disimpan sebagai rujukan saat
   memeriksa hasil migrate-unifikasi-konten.php: di sinilah label,
   petunjuk, dan susunan kolom lama tiap modul tercatat.
   Penggantinya: config/blueprints.php + admin/konten.php.
   ============================================================ */

/* Kolom pembantu (ART_CLASSES, f_icon, f_sort, …) kini tinggal di
   config/fields-umum.php supaya tidak terdefinisi dua kali. */
require_once __DIR__ . '/fields-umum.php';

return [

    /* ============ BERANDA ============ */
    'hero_slides' => [
        'group' => 'Beranda', 'label' => 'Slider Utama', 'icon' => 'bi-images', 'table' => 'hero_slides',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'eyebrow' => 'Label Atas', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'eyebrow'    => ['label' => 'Label Atas (eyebrow)', 'type' => 'text', 'col' => 'half'],
            'title'      => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'tagline'    => ['label' => 'Deskripsi', 'type' => 'textarea'],
        ] + f_visual('art-1') + [
            'btn1_label' => ['label' => 'Tombol 1 — Teks', 'type' => 'text', 'col' => 'half'],
            'btn1_url'   => ['label' => 'Tombol 1 — Tautan', 'type' => 'text', 'col' => 'half'],
            'btn2_label' => ['label' => 'Tombol 2 — Teks', 'type' => 'text', 'col' => 'half'],
            'btn2_url'   => ['label' => 'Tombol 2 — Tautan', 'type' => 'text', 'col' => 'half'],
            'sort'       => f_sort(),
            'is_active'  => f_active(),
        ],
    ],

    'stats' => [
        'group' => 'Beranda', 'label' => 'Statistik', 'icon' => 'bi-bar-chart', 'table' => 'stats',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Label', 'value' => 'Nilai', 'suffix' => 'Akhiran', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'label'     => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'value'     => ['label' => 'Nilai Angka', 'type' => 'number', 'required' => true, 'col' => 'half'],
            'suffix'    => ['label' => 'Akhiran', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: +'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'director_message' => [
        'group' => 'Beranda', 'label' => 'Sambutan Direktur', 'icon' => 'bi-person-badge',
        'table' => 'director_message', 'single' => true,
        'fields' => [
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

    'about_cards' => [
        'group' => 'Beranda', 'label' => 'Kartu Tentang Kami', 'icon' => 'bi-grid', 'table' => 'about_cards',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'description' => 'Deskripsi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'        => f_icon(),
            'title'       => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
            'url'         => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    'quick_links' => [
        'group' => 'Beranda', 'label' => 'Akses Cepat', 'icon' => 'bi-lightning', 'table' => 'quick_links',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Label', 'url' => 'Tautan', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'      => f_icon(),
            'label'     => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'       => ['label' => 'Tautan', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'featured_services' => [
        'group' => 'Beranda', 'label' => 'Layanan Unggulan', 'icon' => 'bi-star', 'table' => 'featured_services',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'description' => 'Deskripsi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'        => f_icon(),
            'title'       => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
            'url'         => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    'regions' => [
        'group' => 'Beranda', 'label' => 'Peta Wilayah OBU', 'icon' => 'bi-geo', 'table' => 'regions',
        'order' => 'sort, id',
        'list'  => ['numeral' => 'Wilayah', 'title' => 'Nama', 'hq' => 'Kantor Pusat', 'is_active' => 'Aktif'],
        'no_create' => true, 'no_delete' => true,
        'fields' => [
            'region_code'    => ['label' => 'Kode Wilayah', 'type' => 'text', 'required' => true, 'col' => 'half', 'readonly' => true,
                                 'hint' => 'Terhubung ke bentuk peta SVG — jangan diubah'],
            'numeral'        => ['label' => 'Angka Romawi', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'title'          => ['label' => 'Nama Wilayah', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'hq'             => ['label' => 'Kantor Pusat (lengkap)', 'type' => 'text', 'col' => 'half'],
            'hq_short'       => ['label' => 'Kantor Pusat (ringkas)', 'type' => 'text', 'col' => 'half', 'hint' => 'Untuk daftar legenda peta'],
            'coverage'       => ['label' => 'Cakupan Provinsi', 'type' => 'textarea'],
            'coverage_short' => ['label' => 'Cakupan (ringkas)', 'type' => 'textarea', 'hint' => 'Untuk daftar legenda peta'],
            'sort'           => f_sort(),
            'is_active'      => f_active(),
        ],
        'children' => [
            'region_airports' => [
                'label' => 'Daftar Bandar Udara', 'table' => 'region_airports', 'foreign_key' => 'region_id', 'order' => 'sort, id',
                'hint'  => 'Tampil saat wilayah ini disorot pada peta beranda. Urutan baris menentukan urutan tampil.',
                'fields' => [
                    'name' => ['label' => 'Nama Bandar Udara', 'type' => 'text', 'required' => true],
                ],
            ],
        ],
    ],

    'rpjmn_programs' => [
        'group' => 'Beranda', 'label' => 'Program RPJMN 2025–2029', 'icon' => 'bi-airplane', 'table' => 'rpjmn_programs',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Bagian', 'eyebrow' => 'Label Atas', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'title'     => ['label' => 'Judul Bagian', 'type' => 'text', 'required' => true],
            'eyebrow'   => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Prioritas Nasional 3'],
            'icon'      => f_icon(),
            'summary'   => ['label' => 'Uraian', 'type' => 'textarea', 'rows' => 5,
                            'hint'  => 'Isi utama bagian: cakupan program dan daftar bandar udara'],
            'focus'     => ['label' => 'Catatan Fokus', 'type' => 'textarea',
                            'hint'  => 'Tampil dengan latar biru — mis. kawasan/DPP yang didukung'],
            'note'      => ['label' => 'Catatan Tambahan', 'type' => 'textarea',
                            'hint'  => 'Tampil dengan latar abu — mis. pertimbangan teknis atau status kesiapan'],
            'sort'      => f_sort(),
            'is_active' => f_active('Tampilkan sebagai bagian korsel'),
        ],
        'children' => [
            'rpjmn_areas' => [
                'label' => 'Wilayah pada Peta', 'table' => 'rpjmn_areas', 'foreign_key' => 'program_id', 'order' => 'sort, id',
                'hint'  => 'Wilayah yang dipilih akan menyala pada peta saat bagian ini aktif. Daftar bandar udara tampil pada tooltip peta.',
                'fields' => [
                    'region_code' => ['label' => 'Wilayah OBU', 'type' => 'select', 'required' => true,
                                      'options_sql' => 'SELECT region_code AS value, CONCAT(numeral, " — ", title, " (", COALESCE(hq_short, ""), ")") AS label FROM regions ORDER BY sort'],
                    'airports'    => ['label' => 'Bandar Udara / Lokasi', 'type' => 'text', 'required' => true,
                                      'hint'  => 'Pisahkan dengan koma'],
                ],
            ],
        ],
    ],

    'home_video' => [
        'group' => 'Beranda', 'label' => 'Video Profil', 'icon' => 'bi-camera-reels',
        'table' => 'home_video', 'single' => true,
        'fields' => [
            'eyebrow'   => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'     => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'video_url' => ['label' => 'URL Video', 'type' => 'text', 'hint' => 'Tautan YouTube/Vimeo. Kosongkan untuk memakai gambar placeholder.'],
            'caption'   => ['label' => 'Keterangan', 'type' => 'text'],
        ] + f_visual('art-2'),
    ],

    'partners' => [
        'group' => 'Beranda', 'label' => 'Mitra Kerja', 'icon' => 'bi-people', 'table' => 'partners',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['name' => 'Nama Mitra', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'name'      => ['label' => 'Nama Mitra', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'       => ['label' => 'Tautan', 'type' => 'text', 'col' => 'half'],
            'logo'      => ['label' => 'Logo', 'type' => 'image', 'hint' => 'Kosongkan untuk menampilkan nama sebagai teks'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ PROFIL ============ */
    'profil_about' => [
        'group' => 'Profil', 'label' => 'Tentang & Visi', 'icon' => 'bi-info-square',
        'table' => 'profil_about', 'single' => true,
        'fields' => [
            'eyebrow'     => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'       => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'body'        => ['label' => 'Uraian Tentang Direktorat', 'type' => 'richtext'],
        ] + f_visual('art-6') + [
            'visi'        => ['label' => 'Visi', 'type' => 'textarea'],
        ],
        /* Tugas & fungsi direktorat diatur di menu "Tugas & Fungsi",
           tampil pada halaman Profil bagian Tugas dan Fungsi. */
    ],

    'timeline_items' => [
        'group' => 'Profil', 'label' => 'Sejarah (Linimasa)', 'icon' => 'bi-clock-history', 'table' => 'timeline_items',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['year' => 'Tahun', 'title' => 'Judul', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'year'        => ['label' => 'Tahun', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'title'       => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    'missions' => [
        'group' => 'Profil', 'label' => 'Misi', 'icon' => 'bi-list-ol', 'table' => 'missions',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['content' => 'Misi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'content'   => ['label' => 'Isi Misi', 'type' => 'textarea', 'required' => true],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'value_cards' => [
        'group' => 'Profil', 'label' => 'Nilai Organisasi', 'icon' => 'bi-gem', 'table' => 'value_cards',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Nilai', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'      => f_icon(),
            'title'     => ['label' => 'Nama Nilai', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'strategic_goals' => [
        'group' => 'Profil', 'label' => 'Sasaran Strategis', 'icon' => 'bi-bullseye', 'table' => 'strategic_goals',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'description' => 'Deskripsi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'        => f_icon(),
            'title'       => ['label' => 'Judul', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    /* Tabel `directorate_functions` tidak lagi dipakai halaman Profil — daftar fungsi
       sekarang diambil dari menu "Tugas & Fungsi" (tabel tf_functions). Datanya
       dibiarkan utuh di basis data, hanya tidak lagi disunting dari panel. */

    /* ============ ORGANISASI ============ */
    'org_units' => [
        'group' => 'Organisasi', 'label' => 'Unit Kerja', 'icon' => 'bi-diagram-3', 'table' => 'org_units',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Unit Kerja', 'unit_key' => 'Kode', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'unit_key'     => ['label' => 'Kode Unit', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half',
                               'hint' => 'Huruf kecil tanpa spasi, harus unik'],
            'icon'         => f_icon(),
            'title'         => ['label' => 'Nama Unit', 'type' => 'text', 'required' => true],
            'description'   => ['label' => 'Deskripsi Singkat', 'type' => 'textarea'],
            'tugas'         => ['label' => 'Tugas', 'type' => 'richtext', 'rows' => 5,
                                'hint'  => 'Tampil pada kartu unit di halaman Organisasi'],
            'head_name'     => ['label' => 'Nama Kepala Subdirektorat', 'type' => 'text', 'col' => 'half',
                                'hint'  => 'Kosongkan untuk unit tanpa pejabat tunggal.'],
            'head_position' => ['label' => 'Jabatan Kepala Subdirektorat', 'type' => 'text', 'col' => 'half',
                                'hint'  => 'Contoh: Kepala Subdirektorat'],
            'head_photo'    => ['label' => 'Foto Kepala Subdirektorat', 'type' => 'image',
                                'hint'  => 'Pasfoto. Bila kosong, kartu menampilkan ikon orang.'],
            'team_lead_name'     => ['label' => 'Nama Kepala Tim', 'type' => 'text', 'col' => 'half',
                                     'hint'  => 'Pejabat kedua yang tampil pada kartu unit.'],
            'team_lead_position' => ['label' => 'Jabatan Kepala Tim', 'type' => 'text', 'col' => 'half',
                                     'hint'  => 'Contoh: Kepala Tim'],
            'team_lead_photo'    => ['label' => 'Foto Kepala Tim', 'type' => 'image',
                                     'hint'  => 'Pasfoto. Bila kosong, kartu menampilkan ikon orang.'],
            'chips'         => ['label' => 'Kata Kunci', 'type' => 'text', 'hint' => 'Pisahkan dengan koma, contoh: Standar Keselamatan, Audit Kepatuhan'],
            'branch_class'  => ['label' => 'Posisi Bagan', 'type' => 'select', 'col' => 'half',
                                'options' => ['org-branch' => 'Cabang Utama', 'org-branch2' => 'Cabang Kedua']],
            'sort'          => f_sort(),
            'is_active'     => f_active(),
        ],
        'children' => [
            'org_unit_functions' => [
                'label' => 'Fungsi Unit', 'table' => 'org_unit_functions', 'foreign_key' => 'unit_id', 'order' => 'sort, id',
                'hint'  => 'Rincian fungsi yang tampil pada kartu unit di halaman Organisasi.',
                'fields' => [
                    'content' => ['label' => 'Isi Fungsi', 'type' => 'textarea', 'rows' => 3, 'required' => true],
                ],
            ],
        ],
    ],

    /* ============ TUGAS & FUNGSI (tampil di halaman Profil) ============ */
    'tf_overview' => [
        'group' => 'Tugas & Fungsi', 'label' => 'Tugas Direktorat', 'icon' => 'bi-bullseye',
        'table' => 'tf_overview', 'single' => true,
        'hint'  => 'Tampil pada halaman Profil, bagian Tugas dan Fungsi.',
        'fields' => [
            'tugas_eyebrow'  => ['label' => 'Label Atas — Tugas', 'type' => 'text', 'col' => 'half'],
            'tugas_title'    => ['label' => 'Judul Seksi Tugas', 'type' => 'text', 'col' => 'half'],
            'tugas'          => ['label' => 'Isi Tugas', 'type' => 'richtext', 'rows' => 6,
                                 'hint'  => 'Pisahkan paragraf dengan baris kosong'],
            'fungsi_eyebrow' => ['label' => 'Label Atas — Fungsi', 'type' => 'text', 'col' => 'half'],
            'fungsi_title'   => ['label' => 'Judul Seksi Fungsi', 'type' => 'text', 'col' => 'half'],
            'fungsi_intro'   => ['label' => 'Pengantar Daftar Fungsi', 'type' => 'textarea'],
        ],
    ],

    'tf_functions' => [
        'group' => 'Tugas & Fungsi', 'label' => 'Daftar Fungsi', 'icon' => 'bi-list-ol', 'table' => 'tf_functions',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['content' => 'Fungsi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'      => f_icon(),
            'content'   => ['label' => 'Isi Fungsi', 'type' => 'textarea', 'rows' => 4, 'required' => true],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ LAYANAN ============ */
    'services' => [
        'group' => 'Layanan', 'label' => 'Daftar Layanan', 'icon' => 'bi-headset', 'table' => 'services',
        'order' => 'sort, id', 'reorder' => true, 'search' => ['name', 'slug'], 'per_page' => 25,
        'list'  => ['name' => 'Nama Layanan', 'klasifikasi' => 'Klasifikasi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'preview' => 'pages/layanan-detail.php?slug={slug}',
        'fields' => [
            'name'              => ['label' => 'Nama Layanan', 'type' => 'text', 'required' => true],
            'slug'              => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'name', 'required' => true, 'col' => 'half'],
            'unit_pengelola'    => ['label' => 'Unit Pengelola', 'type' => 'text', 'col' => 'half', 'default' => 'Direktorat Bandar Udara'],
            'category'          => ['label' => 'Kategori Filter', 'type' => 'select', 'col' => 'half',
                                    'options' => ['sertifikasi' => 'Sertifikasi', 'perizinan' => 'Perizinan', 'layanan' => 'Layanan']],
            'icon'              => f_icon(),
            'sifat'             => ['label' => 'Sifat Layanan', 'type' => 'text', 'col' => 'half'],
            'sifat_badge'       => ['label' => 'Warna Label Sifat', 'type' => 'select', 'options' => BADGE_CLASSES, 'default' => 'badge-success', 'col' => 'half'],
            'klasifikasi'       => ['label' => 'Klasifikasi', 'type' => 'text', 'col' => 'half'],
            'klasifikasi_badge' => ['label' => 'Warna Label Klasifikasi', 'type' => 'select', 'options' => BADGE_CLASSES, 'default' => 'badge-gray', 'col' => 'half'],
            'chip_label'        => ['label' => 'Label Chip Halaman Detail', 'type' => 'text', 'col' => 'half'],
            'description'       => ['label' => 'Deskripsi Layanan', 'type' => 'richtext'],
            'time_process'      => ['label' => 'Waktu Proses', 'type' => 'text', 'col' => 'half'],
            'cost'              => ['label' => 'Biaya', 'type' => 'text', 'col' => 'half'],
            'validity'          => ['label' => 'Masa Berlaku', 'type' => 'text', 'col' => 'half'],
            'method'            => ['label' => 'Metode', 'type' => 'text', 'col' => 'half'],
            'cta_label'         => ['label' => 'Tombol CTA — Teks', 'type' => 'text', 'col' => 'half'],
            'cta_url'           => ['label' => 'Tombol CTA — Tautan', 'type' => 'text', 'col' => 'half'],
            'sort'              => f_sort(),
            'is_active'         => f_active(),
        ],
        'children' => [
            'service_requirements' => [
                'label' => 'Persyaratan', 'table' => 'service_requirements', 'foreign_key' => 'service_id', 'order' => 'sort, id',
                'hint'  => 'Bila dikosongkan, halaman detail memakai Persyaratan Umum.',
                'fields' => ['content' => ['label' => 'Persyaratan', 'type' => 'text', 'required' => true]],
            ],
            'service_steps' => [
                'label' => 'Alur Proses', 'table' => 'service_steps', 'foreign_key' => 'service_id', 'order' => 'sort, id',
                'hint'  => 'Bila dikosongkan, halaman detail memakai Alur Proses umum.',
                'fields' => [
                    'title'       => ['label' => 'Tahap', 'type' => 'text', 'required' => true],
                    'description' => ['label' => 'Keterangan', 'type' => 'text'],
                ],
            ],
            'service_downloads' => [
                'label' => 'Unduhan Formulir', 'table' => 'service_downloads', 'foreign_key' => 'service_id', 'order' => 'sort, id',
                'fields' => [
                    'title' => ['label' => 'Nama Berkas', 'type' => 'text', 'required' => true],
                    'meta'  => ['label' => 'Keterangan', 'type' => 'text', 'hint' => 'Contoh: PDF · 320 KB'],
                    'file'  => ['label' => 'Berkas', 'type' => 'file'],
                ],
            ],
        ],
    ],

    'general_requirements' => [
        'group' => 'Layanan', 'label' => 'Persyaratan Umum', 'icon' => 'bi-clipboard-check',
        'table' => 'general_requirements', 'order' => 'sort, id', 'reorder' => true,
        'list'  => ['content' => 'Persyaratan', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'content'   => ['label' => 'Persyaratan', 'type' => 'text', 'required' => true],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'process_steps' => [
        'group' => 'Layanan', 'label' => 'Alur Proses', 'icon' => 'bi-signpost-split',
        'table' => 'process_steps', 'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Tahap', 'description' => 'Keterangan', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'title'       => ['label' => 'Nama Tahap', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Keterangan', 'type' => 'textarea'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    'faqs' => [
        'group' => 'Layanan', 'label' => 'FAQ', 'icon' => 'bi-question-circle', 'table' => 'faqs',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['question' => 'Pertanyaan', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'question'  => ['label' => 'Pertanyaan', 'type' => 'text', 'required' => true],
            'answer'    => ['label' => 'Jawaban', 'type' => 'richtext'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ REGULASI ============ */
    'regulations' => [
        'group' => 'Regulasi', 'label' => 'Daftar Regulasi', 'icon' => 'bi-journal-text', 'table' => 'regulations',
        'order' => 'year DESC, sort, id', 'search' => ['number', 'title'], 'per_page' => 25,
        'list'  => ['number' => 'Nomor', 'title' => 'Judul', 'year' => 'Tahun', 'status' => 'Status', 'is_active' => 'Aktif'],
        'preview' => 'pages/regulasi-detail.php?slug={slug}',
        'fields' => [
            'number'         => ['label' => 'Nomor Peraturan', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'           => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'number', 'required' => true, 'col' => 'half'],
            'title'          => ['label' => 'Judul Regulasi', 'type' => 'text', 'required' => true],
            'year'           => ['label' => 'Tahun', 'type' => 'number', 'required' => true, 'col' => 'half'],
            'category'       => ['label' => 'Kategori', 'type' => 'select', 'col' => 'half',
                                 'options_sql' => 'SELECT slug AS value, label FROM regulation_categories WHERE is_active = 1 ORDER BY sort'],
            'status'         => ['label' => 'Status', 'type' => 'select', 'col' => 'half',
                                 'options' => ['berlaku' => 'Berlaku', 'dicabut' => 'Dicabut']],
            'date_set'       => ['label' => 'Tanggal Ditetapkan', 'type' => 'date', 'col' => 'half'],
            'date_published' => ['label' => 'Tanggal Diundangkan', 'type' => 'date', 'col' => 'half'],
            'about'          => ['label' => 'Tentang Regulasi', 'type' => 'richtext'],
            'file'           => ['label' => 'Berkas PDF', 'type' => 'file'],
            'source_url'     => ['label' => 'Tautan Sumber (JDIH)', 'type' => 'text',
                                 'hint'  => 'Alamat dokumen pada jdih.kemenhub.go.id — tampil sebagai tombol "Lihat di JDIH Kemenhub"'],
            'sort'           => f_sort(),
            'is_active'      => f_active(),
        ],
        'children' => [
            'regulation_scopes' => [
                'label' => 'Ruang Lingkup Utama', 'table' => 'regulation_scopes', 'foreign_key' => 'regulation_id', 'order' => 'sort, id',
                'fields' => ['content' => ['label' => 'Ruang Lingkup', 'type' => 'text', 'required' => true]],
            ],
        ],
    ],

    'regulation_categories' => [
        'group' => 'Regulasi', 'label' => 'Kategori Regulasi', 'icon' => 'bi-tags',
        'table' => 'regulation_categories', 'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Kategori', 'slug' => 'Slug', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'label'     => ['label' => 'Nama Kategori', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'      => ['label' => 'Slug', 'type' => 'slug', 'from' => 'label', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ BERITA ============ */
    'news' => [
        'group' => 'Berita', 'label' => 'Berita', 'icon' => 'bi-newspaper', 'table' => 'news',
        'order' => 'published_at DESC, id DESC', 'search' => ['title', 'excerpt'], 'per_page' => 20,
        'list'  => ['title' => 'Judul', 'category' => 'Kategori', 'published_at' => 'Tanggal', 'views' => 'Dilihat', 'is_published' => 'Terbit'],
        'preview' => 'pages/berita-detail.php?slug={slug}',
        'fields' => [
            'title'        => ['label' => 'Judul Berita', 'type' => 'text', 'required' => true],
            'slug'         => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half'],
            'category'     => ['label' => 'Kategori', 'type' => 'select', 'col' => 'half',
                               'options_sql' => 'SELECT slug AS value, label FROM news_categories WHERE is_active = 1 ORDER BY sort'],
            'published_at' => ['label' => 'Tanggal Terbit', 'type' => 'date', 'required' => true, 'col' => 'half'],
            'author'       => ['label' => 'Penulis', 'type' => 'text', 'col' => 'half'],
            'excerpt'      => ['label' => 'Ringkasan', 'type' => 'textarea', 'hint' => 'Tampil pada kartu berita'],
            'content'      => ['label' => 'Isi Berita', 'type' => 'richtext', 'rows' => 16, 'hint' => 'Pisahkan paragraf dengan baris kosong'],
        ] + f_visual('art-1') + [
            'tags'         => ['label' => 'Tagar', 'type' => 'text', 'hint' => 'Pisahkan dengan koma'],
            'is_featured'  => ['label' => 'Sorot di beranda', 'type' => 'checkbox', 'col' => 'half'],
            'is_published' => ['label' => 'Terbitkan', 'type' => 'checkbox', 'default' => 1, 'col' => 'half'],
        ],
    ],

    'news_categories' => [
        'group' => 'Berita', 'label' => 'Kategori Berita', 'icon' => 'bi-tag', 'table' => 'news_categories',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Kategori', 'slug' => 'Slug', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'label'     => ['label' => 'Nama Kategori', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'      => ['label' => 'Slug', 'type' => 'slug', 'from' => 'label', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'announcements' => [
        'group' => 'Berita', 'label' => 'Pengumuman', 'icon' => 'bi-megaphone', 'table' => 'announcements',
        'order' => 'published_at DESC, sort', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'category' => 'Kategori', 'published_at' => 'Tanggal', 'is_active' => 'Aktif'],
        'fields' => [
            'title'        => ['label' => 'Judul', 'type' => 'text', 'required' => true],
            'category'     => ['label' => 'Kategori', 'type' => 'text', 'col' => 'half'],
            'published_at' => ['label' => 'Tanggal', 'type' => 'date', 'required' => true, 'col' => 'half'],
            'url'          => ['label' => 'Tautan', 'type' => 'text'],
            'sort'         => f_sort(),
            'is_active'    => f_active(),
        ],
    ],

    /* ============ GALERI ============ */
    'gallery_photos' => [
        'group' => 'Galeri', 'label' => 'Foto', 'icon' => 'bi-image', 'table' => 'gallery_photos',
        'order' => 'sort, id', 'reorder' => true, 'search' => ['title'], 'per_page' => 24,
        'list'  => ['title' => 'Judul', 'album_id' => 'Album', 'show_on_home' => 'Beranda', 'is_active' => 'Aktif'],
        'list_lookup' => ['album_id' => ['table' => 'gallery_albums', 'column' => 'title']],
        'fields' => [
            'title'        => ['label' => 'Judul Foto', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'caption'      => ['label' => 'Keterangan', 'type' => 'text', 'col' => 'half'],
            'album_id'     => ['label' => 'Album', 'type' => 'select', 'col' => 'half', 'empty' => '— Tanpa album (tab Foto) —',
                               'options_sql' => 'SELECT id AS value, title AS label FROM gallery_albums ORDER BY sort'],
            'show_on_home' => ['label' => 'Tampil di beranda', 'type' => 'checkbox', 'col' => 'half'],
        ] + f_visual('art-1') + [
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'gallery_albums' => [
        'group' => 'Galeri', 'label' => 'Album', 'icon' => 'bi-collection', 'table' => 'gallery_albums',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Album', 'slug' => 'Slug', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'preview' => 'pages/galeri-detail.php?slug={slug}',
        'fields' => [
            'title'       => ['label' => 'Nama Album', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'        => ['label' => 'Slug URL', 'type' => 'slug', 'from' => 'title', 'required' => true, 'col' => 'half'],
            'description' => ['label' => 'Deskripsi', 'type' => 'textarea'],
        ] + f_visual('art-1') + [
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'gallery_videos' => [
        'group' => 'Galeri', 'label' => 'Video', 'icon' => 'bi-play-btn', 'table' => 'gallery_videos',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['title' => 'Judul', 'video_url' => 'URL', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'title'     => ['label' => 'Judul Video', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'video_url' => ['label' => 'URL Video', 'type' => 'text', 'col' => 'half', 'hint' => 'YouTube/Vimeo'],
        ] + f_visual('art-2') + [
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ KONTAK ============ */
    'contact_info' => [
        'group' => 'Kontak', 'label' => 'Informasi Kontak', 'icon' => 'bi-telephone', 'table' => 'contact_info',
        'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Label', 'value' => 'Isi', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'icon'      => f_icon(),
            'label'     => ['label' => 'Label', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'value'     => ['label' => 'Isi', 'type' => 'textarea'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    'contact_subjects' => [
        'group' => 'Kontak', 'label' => 'Pilihan Subjek Pesan', 'icon' => 'bi-chat-left-text',
        'table' => 'contact_subjects', 'order' => 'sort, id', 'reorder' => true,
        'list'  => ['label' => 'Subjek', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'label'     => ['label' => 'Subjek', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ TATA LETAK SITUS ============ */
    'page_meta' => [
        'group' => 'Tata Letak', 'label' => 'Judul & SEO Halaman', 'icon' => 'bi-file-text', 'table' => 'page_meta',
        'order' => 'id', 'no_create' => true, 'no_delete' => true,
        'list'  => ['page_label' => 'Halaman', 'heading' => 'Judul Halaman', 'breadcrumb' => 'Remah Roti'],
        'fields' => [
            'page_key'         => ['label' => 'Kunci Halaman', 'type' => 'text', 'readonly' => true, 'col' => 'half'],
            'page_label'       => ['label' => 'Nama Halaman', 'type' => 'text', 'col' => 'half'],
            'meta_title'       => ['label' => 'Judul Tab / SEO', 'type' => 'text'],
            'meta_description' => ['label' => 'Deskripsi SEO', 'type' => 'textarea'],
            'eyebrow'          => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'breadcrumb'       => ['label' => 'Teks Remah Roti', 'type' => 'text', 'col' => 'half'],
            'heading'          => ['label' => 'Judul Besar Halaman', 'type' => 'text'],
            'subtitle'         => ['label' => 'Sub Judul', 'type' => 'textarea'],
        ],
    ],

    'sections' => [
        'group' => 'Tata Letak', 'label' => 'Judul Seksi', 'icon' => 'bi-layout-text-window', 'table' => 'sections',
        'order' => 'page, sort', 'no_create' => true, 'no_delete' => true, 'search' => ['label', 'title'],
        'list'  => ['label' => 'Seksi', 'title' => 'Judul', 'page' => 'Halaman', 'is_active' => 'Aktif'],
        'fields' => [
            'page'        => ['label' => 'Halaman', 'type' => 'text', 'readonly' => true, 'col' => 'half'],
            'section_key' => ['label' => 'Kunci Seksi', 'type' => 'text', 'readonly' => true, 'col' => 'half'],
            'label'       => ['label' => 'Nama Seksi (internal)', 'type' => 'text'],
            'eyebrow'     => ['label' => 'Label Atas', 'type' => 'text', 'col' => 'half'],
            'title'       => ['label' => 'Judul', 'type' => 'text', 'col' => 'half'],
            'subtitle'    => ['label' => 'Sub Judul', 'type' => 'textarea'],
            'body'        => ['label' => 'Teks Tambahan', 'type' => 'textarea',
                              'hint'  => 'Dipakai sebagian seksi untuk catatan di bawah konten, mis. sumber data'],
            'is_active'   => f_active(),
        ],
    ],

    'menu_items' => [
        'group' => 'Tata Letak', 'label' => 'Menu Navigasi', 'icon' => 'bi-list', 'table' => 'menu_items',
        'order' => 'location, COALESCE(parent_id, id), parent_id IS NOT NULL, sort',
        'list'  => ['label' => 'Label', 'location' => 'Lokasi', 'parent_id' => 'Induk', 'url' => 'Tautan', 'is_active' => 'Aktif'],
        'list_lookup' => ['parent_id' => ['table' => 'menu_items', 'column' => 'label']],
        'fields' => [
            'label'       => ['label' => 'Label Menu', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'url'         => ['label' => 'Tautan', 'type' => 'text', 'required' => true, 'col' => 'half',
                              'hint' => 'Relatif terhadap folder frontend, contoh: pages/berita.php'],
            'location'    => ['label' => 'Lokasi', 'type' => 'select', 'col' => 'half',
                              'options' => ['navbar' => 'Menu Utama', 'footer_quick' => 'Footer — Tautan Cepat', 'footer_service' => 'Footer — Layanan']],
            'parent_id'   => ['label' => 'Menu Induk', 'type' => 'select', 'col' => 'half', 'empty' => '— Menu utama —',
                              'options_sql' => 'SELECT id AS value, label FROM menu_items WHERE parent_id IS NULL AND location = "navbar" ORDER BY sort'],
            'match_pages' => ['label' => 'Berkas Penanda Aktif', 'type' => 'text',
                              'hint' => 'Pisahkan dengan spasi, contoh: berita.php berita-detail.php'],
            'is_external' => ['label' => 'Tautan eksternal', 'type' => 'checkbox', 'col' => 'half'],
            'sort'        => f_sort(),
            'is_active'   => f_active(),
        ],
    ],

    /* ============ SISTEM ============ */
    'users' => [
        'group' => 'Sistem', 'label' => 'Pengguna', 'icon' => 'bi-person-gear', 'table' => 'users',
        'order' => 'id', 'admin_only' => true,
        'list'  => ['name' => 'Nama', 'username' => 'Nama Pengguna', 'role' => 'Peran', 'last_login_at' => 'Login Terakhir', 'is_active' => 'Aktif'],
        'fields' => [
            'name'      => ['label' => 'Nama Lengkap', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'username'  => ['label' => 'Nama Pengguna', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'email'     => ['label' => 'Email', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'role'      => ['label' => 'Peran', 'type' => 'select', 'col' => 'half',
                            'options' => ['admin' => 'Administrator', 'editor' => 'Editor']],
            'password'  => ['label' => 'Kata Sandi', 'type' => 'password', 'column' => 'password_hash',
                            'hint' => 'Kosongkan bila tidak ingin mengubah'],
            'is_active' => f_active('Akun aktif'),
        ],
    ],
];
