<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;

class PembelianController extends BaseController
{
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
    }

    public function index()
    {
        $data = [
            "title" => "Data Pembelian",
            "data" => $this->transaksiModel->getPenjualan(session('clientId'), 'in'),
        ];

        return view('pembelian/index', $data);
    }

    public function create()
    {
        $data = [
            "title" => "Tambah Data Pembelian",
            "sampah" => $this->sampahModel->where('client_id', session('clientId'))->findAll(),
            "bayar" => $this->metodeBayarModel->where('client_id', session('clientId'))->findAll(),
            "klien" => $this->klienModel->where('client_id', session('clientId'))->findAll(),
        ];

        return view('pembelian/create', $data);
    }

    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Penjualan",
            "sampah" => $this->sampahModel->where('client_id', session('clientId'))->findAll(),
            "bayar" => $this->metodeBayarModel->where('client_id', session('clientId'))->findAll(),
            "data" => $this->transaksiModel->find($id),
            "klien" => $this->klienModel->where('client_id', session('clientId'))->findAll(),

        ];

        return view('pembelian/edit', $data);
    }

    public function sampahAjax()
    {
        $id = $this->request->getPost('id');
        $data = $this->sampahModel->find($id);
        return $this->response->setJSON($data);
    }

    public function store()
    {
        $tanggal = $this->request->getPost('tanggal');
        $nama_sampah = $this->request->getPost('nama_sampah');
        $jumlah_beli = $this->request->getPost('jumlah_beli');
        $id = $this->request->getPost('id');
        $pembeli = $this->request->getPost('pembeli');   
        
        
        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_beli,
            'pembeli' => $pembeli,
        ];

        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'in';
        }

        try {
            $this->transaksiModel->save($data);
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('pembelian');
        } catch (\Throwable $th) {
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            $this->transaksiModel->delete($id);
            $response = ["title" => "Berhasil", "text" => "Data berhasil dihapus", "icon" => "success"];
        } catch (\Throwable $th) {
            $response = ["title" => "Gagal", "text" => "Data gagal dihapus", "icon" => "error"];
        }
        return $this->response->setJSON($response);
    }
}
