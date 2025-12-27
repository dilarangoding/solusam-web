<?php

// Namespace Config digunakan untuk semua file konfigurasi CodeIgniter
namespace Config;

// Menggunakan AutoloadConfig bawaan CodeIgniter
// Class ini khusus untuk pengaturan autoload (pemanggilan otomatis file)
use CodeIgniter\Config\AutoloadConfig;

/**
 * -------------------------------------------------------------------
 * AUTOLOADER CONFIGURATION
 * -------------------------------------------------------------------
  *
 * File ini mengatur bagaimana CodeIgniter memuat (autoload)
 * class, namespace, file, dan helper secara otomatis.
 *
 * Autoloader akan mencari file sesuai konfigurasi di sini
 * tanpa perlu require/include manual.
 *
 * CATATAN:
 * - Jika key yang sama digunakan di $psr4 atau $classmap,
 *   maka konfigurasi di file ini akan menimpa konfigurasi framework.
 *
 * - File ini dipanggil SEBELUM Autoloader diinisialisasi,
 *   sehingga class ini TIDAK mewarisi BaseConfig.
 */
class Autoload extends AutoloadConfig
{
    /**
     * -------------------------------------------------------------------
     * Namespaces
     * -------------------------------------------------------------------
         *
     * Digunakan untuk memetakan namespace ke folder fisik.
     * Autoloader akan mencari file berdasarkan namespace ini.
     *
     * Contoh:
     * App\Controllers\Home  -> app/Controllers/Home.php
     *
     * Namespace default yang sudah ada:
     * - Config     -> app/Config
     * - CodeIgniter-> system
     *
     * APP_NAMESPACE biasanya bernilai "App"
     * APPPATH menunjuk ke folder /app
     *
     * @var array<string, list<string>|string>
     */
    public $psr4 = [
        APP_NAMESPACE => APPPATH,
    ];

    /**
     * -------------------------------------------------------------------
     * Class Map
     * -------------------------------------------------------------------
      *
     * Digunakan untuk memetakan nama class langsung ke file tertentu.
     * Lebih cepat daripada PSR-4 karena tidak perlu scanning folder.
     *
     * Cocok untuk:
     * - Library khusus
     * - File lama (legacy)
     *
     * Contoh:
     * 'MyClass' => '/path/to/MyClass.php'
     *
     * @var array<string, string>
     */
    public $classmap = [];

    /**
     * -------------------------------------------------------------------
     * Files
     * -------------------------------------------------------------------
     *
     * Digunakan untuk memuat file NON-class secara otomatis.
     * Biasanya berisi file helper custom atau fungsi global.
     *
     * File di sini akan diload setiap request.
     *
     * Contoh penggunaan:
     * - File fungsi global
     * - File bootstrap tambahan
     *
     * @var list<string>
     */
    public $files = [];

    /**
     * -------------------------------------------------------------------
     * Helpers
     * -------------------------------------------------------------------
     * Helper yang akan dimuat otomatis tanpa perlu memanggil helper()
     *
     * Pada konfigurasi ini:
     * - 'form'    -> helper untuk form (form_open, form_input, dll)
     * - 'session' -> helper untuk manajemen session
     *
     * Helper ini langsung tersedia di semua controller dan view.
     *
     * @var list<string>
     */
    public $helpers = ['form', 'session'];
}
