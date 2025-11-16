<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class DataKlienController extends BaseController
{
    protected $klienModel;
    public function __construct()
    {
        $this->klienModel = new \App\Models\Client();
    }
    public function index()
    {
        $data = [
            "title" => "Data Klien",
            "data" => $this->klienModel->where('client_id', session()->get('clientId'))->findAll()
        ];

        return view('klien/index', $data);
    }

    public function create()
    {
        $data = [
            "title" => "Tambah Data Klien"
        ];

        return view('klien/create', $data);
    }

    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Klien",
            "data" => $this->klienModel->where('id', $id)->first()
        ];

        return view('klien/edit', $data);
    }

    public function store()
    {
        $nama_lengkap = strtoupper($this->request->getPost('nama_lengkap'));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $jenis_usaha = $this->request->getPost('jenis_usaha');
        $id = $this->request->getPost('id');

        $data = [
            'nama_lengkap' => $nama_lengkap,
            'no_telp' => $no_telp,
            'alamat' => $alamat,
            'jenis_usaha' => $jenis_usaha,
        ];

        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
        }

        try {
            $this->klienModel->save($data);
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('data-klien');
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
            $this->klienModel->delete($id);
            $response = ["title" => "Berhasil", "text" => "Data berhasil dihapus", "icon" => "success"];
        } catch (\Throwable $th) {
            $response = ["title" => "Gagal", "text" => "Data gagal dihapus", "icon" => "error"];
        }
        return $this->response->setJSON($response);
    }
}
