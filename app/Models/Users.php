<?php

namespace App\Models;

use CodeIgniter\Model;

class Users extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $allowedFields = ['kode_user', 'username', 'email', 'password', 'nama_lengkap', 'no_telp', 'alamat', 'google_id', 'role', 'status', 'last_login','auth_type'];

    // Dates
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function generateKode($nama)
    {
        $builder = $this->db->table('users');

        $namaDepan = explode(' ', trim($nama))[0];

        $inisial = strtoupper(substr($namaDepan, 0, 2));

        $tahun = date('y'); // contoh: 25
        $bulan = date('m'); // contoh: 08

        // cari nomor urut terakhir bulan ini
        $likeKode = $inisial . $tahun . $bulan;
        $last = $builder->select('kode_user')
            ->like('kode_user', $likeKode, 'after')
            ->orderBy('id', 'DESC')
            ->get()
            ->getRow();


        if ($last) {
            // ambil digit terakhir setelah tahun+bulan
            $lastNumber = (int) substr($last->kode_user, -1);
            $newNumber  = $lastNumber + 1;
        } else {
            $newNumber = 1; // reset awal bulan
        }

        return $inisial . $tahun . $bulan . $newNumber;
    }

  public function getUser($user)
{
    return $this->where('username', $user)
                ->orWhere('email', $user)
                ->first();
}


}
