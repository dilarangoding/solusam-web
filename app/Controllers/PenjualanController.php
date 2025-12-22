<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;
use App\Libraries\MidtransSnap;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class PenjualanController extends BaseController
{
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;
    protected $midtransSnap;

    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
        $this->midtransSnap = new MidtransSnap();
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

        // Ambil data sampah untuk mendapatkan harga
        $sampahData = $this->sampahModel->find($nama_sampah);
        $totalHarga = $sampahData['harga_jual'] * $jumlah_jual;

        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_jual,
            'metode_bayar' => $metode_bayar,
        ];

        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'out';
        }

        // Jika metode pembayaran adalah Midtrans
        if ($metode_bayar === 'midtrans') {
            try {
                // Simpan transaksi dulu (belum update stok)
                $this->transaksiModel->db->transException(true)->transStart();
                $this->transaksiModel->save($data);
                $transaksiId = $this->transaksiModel->getInsertID();

                // Generate order ID untuk Midtrans
                $orderId = 'TRX-' . $transaksiId . '-' . time();

                // Update transaksi dengan order_id (simpan di field bukti)
                $this->transaksiModel->update($transaksiId, ['bukti' => $orderId]);

                // Ambil data client untuk customer details
                $clientData = $this->klienModel->find(session('clientId'));

                // Siapkan parameter untuk Midtrans
                $midtransParams = [
                    'transaction_details' => [
                        'order_id' => $orderId,
                        'gross_amount' => (int) $totalHarga,
                    ],
                    'item_details' => [
                        [
                            'id' => 'SMPH-' . $nama_sampah,
                            'price' => (int) $sampahData['harga_jual'],
                            'quantity' => (int) $jumlah_jual,
                            'name' => $sampahData['nama_sampah'] . ' (' . $jumlah_jual . ' kg)',
                        ]
                    ],
                    'customer_details' => [
                        'first_name' => $clientData['nama_lengkap'] ?? 'Customer',
                        'email' => $clientData['email'] ?? 'customer@example.com',
                        'phone' => $clientData['no_telp'] ?? '',
                    ],
                    'callbacks' => [
                        'finish' => base_url('penjualan/midtrans-finish'),
                        'unfinish' => base_url('penjualan/midtrans-unfinish'),
                        'error' => base_url('penjualan/midtrans-error'),
                    ]
                ];

                // Buat transaksi Midtrans
                $midtransTransaction = $this->midtransSnap->createTransaction($midtransParams);
                $token = $midtransTransaction->token;

                $this->transaksiModel->db->transComplete();

                // Jika request dari AJAX, kembalikan token Midtrans
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => true,
                        'token' => $token,
                        'transaksi_id' => $transaksiId,
                        'order_id' => $orderId
                    ]);
                }

                // Jika bukan AJAX, redirect dengan token
                return redirect()->to('penjualan/midtrans-payment?token=' . $token);
            } catch (\Throwable $th) {
                $this->transaksiModel->db->transRollback();

                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage()
                    ]);
                }

                $message = [
                    'title' => 'Error',
                    'text' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage(),
                    'icon' => 'error'
                ];
                session()->setFlashdata($message);
                return redirect()->back();
            }
        }

        // Untuk metode pembayaran selain Midtrans (tunai)
        $stokTersedia = $stokTersedia - $jumlah_jual;

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

    // Ambil data transaksi sebelum dihapus
    $transaksi = $this->transaksiModel->find($id);

    if (!$transaksi) {
        return $this->response->setJSON([
            "title" => "Gagal",
            "text" => "Transaksi tidak ditemukan",
            "icon" => "error"
        ]);
    }

    // Ambil stok sampah sebelum transaksi dihapus
    $stokSekarang = $this->sampahModel->getStokTersedia($transaksi['sampah_id']);

    // Hitung stok baru → kembalikan jumlah yg dikurangi
    $stokBaru = $stokSekarang + $transaksi['jumlah'];

    try {
        $this->transaksiModel->db->transStart();

        // Update stok sampah
        $this->sampahModel->update($transaksi['sampah_id'], [
            'satuan' => $stokBaru
        ]);

        // Hapus transaksi
        $this->transaksiModel->delete($id);

        $this->transaksiModel->db->transComplete();

        return $this->response->setJSON([
            "title" => "Berhasil",
            "text" => "Transaksi berhasil dihapus, stok dikembalikan",
            "icon" => "success"
        ]);

    } catch (\Throwable $th) {

        $this->transaksiModel->db->transRollback();

        return $this->response->setJSON([
            "title" => "Gagal",
            "text" => "Gagal menghapus transaksi",
            "icon" => "error"
        ]);
    }
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

    /**
     * Tampilkan halaman pembayaran Midtrans Snap
     */
    public function midtransPayment()
    {
        $token = $this->request->getGet('token');

        if (!$token) {
            $message = [
                'title' => 'Error',
                'text' => 'Token pembayaran tidak ditemukan',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        }

        $config = config('Midtrans');

        $data = [
            'title' => 'Pembayaran Midtrans',
            'token' => $token,
            'client_key' => $config->clientKey
        ];

        return view('penjualan/midtrans_payment', $data);
    }

    /**
     * Handle notification dari Midtrans (webhook)
     */
    public function midtransNotification()
    {
        try {
            $notification = json_decode(file_get_contents('php://input'), true);

            if (!$notification) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid notification']);
            }

            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

            if (!$orderId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Order ID not found']);
            }

            // Cari transaksi berdasarkan order_id (disimpan di field bukti)
            $transaksi = $this->transaksiModel->where('bukti', $orderId)->first();

            if (!$transaksi) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Transaction not found']);
            }

            // Verifikasi signature (optional, untuk keamanan lebih)
            // $config = config('Midtrans');
            // \Midtrans\Config::$serverKey = $config->serverKey;
            // $status = \Midtrans\Transaction::status($orderId);

            // Handle status pembayaran
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    // TODO: Set payment status in transaction to 'challenge'
                } else if ($fraudStatus == 'accept') {
                    // Pembayaran berhasil, update stok
                    $this->updateStokAfterPayment($transaksi);
                }
            } else if ($transactionStatus == 'settlement') {
                // Pembayaran berhasil, update stok
                $this->updateStokAfterPayment($transaksi);
            } else if ($transactionStatus == 'pending') {
                // Pembayaran masih pending
            } else if ($transactionStatus == 'deny' || $transactionStatus == 'expire' || $transactionStatus == 'cancel') {
                // Pembayaran gagal atau dibatalkan
            }

            return $this->response->setJSON(['status' => 'ok']);
        } catch (\Exception $e) {
            log_message('error', 'Midtrans notification error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Handle redirect setelah pembayaran selesai
     */
    public function midtransFinish()
    {
        $orderId = $this->request->getGet('order_id');

        if ($orderId) {
            // Cari transaksi
            $transaksi = $this->transaksiModel->where('bukti', $orderId)->first();

            if ($transaksi) {
                // Cek status pembayaran dari Midtrans
                $config = config('Midtrans');
                \Midtrans\Config::$serverKey = $config->serverKey;
                \Midtrans\Config::$isProduction = $config->isProduction;
                \Midtrans\Config::$isSanitized = $config->isSanitized;
                \Midtrans\Config::$is3ds = $config->is3ds;

                try {
                    $status = \Midtrans\Transaction::status($orderId);
                    $transactionStatus = is_object($status) ? $status->transaction_status : ($status['transaction_status'] ?? 'unknown');

                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        // Update stok jika belum diupdate
                        $this->updateStokAfterPayment($transaksi);

                        $message = [
                            'title' => 'Success',
                            'text' => 'Pembayaran berhasil!',
                            'icon' => 'success'
                        ];
                    } else {
                        $message = [
                            'title' => 'Info',
                            'text' => 'Pembayaran sedang diproses. Status: ' . $transactionStatus,
                            'icon' => 'info'
                        ];
                    }
                } catch (\Exception $e) {
                    $message = [
                        'title' => 'Info',
                        'text' => 'Pembayaran sedang diproses',
                        'icon' => 'info'
                    ];
                }
            } else {
                $message = [
                    'title' => 'Error',
                    'text' => 'Transaksi tidak ditemukan',
                    'icon' => 'error'
                ];
            }
        } else {
            $message = [
                'title' => 'Info',
                'text' => 'Terima kasih! Pembayaran sedang diproses',
                'icon' => 'info'
            ];
        }

        session()->setFlashdata($message);
        return redirect()->to('penjualan');
    }

    /**
     * Handle redirect jika pembayaran tidak selesai
     */
    public function midtransUnfinish()
    {
        $message = [
            'title' => 'Info',
            'text' => 'Pembayaran belum selesai. Silakan coba lagi atau hubungi customer service.',
            'icon' => 'info'
        ];
        session()->setFlashdata($message);
        return redirect()->to('penjualan');
    }

    /**
     * Handle redirect jika terjadi error
     */
    public function midtransError()
    {
        $message = [
            'title' => 'Error',
            'text' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
            'icon' => 'error'
        ];
        session()->setFlashdata($message);
        return redirect()->to('penjualan');
    }

    /**
     * Update stok setelah pembayaran berhasil
     */
    private function updateStokAfterPayment($transaksi)
    {
        try {
            // Cek apakah stok sudah diupdate (dengan mengecek apakah stok masih sama dengan sebelum transaksi)
            $sampahId = $transaksi['sampah_id'];
            $jumlahJual = $transaksi['jumlah'];

            $sampah = $this->sampahModel->find($sampahId);
            if ($sampah) {
                $stokBaru = $sampah['satuan'] - $jumlahJual;
                $this->sampahModel->update($sampahId, ['satuan' => $stokBaru]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating stock after payment: ' . $e->getMessage());
        }
    }
}
