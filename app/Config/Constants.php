<?php

/*
 | --------------------------------------------------------------------
 | App Namespace
 | --------------------------------------------------------------------
  |
 | Konstanta APP_NAMESPACE digunakan sebagai namespace utama aplikasi.
 | Semua class di folder app/ secara default akan berada di bawah
 | namespace ini (misalnya: App\Controllers, App\Models, dll).
 |
 | Jika konstanta ini diubah, maka SEMUA namespace class App\*
 | harus ikut diubah secara manual.
 */
defined('APP_NAMESPACE') || define('APP_NAMESPACE', 'App');

/*
 | --------------------------------------------------------------------------
 | Composer Path
 | --------------------------------------------------------------------------
 |
 | Menentukan lokasi file autoload milik Composer.
 | File ini digunakan untuk memuat library eksternal
 | yang diinstall melalui Composer.
 |
 | Secara default berada di folder vendor/autoload.php
 */
defined('COMPOSER_PATH') || define('COMPOSER_PATH', ROOTPATH . 'vendor/autoload.php');

/*
 |--------------------------------------------------------------------------
 | Timing Constants
 |--------------------------------------------------------------------------
 |
 | Konstanta waktu untuk mempermudah penulisan durasi
 | tanpa harus menghitung detik secara manual.
 |
 | Contoh penggunaan:
 | cache()->save('key', 'value', 5 * MINUTE);
 */
defined('SECOND') || define('SECOND', 1);
defined('MINUTE') || define('MINUTE', 60);
defined('HOUR')   || define('HOUR', 3600);
defined('DAY')    || define('DAY', 86400);
defined('WEEK')   || define('WEEK', 604800);
defined('MONTH')  || define('MONTH', 2_592_000);
defined('YEAR')   || define('YEAR', 31_536_000);
defined('DECADE') || define('DECADE', 315_360_000);

/*
 | --------------------------------------------------------------------------
 | Exit Status Codes
 | --------------------------------------------------------------------------
 |
 | Konstanta exit status digunakan sebagai kode keluar (exit code)
 | saat aplikasi dihentikan menggunakan fungsi exit().
 |
 | Nilai-nilai ini mengikuti praktik umum dari sistem Unix/Linux.
 */

/**
 * Exit tanpa error (program berhasil dijalankan)
 |
 */
defined('EXIT_SUCCESS')        || define('EXIT_SUCCESS', 0);        // no errors
defined('EXIT_ERROR')          || define('EXIT_ERROR', 1);          // generic error
defined('EXIT_CONFIG')         || define('EXIT_CONFIG', 3);         // configuration error
defined('EXIT_UNKNOWN_FILE')   || define('EXIT_UNKNOWN_FILE', 4);   // file not found
defined('EXIT_UNKNOWN_CLASS')  || define('EXIT_UNKNOWN_CLASS', 5);  // unknown class
defined('EXIT_UNKNOWN_METHOD') || define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     || define('EXIT_USER_INPUT', 7);     // invalid user input
defined('EXIT_DATABASE')       || define('EXIT_DATABASE', 8);       // database error
defined('EXIT__AUTO_MIN')      || define('EXIT__AUTO_MIN', 9);      // Batas minimum error code otomatis
defined('EXIT__AUTO_MAX')      || define('EXIT__AUTO_MAX', 125);    // Batas maksimum error code otomatis
