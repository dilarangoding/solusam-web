<?php

namespace App\Models;

use CodeIgniter\Model;

class Client extends Model
{
    protected $table            = 'client';
    protected $allowedFields    = ['user_id', 'nama_lengkap', 'no_telp', 'alamat', 'jenis_usaha', 'client_id'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
