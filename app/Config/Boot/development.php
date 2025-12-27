<?php

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Pengaturan ini mengatur bagaimana error PHP ditampilkan
 | pada environment development.
 | Di mode development, semua error ditampilkan agar
 | memudahkan proses debugging dan pengembangan aplikasi.
 |
 | If you set 'display_errors' to '1', CI4's detailed error report will show.
 */
error_reporting(E_ALL);  // → Menampilkan SEMUA jenis error PHP
ini_set('display_errors', '1'); // → Mengizinkan error ditampilkan di browser

/*
 |--------------------------------------------------------------------------
 | DEBUG BACKTRACES
 |--------------------------------------------------------------------------
 | Konstanta ini mengatur apakah stack trace (alur pemanggilan fungsi)
 | ditampilkan pada halaman error CodeIgniter.
 |
 | Jika bernilai true:
 | - Error page akan menampilkan detail file, baris kode,
 |   dan alur eksekusi program
 |
 | defined(...) digunakan agar konstanta tidak didefinisikan ulang
 | jika sudah ada sebelumnya.
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
  | CI_DEBUG adalah flag utama mode debug CodeIgniter 4.
 |
 | Jika true:
 | - Kint (debugging tool CodeIgniter) akan aktif
 | - Error akan ditampilkan secara detail
 | - Fitur debugging internal CodeIgniter diaktifkan
 |
 | Mode ini hanya untuk DEVELOPMENT,
 | dan HARUS dimatikan saat production.
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);
