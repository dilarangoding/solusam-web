<?php

namespace App\Controllers;

use App\Controllers\BaseController;

/**
 * SampahController
 * Controller untuk mengelola data sampah (CRUD)
 * Data sampah digunakan dalam transaksi pembelian dan penjualan
 */
class SampahController extends BaseController
{   
    // Model untuk tabel data sampah
    protected $sampahModel;
    // Model untuk tabel transaksi
    protected $transaksiModel;

      /**
     * Constructor
     * Inisialisasi model yang digunakan
     */
    public function __construct()
    {
        $this->sampahModel = new \App\Models\Sampah();
         $this->transaksiModel = new \App\Models\Transaksi();
    }

    
    /**
     * Menampilkan daftar data sampah milik client yang sedang login
     */
    public function index()
    {
        $data = [
            "title" => "Data Sampah",
             // Ambil data sampah berdasarkan client_id dari session
            "data" => $this->sampahModel
                ->where('client_id', session()->get('clientId'))
                ->findAll()
        ];

        return view('sampah/index', $data);
    }

    /**
     * Menampilkan form tambah data sampah
     */
    public function create()
    {
        $data = [
            "title" => "Tambah Data Sampah"
        ];

        return view('sampah/create', $data);
    }

    
    /**
     * Menampilkan form edit data sampah
     * @param int $id ID data sampah
     */
    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Sampah",
            // Ambil data sampah berdasarkan ID
            "data" => $this->sampahModel
                ->where('id', $id)
                ->first()
        ];

        return view('sampah/edit', $data);
    }

     /**
     * Menyimpan data sampah (insert atau update)
     */
    public function store()
    {
         // Ambil data dari form
        $nama_sampah = $this->request->getPost('nama_sampah');
        $harga_beli = $this->request->getPost('harga_beli');
        $harga_jual = $this->request->getPost('harga_jual');
        $satuan = $this->request->getPost('satuan');
        $id = $this->request->getPost('id');

         // Data yang akan disimpan
        $data = [
            'nama_sampah' => $nama_sampah,
            'harga_beli' => $harga_beli,
            'harga_jual' => $harga_jual,
            'satuan' => $satuan,
        ];

        // Jika ada ID berarti update
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
         // Jika tidak ada ID berarti insert data baru
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
        }

        try {
             // Simpan data ke database
            $this->sampahModel->save($data);
              // Pesan sukses
            $message = [
                'title' => 'Success',
                'text' => 'Data sampah berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('sampah');
        } catch (\Throwable $th) {
             // Pesan gagal
            $message = [
                'title' => 'Error',
                'text' => 'Data sampah gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

  /**
     * Menghapus data sampah
     * Dilarang menghapus jika masih memiliki transaksi penjualan
     */
  public function delete()
{
    // Ambil ID sampah dari request
    $id = $this->request->getPost('id');
    
    try {
        // Cek transaksi penjualan (jenis 'out')
        $transaksiPenjualan = $this->transaksiModel
            ->where('sampah_id', $id)
            ->where('jenis', 'out')
            ->countAllResults();
        
        // Jika ada transaksi penjualan, tidak boleh hapus
        if ($transaksiPenjualan > 0) {
            $response = [
                "title" => "Tidak Dapat Dihapus", 
                "text" => "Data sampah ini tidak dapat dihapus karena masih memiliki $transaksiPenjualan transaksi penjualan. Hapus transaksi terkait terlebih dahulu.", 
                "icon" => "warning"
            ];
        } else {
            // Jika tidak ada transaksi penjualan, lanjutkan proses hapus
            
            // TAMBAHAN: Hapus semua transaksi pembelian yang terkait
            $this->transaksiModel
                ->where('sampah_id', $id)
                ->where('jenis', 'in')
                ->delete();
            
            // Baru hapus data sampah
            $this->sampahModel->delete($id);
            
            $response = [
                "title" => "Berhasil", 
                "text" => "Data berhasil dihapus", 
                "icon" => "success"
            ];
        }
        
    } catch (\Exception $e) {
        // Jika terjadi error
        $response = [
            "title" => "Gagal", 
            "text" => "Data gagal dihapus: " . $e->getMessage(), 
            "icon" => "error"
        ];
    }

    // Kembalikan response JSON (biasanya dipakai AJAX)
    return $this->response->setJSON($response);
}

}
