<?php

// Namespace controller sesuai struktur CodeIgniter 4
namespace App\Controllers;

// Import BaseController bawaan CI4
use App\Controllers\BaseController;
// Import Model Users (tabel users)
use App\Models\Users;
// Import library Google OAuth Client
use Google\Client as GoogleClient;
// Import service OAuth2 Google
use Google\Service\Oauth2;

class AuthController extends BaseController
{
     /**
     * @var GoogleClient
     * Menyimpan instance Google OAuth Client
     */
    protected $googleClient;
     /**
     * @var Users
     * Model untuk mengakses tabel users
     */
    protected $usersModel;

    /**
     * Constructor
     * Dieksekusi otomatis saat controller dipanggil
     */
    public function __construct()
    {
        // Ambil konfigurasi dari .env
        $clientId     = env('GOOGLE_CLIENT_ID');
        $clientSecret = env('GOOGLE_CLIENT_SECRET');
        $redirectUri  = env('GOOGLE_REDIRECT_URI');

         // Validasi konfigurasi OAuth
        // Jika salah satu kosong → hentikan aplikasi
        if (!$clientId || !$clientSecret || !$redirectUri) {
            log_message('error', 'Google OAuth Configuration Missing in .env');
            die('Error: Google Client ID/Secret/Redirect URI tidak ditemukan. Periksa file .env Anda.');
        }

        // Inisialisasi model Users
        $this->usersModel = new Users();

        // Inisialisasi Google Client
        $this->googleClient = new GoogleClient();

         // Set kredensial Google OAuth
        $this->googleClient->setClientId($clientId);
        $this->googleClient->setClientSecret($clientSecret);
        $this->googleClient->setRedirectUri($redirectUri);

         // Scope OAuth → data yang diminta dari Google
        $this->googleClient->addScope('email');
        $this->googleClient->addScope('profile');
    }

    // Redirect ke Google
    public function redirectToGoogle()
    {
        // createAuthUrl() menghasilkan URL OAuth Google
        return redirect()->to($this->googleClient->createAuthUrl());
    }

     // Generate username unik dari email
     private function generateUniqueUsername($email)
    {
        // Ambil bagian sebelum @
        $baseUsername = explode('@', $email)[0];
        $baseUsername = strtolower($baseUsername);
        
        // Potong jika lebih dari 15 karakter
        if (strlen($baseUsername) > 15) {
            $baseUsername = substr($baseUsername, 0, 15);
        }
        
        // Cek apakah sudah ada
        $username = $baseUsername;
        $counter = 1;

         // Loop selama username sudah ada di database
        while ($this->usersModel->where('username', $username)->first()) {
            // Jika sudah ada, tambahkan angka
            $suffix = (string)$counter;
             // Hitung sisa panjang karakter
            $maxLength = 15 - strlen($suffix);
            $username = substr($baseUsername, 0, $maxLength) . $suffix;
            $counter++;
        }
         // Kembalikan username unik
        return $username;
    }

    // Callback setelah login Google
    public function handleGoogleCallback()
    {
        // Ambil parameter 'code' dari URL
        $code = $this->request->getVar('code');

         // Jika tidak ada code → login gagal
        if (!$code) {
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: No code received.']);
        }

        try {
            // Tukar authorization code menjadi access token
            $token = $this->googleClient->fetchAccessTokenWithAuthCode($code);

            // Jika Google mengembalikan error
            if (isset($token['error'])) {
                log_message('error', 'Google OAuth Token Error: ' . ($token['error_description'] ?? $token['error']));
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . ($token['error_description'] ?? $token['error'])]);
            }

             // Set access token ke Google Client
            $this->googleClient->setAccessToken($token);

            // Ambil data user dari Google
            $oauth2   = new Oauth2($this->googleClient);
            $userInfo = $oauth2->userinfo->get(); // object
            $existingUser = null;
            
            log_message('debug', 'Google User Info: ' . json_encode($userInfo));

              // Validasi data user
            if (!$userInfo || !isset($userInfo->id)) {
                log_message('error', 'Google OAuth Error: userInfo is null or missing ID');
                return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: user info not found']);
            }

            // Cek user berdasarkan google_id
            $user = $this->usersModel->where('google_id', $userInfo->id)->first();
            log_message('debug', 'User berdasarkan google_id: ' . json_encode($user));
            
            if (!$user) {
             // Jika google_id belum ada → cek berdasarkan email
            $existingUser = $this->usersModel->where('email', $userInfo->email)->first();
                
            log_message('debug', 'User berdasarkan email: ' . json_encode($existingUser));
            
            if ($existingUser) {
                // User lama tapi belum punya google_id
                 session()->remove('is_new_user');
                // Jika google_id masih kosong atau berbeda, update
                if (empty($existingUser['google_id']) || $existingUser['google_id'] !== $userInfo->id) {
                    $this->usersModel->update($existingUser['id'], [
                        'google_id' => $userInfo->id,
                    ]);
                    log_message('debug', 'Update google_id untuk user: ' . $existingUser['id']);
        }

        // Ambil ulang data user setelah update
        $user = $this->usersModel->where('id', $existingUser['id'])->first();
                
   } else {
    // Jika user belum ada sama sekali → daftarkan otomatis
    
   // Generate username unik
    $username = $this->generateUniqueUsername($userInfo->email);

    // Generate kode_user baru
    $kodeBaru = $this->usersModel->generateKode($userInfo->email);

    // Data user baru
    $newUserData = [
        'kode_user' => $kodeBaru, 
        'username'  => $username,
        'email'     => $userInfo->email,
        'google_id' => $userInfo->id,
        'role'      => 2,  
        'status'    => 1,   
        'auth_type' => 'google' 
    ];

    log_message('debug', 'User baru akan dibuat: ' . json_encode($newUserData));

    // Insert ke database
    $newUserId = $this->usersModel->insert($newUserData);

     // Ambil data user baru
    $user = $this->usersModel->find($newUserId);
     // Tandai sebagai user baru
    session()->set('is_new_user', true); 
   }

}

log_message('debug', 'Final User Session: ' . json_encode($user)); 

// Setelah semua aman → set session untuk login
session()->set([
    'isLoggedIn' => true,
    'userId'    => $user['id'],
    'clientId'   => $user['id'],
    'email'      => $user['email'],
    'role'       => $user['role'],
    'auth_type'  => $user['auth_type']
]);

// Redirect ke dashboard
return redirect()->to(base_url('dashboard')); 


        } catch (\Exception $e) {
             // Tangani error tak terduga
            log_message('error', 'Google Login Exception: ' . $e->getMessage());
            
            return redirect()->to(base_url('login'))->with('errors-login', ['Google login failed: ' . $e->getMessage()]);
        }
    }
}
