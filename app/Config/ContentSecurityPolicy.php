<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;


/**
 * Class ContentSecurityPolicy
 *
 * File konfigurasi ini digunakan untuk mengatur
 * Content Security Policy (CSP) pada aplikasi CodeIgniter.
 *
 * CSP berfungsi untuk meningkatkan keamanan aplikasi web
 * dengan membatasi sumber (source) mana saja yang diizinkan
 * untuk memuat script, style, gambar, font, iframe, dan resource lainnya.
 */
class ContentSecurityPolicy extends BaseConfig
{
    // -------------------------------------------------------------------------
    // Broadbrush CSP management
    // -------------------------------------------------------------------------

     /**
     * Menentukan apakah CSP dijalankan dalam mode report-only.
     *
     * false = CSP benar-benar diterapkan (resource diblokir)
     * true  = CSP hanya melaporkan pelanggaran tanpa memblokir
     */
    public bool $reportOnly = false;

    /**
      * URL tujuan laporan jika terjadi pelanggaran CSP.
     *
     * Browser akan mengirim laporan ke URL ini
     * jika ada resource yang melanggar kebijakan CSP.
     *
     * null berarti tidak mengirim laporan ke mana pun.
     */
    public ?string $reportURI = null;

    /**
    * Jika true, browser akan otomatis mengubah request
     * dari HTTP menjadi HTTPS.
     *
     * Berguna jika aplikasi memiliki banyak URL lama
     * yang masih menggunakan HTTP.
     */
    public bool $upgradeInsecureRequests = false;

    // -------------------------------------------------------------------------
    // Sumber (Source) yang diizinkan
    // CATATAN: jika diset 'none', tidak bisa dibatasi lagi
    // -------------------------------------------------------------------------

    /**
     * Sumber default untuk semua resource.
     *
     * Jika tidak diset, akan otomatis menggunakan 'self'
     * (resource hanya boleh berasal dari domain sendiri).
     *
     * @var list<string>|string|null
     */
    public $defaultSrc;

    /**
    * Menentukan sumber yang diizinkan untuk JavaScript.
     *
     * 'self' artinya hanya script dari domain sendiri
     * yang diperbolehkan.
     *
     * @var list<string>|string
     */
    public $scriptSrc = 'self';

    /**
      * Menentukan sumber yang diizinkan untuk file CSS.
     * @var list<string>|string
     */
    public $styleSrc = 'self';

    /**
      * Menentukan sumber yang diizinkan untuk gambar.
     * @var list<string>|string
     */
    public $imageSrc = 'self';

    /**
    * Membatasi URL yang boleh digunakan
     * pada tag <base>.
     *
     * @var list<string>|string|null
     */
    public $baseURI;

    /**
    * Menentukan sumber untuk worker dan iframe anak.
     *
     * @var list<string>|string
     */
    public $childSrc = 'self';

    /**
     * Menentukan sumber yang boleh dihubungi
     * melalui AJAX, WebSocket, EventSource, dll.
     *
     * @var list<string>|string
     */
    public $connectSrc = 'self';

    /**
      * Menentukan sumber font (web font).
     *
     * @var list<string>|string
     */
    public $fontSrc;

    /**
     * Menentukan URL tujuan submit form.
     *
     * @var list<string>|string
     */
    public $formAction = 'self';

    /**
    * Menentukan domain mana saja
     * yang diizinkan untuk menampilkan halaman ini
     * dalam iframe.
     *
     * @var list<string>|string|null
     */
    public $frameAncestors;

    /**
      * Menentukan sumber yang diizinkan
     * untuk konten iframe.
     *
     * @var list<string>|string|null
     */
    public $frameSrc;

    /**
  * Menentukan sumber audio dan video.
     *
     * @var list<string>|string|null
     */
    public $mediaSrc;

    /**
     * Menentukan sumber object seperti Flash.
     *
     * @var list<string>|string
     */
    public $objectSrc = 'self';

    /**
     * Menentukan sumber manifest web app.
     *
     * @var list<string>|string|null
     */
    public $manifestSrc;

    /**
     * Menentukan tipe plugin yang diizinkan.
     *
     * @var list<string>|string|null
     */
    public $pluginTypes;

    /**
      * Mengatur sandboxing untuk halaman.
     *
     * @var list<string>|string|null
     */
    public $sandbox;

    /**
       * Placeholder nonce untuk tag style.
     *
     * Digunakan untuk mengizinkan inline CSS
     * yang memiliki nonce yang valid.
     */
    public string $styleNonceTag = '{csp-style-nonce}';
    /**
     * Placeholder nonce untuk tag script.
     *
     * Digunakan untuk mengizinkan inline JavaScript
     * yang memiliki nonce yang valid.
     */
    public string $scriptNonceTag = '{csp-script-nonce}';

    /**
      * Jika true, CodeIgniter akan otomatis
     * mengganti nonce tag dengan nilai nonce yang valid.
     */
    public bool $autoNonce = true;
}
