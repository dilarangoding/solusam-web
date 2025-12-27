<?php

// Namespace Config digunakan untuk semua file konfigurasi CodeIgniter
namespace Config;

// Menggunakan BaseConfig sebagai dasar konfigurasi
// Semua file config di CodeIgniter biasanya mewarisi BaseConfig
use CodeIgniter\Config\BaseConfig;

// Class CURLRequest digunakan untuk mengatur perilaku HTTP Client (CURL)
// di seluruh aplikasi CodeIgniter
class CURLRequest extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * CURLRequest Share Options
     * --------------------------------------------------------------------------
    *
     * Properti ini menentukan apakah opsi CURL
     * akan dibagikan (di-share) antar request HTTP atau tidak.
     *
     * Jika bernilai true:
     * - Opsi CURL seperti header, timeout, dll
     *   akan tetap digunakan pada request berikutnya.
     * - Berpotensi menyebabkan error karena header lama
     *   ikut terbawa ke request lain.
     *
     * Jika bernilai false (default):
     * - Setiap request HTTP menggunakan konfigurasi baru
     * - Lebih aman untuk aplikasi web
     *
     * Umumnya disarankan tetap false kecuali benar-benar dibutuhkan.
     */
    public bool $shareOptions = false;
}
