<?php


// Namespace Config digunakan untuk menyimpan seluruh file konfigurasi aplikasi CodeIgniter
namespace Config;

// Mengimpor BaseConfig sebagai kelas dasar konfigurasi
use CodeIgniter\Config\BaseConfig;
// Digunakan untuk tipe data tanggal/waktu pada properti expires
use DateTimeInterface;

// Class Cookie berfungsi sebagai konfigurasi global cookie di aplikasi
class Cookie extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Cookie Prefix
     * --------------------------------------------------------------------------
     *
     * Prefix akan ditambahkan di depan nama setiap cookie.
     * Berguna untuk menghindari bentrok cookie jika ada beberapa aplikasi
     * dalam satu domain.
     */
    public string $prefix = '';

    /**
     * --------------------------------------------------------------------------
     * Cookie Expires Timestamp
     * --------------------------------------------------------------------------
    * Menentukan waktu kedaluwarsa cookie.
     * - 0  : cookie bersifat session (hilang saat browser ditutup)
     * - int/string/DateTimeInterface : waktu tertentu
     *
     * @var DateTimeInterface|int|string
     */
    public $expires = 0;

    /**
     * --------------------------------------------------------------------------
     * Cookie Path
     * --------------------------------------------------------------------------
    *
     * Menentukan path URL di mana cookie berlaku.
     * '/' berarti cookie berlaku untuk seluruh website.
     */
    public string $path = '/';

    /**
     * --------------------------------------------------------------------------
     * Cookie Domain
     * --------------------------------------------------------------------------
     *
     * Menentukan domain cookie.
     * Contoh: '.example.com' agar berlaku di seluruh subdomain.
     */
    public string $domain = '';

    /**
     * --------------------------------------------------------------------------
     * Cookie Secure
     * --------------------------------------------------------------------------
     *
     * Jika true, cookie hanya akan dikirim melalui koneksi HTTPS.
     * Sangat disarankan untuk aplikasi production.
     */
    public bool $secure = false;

    /**
     * --------------------------------------------------------------------------
     * Cookie HTTPOnly
     * --------------------------------------------------------------------------
     *
     * Jika true, cookie tidak bisa diakses oleh JavaScript.
     * Ini melindungi dari serangan XSS (Cross-Site Scripting).
     */
    public bool $httponly = true;

    /**
     * --------------------------------------------------------------------------
     * Cookie SameSite
     * --------------------------------------------------------------------------
     *
     * Mengatur kebijakan pengiriman cookie lintas situs (cross-site).
     *
     * Nilai yang diperbolehkan:
     * - None   : dikirim di semua request (harus HTTPS)
     * - Lax    : dikirim pada navigasi normal (default)
     * - Strict : hanya dikirim dari situs yang sama
     * - ''     : mengikuti default browser
     *
     * @var ''|'Lax'|'None'|'Strict'
     */
    public string $samesite = 'Lax';

    /**
     * --------------------------------------------------------------------------
     * Cookie Raw
     * --------------------------------------------------------------------------
     *
     * Jika true, nama dan nilai cookie tidak akan di-URL encode.
     * Harus dipastikan karakter cookie sesuai standar RFC 2616.
     */
    public bool $raw = false;
}
