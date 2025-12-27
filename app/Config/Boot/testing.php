<?php

/*
 | File ini digunakan khusus untuk environment "testing"
 | yang diperuntukkan bagi pengujian otomatis menggunakan PHPUnit.
 |
 | Environment testing:
 | - BUKAN untuk development harian
 | - BUKAN untuk production
 | - Digunakan saat menjalankan unit test dan feature test
 |
 | CodeIgniter menyediakan perlakuan khusus pada environment ini
 | untuk membantu proses pengujian, seperti isolasi data dan error.
 */

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Pada environment testing:
 | - Semua error ditampilkan secara penuh
 | - Bertujuan agar pengujian dapat langsung mendeteksi kegagalan
 | - Sangat membantu saat menjalankan automated test
 |
 | error_reporting(E_ALL)
 | → Menampilkan semua jenis error PHP
 */
error_reporting(E_ALL);

/*
 |--------------------------------------------------------------------------
 | DISPLAY ERRORS
 |--------------------------------------------------------------------------
 | Mengatur apakah error ditampilkan ke output.
 |
 | '1' berarti:
 | - Error ditampilkan
 | - Informasi detail error tersedia untuk test runner
 | - Membantu assert error saat pengujian
 */
ini_set('display_errors', '1');

/*
 |--------------------------------------------------------------------------
 | DEBUG BACKTRACES
 |--------------------------------------------------------------------------
 | Mengatur apakah stack trace (alur pemanggilan fungsi)
 | ditampilkan pada layar error.
 |
 | true berarti:
 | - Informasi baris kode dan pemanggil fungsi ditampilkan
 | - Sangat membantu dalam analisis kegagalan test
 */
defined('SHOW_DEBUG_BACKTRACE') || define('SHOW_DEBUG_BACKTRACE', true);

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | CI_DEBUG adalah flag debugging utama CodeIgniter 4.
 |
 | Pada environment testing:
 | - Debug mode AKTIF
 | - Mendukung kebutuhan debugging saat unit test
 | - Membantu mendeteksi error tersembunyi
 */
defined('CI_DEBUG') || define('CI_DEBUG', true);
