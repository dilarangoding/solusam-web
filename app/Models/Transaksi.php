<?php

namespace App\Models; // Namespace model agar bisa dipanggil dari controller atau library lain

use CodeIgniter\Model; // Menggunakan Model bawaan CodeIgniter 4

class Transaksi extends Model
{
    protected $table = 'transaksi'; // Nama tabel di database
    // Field yang diperbolehkan untuk insert atau update
    protected $allowedFields = [
        'tanggal',       // Tanggal transaksi
        'sampah_id',     // ID sampah yang dijual atau dibeli
        'jumlah',        // Jumlah sampah
        'jenis',         // Jenis transaksi (pembelian/penjualan)
        'client_id',     // ID client pemilik transaksi
        'pembeli',       // ID pembeli (client)
        'metode_bayar',  // Metode pembayaran
        'bukti'          // Bukti pembayaran (file)
    ];

    // Mengaktifkan timestamps otomatis
    protected $useTimestamps = true;
    protected $createdField  = 'created_at'; // Field untuk tanggal dibuat
    protected $updatedField  = 'updated_at'; // Field untuk tanggal update

    /**
     * Mengambil data penjualan atau pembelian berdasarkan client dan jenis transaksi
     * @param int $client_id ID client
     * @param string $jenis Jenis transaksi ('penjualan' atau 'pembelian')
     * @return array Array hasil query transaksi beserta detail sampah dan client
     */
    public function getPenjualan($client_id, $jenis)
    {
        // Membuat query builder untuk tabel transaksi (alias t)
        $builder = $this->db->table('transaksi t');

        // Memilih field dari transaksi, data_sampah, dan client
        $builder->select('
            t.*, 
            s.nama_sampah, s.harga_beli, s.harga_jual, s.satuan,
            c.nama_lengkap, c.no_telp, c.alamat, c.jenis_usaha
        ');

        // Join tabel data_sampah untuk mendapatkan info sampah
        $builder->join('data_sampah s', 's.id = t.sampah_id');
        // $builder->join('metode_pembayaran m', 'm.id = t.metode_bayar_id', 'left');

        // Join tabel client untuk mendapatkan info pembeli (LEFT JOIN agar tetap muncul meski null)
        $builder->join('client c', 'c.id = t.pembeli', 'left');

        // Filter berdasarkan client_id
        $builder->where('t.client_id', $client_id);

        // Filter berdasarkan jenis transaksi
        $builder->where('t.jenis', $jenis);

        // Urutkan hasil berdasarkan tanggal terbaru
        $builder->orderBy('t.tanggal', 'DESC');

        // Eksekusi query dan ambil hasil sebagai array
        $query = $builder->get()->getResultArray();

        return $query; // Kembalikan hasil query
    }

    // public function getLaporan($client_id, $tahun = null, $bulan = null, $tanggal_mulai = null, $tanggal_selesai = null)
    // {
    //     $builder = $this->db->table('transaksi t');
    //     $builder->select(
    //         'COUNT(t.id) as jumlah, 
    //         SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
    //         SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
    //         SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_jual) ELSE 0 END) - SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_keuntungan'
    //     );
    //     $builder->join('data_sampah s', 's.id = t.sampah_id');
    //     $builder->where('t.client_id', $client_id);

    //     if ($tahun) {
    //         $builder->where('YEAR(t.tanggal)', $tahun);
    //     }

    //     if ($bulan) {
    //         $builder->where('MONTH(t.tanggal)', $bulan);
    //     }

    //     if ($tanggal_mulai && $tanggal_selesai) {
    //         $builder->where('DATE(t.tanggal) >=', $tanggal_mulai);
    //         $builder->where('DATE(t.tanggal) <=', $tanggal_selesai);
    //     }

    //     $query = $builder->get()->getRowArray();
    //     return $query;
    // }

   public function getLaporan($client_id, $tahun = null, $bulan = null, $tanggal_mulai = null, $tanggal_selesai = null)
{
    $builder = $this->db->table('transaksi t'); // Membuat query builder untuk tabel transaksi (alias t)
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) - 
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_keuntungan'
    ); // Memilih field agregat: jumlah transaksi, total pendapatan, pengeluaran, dan keuntungan

    $builder->join('data_sampah s', 's.id = t.sampah_id'); // Join tabel data_sampah untuk mendapatkan harga
    $builder->where('t.client_id', $client_id); // Filter berdasarkan client_id

    // CASE: Group by Tahun → per Bulan
    if ($tahun && !$bulan && !$tanggal_mulai && !$tanggal_selesai) {
        $builder->select('MONTHNAME(t.tanggal) as periode'); // Ambil nama bulan sebagai periode
        $builder->where('YEAR(t.tanggal)', $tahun); // Filter berdasarkan tahun
        $builder->groupBy('YEAR(t.tanggal), MONTH(t.tanggal)'); // Group per bulan
        $builder->orderBy('MONTH(t.tanggal)', 'ASC'); // Urutkan dari Januari → Desember
    }

    // CASE: Group by Bulan → per Tanggal
    if ($bulan && $tahun) {
        $builder->select('DATE(t.tanggal) as periode'); // Ambil tanggal sebagai periode
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('MONTH(t.tanggal)', $bulan);
        $builder->groupBy('DATE(t.tanggal)'); // Group per tanggal
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); // Urutkan dari tanggal awal → akhir
    }

    // CASE: Range Harian
    if ($tanggal_mulai && $tanggal_selesai) {
        $builder->select('DATE(t.tanggal) as periode'); // Ambil tanggal sebagai periode
        $builder->where('DATE(t.tanggal) >=', $tanggal_mulai); // Filter mulai tanggal
        $builder->where('DATE(t.tanggal) <=', $tanggal_selesai); // Filter sampai tanggal
        $builder->groupBy('DATE(t.tanggal)'); // Group per tanggal
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); // Urutkan dari tanggal awal → akhir
    }

    $query = $builder->get()->getResultArray(); // Eksekusi query dan ambil hasil sebagai array
    return $query; // Kembalikan hasil

    public function getLastTransaction($client_id, $bulan)
    {
    $builder = $this->db->table('transaksi t'); // Query builder tabel transaksi
    $builder->select('t.*, s.nama_sampah, s.harga_beli, s.harga_jual, s.satuan'); // Pilih semua field transaksi + info sampah
    $builder->join('data_sampah s', 's.id = t.sampah_id'); // Join tabel sampah
    $builder->where('t.client_id', $client_id); // Filter client
    $builder->where('MONTH(t.tanggal)', $bulan); // Filter bulan transaksi
    $builder->orderBy('t.tanggal', 'DESC'); // Urutkan terbaru ke lama
    $builder->limit(3); // Ambil 3 transaksi terakhir
    $query = $builder->get()->getResultArray(); // Eksekusi query
    return $query; // Kembalikan hasil
    }

    public function getRingkasanBulan($client_id, $bulan, $tahun)
    {
    $builder = $this->db->table('transaksi t'); // Query builder tabel transaksi
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(t.jumlah) as total_jml,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(
            CASE 
                WHEN t.jenis = "out"  THEN (t.jumlah * s.harga_jual)
                WHEN t.jenis = "in" THEN -(t.jumlah * s.harga_beli)
                ELSE 0 
            END
        ) as total_keuntungan'
    ); // Hitung ringkasan bulan: jumlah, total jumlah, pendapatan, pengeluaran, keuntungan
    $builder->join('data_sampah s', 's.id = t.sampah_id'); // Join tabel data_sampah
    $builder->where('t.client_id', $client_id); // Filter client

    // CASE: Group by Bulan → per Tanggal
    if ($bulan && $tahun) {
        $builder->where('YEAR(t.tanggal)', $tahun);
        $builder->where('MONTH(t.tanggal)', $bulan);
        }

    $query = $builder->get()->getRowArray(); // Ambil satu baris hasil
    return $query; // Kembalikan hasil
    }

    public function getTotalAll($client_id)
    {
    $builder = $this->db->table('transaksi t'); // Query builder tabel transaksi
    $builder->select(
        'COUNT(t.id) as jumlah, 
        SUM(t.jumlah) as total_jml,
        SUM(CASE WHEN t.jenis = "out" THEN (t.jumlah * s.harga_jual) ELSE 0 END) as total_pendapatan,
        SUM(CASE WHEN t.jenis = "in" THEN (t.jumlah * s.harga_beli) ELSE 0 END) as total_pengeluaran,
        SUM(
            CASE 
                WHEN t.jenis = "out"  THEN (t.jumlah * s.harga_jual)
                WHEN t.jenis = "in" THEN -(t.jumlah * s.harga_beli)
                ELSE 0 
            END
        ) as total_keuntungan'
    ); // Hitung total keseluruhan: jumlah transaksi, total jumlah, pendapatan, pengeluaran, keuntungan
    $builder->join('data_sampah s', 's.id = t.sampah_id'); // Join tabel data_sampah
    $builder->where('t.client_id', $client_id); // Filter client

    $query = $builder->get()->getRowArray(); // Ambil satu baris hasil
    return $query; // Kembalikan hasil
    }

    public function getDataInOutLaporan($client_id, $jenis = null, $tahun = null, $bulan = null, $tanggal_mulai = null, $tanggal_selesai = null)
{
    $builder = $this->db->table('transaksi t'); // Query builder tabel transaksi
    $builder->select(
        't.*,
        s.nama_sampah,
        s.harga_jual,
        s.harga_beli,
        s.satuan,
        c.nama_lengkap,
        (t.jumlah * s.harga_jual) as total_pendapatan,
        (t.jumlah * s.harga_beli) as total_pengeluaran'
    ); // Pilih field transaksi, info sampah, info client, total pendapatan & pengeluaran
    $builder->join('data_sampah s', 's.id = t.sampah_id'); // Join data_sampah
    $builder->join('client c', 'c.id = t.pembeli', 'left'); // Join client (LEFT JOIN)
    $builder->where('t.client_id', $client_id); // Filter client

    if ($jenis == 'in') {
        $builder->where('t.jenis', 'in'); // Filter transaksi masuk
        } else {
        $builder->where('t.jenis', 'out'); // Filter transaksi keluar
        }

    // CASE: Group by Tahun → per Bulan
        if ($tahun && !$bulan && !$tanggal_mulai && !$tanggal_selesai) {
        $builder->where('YEAR(t.tanggal)', $tahun); // Filter tahun
        // $builder->groupBy('YEAR(t.tanggal), MONTH(t.tanggal)');
        $builder->orderBy('MONTH(t.tanggal)', 'ASC'); // Urutkan bulan
        }

    // CASE: Group by Bulan → per Tanggal
        if ($bulan && $tahun) {
        $builder->where('YEAR(t.tanggal)', $tahun); // Filter tahun
        $builder->where('MONTH(t.tanggal)', $bulan); // Filter bulan
        // $builder->groupBy('DATE(t.tanggal)');
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); // Urutkan tanggal
        }

    // CASE: Range Harian
        if ($tanggal_mulai && $tanggal_selesai) {
        $builder->where('DATE(t.tanggal) >=', $tanggal_mulai); // Filter mulai tanggal
        $builder->where('DATE(t.tanggal) <=', $tanggal_selesai); // Filter sampai tanggal
        // $builder->groupBy('DATE(t.tanggal)');
        $builder->orderBy('DATE(t.tanggal)', 'ASC'); // Urutkan tanggal
        }

    $query = $builder->get()->getResultArray(); // Eksekusi query
    return $query; // Kembalikan hasil
    }


    /**
     * Menghitung stok tersedia untuk sampah tertentu
     * @param int $sampah_id ID sampah
     * @param int $client_id ID client
     * @return float Stok tersedia
     */
    public function getStokTersedia($sampah_id, $client_id)
    {
    $builder = $this->db->table('transaksi'); // Buat query builder untuk tabel transaksi
    $builder->select('
        SUM(CASE WHEN jenis = "in" THEN jumlah ELSE 0 END) - 
        SUM(CASE WHEN jenis = "out" THEN jumlah ELSE 0 END) as stok_tersedia
    '); // Hitung stok tersedia: total masuk - total keluar

    $builder->where('sampah_id', $sampah_id); // Filter berdasarkan ID sampah
    $builder->where('client_id', $client_id); // Filter berdasarkan client_id

    $result = $builder->get()->getRowArray(); // Eksekusi query dan ambil hasil sebagai array
    return $result['stok_tersedia'] ?? 0; // Kembalikan stok_tersedia, jika null kembalikan 0
    }

}
