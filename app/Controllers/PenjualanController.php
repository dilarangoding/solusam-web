<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PenjualanController extends BaseController
{
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;

    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
    }

    public function index()
    {
        $data = [
            "title" => "Data Penjualan",
            "data" => $this->transaksiModel->getPenjualan(session('clientId'), 'out'),
        ];

        return view('penjualan/index', $data);
    }

    public function create()
    {
        $data = [
            "title" => "Tambah Data Penjualan",
            "sampah" => $this->sampahModel->where('client_id', session('clientId'))->findAll(),
            "bayar" => $this->metodeBayarModel->where('client_id', session('clientId'))->findAll(),
            "klien" => $this->klienModel->where('client_id', session('clientId'))->findAll(),
        ];

        return view('penjualan/create', $data);
    }

    public function edit($id)
    {
        $data = [
            "title" => "Edit Data Penjualan",
            "sampah" => $this->sampahModel->where('client_id', session('clientId'))->findAll(),
            "bayar" => $this->metodeBayarModel->where('client_id', session('clientId'))->findAll(),
            "klien" => $this->klienModel->where('client_id', session('clientId'))->findAll(),
            "data" => $this->transaksiModel->find($id),
        ];

        return view('penjualan/edit', $data);
    }

    public function sampahAjax()
    {
        $id = $this->request->getPost('id');
        $data = $this->sampahModel->find($id);

        // Tambahkan informasi stok tersedia
        if ($data) {
            $stokTersedia = $this->sampahModel->getStokTersedia($id);
            $data['stok_tersedia'] = $stokTersedia;
        }

        return $this->response->setJSON($data);
    }

    public function store()
    {
        $tanggal = $this->request->getPost('tanggal');
        $nama_sampah = $this->request->getPost('nama_sampah');
        $jumlah_jual = $this->request->getPost('jumlah_jual');
        $metode_bayar = $this->request->getPost('metode_bayar');
        $id = $this->request->getPost('id');
        // $bukti_qris = $this->request->getFile('bukti_qris');

        // Validasi stok tersedia
        $stokTersedia = $this->sampahModel->getStokTersedia($nama_sampah);
        if ($jumlah_jual > $stokTersedia) {
            $message = [
                'title' => 'Error',
                'text' => 'Jumlah yang dijual (' . $jumlah_jual . ' kg) melebihi stok tersedia (' . $stokTersedia . ' kg)',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }

        $stokTersedia = $stokTersedia - $jumlah_jual;

        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_jual,
            'metode_bayar' => $metode_bayar,
        ];


        // Handle upload file bukti QRIS
        // if ($bukti_qris && $bukti_qris->isValid() && !$bukti_qris->hasMoved()) {
        //     $filename = $bukti_qris->getRandomName();
        //     $data['bukti'] = $filename;

        //     // Pastikan folder bukti ada
        //     $buktiPath = ROOTPATH . 'public/bukti';
        //     if (!is_dir($buktiPath)) {
        //         mkdir($buktiPath, 0755, true);
        //     }

        //     $bukti_qris->move($buktiPath, $filename);
        // }

        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'out';
        }

        try {
            $this->transaksiModel->db->transException(true)->transStart();
            $this->transaksiModel->save($data);
            $this->sampahModel->update($nama_sampah, ['satuan' => $stokTersedia]);
            $this->transaksiModel->db->transComplete();

            // Jika request dari AJAX (untuk QRIS), kembalikan JSON dengan ID transaksi
            if ($this->request->isAJAX()) {
                $transaksiId = $this->transaksiModel->getInsertID();
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil ' . $text,
                    'transaksi_id' => $transaksiId,
                    'redirect_url' => base_url('penjualan/qrcode/' . $transaksiId)
                ]);
            }

            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        } catch (\Throwable $th) {
            $this->transaksiModel->db->transRollback();
            // Jika request dari AJAX, kembalikan JSON error
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data gagal ' . $text
                ]);
            }

            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        try {
            $this->transaksiModel->delete($id);
            $response = ["title" => "Berhasil", "text" => "Data berhasil dihapus", "icon" => "success"];
        } catch (\Throwable $th) {
            $response = ["title" => "Gagal", "text" => "Data gagal dihapus", "icon" => "error"];
        }
        return $this->response->setJSON($response);
    }

    /**
     * Generate QR Code untuk pembayaran QRIS
     * @param int $id ID transaksi
     */
    public function generateQrCode($id)
    {
        try {
            // Ambil data transaksi
            $transaksi = $this->transaksiModel->find($id);

            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            // Pastikan transaksi milik client yang sedang login
            if ($transaksi['client_id'] != session('clientId')) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak');
            }

            // Data untuk QR Code - URL publik untuk detail transaksi
            $qrData = base_url('public/transaksi/' . $id);

            // Generate QR Code
            $qrCode = new QrCode($qrData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Set header untuk response image
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="qrcode-' . $id . '.png"');

            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
            // Jika ada error, tampilkan error image
            $this->response->setHeader('Content-Type', 'image/png');
            $this->response->setHeader('Content-Disposition', 'inline; filename="error.png"');

            // Buat error image sederhana
            $errorImage = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==');
            return $this->response->setBody($errorImage);
        }
    }

    /**
     * Tampilkan halaman QR Code untuk pembayaran
     * @param int $id ID transaksi
     */
    public function showQrCode($id)
    {
        try {
            // Ambil data transaksi dengan join ke tabel sampah
            $builder = $this->transaksiModel->db->table('transaksi t');
            $builder->select('t.*, s.nama_sampah, s.harga_jual, s.satuan');
            $builder->join('data_sampah s', 's.id = t.sampah_id');
            $builder->where('t.id', $id);
            $builder->where('t.client_id', session('clientId'));

            $transaksi = $builder->get()->getRowArray();

            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            // Generate QR Code sebagai base64 - URL publik untuk detail transaksi
            $qrData = base_url('public/transaksi/' . $id);

            $qrCode = new QrCode($qrData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qrCodeBase64 = 'data:image/png;base64,' . base64_encode($result->getString());

            $data = [
                'title' => 'QR Code Pembayaran',
                'transaksi' => $transaksi,
                'qr_code_base64' => $qrCodeBase64,
                'qr_code_url' => base_url('penjualan/qrcode-image/' . $id)
            ];

            return view('penjualan/qrcode', $data);
        } catch (\Exception $e) {
            // Jika ada error, redirect ke halaman penjualan dengan pesan error
            $message = [
                'title' => 'Error',
                'text' => 'Gagal menampilkan QR Code: ' . $e->getMessage(),
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        }
    }

    /**
     * Generate QR Code sederhana menggunakan Google Charts API sebagai fallback
     * @param int $id ID transaksi
     */
    public function generateQrCodeSimple($id)
    {
        try {
            // Ambil data transaksi
            $transaksi = $this->transaksiModel->find($id);

            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            // Pastikan transaksi milik client yang sedang login
            if ($transaksi['client_id'] != session('clientId')) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Akses ditolak');
            }

            // Data untuk QR Code - URL publik untuk detail transaksi
            $qrData = base_url('public/transaksi/' . $id);

            // Generate QR Code menggunakan Google Charts API sebagai fallback
            $qrUrl = 'https://chart.googleapis.com/chart?chs=300x300&chld=M|0&cht=qr&chl=' . urlencode($qrData);

            // Redirect ke Google Charts API
            return redirect()->to($qrUrl);
        } catch (\Exception $e) {
            // Jika ada error, tampilkan error page
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Gagal generate QR Code: ' . $e->getMessage());
        }
    }

    /**
     * Test QR Code generation untuk debugging
     */
    public function testQrCode()
    {
        try {
            // Test data sederhana
            $testData = json_encode(['test' => 'data', 'id' => 123]);

            $qrCode = new QrCode($testData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            $this->response->setHeader('Content-Type', 'image/png');
            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
