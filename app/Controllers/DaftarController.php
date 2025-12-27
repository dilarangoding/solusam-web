<?php

namespace App\Controllers;

// Menggunakan BaseController agar bisa memakai request, session, dll
use App\Controllers\BaseController;
// Menggunakan model Users untuk akses tabel users
use App\Models\Users;

class DaftarController extends BaseController
{
    /**
     * Method index()
     * 
     * Berfungsi untuk menampilkan halaman pendaftaran (view daftar)
     * Dipanggil saat user mengakses URL daftar
     */
    public function index()
    {
        // Data yang dikirim ke view
        $data = [
            "title" => "Daftar - Solusam", // Judul halaman
        ];
         // Menampilkan view 'daftar'
        return view('daftar', $data);
    }

     /**
     * Method register()
     * 
     * Berfungsi untuk memproses data registrasi user
     * Method ini dipanggil saat form daftar disubmit (POST)
     */
    public function register()
    {
        // Inisialisasi model Users
        $userModel = new Users();

         /**
         * Aturan validasi input form
         * Setiap field memiliki rule dan pesan error masing-masing
         */
        $rules = [
            'username' => [
                'rules' => 'required|min_length[5]|max_length[15]|is_unique[users.username]',
                'errors' => [
                    'required' => 'Username wajib diisi',
                    'min_length' => 'Username minimal 5 karakter',
                    'max_length' => 'Username maksimal 15 karakter',
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
        ];
        
         /**
         * Jika validasi gagal
         * - Simpan error ke session
         * - Kembalikan user ke halaman sebelumnya
         * - Input lama tetap ada
         */
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors-daftar', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        
        /**
         * Ambil data dari form POST
         * trim() untuk menghapus spasi
         */
        $username = trim($this->request->getPost('username'));
        $email = trim($this->request->getPost('email'));
        $nama_lengkap = trim(strtoupper($this->request->getPost('nama_lengkap')));
        $no_telp = $this->request->getPost('no_telp');
        $alamat = $this->request->getPost('alamat');
        $password = $this->request->getPost('password');

        // Hash password sebelum disimpan
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            /**
             * Data user yang akan disimpan ke database
             */
            $userData = [
                'kode_user' => $userModel->generateKode($nama_lengkap),
                'username' => trim(strtolower($username)),
                'email' => $email,
                'password' => $hashedPassword,
                'nama_lengkap' => $nama_lengkap,
                'no_telp' => $no_telp,
                'alamat' => $alamat, 
                'role' => 2,
            ];

            // Simpan data user ke database
            $userModel->insert($userData);

            // Redirect ke halaman login dengan pesan sukses
            return redirect()->to(base_url('/'))->with('login', 'Registrasi berhasil. Silakan login.');
        } catch (\Throwable $th) {
             /**
             * Jika terjadi error saat insert database
             * Tangkap error agar aplikasi tidak crash
             */
            session()->setFlashdata('errors-daftar', ['Terjadi kesalahan saat menyimpan data. Silakan coba lagi.']);
            return redirect()->back()->withInput();
        }
    }
}
