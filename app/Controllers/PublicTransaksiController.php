<?php

namespace App\Controllers;

use App\Controllers\BaseController;
// Import model Transaksi
use App\Models\Transaksi;
// Import model Sampah
use App\Models\Sampah;

/**
 * PublicTransaksiController
 * Controller untuk menampilkan data transaksi secara publik
 * (tanpa perlu login), termasuk detail transaksi dan QR Code
 */
class PublicTransaksiController extends BaseController
{
     // Properti untuk menyimpan instance model Transaksi
    protected $transaksiModel;
     // Properti untuk menyimpan instance model Sampah
    protected $sampahModel;

     /**
     * Constructor
     * Dipanggil otomatis saat controller dibuat
     * Berfungsi untuk inisialisasi model yang digunakan
     */
    public function __construct()
    {
        // Inisialisasi model Transaksi
        $this->transaksiModel = new Transaksi();
         // Inisialisasi model Sampah
        $this->sampahModel = new Sampah();
    }

     /**
     * Menampilkan detail transaksi secara publik berdasarkan ID
     *
     * @param int $id ID transaksi
     * @return mixed View detail transaksi atau halaman error
     */
    public function detail($id)
    {
        try {
           // Membuat query builder manual dari database
            // Alias tabel transaksi sebagai "t"
            $builder = $this->transaksiModel->db->table('transaksi t');
            
             // Menentukan kolom yang akan diambil dari beberapa tabel
            $builder->select(
                't.*, 
                s.nama_sampah, 
                s.harga_jual, 
                s.satuan, 
                c.nama_lengkap, 
                c.no_telp, 
                c.alamat, 
                c.jenis_usaha'
            );
            // Join ke tabel data_sampah berdasarkan sampah_id
            $builder->join('data_sampah s', 's.id = t.sampah_id');
            // Join ke tabel client berdasarkan client_id (LEFT JOIN agar tidak error jika null)
            $builder->join('client c', 'c.id = t.client_id', 'left');
             // Filter transaksi berdasarkan ID
            $builder->where('t.id', $id);

             // Eksekusi query dan ambil satu baris hasil sebagai array
            $transaksi = $builder->get()->getRowArray();

             // Jika transaksi tidak ditemukan, lempar error 404
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

             // Data yang akan dikirim ke view
            $data = [
                'title' => 'Detail Transaksi #' . $id,
                'transaksi' => $transaksi,
                'total_harga' => $transaksi['jumlah'] * $transaksi['harga_jual']
            ];

            // Tampilkan view detail transaksi publik
            return view('public/transaksi_detail', $data);
        } catch (\Exception $e) {
            // Jika terjadi error (data tidak ada / terhapus)
            return view('public/error', [
                'title' => 'Error',
                'message' => 'Transaksi tidak ditemukan atau telah dihapus.',
                'error_code' => 404
            ]);
        }
    }

    /**
     * Generate QR Code untuk transaksi publik
     *
     * QR Code berisi URL publik yang mengarah ke detail transaksi
     *
     * @param int $id ID transaksi
     * @return Response image PNG
     */
    public function generateQrCode($id)
    {
        try {
            // Ambil data transaksi
            $transaksi = $this->transaksiModel->find($id);

             // Jika transaksi tidak ditemukan, lempar error 404
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            // URL publik yang akan dimasukkan ke dalam QR Code
            $qrData = base_url('public/transaksi/' . $id);

             // Membuat objek QR Code dengan data URL
            $qrCode = new \Endroid\QrCode\QrCode($qrData);
              // Menggunakan writer PNG
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            // Generate hasil QR Code
            $result = $writer->write($qrCode);

          // Set header response sebagai gambar PNG
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="qrcode-' . $id . '.png"');

             // Kirim hasil QR Code ke browser
            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
             // Jika terjadi error, tetap kembalikan gambar PNG
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="error.png"');

            // Buat error image sederhana
            $errorImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            return $this->response->setBody($errorImage);
        }
    }
}
