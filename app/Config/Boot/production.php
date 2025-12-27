<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Bagian ini mengatur bagaimana error PHP ditangani
 | pada environment PRODUCTION.
 |
 | Pada mode production:
 | - Error TIDAK ditampilkan ke user
 | - Sistem hanya menampilkan pesan error umum
 | - Detail error dicatat ke log (bukan ke layar)
 */
error_reporting(E_ALL & ~E_DEPRECATED); // → Menampilkan semua error KECUALI error deprecated, (fitur lama yang masih berjalan tapi tidak disarankan)

/*
 |--------------------------------------------------------------------------
 | DISPLAY ERRORS
 |--------------------------------------------------------------------------
 | Mengatur apakah error ditampilkan di browser.
 |
 | '0' berarti:
 | - Error TIDAK ditampilkan ke pengguna
 | - Sangat penting untuk keamanan aplikasi production
 | - Mencegah bocornya struktur kode dan konfigurasi server
 */
ini_set('display_errors', '0');

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | CI_DEBUG adalah flag utama debugging CodeIgniter 4.
 |
 | Pada production:
 | - Debug mode HARUS dimatikan (false)
 | - Kint dan debugging detail TIDAK aktif
 | - Performa aplikasi lebih stabil dan aman
 |
 */
defined('CI_DEBUG') || define('CI_DEBUG', false);
