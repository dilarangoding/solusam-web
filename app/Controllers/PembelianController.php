<?php

namespace App\Controllers;

use App\Controllers\BaseController;

// Mengimpor model-model yang dibutuhkan
use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;

// Controller untuk mengelola transaksi pembelian (jenis = in)
class PembelianController extends BaseController
{
     // Properti model yang digunakan di controller
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;

    // Constructor: inisialisasi semua model
    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
    }

     // Method index()
    // Menampilkan daftar data pembelian
    public function index()
    {
        $data = [
            "title" => "Data Pembelian",
             // Mengambil data transaksi jenis 'in' (pembelian) berdasarkan client
            "data" => $this->transaksiModel
                ->getPenjualan(session('clientId'), 'in'),
        ];

        
        // Menampilkan view daftar pembelian
        return view('pembelian/index', $data);
    }

      // Method create()
    // Menampilkan form tambah data pembelian
    public function create()
    {
        $data = [
            "title" => "Tambah Data Pembelian",

            // Data sampah milik client
            "sampah" => $this->sampahModel
                ->where('client_id', session('clientId'))
                ->findAll(),
            // Data metode pembayaran milik client
            "bayar" => $this->metodeBayarModel
                ->where('client_id', session('clientId'))
                ->findAll(),
            // Data klien milik client
            "klien" => $this->klienModel
                ->where('client_id', session('clientId'))
                ->findAll(),
        ];

        // Menampilkan view form tambah pembelian
        return view('pembelian/create', $data);
    }

     // Method edit($id)
    // Menampilkan form edit data pembelian
    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Penjualan",
           
            // Data sampah
            "sampah" => $this->sampahModel
                ->where('client_id', session('clientId'))
                ->findAll(),
              // Data metode pembayaran
            "bayar" => $this->metodeBayarModel
                ->where('client_id', session('clientId'))
                ->findAll(),
             // Data transaksi yang akan diedit
            "data" => $this->transaksiModel->find($id),
            // Data klien
            "klien" => $this->klienModel
                ->where('client_id', session('clientId'))
                ->findAll(),

        ];

        // Menampilkan view edit pembelian
        return view('pembelian/edit', $data);
    }

    // Method sampahAjax()
    // Digunakan untuk request AJAX mengambil detail sampah berdasarkan ID
    public function sampahAjax()
    {
        // Ambil ID sampah dari POST
        $id = $this->request->getPost('id');
        // Ambil data sampah berdasarkan ID
        $data = $this->sampahModel->find($id);
        // Kembalikan data dalam bentuk JSON
        return $this->response->setJSON($data);
    }

    // Method store()
    // Menyimpan data pembelian (insert atau update)
    public function store()
    {
        // Ambil data dari form
        $tanggal = $this->request->getPost('tanggal');
        $nama_sampah = $this->request->getPost('nama_sampah');
        $jumlah_beli = $this->request->getPost('jumlah_beli');
        $id = $this->request->getPost('id');
        $pembeli = $this->request->getPost('pembeli');
        
        // Mencegah jumlah menjadi 0 atau kosong saat edit data
        if ($id && ($jumlah_beli === null || $jumlah_beli === "")) {
            $jumlah_beli = $this->transaksiModel->find($id)['jumlah'];
        }
        
        // Data transaksi yang akan disimpan
        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_beli,
            'pembeli' => $pembeli,
        ];

         // Jika ada ID, berarti update data
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
             // Jika tidak ada ID, berarti insert data baru
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'in'; // Penanda transaksi pembelian
        }

        try {
             // Simpan data transaksi
            $this->transaksiModel->save($data);
            // Pesan sukses
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('pembelian');
        } catch (\Throwable $th) {
            // Pesan error
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

    // Method delete()
    // Menghapus data pembelian
    public function delete()
    {
        // Ambil ID dari request POST
        $id = $this->request->getPost('id');
        
        try {
            // Hapus data transaksi
            $this->transaksiModel->delete($id);

            // Response sukses (JSON)
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"];
            
        } catch (\Throwable $th) {
             // Response gagal (JSON)
            $response = [
                "title" => "Gagal", 
                "text" => "Data gagal dihapus", 
                "icon" => "error"];
        }

        // Kembalikan response JSON (untuk AJAX)
        return $this->response->setJSON($response);
    }
}
