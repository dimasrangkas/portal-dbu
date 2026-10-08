<?php
/* ============================================================
   Kolom formulir yang dipakai berulang oleh blueprints.php
   dan resources.php.
   ============================================================ */

const ART_CLASSES = [
    'art-1' => 'Biru Terang (art-1)', 'art-2' => 'Biru Tua (art-2)', 'art-3' => 'Navy (art-3)',
    'art-4' => 'Biru Laut (art-4)',   'art-5' => 'Oranye (art-5)',   'art-6' => 'Biru Langit (art-6)',
    'art-7' => 'Biru Abu (art-7)',    'art-8' => 'Biru Dalam (art-8)',
];

const BADGE_CLASSES = [
    'badge-primary' => 'Biru', 'badge-success' => 'Hijau', 'badge-accent' => 'Kuning',
    'badge-gray'    => 'Abu',  'badge-danger'  => 'Merah',
];

/** Field standar: ikon Bootstrap. */
function f_icon(string $label = 'Ikon'): array
{
    return ['label' => $label, 'type' => 'icon', 'hint' => 'Nama ikon Bootstrap Icons, contoh: bi-airplane-fill', 'col' => 'half'];
}

/** Field standar: urutan tampil di portal. */
function f_sort(): array
{
    return ['label' => 'Urutan', 'type' => 'number', 'default' => 0, 'col' => 'half', 'hint' => 'Angka kecil tampil lebih dahulu'];
}

/** Field standar: status aktif. */
function f_active(string $label = 'Tampilkan di situs'): array
{
    return ['label' => $label, 'type' => 'checkbox', 'default' => 1, 'col' => 'half'];
}

/** Field standar: gambar + fallback gradien. */
function f_visual(string $defaultArt = 'art-1'): array
{
    return [
        'image'     => ['label' => 'Gambar', 'type' => 'image', 'col' => 'full', 'hint' => 'Kosongkan untuk memakai gradien placeholder di bawah'],
        'art_class' => ['label' => 'Warna Placeholder', 'type' => 'select', 'options' => ART_CLASSES, 'default' => $defaultArt, 'col' => 'half'],
        'icon'      => f_icon('Ikon Placeholder'),
    ];
}
