<?php

namespace App\Models; // Mendefinisikan namespace model

use CodeIgniter\Model;

class Users extends Model
{
    protected $table         = 'users'; // Nama tabel
    protected $primaryKey    = 'id';    // Primary key
    protected $allowedFields = ['kode_user', 'username', 'email', 'password', 'google_id', 'role', 'status', 'last_login']; // Field yang bisa diisi massal

    // Dates
    protected $useTimestamps = true;      // Aktifkan automatic timestamps
    protected $createdField  = 'created_at'; // Field created_at
    protected $updatedField  = 'updated_at'; // Field updated_at

    /**
     * Generate kode user berdasarkan nama dan bulan-tahun saat ini
     * @param string $nama Nama user
     * @return string Kode user baru
     */
    public function generateKode($nama)
    {
        $builder = $this->db->table('users'); // Buat query builder untuk tabel users

        $namaDepan = explode(' ', trim($nama))[0]; // Ambil kata pertama dari nama

        $inisial = strtoupper(substr($namaDepan, 0, 2)); // Ambil 2 huruf pertama sebagai inisial

        $tahun = date('y'); // Ambil 2 digit tahun, contoh: 25
        $bulan = date('m'); // Ambil 2 digit bulan, contoh: 08

        // Membuat pola kode untuk mencari kode terakhir
        $likeKode = $inisial . $tahun . $bulan;

        // Ambil kode terakhir yang sesuai pola
        $last = $builder->select('kode_user')
            ->like('kode_user', $likeKode, 'after') // Cari kode yang diawali dengan inisial+tahun+bulan
            ->orderBy('id', 'DESC') // Urutkan dari ID terbesar
            ->get()
            ->getRow(); // Ambil satu row terakhir

        if ($last) {
            // Ambil digit terakhir dari kode terakhir dan tambah 1
            $lastNumber = (int) substr($last->kode_user, -1);
            $newNumber  = $lastNumber + 1;
        } else {
            $newNumber = 1; // Jika tidak ada kode sebelumnya, mulai dari 1
        }

        return $inisial . $tahun . $bulan . $newNumber; // Gabungkan menjadi kode baru
    }

    /**
     * Ambil data user beserta data client
     * @param string $user Username atau email
     * @return array|null Data user dan client
     */
    public function getUser($user)
    {
        $builder = $this->db->table('users u'); // Buat query builder dari tabel users
        $builder->select('u.kode_user, u.username, u.email, u.role, u.status, u.id as user_id, u.password,
        c.id, c.nama_lengkap, c.no_telp, c.alamat, c.jenis_usaha'); // Pilih field dari users dan client
        $builder->join('client c', 'c.user_id = u.id'); // Join tabel client berdasarkan user_id
        $builder->where('u.username', $user)->orWhere('u.email', $user); // Filter berdasarkan username atau email
        $query = $builder->get()->getRowArray(); // Eksekusi query dan ambil satu row
        return $query; // Kembalikan hasil
    }
}
