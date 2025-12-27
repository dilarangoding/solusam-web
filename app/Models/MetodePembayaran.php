<?php

// Namespace model sesuai struktur CodeIgniter 4
namespace App\Models;

// Menggunakan Model bawaan CodeIgniter
use CodeIgniter\Model;

/**
 * MetodePembayaran Model
 * Model ini digunakan untuk berinteraksi dengan tabel metode_pembayaran
 * yang menyimpan daftar metode pembayaran milik masing-masing client
 */
class MetodePembayaran extends Model
{
     // Nama tabel di database yang diwakili oleh model ini
    protected $table            = 'metode_pembayaran';
    // Field yang diizinkan untuk operasi insert dan update
    // Digunakan untuk mencegah mass assignment vulnerability
    protected $allowedFields    = [
        'nama',  // Nama metode pembayaran (contoh: Tunai, Transfer, QRIS)
        'client_id'  // ID client pemilik metode pembayaran
    ];

    
    // Mengaktifkan fitur timestamps otomatis
    // created_at dan updated_at akan diisi otomatis oleh CodeIgniter
    protected $useTimestamps = true;
     // Nama kolom untuk waktu pembuatan data
    protected $createdField  = 'created_at';
     // Nama kolom untuk waktu terakhir update data
    protected $updatedField  = 'updated_at';
}
