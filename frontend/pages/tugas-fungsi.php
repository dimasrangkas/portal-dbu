<?php
/* Halaman tersendiri sudah dilebur ke Profil (bagian Tugas & Fungsi).
   Tautan lama tetap hidup lewat pengalihan permanen. */
require_once dirname(__DIR__) . '/bootstrap.php';

header('Location: ' . url('pages/profil#tugas-pokok'), true, 301);
exit;
