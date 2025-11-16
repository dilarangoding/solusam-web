<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Users;
use CodeIgniter\HTTP\ResponseInterface;

class ResetController extends BaseController
{
    public function index()
    {
        $data = [
            "title" => "Reset Password",
        ];

        return view('reset', $data);
    }

    public function update()
    {
        $users = new Users();
        $userId = session('userId');

        if (!$userId) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $rules = [
            'current_password'      => 'required',
            'new_password'          => 'required|min_length[8]',
            'confirm_new_password'  => 'required|matches[new_password]',

            'current_password' => [
                'rules' => 'required',
                'errors' => [
                    'required' => 'Password wajib diisi',
                ]
            ],
            'new_password' => [
                'rules' => 'required|min_length[6]',
                'errors' => [
                    'required' => 'New Password wajib diisi',
                    'min_length' => 'New Password minimal 6 karakter'
                ]
            ],
            'confirm_new_password' => [
                'rules' => 'required|matches[new_password]',
                'errors' => [
                    'required' => 'Konfirmasi Password wajib diisi',
                    'matches' => 'Konfirmasi Password tidak sesuai dengan New Password'
                ]
            ]
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors-reset', $this->validator->getErrors());
        }

        $currentPassword = $this->request->getPost('current_password');
        $newPassword     = $this->request->getPost('new_password');

        // Ambil user dari DB
        $user = $users->find($userId);

        if (!$user) {
            return redirect()->back()->with('errors-reset', ['User tidak ditemukan.']);
        }

        // Cek apakah password lama benar
        if (!password_verify($currentPassword, $user['password'])) {
            return redirect()->back()->with('errors-reset', ['Password lama tidak sesuai.']);
        }

        // Hash password baru
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update ke database
        $users->update($userId, [
            'password' => $hash
        ]);

        $message = [
            'title' => 'Success',
            'text' => 'Reset Password Berhasil',
            'icon' => 'success'
        ];
        session()->setFlashdata($message);
        return redirect()->back();
    }
}
