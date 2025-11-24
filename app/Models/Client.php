<?php

namespace App\Models; // Namespace model agar bisa dipanggil di controller

use CodeIgniter\Model; // Menggunakan class Model dari CI4

class Client extends Model
{
    protected $table         = 'client'; // Nama tabel di database
    protected $allowedFields = [          // Field yang boleh diisi melalui insert/update
        'user_id', 
        'nama_lengkap', 
        'no_telp', 
        'alamat', 
        'jenis_usaha', 
        'client_id'
    ];

    // Fitur tanggal otomatis
    protected $useTimestamps = true;      // Aktifkan automatic timestamps
    protected $createdField  = 'created_at'; // Nama field untuk tanggal dibuat
    protected $updatedField  = 'updated_at'; // Nama field untuk tanggal update terakhir
}
