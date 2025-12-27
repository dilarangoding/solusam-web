<?php

// Menentukan namespace controller agar dikenali oleh CodeIgniter 4
namespace App\Controllers;

// Memanggil BaseController sebagai parent controller
use App\Controllers\BaseController;
// Memanggil Model Users untuk akses data user di database
use App\Models\Users;

// Controller Login untuk menangani proses autentikasi
class LoginController extends BaseController
{
    // Properti untuk menyimpan instance model Users
    protected $m_users;
    
    // Constructor: dijalankan otomatis saat controller dipanggil
    public function __construct()
    {
        // Inisialisasi model Users agar bisa digunakan di method lain
          $this->m_users = new Users();
    }

    // Method index()
    // Digunakan untuk menampilkan halaman login
    public function index()
    {
         // Data yang dikirim ke view login
        $data = [
            "title" => "Login",
        ];
           // Menampilkan view login
        return view('login', $data);
    }

    // Method attempLogin()
    // Digunakan untuk memproses login setelah form dikirim
    public function attempLogin()
    {
        // Mengambil input username dan password dari form (POST)
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        // Aturan validasi form login
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
             // Simpan error validasi ke flashdata
            session()->setFlashdata('errors-login', $this->validator->getErrors());
             // Kembali ke halaman login dengan input sebelumnya
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

          // Mengecek status akun user (aktif / tidak aktif)
        if ($user['status'] != '1') {
            session()->setFlashdata('errors-login', ['Akun Anda tidak aktif']);
            return redirect()->back();
        }

        // Data yang disimpan ke dalam session setelah login berhasil
        $sessionData = [
            'kodeUser' => $user['kode_user'],  // Kode unik user
            'userId' => $user['id'],          // ID user
            'clientId'   => $user['id'],     // ID client (digunakan di transaksi)
            'username' => $user['username'],// Username login
            'role' => $user['role'],       // Role user (admin, client, dll)
            'auth_type'  => 'local',     // Jenis autentikasi (local)
            'isLoggedIn' => true         // Penanda user sudah login
        ];

        // Jenis autentikasi (local)
        session()->set($sessionData);

         // Update waktu terakhir login user
        $this->m_users->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);

        // Redirect ke halaman dashboard setelah login sukses
        return redirect()->to('dashboard');
    }

     // Method logout()
    // Digunakan untuk keluar dari sistem
    public function logout()
    {
        // Menghapus seluruh data session
        session()->destroy();

       // Redirect kembali ke halaman login
        return redirect()->to('/');
    }
}
