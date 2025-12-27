<?php

namespace App\Controllers;

use App\Models\Users;
use CodeIgniter\Controller;
use Config\Services;

class ForgotPasswordController extends Controller
{
    // Menampilkan halaman form forgot password
    public function index()
    {
        return view('forgot_password');
    }

    // Mengirim link reset password ke email user
    public function sendResetLink()
    {
        // Mengambil email dari form
        $email = $this->request->getPost('email');
         // Inisialisasi model Users
        userModel = new Users();
        // Mencari user berdasarkan email
        $user = $userModel->where('email', $email)->first();

        // Cek apakah email terdaftar
        if (!$user) {
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }
        
        // Generate token unik untuk reset password
        $token = bin2hex(random_bytes(16));
        // Waktu kadaluarsa token (15 menit)
        $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        // ✅ Simpan ke tabel password_resets, Koneksi ke database
        $db = \Config\Database::connect();
        $builder = $db->table('password_resets');
        
        // Data token reset password
        $dataInsert = [
            'email'      => $email,
            'token'      => $token,
            'expires_at' => $expiresAt,
            'created_at' => date('Y-m-d H:i:s'),
        ];
        
         // Simpan token ke tabel password_resets
        $builder->insert($dataInsert);
        
         // Membuat link reset password
        $resetLink = base_url('reset-password/' . $token);

        // Inisialisasi email service
        $emailService = \Config\Services::email();
        $emailService->setTo($email);
        $emailService->setSubject('Reset Password');
        
        // Isi email reset password
        $emailService->setMessage(
            "Klik link berikut untuk mereset password Anda (berlaku 15 menit): 
            <a href='{$resetLink}'>Reset Password</a>"
        );
        
        // Mengirim email
        if ($emailService->send()) {
            return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda.');
        } else {
            return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
        }
}
    // Menampilkan halaman reset password berdasarkan token
    public function resetPassword($token)
    {
        // Koneksi ke database
        $db = \Config\Database::connect();
        // Mencari token reset password
        $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

        // Validasi token dan waktu kadaluarsa
        if (!$reset || strtotime($reset->expires_at) < time()) {
            return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
        }

         // Menampilkan form reset password
        return view('reset_password', ['token' => $token]);
    }

     // Memproses update password baru
    public function updatePassword()
{
    // Mengambil data dari form
    $token = $this->request->getPost('token');
    $password = $this->request->getPost('password');
    $confirmPassword = $this->request->getPost('confirm_password');

    // Validasi konfirmasi password
    if ($password !== $confirmPassword) {
        return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok.');
    }

     // Koneksi ke database
    $db = \Config\Database::connect();
    // Mencari token reset
    $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

    // Validasi token
    if (!$reset || strtotime($reset->expires_at) < time()) {
        return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
    }

    // Mengambil data user berdasarkan email
    $userModel = new \App\Models\Users();
    $user = $userModel->where('email', $reset->email)->first();

    // Jika user tidak ditemukan
    if (!$user) {
        return redirect()->to('forgot-password')->with('error', 'User tidak ditemukan.');
    }

    // Inisialisasi model password history
    $passwordHistoryModel = new \App\Models\PasswordHistory();

    // Cek apakah password baru sama dengan password sekarang
    if (password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password saat ini.');
    }

    // Mengambil 3 password terakhir dari password_history
    $oldPasswords = $passwordHistoryModel
        ->where('user_id', $user['id'])
        ->orderBy('created_at', 'DESC')
        ->findAll(3);

    // Cek apakah password baru pernah digunakan
    foreach ($oldPasswords as $old) {
        if (password_verify($password, $old['password_hash'])) {
            return redirect()->back()->with('error', 'Password ini sudah pernah digunakan sebelumnya.');
        }
    }

    // Menyimpan password lama ke password_history
    $passwordHistoryModel->insert([
        'user_id' => $user['id'],
        'password_hash' => $user['password'],
    ]);
    
     // Update password baru ke tabel users
    $userModel->update($user['id'], [
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    // Menghapus token reset agar tidak bisa digunakan kembali
    $db->table('password_resets')->where('token', $token)->delete();

    // Redirect ke halaman login
    return redirect()->to('login')->with('success', 'Password berhasil diubah. Silakan login.');
}

}
