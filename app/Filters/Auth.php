<?php

namespace App\Filters; // Namespace untuk filter

use CodeIgniter\Filters\FilterInterface; // Interface untuk filter
use CodeIgniter\HTTP\RequestInterface;   // Interface untuk HTTP request
use CodeIgniter\HTTP\ResponseInterface;  // Interface untuk HTTP response

class Auth implements FilterInterface
{
    /**
     ** Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
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
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param array|null $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Saat ini tidak ada proses yang dijalankan setelah controller
    }
}
