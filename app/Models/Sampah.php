<?php

namespace App\Models; // Namespace model agar bisa dipanggil dari controller atau library lain

use CodeIgniter\Model; // Menggunakan Model bawaan CodeIgniter 4

class Sampah extends Model
{
    protected $table = 'data_sampah'; // Nama tabel di database
    // Field yang diperbolehkan untuk insert atau update
    protected $allowedFields = [
        'nama_sampah',  // Nama jenis sampah
        'harga_beli',   // Harga beli dari klien
        'harga_jual',   // Harga jual ke pihak lain
        'satuan',       // Satuan atau jumlah stok
        'client_id'     // ID client pemilik sampah
    ];

    // Mengaktifkan timestamps otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // Field untuk tanggal dibuat
    protected $updatedField  = 'updated_at'; // Field untuk tanggal update

    /**
     * Mengambil stok tersedia dari sampah tertentu
     * @param int $sampah_id ID sampah
     * @return int Stok tersedia (default 0 jika tidak ditemukan)
     */
    public function getStokTersedia($sampah_id)
    {
        // Membuat query builder untuk tabel data_sampah
        $builder = $this->db->table('data_sampah');

        // Memilih field 'satuan' sebagai stok_tersedia
        $builder->select('satuan as stok_tersedia');

        // Menentukan kondisi WHERE id = $sampah_id
        $builder->where('id', $sampah_id);

        // Menjalankan query dan mengambil hasil sebagai array
        $result = $builder->get()->getRowArray();

        // Mengembalikan nilai stok_tersedia, jika tidak ada maka default 0
        return $result['stok_tersedia'] ?? 0;
    }
}
