<?php

namespace App\Controllers;

use App\Controllers\BaseController;

// Menggunakan model Users untuk akses data user
use App\Models\Users;
// Menggunakan ResponseInterface (opsional, tidak dipakai langsung di kode)
use CodeIgniter\HTTP\ResponseInterface;


/**
 * ResetController
 * Controller yang menangani proses reset / ganti password
 * khusus untuk user dengan autentikasi lokal (local auth)
 */
class ResetController extends BaseController
{
     /**
     * Menampilkan halaman reset password
     */
    public function index()
    {
         // Jika user bukan login dengan metode local,
        // maka tidak diizinkan reset password
        if(session()->get('auth_type') != 'local'){
        return redirect()->to(base_url('dashboard'));
    }
          // Data yang dikirim ke view
        $data = [
            "title" => "Reset Password",
        ];

         // Tampilkan halaman reset password
        return view('reset', $data);
    }

    /**
     * Proses update / reset password
     */
    public function update()
    {
        // Cegah reset password jika bukan login local
         if(session()->get('auth_type') != 'local'){
        return redirect()->to(base_url('dashboard'));
    }
         // Inisialisasi model Users
        $users = new Users();
         // Ambil ID user dari session
        $userId = session('userId');

        // Jika user belum login, arahkan ke halaman login
        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Aturan validasi form reset password
        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'confirm_new_password'  => 'required|matches[new_password]',

             // Validasi password lama
            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi',
                ]
            ],
            // Validasi password baru
            'new_password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'New Password wajib diisi',
                    'min_length' => 'New Password minimal 6 karakter'
                ]
            ],
            // Validasi konfirmasi password
            'confirm_new_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Konfirmasi Password wajib diisi',
                    'matches' => 'Konfirmasi Password tidak sesuai dengan New Password'
                ]
            ]
        ];

        // Jika validasi gagal, kembalikan ke halaman sebelumnya
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors-reset', $this->validator->getErrors());
        }

        // Ambil password lama dari form
        $currentPassword = $this->request->getPost('current_password');

         // Ambil password baru dari form
        $newPassword     = $this->request->getPost('new_password');

       // Ambil data user dari database berdasarkan ID
        $user = $users->find($userId);

        
        // Jika user tidak ditemukan
        if (!$user) {
            return redirect()->back()->with('errors-reset', ['User tidak ditemukan.']);
        }

         // Verifikasi apakah password lama sesuai dengan hash di database
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('errors-reset', ['Password lama tidak sesuai.']);
        }

        // Hash password baru
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update ke database
        $users->update($userId, [
            'password' => $hash
        ]);

        // Pesan sukses
        $message = [
            'title' => 'Success',
            'text' => 'Reset Password Berhasil',
            'icon' => 'success'
        ];

         // Simpan pesan ke flashdata session
        session()->setFlashdata($message);
        // Kembali ke halaman reset password
        return redirect()->back();
    }
}
