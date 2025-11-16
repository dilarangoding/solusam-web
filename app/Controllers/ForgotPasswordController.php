<?php

namespace App\Controllers;

use App\Models\Users;
use CodeIgniter\Controller;
use Config\Services;

class ForgotPasswordController extends Controller
{
    public function index()
    {
        return view('forgot_password');
    }
public function sendResetLink()
{
    $email = $this->request->getPost('email');
    $userModel = new Users();
    $user = $userModel->where('email', $email)->first();

    // ✅ Cek apakah email terdaftar
    if (!$user) {
        return redirect()->back()->with('error', 'Email tidak ditemukan.');
    }

    // ✅ Generate token dan waktu kadaluarsa
    $token = bin2hex(random_bytes(16));
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // ✅ Simpan ke tabel password_resets
    $db = \Config\Database::connect();
    $builder = $db->table('password_resets');

    $dataInsert = [
        'email'      => $email,
        'token'      => $token,
        'expires_at' => $expiresAt,
        'created_at' => date('Y-m-d H:i:s'),
    ];

    $builder->insert($dataInsert);

    // ✅ Kirim link reset password ke email user
    $resetLink = base_url('reset-password/' . $token);

    $emailService = \Config\Services::email();
    $emailService->setTo($email);
    $emailService->setSubject('Reset Password');
    $emailService->setMessage(
        "Klik link berikut untuk mereset password Anda (berlaku 15 menit): 
        <a href='{$resetLink}'>Reset Password</a>"
    );

    // ✅ Kirim email dan beri notifikasi ke user
    if ($emailService->send()) {
        return redirect()->back()->with('success', 'Link reset password telah dikirim ke email Anda.');
    } else {
        return redirect()->back()->with('error', 'Gagal mengirim email. Silakan coba lagi nanti.');
    }
}



    public function resetPassword($token)
    {
        $db = \Config\Database::connect();
        $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

        if (!$reset || strtotime($reset->expires_at) < time()) {
            return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
        }

        return view('reset_password', ['token' => $token]);
    }

    public function updatePassword()
{
    $token = $this->request->getPost('token');
    $password = $this->request->getPost('password');
    $confirmPassword = $this->request->getPost('confirm_password');

    if ($password !== $confirmPassword) {
        return redirect()->back()->with('error', 'Password dan konfirmasi password tidak cocok.');
    }

    $db = \Config\Database::connect();
    $reset = $db->table('password_resets')->where('token', $token)->get()->getRow();

    if (!$reset || strtotime($reset->expires_at) < time()) {
        return redirect()->to('forgot-password')->with('error', 'Token tidak valid atau telah kadaluarsa.');
    }

    $userModel = new \App\Models\Users();
    $user = $userModel->where('email', $reset->email)->first();

    if (!$user) {
        return redirect()->to('forgot-password')->with('error', 'User tidak ditemukan.');
    }

    // 🔹 Tambahkan mulai dari sini
    $passwordHistoryModel = new \App\Models\PasswordHistory();

    // 1️⃣ Cek apakah password baru sama dengan password sekarang
    if (password_verify($password, $user['password'])) {
        return redirect()->back()->with('error', 'Password baru tidak boleh sama dengan password saat ini.');
    }

    // 2️⃣ Cek terhadap password lama di password_history (misal 3 terakhir)
    $oldPasswords = $passwordHistoryModel
        ->where('user_id', $user['id'])
        ->orderBy('created_at', 'DESC')
        ->findAll(3);

    foreach ($oldPasswords as $old) {
        if (password_verify($password, $old['password_hash'])) {
            return redirect()->back()->with('error', 'Password ini sudah pernah digunakan sebelumnya.');
        }
    }

    // 3️⃣ Simpan password lama ke history
    $passwordHistoryModel->insert([
        'user_id' => $user['id'],
        'password_hash' => $user['password'],
    ]);
    // 🔹 Sampai sini

    // 🔹 Update password baru ke tabel users
    $userModel->update($user['id'], [
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    // 🔹 Hapus token reset agar tidak bisa dipakai lagi
    $db->table('password_resets')->where('token', $token)->delete();

    return redirect()->to('login')->with('success', 'Password berhasil diubah. Silakan login.');
}

}
