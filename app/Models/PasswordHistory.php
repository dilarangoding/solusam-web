<?php
namespace App\Models; // Namespace model agar bisa dipanggil dari controller atau library lain

use CodeIgniter\Model; // Menggunakan class Model bawaan CodeIgniter 4

class PasswordHistory extends Model
{
    protected $table = 'password_history'; // Nama tabel di database

    // Field yang diperbolehkan untuk insert atau update
    protected $allowedFields = [
        'user_id',       // ID user yang password-nya disimpan
        'password_hash', // Hash password
        'created_at',    // Tanggal dibuat (opsional jika ingin override default)
        'updated_at'     // Tanggal update (opsional jika ingin override default)
    ];

    // Aktifkan timestamps otomatis (CI4 akan otomatis mengisi created_at & updated_at)
    protected $useTimestamps = true;
}
