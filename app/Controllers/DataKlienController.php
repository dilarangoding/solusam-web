<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DataKlienController extends BaseController
{
    // Model untuk tabel klien
    protected $klienModel;
    // Model untuk tabel transaksi
    protected $transaksiModel;

    // Model untuk tabel transaksi
    public function __construct()
    {
         // Inisialisasi model Client
        $this->klienModel = new \App\Models\Client();
        // Inisialisasi model Transaksi
        $this->transaksiModel = new \App\Models\Transaksi();
    }
    
    // Menampilkan daftar data klien
    public function index()
    {
        $data = [
            "title" => "Data Klien",
            // Mengambil semua data klien berdasarkan client yang sedang login
            "data" => $this->klienModel ->where('client_id', session('clientId'))
            ->findAll()
        ];

        // Menampilkan view daftar klien
        return view('klien/index', $data);
    }

    // Menampilkan form tambah klien
    public function create()
    {
        $data = [
            "title" => "Tambah Data Klien"
        ];

        // Menampilkan view form tambah klien
        return view('klien/create', $data);
    }

    // Menampilkan form edit klien berdasarkan ID
    public function edit($id)
    {
       $data = [
    "title" => "Edit Data Klien",
    // Mengambil data klien berdasarkan:
            // - id klien
            // - user yang login
            // - client yang login
    "data" => $this->klienModel
        ->where('id', $id)
        ->where('user_id', session('userId'))
        ->where('client_id', session('clientId'))
        ->first()
];
        // Menampilkan view edit klien
        return view('klien/edit', $data);
    }

    // Menyimpan data klien (insert & update)
    public function store()
    {
        // Mengambil input dari form
        $nama_lengkap = strtoupper($this->request->getPost('nama_lengkap'));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $jenis_usaha = $this->request->getPost('jenis_usaha');
        $id = $this->request->getPost('id');
        
        // Data yang akan disimpan ke database
        $data = [
            'user_id'  => session('userId'),
            'client_id' => session('clientId'),
            'nama_lengkap' => $nama_lengkap,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'jenis_usaha' => $jenis_usaha,
        ];

         // Jika ada ID berarti update
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            // Jika tidak ada ID berarti insert
            $text = 'ditambahkan';
        }

        try {
            // Simpan data ke database (insert / update otomatis)
            $this->klienModel->save($data);
            // Pesan sukses
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('data-klien');
        } catch (\Throwable $th) {
            // Pesan error jika gagal
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }
    
    // Menghapus data klien
   public function delete()
{
    // Mengambil ID klien dari request

    $id = $this->request->getPost('id');
    
    try {
        // Mengecek apakah klien memiliki transaksi pembelian (jenis 'in')
        $transaksiPembelian = $this->transaksiModel
            ->where('pembeli', $id)
            ->where('jenis', 'in')
            ->countAllResults();
        
        // Mengecek apakah klien memiliki transaksi penjualan (jenis 'out')
        $transaksiPenjualan = $this->transaksiModel
            ->where('pembeli', $id)
            ->where('jenis', 'out')
            ->countAllResults();

         // Total seluruh transaksi
        $totalTransaksi = $transaksiPembelian + $transaksiPenjualan;
        
         // Jika masih ada transaksi, klien tidak boleh dihapus
        if ($totalTransaksi > 0) {
            $jenisTransaksi = [];
            if ($transaksiPembelian > 0) {
                $jenisTransaksi[] = "pembelian ($transaksiPembelian transaksi)";
            }
            if ($transaksiPenjualan > 0) {
                $jenisTransaksi[] = "penjualan ($transaksiPenjualan transaksi)";
            }
            
            $response = [
                "title" => "Tidak Dapat Dihapus", 
                "text" => "Klien ini tidak dapat dihapus karena masih memiliki " . implode(" dan ", $jenisTransaksi) . ". Hapus transaksi terkait terlebih dahulu.", 
                "icon" => "warning"
            ];
        } else {
            // Jika tidak ada transaksi, klien boleh dihapus
            $this->klienModel->delete($id);
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        }
        
    } catch (\Throwable $th) {
        // Jika terjadi error saat penghapusan
        $response = [
            "title" => "Gagal", 
            "text" => "Data gagal dihapus: " . $th->getMessage(), 
            "icon" => "error"
        ];
    }

     // Mengembalikan response dalam format JSON
    return $this->response->setJSON($response);
}

}
