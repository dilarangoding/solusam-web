<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Client;
use App\Models\Users;

class DaftarController extends BaseController
{
    public function index()
    {
        $data = [
            "title" => "Daftar - Solusam",
        ];
        return view('daftar', $data);
    }

    public function register()
    {
        $clientModel = new Client();
        $userModel = new Users();

        $rules = [
            'username' => [
                'rules' => 'required|min_length[5]|max_length[10]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username wajib diisi',
                    'min_length' => 'Username minimal 5 karakter',
                    'max_length' => 'Username maksimal 50 karakter',
                    'is_unique' => 'Username sudah digunakan'
                ]
            ],
            'email' => [
                'rules' => 'required|valid_email|is_unique[users.email]',
                'errors' => [
                    'required' => 'Email wajib diisi',
                    'valid_email' => 'Email tidak valid',
                    'is_unique' => 'Email sudah digunakan'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ],
            'konfirmasi_password' => [
                'rules' => 'required|matches[password]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'matches' => 'Konfirmasi Password tidak sesuai'
                ]
            ],
            'nama_lengkap' => [
                'rules' => 'required|max_length[200]',
                'errors' => [
                    'required' => 'Nama lengkap wajib diisi',
                    'max_length' => 'Nama lengkap maksimal 200 karakter'
                ]
            ],
            'no_telp' => [
                'rules' => 'required|numeric|max_length[15]',
                'errors' => [
                    'required' => 'No telp wajib diisi',
                    'numeric' => 'No telp hars berupa angka',
                    'max_length' => 'No telp maksimal 15 karakter'
                ]
            ],
            'alamat' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Alamat wajib diisi',
                ]
            ],
            'jenis_usaha' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Jenis usaha wajib diisi',
                ]
            ],
        ];

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors-daftar', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $username = trim($this->request->getPost('username'));
        $email = trim($this->request->getPost('email'));
        $nama_lengkap = trim(strtoupper($this->request->getPost('nama_lengkap')));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $jenis_usaha = $this->request->getPost('jenis_usaha');
        $password = $this->request->getPost('password');

        // Hash password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            // Data untuk tabel user
            $userData = [
                'kode_user' => $userModel->generateKode($nama_lengkap),
                'username' => trim(strtolower($username)),
                'email' => $email,
                'password' => $hashedPassword,
                'role' => 2,
            ];

            // Simpan data user
            $userModel->insert($userData);
            // Data untuk tabel client
            $clientData = [
                'user_id' => $userModel->getInsertID(),
                'nama_lengkap' => $nama_lengkap,
                'no_telp' => $no_telp,
                'alamat' => $alamat,
                'jenis_usaha' => $jenis_usaha,
            ];

            // Simpan data client
            $clientModel->insert($clientData);

            return redirect()->to(base_url('/'))->with('login', 'Registrasi berhasil. Silakan login.');
        } catch (\Throwable $th) {
            // Tangani error jika terjadi masalah saat penyimpanan
            session()->setFlashdata('errors-daftar', ['Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
            return redirect()->back()->withInput();
        }
    }
}
