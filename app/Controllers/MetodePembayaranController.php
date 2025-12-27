<?php

namespace App\Controllers;

use App\Controllers\BaseController;

// Controller untuk mengelola metode pembayaran
class MetodePembayaranController extends BaseController
{
    // Properti untuk menyimpan instance model MetodePembayaran
    protected $metodeBayarModel;

    
    // Constructor: dijalankan otomatis saat controller dipanggil
    public function __construct()
    {
         // Inisialisasi model MetodePembayaran
        $this->metodeBayarModel = new \App\Models\MetodePembayaran();
    }

    // Method index()
    // Menampilkan daftar metode pembayaran milik client yang sedang login
    public function index()
    {
        // Data yang dikirim ke view
        $data = [
            "title" => "Data Metode Pembayaran",
            
            // Mengambil semua metode pembayaran berdasarkan client yang login
            "data" => $this->metodeBayarModel
            ->where('client_id', session()->get('clientId'))
            ->findAll()
        ];

        // Menampilkan view daftar metode pembayaran
        return view('metode_pembayaran/index', $data);
    }

    // Method create()
    // Menampilkan form tambah metode pembayaran
    public function create()
    {   
        // Data untuk view form tambah
        $data = [
            "title" => "Tambah Data Pembayaran"
        ];

          // Menampilkan view create
        return view('metode_pembayaran/create', $data);
    }

     // Method edit($id)
    // Menampilkan form edit metode pembayaran berdasarkan ID
    public function edit($id)
    {
        // Data untuk view edit
        $data = [
            "title" => "Edit Data Metode Pembayaran",
            
             // Mengambil satu data metode pembayaran berdasarkan ID
            "data" => $this->metodeBayarModel
            ->where('id', $id)
            ->first()
        ];

         // Menampilkan view edit
        return view('metode_pembayaran/edit', $data);
    }

    // Method store()
    // Digunakan untuk menyimpan data metode pembayaran (insert atau update)
    public function store()
    {
        // Mengambil input nama metode pembayaran
        $nama = $this->request->getPost('nama');
         // Mengambil ID (jika ada, berarti update)
        $id = $this->request->getPost('id');
        
        // Data yang akan disimpan ke database
        $data = [
            'nama' => $nama,
        ];

        // Jika ada ID, berarti update data
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
              // Jika tidak ada ID, berarti insert data baru
            $text = 'ditambahkan';
             // Set client_id berdasarkan client yang login
            $data['client_id'] = session('clientId');
        }

        try {
             // Simpan data ke database (insert / update)
            $this->metodeBayarModel->save($data);
            // Pesan sukses
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
             // Simpan pesan ke flashdata
            session()->setFlashdata($message);
             // Redirect ke halaman metode pembayaran
            return redirect()->to('metode-bayar');
        } catch (\Throwable $th) {
            // Pesan error jika gagal
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
              // Simpan pesan error ke flashdata
            session()->setFlashdata($message);

             // Kembali ke halaman sebelumnya 
            return redirect()->back();
        }
    }

    // Method delete()
    // Digunakan untuk menghapus data metode pembayaran
    public function delete()
    {
          // Mengambil ID dari request POST
        $id = $this->request->getPost('id');
        try {
             // Menghapus data metode pembayaran berdasarkan ID
            $this->metodeBayarModel->delete($id);
            // Response sukses (JSON)
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        } catch (\Throwable $th) {
           // Response gagal (JSON)
            $response = [
                "title" => "Gagal", 
                "text" => "Data gagal dihapus", 
                "icon" => "error"];
        }
        return $this->response->setJSON($response);
    }
}
