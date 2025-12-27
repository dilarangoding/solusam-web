<?php

// Semua controller aplikasi berada di App\Controllers
namespace App\Controllers;

// Import class Controller utama dari CodeIgniter
use CodeIgniter\Controller;
// Import class Request khusus CLI (command line)
use CodeIgniter\HTTP\CLIRequest;
// Import class Request untuk HTTP (web request)
use CodeIgniter\HTTP\IncomingRequest;
// Import interface Request
use CodeIgniter\HTTP\RequestInterface;
// Import interface Response
use CodeIgniter\HTTP\ResponseInterface;
// Import Logger sesuai standar PSR (PHP Standard Recommendation)
use Psr\Log\LoggerInterface;

/**
 * Class BaseController
 *
 * BaseController adalah controller induk (parent controller)
 * Semua controller lain DIANJURKAN mewarisi class ini.
 *
 * Tujuan:
 * - Menyediakan tempat umum untuk inisialisasi
 * - Menghindari duplikasi kode di setiap controller
 *
 * Contoh penggunaan:
 * class Home extends BaseController
 *
 * Catatan keamanan:
 * Method baru sebaiknya protected atau private
 */
abstract class BaseController extends Controller
{
    /**
     * Instance utama Request
     * Bisa berupa:
     * - CLIRequest → jika dijalankan lewat terminal
     * - IncomingRequest → jika lewat browser
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

      /**
     * Daftar helper yang otomatis dimuat
     * saat controller dipanggil
     *
     * Helper adalah kumpulan fungsi siap pakai
     * Contoh: form, url, text, dll
     *
     * @var list<string>
     */
    protected $helpers = [];

     
    /**
     * Catatan PHP 8.2:
     * Properti dinamis tidak disarankan
     *
     * Jika ingin menggunakan session,
     * sebaiknya dideklarasikan di sini
     */
    // protected $session;

    /**
     * Method initController
     *
     * Method ini otomatis dipanggil SETIAP KALI
     * controller dijalankan
     *
     * @param RequestInterface  $request  → data request dari user
     * @param ResponseInterface $response → response ke browser
     * @param LoggerInterface   $logger   → pencatatan log sistem
     *
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
         // WAJIB dipanggil
        // Menjalankan proses inisialisasi bawaan CI4
        parent::initController($request, $response, $logger);

       // Tempat untuk preload:
        // - session
        // - model
        // - library
        // - service lain

        // Contoh jika ingin aktifkan session:
        // $this->session = service('session');
    }
}
