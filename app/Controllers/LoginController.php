/**
 * --------------------------------------------------------------------
 * LoginController
 * --------------------------------------------------------------------
 *
 * Controller ini bertanggung jawab untuk:
 * - Menampilkan halaman login
 * - Memproses autentikasi login user
 * - Mengatur session login
 * - Logout user dari sistem
 *
 * Controller ini menggunakan Model Users untuk mengambil data user
 * dari database dan melakukan verifikasi login.
 */


<?php

namespace App\Controllers;

use App\Controllers\BaseController; // Controller dasar CodeIgniter
use App\Models\Users;  // Model Users untuk akses data user


class LoginController extends BaseController
     
    /**
     * ----------------------------------------------------------------
     * Property Model Users
     * ----------------------------------------------------------------
     *
     * Digunakan untuk menyimpan instance dari model Users
     * agar bisa dipakai di seluruh method controller ini.
     */
{
    protected $m_users;

      /**
     * ----------------------------------------------------------------
     * Constructor
     * ----------------------------------------------------------------
     *
     * Method ini dijalankan otomatis saat controller dipanggil.
     * Digunakan untuk menginisialisasi model Users.
     */
    
     
    public function __construct()
    {
         $this->m_users = new Users();
        
    }

      /**
     * ----------------------------------------------------------------
     * Method index()
     * ----------------------------------------------------------------
     *
     * Menampilkan halaman login.
     * Method ini biasanya dipanggil saat user mengakses URL login.
     */
    public function index()
    {
        $data = [
            "title" => "SOLUSAM",
        ];
        return view('login', $data);
    }

      /**
     * ----------------------------------------------------------------
     * Method attempLogin()
     * ----------------------------------------------------------------
     *
     * Method ini digunakan untuk memproses login user.
     * - Mengambil input username dan password
     * - Melakukan validasi form
     * - Mengecek data user ke database
     * - Verifikasi password
     * - Membuat session login
     */
    public function attempLogin()
    {
        // Mengambil data username dan password dari form POST
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

          /**
         * Aturan validasi input login
         */
        $rules = [
            'username' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Username wajib diisi'
                ]
            ],
            'password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'Password wajib diisi',
                    'min_length' => 'Password minimal 6 karakter'
                ]
            ]
        ];

        // Jika validasi gagal
        if (!$this->validate($rules)) {
            session()->setFlashdata('errors-login', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

         // Mengambil data user berdasarkan username
        $user = $this->m_users->getUser($username);

        // Jika user tidak ditemukan
        if (!$user) {
            session()->setFlashdata('errors-login', ['Username atau password salah']);
            return redirect()->back();
        }

        // Verifikasi password
        if (!password_verify($password, $user['password'])) {
            session()->setFlashdata('errors-login', ['Username atau password salah']);
            return redirect()->back();
        }

        // Cek status user
        if ($user['status'] != '1') {
            session()->setFlashdata('errors-login', ['Akun Anda tidak aktif']);
            return redirect()->back();
        }

          /**
         * Data session yang akan disimpan setelah login berhasil
         */
        $sessionData = [
            'kodeUser' => $user['kode_user'], // Kode unik user
            'userId' => $user['id'], // ID user
           'clientId'  => $user['id'],  // ID client (disamakan dengan user)
            'username' => $user['username'],  // Username login
            'role' => $user['role'],
            'auth_type'  => 'local',   // Jenis autentikasi
            'isLoggedIn' => true  // Status login
        ];

        // Menyimpan data ke session
        session()->set($sessionData);

        // Update last login
        $this->m_users->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

         // Redirect ke halaman dashboard
        return redirect()->to('dashboard');
    }

    public function logout()
    {
        // Hapus semua data session
        session()->destroy();

        // Redirect ke halaman login
        return redirect()->to('/');
    }
}
