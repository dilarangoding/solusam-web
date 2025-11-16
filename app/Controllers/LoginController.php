<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class LoginController extends BaseController
{
    protected $m_users;
    public function __construct()
    {
        $this->m_users = new \App\Models\Users();
    }

    public function index()
    {
        $data = [
            "title" => "Login",
        ];
        return view('login', $data);
    }

    public function attempLogin()
    {
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

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

        if (!$this->validate($rules)) {
            session()->setFlashdata('errors-login', $this->validator->getErrors());
            return redirect()->back()->withInput();
        }

        $user = $this->m_users->getUser($username);

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

        $sessionData = [
            'kodeUser' => $user['kode_user'],
            'userId' => $user['user_id'],
            'clientId' => $user['id'],
            'username' => $user['username'],
            'role' => $user['role'],
            'nama' => $user['nama_lengkap'],
            'isLoggedIn' => true
        ];

        session()->set($sessionData);

        // Update last login
        $this->m_users->update($user['user_id'], ['last_login' => date('Y-m-d H:i:s')]);

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
