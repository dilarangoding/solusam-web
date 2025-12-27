<?php

// Interface dasar untuk semua cache handler
namespace Config;

// Interface dasar untuk semua cache handler
use CodeIgniter\Cache\CacheInterface;
// Handler cache "dummy" (tidak benar-benar menyimpan cache)
use CodeIgniter\Cache\Handlers\DummyHandler;
// Handler cache berbasis file (disimpan di folder writable/cache)
use CodeIgniter\Cache\Handlers\FileHandler;
// Handler cache menggunakan layanan Memcached
use CodeIgniter\Cache\Handlers\MemcachedHandler;
// Handler cache Redis menggunakan library Predis (PHP murni)
use CodeIgniter\Cache\Handlers\PredisHandler;
// Handler cache Redis menggunakan ekstensi PHP Redis
use CodeIgniter\Cache\Handlers\RedisHandler;
// Handler cache untuk Windows (WinCache)
use CodeIgniter\Cache\Handlers\WincacheHandler;
// BaseConfig sebagai kelas dasar konfigurasi
use CodeIgniter\Config\BaseConfig;

// Class Cache digunakan untuk mengatur sistem caching global aplikasi
class Cache extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Primary Handler
     * --------------------------------------------------------------------------
      *
     * Menentukan handler cache utama yang digunakan aplikasi.
     * Jika handler ini tidak tersedia, maka $backupHandler akan digunakan.
     *
     * Contoh:
     * 'file'      -> cache disimpan dalam file
     * 'redis'     -> cache disimpan di Redis
     * 'memcached' -> cache disimpan di Memcached
     */
    public string $handler = 'file';

    /**
     * --------------------------------------------------------------------------
     * Backup Handler
     * --------------------------------------------------------------------------
     *
     * Handler cadangan jika handler utama gagal atau tidak tersedia.
     *
     * 'dummy' berarti cache akan dinonaktifkan tanpa error.
     * Biasanya digunakan agar aplikasi tetap berjalan.
     */
    public string $backupHandler = 'dummy';

    /**
     * --------------------------------------------------------------------------
     * Key Prefix
     * --------------------------------------------------------------------------
     *
     * Prefix yang akan ditambahkan pada setiap key cache.
     * Berguna untuk menghindari konflik cache jika:
     * - Satu server digunakan oleh banyak aplikasi
     */
    public string $prefix = '';

    /**
     * --------------------------------------------------------------------------
     * Default TTL
     * --------------------------------------------------------------------------
     *
     * Lama waktu (dalam detik) data cache disimpan secara default.
     *
     * Contoh:
     * 60 = cache berlaku selama 1 menit
     */
    public int $ttl = 60;

    /**
     * --------------------------------------------------------------------------
     * Reserved Characters
     * --------------------------------------------------------------------------
     *
     * Karakter yang tidak boleh digunakan pada key cache.
     * Jika digunakan, cache handler akan melempar error.
     *
     * Ini diperlukan untuk standar PSR-6.
     */
    public string $reservedCharacters = '{}()/\@:';

    /**
     * --------------------------------------------------------------------------
     * File settings
     * --------------------------------------------------------------------------
     *
     * Pengaturan khusus jika menggunakan FileHandler.
     *
     * storePath → lokasi folder penyimpanan cache
     * mode      → permission file cache
     * @var array{storePath?: string, mode?: int}
     */
    public array $file = [
        'storePath' => WRITEPATH . 'cache/',
        'mode'      => 0640,
    ];

    /**
     * -------------------------------------------------------------------------
     * Memcached settings
     * -------------------------------------------------------------------------
     *
     * Konfigurasi server Memcached jika digunakan.
     *
     * host   → alamat server
     * port   → port Memcached
     * weight → prioritas server
     * raw    → format penyimpanan data
     * @var array{host?: string, port?: int, weight?: int, raw?: bool}
     */
    public array $memcached = [
        'host'   => '127.0.0.1',
        'port'   => 11211,
        'weight' => 1,
        'raw'    => false,
    ];

    /**
     * -------------------------------------------------------------------------
     * Redis settings
     * -------------------------------------------------------------------------
      *
     * Konfigurasi Redis jika menggunakan RedisHandler atau PredisHandler.
     *
     * host     → alamat server Redis
     * password → password Redis (jika ada)
     * port     → port Redis
     * timeout  → batas waktu koneksi
     * database → index database Redis
     */
    public array $redis = [
        'host'     => '127.0.0.1',
        'password' => null,
        'port'     => 6379,
        'timeout'  => 0,
        'database' => 0,
    ];

    /**
     * --------------------------------------------------------------------------
     * Available Cache Handlers
     * --------------------------------------------------------------------------
     *
     * Daftar handler cache yang diperbolehkan digunakan aplikasi.
     *
     * Jika handler tidak ada di sini, maka tidak bisa dipakai.
     * @var array<string, class-string<CacheInterface>>
     */
    public array $validHandlers = [
        'dummy'     => DummyHandler::class,
        'file'      => FileHandler::class,
        'memcached' => MemcachedHandler::class,
        'predis'    => PredisHandler::class,
        'redis'     => RedisHandler::class,
        'wincache'  => WincacheHandler::class,
    ];

    /**
     * --------------------------------------------------------------------------
     * Web Page Caching: Cache Include Query String
     * --------------------------------------------------------------------------
     *
     * Mengatur apakah query string URL ikut dipertimbangkan
     * saat membuat cache halaman.
     *
     * false → query string diabaikan
     * true  → semua query string diperhitungkan
     * ['q'] → hanya parameter tertentu yang diperhitungkan
     * @var bool|list<string>
     */
    public $cacheQueryString = false;
}
