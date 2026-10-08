<?php
/* ============================================================
   Modul CRUD generik di luar model konten.

   Seluruh isi halaman portal — dulu 36 modul di berkas ini —
   kini dikelola lewat admin/konten.php pada tiga sumbu:
   kategori, sub kategori, dan tag. Yang tersisa di sini hanyalah
   tabel yang memang bukan konten halaman.

   Versi lama berkas ini disimpan sebagai resources-lama.php
   sebagai rujukan saat memeriksa hasil migrasi.
   ============================================================ */
require_once __DIR__ . '/fields-umum.php';

return [

    /* ============ KONTEN ============ */
    'tags' => [
        'group' => 'Konten', 'label' => 'Tag', 'icon' => 'bi-tags', 'table' => 'tags',
        'order' => 'sort, id', 'reorder' => true, 'search' => ['label', 'slug'],
        'list'  => ['label' => 'Tag', 'slug' => 'Slug', 'sort' => 'Urutan', 'is_active' => 'Aktif'],
        'fields' => [
            'label'     => ['label' => 'Nama Tag', 'type' => 'text', 'required' => true, 'col' => 'half'],
            'slug'      => ['label' => 'Slug', 'type' => 'slug', 'from' => 'label', 'required' => true, 'col' => 'half'],
            'sort'      => f_sort(),
            'is_active' => f_active(),
        ],
    ],

    /* ============ DATA BANDAR UDARA ============ */
    /* Isi tabel `bandaras` datang dari impor database/bandaras.sql (608 baris),
       jadi modul ini terutama dipakai untuk melengkapi data rinci tiap bandara
       — yang tampil sebagai tab pada halaman detail. Tabel anaknya dibuat oleh
       database/migrate-detail-bandara.sql. */
    'bandaras' => [
        'group' => 'Data Bandar Udara', 'label' => 'Bandar Udara', 'icon' => 'bi-airplane', 'table' => 'bandaras',
        'order' => 'nama', 'per_page' => 40,
        'search' => ['nama', 'kode_iata', 'kode_icao', 'provinsi', 'kabupaten_kota'],
        'preview' => 'pages/bandara-detail.php?id={id}',
        'list'  => ['nama' => 'Nama', 'kode_icao' => 'ICAO', 'kode_iata' => 'IATA',
                    'provinsi' => 'Provinsi', 'kelas' => 'Kelas', 'pengelola' => 'Pengelola'],
        'fields' => [
            /* --- Identitas --- */
            'nama'              => ['label' => 'Nama Bandar Udara', 'type' => 'text', 'required' => true],
            'kode_icao'         => ['label' => 'Kode ICAO', 'type' => 'text', 'col' => 'half'],
            'kode_iata'         => ['label' => 'Kode IATA', 'type' => 'text', 'col' => 'half'],

            /* --- Tab Data Umum --- */
            'status_operasi'    => ['label' => 'Status Operasi', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Umum'],
            'tipe'              => ['label' => 'Penggunaan', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Domestik / Internasional'],
            'hierarki'          => ['label' => 'Hierarki', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: PT'],
            'klasifikasi'       => ['label' => 'Klasifikasi', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: 4D'],
            'critical_aircraft' => ['label' => 'Critical Aircraft', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: BOEING : 737-900ER'],
            'pesawat_beroperasi'=> ['label' => 'Pesawat Beroperasi', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: BOEING : 737-900ER'],
            'pkp_pk'            => ['label' => 'PKP-PK', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Kategori 7'],
            'kelas'             => ['label' => 'Kelas', 'type' => 'text', 'col' => 'half'],
            'pengelola'         => ['label' => 'Pengelola', 'type' => 'text', 'col' => 'half'],
            'kantor_otoritas'   => ['label' => 'Kantor Otoritas', 'type' => 'text'],
            'airnav'            => ['label' => 'Airnav Indonesia', 'type' => 'text', 'hint' => 'Nama kantor cabang beserta alamatnya'],
            'status_blu'        => ['label' => 'Status BLU', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: PM 15 Tahun 2018'],
            'status_blu_url'    => ['label' => 'Tautan Status BLU', 'type' => 'text', 'col' => 'half'],
            'dokumen_pendukung' => ['label' => 'Dokumen Pendukung', 'type' => 'textarea', 'hint' => 'Satu dokumen per baris, contoh: Renstra Bandara'],

            /* --- Lokasi --- */
            'alamat'            => ['label' => 'Alamat', 'type' => 'textarea'],
            'kelurahan_desa'    => ['label' => 'Kelurahan / Desa', 'type' => 'text', 'col' => 'half'],
            'kecamatan'         => ['label' => 'Kecamatan', 'type' => 'text', 'col' => 'half'],
            'kabupaten_kota'    => ['label' => 'Kabupaten / Kota', 'type' => 'text', 'col' => 'half'],
            'provinsi'          => ['label' => 'Provinsi', 'type' => 'text', 'col' => 'half'],
            'lokasi_arp'        => ['label' => 'Koordinat ARP', 'type' => 'text',
                                    'hint' => 'Bentuk derajat-menit-detik, contoh: 00° 55\' 06.797" LS 119° 54\' 23.207" BT — dipakai peta dan penghitungan bandara terdekat'],

            /* --- Kontak --- */
            'telepon'           => ['label' => 'Telepon', 'type' => 'text', 'col' => 'half'],
            'fax'               => ['label' => 'Faksimile', 'type' => 'text', 'col' => 'half'],
            'email'             => ['label' => 'Surel', 'type' => 'text', 'col' => 'half'],
            'website'           => ['label' => 'Situs Web', 'type' => 'text', 'col' => 'half'],
            'microsite_url'     => ['label' => 'Tautan Microsite', 'type' => 'text',
                                    'hint' => 'Microsite OBU/UPBU bandara ini — beda dari Situs Web. Selama kosong, halaman detail menampilkan "—".'],

            /* --- Cuaca terkini (hero halaman detail) ---
               Situs resmi menariknya dari API BMKG; di sini diisi manual
               sampai ada penyelaras otomatis. */
            'cuaca_suhu'        => ['label' => 'Suhu (°C)', 'type' => 'number', 'col' => 'half',
                                    'nullable' => true, 'hint' => 'Contoh: 32. Dikosongkan berarti belum ada data, bukan 0 °C.'],
            'cuaca_kondisi'     => ['label' => 'Kondisi Cuaca', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Cerah, Berawan, Hujan Ringan'],
            'cuaca_diperbarui'  => ['label' => 'Cuaca Diperbarui', 'type' => 'datetime', 'col' => 'half'],

            /* --- Lalu lintas udara --- */
            'tahun_data'        => ['label' => 'Tahun Data', 'type' => 'number', 'col' => 'half', 'hint' => 'Contoh: 2025'],
            'jumlah_pesawat'    => ['label' => 'Pergerakan Pesawat', 'type' => 'number', 'col' => 'half'],
            'jumlah_penumpang'  => ['label' => 'Jumlah Penumpang', 'type' => 'number', 'col' => 'half'],
            'jumlah_kargo'      => ['label' => 'Jumlah Kargo', 'type' => 'number', 'col' => 'half'],
        ],
        'children' => [
            'rute' => [
                'label' => 'Rute Domestik', 'table' => 'bandara_rute',
                'foreign_key' => 'bandara_id', 'order' => 'sort, id',
                'hint' => 'Tampil pada tab "Rute Domestik".',
                'fields' => [
                    'keberangkatan' => ['label' => 'Keberangkatan', 'type' => 'text', 'col' => 'half', 'hint' => 'Bandar udara asal'],
                    'tujuan'        => ['label' => 'Kedatangan', 'type' => 'text', 'col' => 'half', 'hint' => 'Bandar udara tujuan'],
                    'maskapai'      => ['label' => 'Maskapai', 'type' => 'text', 'col' => 'half'],
                    'pesawat'       => ['label' => 'Pesawat', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: A320'],
                    'frekuensi'     => ['label' => 'Frekuensi / Minggu', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: 7'],
                ],
            ],
            'fasilitas_udara' => [
                'label' => 'Fasilitas Sisi Udara', 'table' => 'bandara_fasilitas_udara',
                'foreign_key' => 'bandara_id', 'order' => 'sort, id',
                'hint' => 'Tampil pada tab "Fasilitas Udara". Kategori menentukan sub-tabnya.',
                'fields' => [
                    'kategori'           => ['label' => 'Kategori', 'type' => 'select', 'options' => BANDARA_FASILITAS_UDARA, 'col' => 'half'],
                    'nama'               => ['label' => 'Nama Fasilitas', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: RUNWAY 15-33'],
                    'dimensi_verifikasi' => ['label' => 'Dimensi / Luas Terverifikasi', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: 2.500,00 m x 45,00 m — atau 124,00 m2'],
                    'dimensi_terbangun'  => ['label' => 'Dimensi / Luas Terbangun', 'type' => 'text', 'col' => 'half'],
                    'konstruksi'         => ['label' => 'Konstruksi', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Aspal Hotmix'],
                    'daya_dukung'        => ['label' => 'Daya Dukung', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: PCR 510/F/C/X/T'],
                    'azimuth'            => ['label' => 'Azimuth', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: 15-33'],
                    'critical_aircraft'  => ['label' => 'Critical Aircraft', 'type' => 'text', 'col' => 'half'],
                    'pesawat_beroperasi' => ['label' => 'Pesawat Beroperasi', 'type' => 'text', 'col' => 'half'],
                    'tipe_runway'        => ['label' => 'Tipe Runway', 'type' => 'textarea', 'hint' => 'Contoh: Runway 15 Instrument Precision Code Number II & III'],
                    'declare_distance'   => ['label' => 'Declare Distance', 'type' => 'textarea',
                                             'hint' => 'Bentuk JSON, contoh: {"Runway 02":{"TORA":"2.220 m","TODA":"2.280 m","ASDA":"2.220 m","LDA":"0 m"}}'],
                ],
            ],
            'fasilitas_darat' => [
                'label' => 'Fasilitas Sisi Darat', 'table' => 'bandara_fasilitas_darat',
                'foreign_key' => 'bandara_id', 'order' => 'sort, id',
                'hint' => 'Tampil pada tab "Fasilitas Darat". Kapasitas dan kategori umumnya hanya diisi untuk gedung terminal.',
                'fields' => [
                    'kategori'   => ['label' => 'Kategori', 'type' => 'select', 'options' => BANDARA_FASILITAS_DARAT, 'col' => 'half'],
                    'nama'       => ['label' => 'Nama Gedung', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Gedung Kantor Administrasi'],
                    'luas'       => ['label' => 'Luas (m²)', 'type' => 'decimal', 'col' => 'half', 'hint' => 'Boleh berkoma, contoh: 1039.24'],
                    'kapasitas'  => ['label' => 'Kapasitas', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: 1.738.929 Penumpang/Tahun'],
                    'keterangan' => ['label' => 'Kategori Terminal', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Domestik'],
                ],
            ],
            'fasilitas_umum' => [
                'label' => 'Fasilitas Umum & Transportasi', 'table' => 'bandara_fasilitas_umum',
                'foreign_key' => 'bandara_id', 'order' => 'sort, id',
                'hint' => 'Jenis menentukan tab tempatnya tampil: "Fasilitas Umum" atau "Transportasi".',
                'fields' => [
                    'jenis' => ['label' => 'Jenis', 'type' => 'select', 'col' => 'half',
                                'options' => ['umum' => 'Fasilitas Umum', 'transportasi' => 'Transportasi']],
                    'nama'  => ['label' => 'Nama', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Free Wifi'],
                    'icon'  => f_icon('Ikon'),
                ],
            ],
            /* Tab peralatan (Kendaraan Utama, Kendaraan Pendukung, Catu Daya,
               Alat Bantu Pendaratan, Lampu Pendaratan) TIDAK dikelola lewat CMS
               ini — datanya dummy, ditulis langsung di bandara_peralatan_dummy()
               (shared/functions.php) menunggu endpoint/API resmi. Jangan
               tambahkan child di sini. */
            'galeri' => [
                'label' => 'Galeri Foto', 'table' => 'bandara_galeri',
                'foreign_key' => 'bandara_id', 'order' => 'sort, id',
                'hint' => 'Tampil pada tab "Galeri".',
                'fields' => [
                    'image'      => ['label' => 'Foto', 'type' => 'image', 'col' => 'half'],
                    'keterangan' => ['label' => 'Keterangan', 'type' => 'text', 'col' => 'half', 'hint' => 'Contoh: Ruang Tunggu'],
                ],
            ],
        ],
    ],

    /* ============ TATA LETAK SITUS ============ */
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
            'group_label' => ['label' => 'Judul Kelompok', 'type' => 'text', 'col' => 'half',
                              'hint' => 'Untuk menu bertingkat lebar (mega menu)'],
            'description' => ['label' => 'Keterangan Singkat', 'type' => 'text'],
            'style'       => ['label' => 'Gaya Menu', 'type' => 'select', 'col' => 'half',
                              'options' => ['link' => 'Tautan biasa', 'mega' => 'Mega menu']],
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
