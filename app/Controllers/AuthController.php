<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Users;
use Google\Client as GoogleClient;
use Google\Service\Oauth2;

class AuthController extends BaseController
{
    protected $googleClient;
    protected $usersModel;

    public function __construct()
    {
        // Ambil konfigurasi dari .env
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri  = env('GOOGLE_REDIRECT_URI');

        if (!$clientId || !$clientSecret || !$redirectUri) {
            log_message('error', 'Google OAuth Configuration Missing in .env');
            die('Error: Google Client ID/Secret/Redirect URI tidak ditemukan. Periksa file .env Anda.');
        }

        $this->usersModel = new Users();

        $this->googleClient = new GoogleClient();
        $this->googleClient->setClientId($clientId);
        $this->googleClient->setClientSecret($clientSecret);
        $this->googleClient->setRedirectUri($redirectUri);
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }

    // Redirect ke Google
    public function redirectToGoogle()
    {
        return redirect()->to($this->googleClient->createAuthUrl());
    }

    // Callback setelah login Google
    public function handleGoogleCallback()
    {
        $code = $this->request->getVar('code');

        if (!$code) {
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: No code received.']);
        }

        try {
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                log_message('error', 'Google OAuth Token Error: ' . ($token['error_description'] ?? $token['error']));
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . ($token['error_description'] ?? $token['error'])]);
            }

            $this->googleClient->setAccessToken($token);

            $oauth2   = new Oauth2($this->googleClient);
            $userInfo = $oauth2->userinfo->get();
            $existingUser = null;
            log_message('debug', 'Google User Info: ' . json_encode($userInfo));

            if (!$userInfo || !isset($userInfo->id)) {
                log_message('error', 'Google OAuth Error: userInfo is null or missing ID');
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: user info not found']);
            }

            // Cek user berdasarkan google_id
            // Cek user berdasarkan google_id
            $user = $this->usersModel->where('google_id', $userInfo->id)->first();
            log_message('debug', 'User berdasarkan google_id: ' . json_encode($user));
            
            if (!$user) {
            // Cek user berdasarkan email jika google_id belum terdaftar
            $existingUser = $this->usersModel->where('email', $userInfo->email)->first();
            log_message('debug', 'User berdasarkan email: ' . json_encode($existingUser));
            
            if ($existingUser) {
                 session()->remove('is_new_user');
                // Jika google_id masih kosong atau berbeda, update
                if (empty($existingUser['google_id']) || $existingUser['google_id'] !== $userInfo->id) {
                    $this->usersModel->update($existingUser['id'], [
                        'google_id' => $userInfo->id,
                    ]);
                    log_message('debug', 'Update google_id untuk user: ' . $existingUser['id']); // Tambahkan ini
        }

        // Ambil ulang data user setelah update
        $user = $this->usersModel->where('id', $existingUser['id'])->first();
   } else {
    // Jika user belum ada sama sekali → daftarkan otomatis

    // Generate kode_user baru
    $kodeBaru = $this->usersModel->generateKode($userInfo->email);

    $newUserData = [
        'kode_user' => $kodeBaru, // ✅ Tambahkan ini
        'username'  => $userInfo->email,
        'email'     => $userInfo->email,
        'google_id' => $userInfo->id,
        'role'      => 2,  // Role default (ubah sesuai kebutuhanmu)
        'status'    => 1   // Aktif secara default
    ];

    log_message('debug', 'User baru akan dibuat: ' . json_encode($newUserData));

    $newUserId = $this->usersModel->insert($newUserData);

    $user = $this->usersModel->find($newUserId);
    session()->set('is_new_user', true); 
   }

}

 $clientModel = new \App\Models\Client();
        $client = $clientModel->where('user_id', $user['id'])->first();

        if (!$client) {
            $clientId = $clientModel->insert([
                'user_id'      => $user['id'],
                'nama_lengkap' => $userInfo->name ?? $user['username'],
                'no_telp'      => null,
                'alamat'       => null,
                'jenis_usaha'  => null,
            ]);
            $client = $clientModel->find($clientId);
        }

        

log_message('debug', 'Final User Session: ' . json_encode($user)); 

// Setelah semua aman → set session untuk login
session()->set([
    'isLoggedIn' => true,
    'user_id'    => $user['id'],
    'email'      => $user['email'],
    'role'       => $user['role'],
    'clientId'   => $client['id'],
]);

return redirect()->to(base_url('dashboard')); // Ganti sesuai route dashboard kamu


        } catch (\Exception $e) {
            log_message('error', 'Google Login Exception: ' . $e->getMessage());
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . $e->getMessage()]);
        }
    }
}
