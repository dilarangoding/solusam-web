<?php

// Namespace konfigurasi bawaan CodeIgniter 4
// Semua file config berada di namespace Config
namespace Config;

// Menggunakan BaseConfig sebagai parent class
// BaseConfig menyediakan struktur dasar konfigurasi aplikasi
use CodeIgniter\Config\BaseConfig;


// Kelas App berisi konfigurasi utama aplikasi CodeIgnite
class App extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Base Site URL
     * --------------------------------------------------------------------------
      * URL dasar aplikasi CodeIgniter.
     * Digunakan untuk membuat URL otomatis (base_url(), site_url()).
     * WAJIB diakhiri dengan slash (/).
     *
     * Contoh:
     * http://localhost/solusam/public/
     */
    public string $baseURL = 'http://localhost/solusam/public/';

    /**
     * Allowed Hostnames
     *
     * Daftar hostname tambahan yang diizinkan selain hostname dari baseURL.
     * Berguna jika aplikasi diakses dari beberapa domain/subdomain.
     *
     * Jika tidak digunakan, biarkan kosong.
     * @var list<string>
     */
    public array $allowedHostnames = [];

    /**
     * --------------------------------------------------------------------------
     * Index File
     * --------------------------------------------------------------------------
      *
     * Nama file index utama aplikasi.
     * Biasanya `index.php`.
     *
     * Jika server sudah dikonfigurasi untuk menghilangkan index.php dari URL,
     * maka nilai ini dikosongkan.
     */
    public string $indexPage = '';

    /**
     * --------------------------------------------------------------------------
     * URI PROTOCOL
     * --------------------------------------------------------------------------
     *
     * Menentukan variabel server yang digunakan untuk membaca URI.
     *
     * REQUEST_URI  -> Umum dan direkomendasikan
     * QUERY_STRING -> Mengambil dari query string
     * PATH_INFO    -> Mengambil dari PATH_INFO
     */
    public string $uriProtocol = 'REQUEST_URI';

    /*
    |--------------------------------------------------------------------------
    | Allowed URL Characters
    |--------------------------------------------------------------------------
     * Menentukan karakter yang diizinkan dalam URL.
     * Digunakan sebagai pengaman agar URL tidak mengandung karakter berbahaya.
     *
     * Default:
     * a-z 0-9 ~ % . : _ -
    */
    public string $permittedURIChars = 'a-z 0-9~%.:_\-';

    /**
     * --------------------------------------------------------------------------
     * Default Locale
     * --------------------------------------------------------------------------
      *
     * Bahasa default aplikasi.
     * Digunakan untuk file bahasa (Language Files).
     *
     * Contoh:
     * en  -> English
     * id  -> Indonesia
     */
    public string $defaultLocale = 'en';

    /**
     * --------------------------------------------------------------------------
     * Negotiate Locale
     * --------------------------------------------------------------------------
      *
     * Jika true:
     * Bahasa otomatis dipilih berdasarkan header Accept-Language browser user.
     *
     * Jika false:
     * Bahasa selalu menggunakan defaultLocale.
     */
    public bool $negotiateLocale = false;

    /**
     * --------------------------------------------------------------------------
     * Supported Locales
     * --------------------------------------------------------------------------
     *
     * Daftar bahasa yang didukung aplikasi.
     * Digunakan jika negotiateLocale diaktifkan.
     * @var list<string>
     */
    public array $supportedLocales = ['en'];

    /**
     * --------------------------------------------------------------------------
     * Application Timezone
     * --------------------------------------------------------------------------
     *
     * Zona waktu default aplikasi.
     * Digunakan untuk fungsi tanggal dan waktu (date, time).
     *
     * Asia/Jakarta cocok untuk aplikasi di Indonesia.
     */
    public string $appTimezone = 'Asia/Jakarta';

    /**
     * --------------------------------------------------------------------------
     * Default Character Set
     * --------------------------------------------------------------------------
      *
     * Charset default aplikasi.
     * UTF-8 mendukung hampir semua karakter (aman untuk multibahasa).
     */
    public string $charset = 'UTF-8';

    /**
     * --------------------------------------------------------------------------
     * Force Global Secure Requests
     * --------------------------------------------------------------------------
     *
     * Jika true:
     * Semua request akan dipaksa menggunakan HTTPS.
     *
     * Jika request HTTP masuk, otomatis redirect ke HTTPS.
     */
    public bool $forceGlobalSecureRequests = false;

    /**
     * --------------------------------------------------------------------------
     * Reverse Proxy IPs
     * --------------------------------------------------------------------------
     *
     * Digunakan jika aplikasi berada di balik reverse proxy
     * (contoh: Cloudflare, Nginx, Load Balancer).
     *
     * Agar IP user terbaca dengan benar.
     * @var array<string, string>
     */
    public array $proxyIPs = [];

    /**
     * --------------------------------------------------------------------------
     * Content Security Policy
     * --------------------------------------------------------------------------
     *
     * Mengaktifkan Content Security Policy.
     * Digunakan untuk meningkatkan keamanan terhadap XSS dan injection.
     *
     * Jika true:
     * Aturan CSP diambil dari Config/ContentSecurityPolicy.php
     */
    public bool $CSPEnabled = false;
}
