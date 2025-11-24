<?php

namespace App\Models; // Namespace model agar bisa dipanggil dari controller

use CodeIgniter\Model; // Menggunakan class Model bawaan CI4

class MetodePembayaran extends Model
{
    protected $table         = 'metode_pembayaran'; // Nama tabel di database
    protected $allowedFields = [                     // Field yang diperbolehkan untuk insert/update
        'nama', 
        'client_id'
    ];

    // Fitur tanggal otomatis
    protected $useTimestamps = true;       // Aktifkan automatic timestamps
    protected $createdField  = 'created_at'; // Field untuk tanggal dibuat
    protected $updatedField  = 'updated_at'; // Field untuk tanggal update terakhir
}
