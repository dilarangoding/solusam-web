<?php

namespace App\Filters; // Namespace untuk filter

use CodeIgniter\Filters\FilterInterface; // Interface untuk filter
use CodeIgniter\HTTP\RequestInterface;   // Interface untuk HTTP request
use CodeIgniter\HTTP\ResponseInterface;  // Interface untuk HTTP response

class Auth implements FilterInterface
{
    /**
     * Method ini dijalankan sebelum controller dieksekusi.
     * Gunanya untuk memeriksa apakah user sudah login.
     * Jika tidak, redirect ke halaman login.
     *
     * @param RequestInterface $request
     * @param array|null $arguments
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek session 'isLoggedIn', jika false maka redirect ke halaman login
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/')->with('error', 'Silakan login terlebih dahulu');
        }
        // Jika sudah login, biarkan eksekusi controller berjalan
    }

    /**
     * Method ini dijalankan setelah controller dieksekusi.
     * Bisa digunakan untuk memodifikasi response.
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Saat ini tidak ada proses yang dijalankan setelah controller
    }
}
