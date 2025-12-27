<?php

namespace App\Controllers;

use App\Controllers\BaseController;

// Import model-model yang digunakan
use App\Models\Client;
use App\Models\MetodePembayaran;
use App\Models\Sampah;
use App\Models\Transaksi;

// Library Midtrans Snap untuk pembayaran online
use App\Libraries\MidtransSnap;

// Library QR Code
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

// Controller untuk mengelola transaksi penjualan (jenis = out)
class PenjualanController extends BaseController
{
     // Properti model dan library
    protected $sampahModel;
    protected $metodeBayarModel;
    protected $klienModel;
    protected $transaksiModel;
    protected $midtransSnap;

    // Constructor: inisialisasi model & library
    public function __construct()
    {
        $this->sampahModel = new Sampah();
        $this->metodeBayarModel = new MetodePembayaran();
        $this->klienModel = new Client();
        $this->transaksiModel = new Transaksi();
        $this->midtransSnap = new MidtransSnap();
    }

      // Menampilkan daftar data penjualan
    public function index()
    {
        $data = [
            "title" => "Data Penjualan",
            // Ambil transaksi jenis 'out' berdasarkan client
            "data" => $this->transaksiModel->getPenjualan(session('clientId'), 'out'),
        ];

        return view('penjualan/index', $data);
    }

    // Menampilkan form tambah penjualan
    public function create()
    {
        $data = [
            "title" => "Tambah Data Penjualan",
             // Data sampah
            "sampah" => $this->sampahModel
                ->where('client_id', session('clientId'))
                ->findAll(),
             // Data metode pembayaran
            "bayar" => $this->metodeBayarModel
                ->where('client_id', session('clientId'))
                ->findAll(),
             // Data klien
            "klien" => $this->klienModel
                ->where('client_id', session('clientId'))
                ->findAll(),
        ];

        return view('penjualan/create', $data);
    }

    // AJAX untuk mengambil data sampah + stok tersedia
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

    // Menyimpan data penjualan
    public function store()
    {
        // Ambil data dari form
        $tanggal = $this->request->getPost('tanggal');
        $nama_sampah = $this->request->getPost('nama_sampah');
        $jumlah_jual = $this->request->getPost('jumlah_jual');
        $metode_bayar = $this->request->getPost('metode_bayar');
        $id = $this->request->getPost('id');

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

       // Ambil harga jual sampah
        $sampahData = $this->sampahModel->find($nama_sampah);
        $totalHarga = $sampahData['harga_jual'] * $jumlah_jual;

         // Data transaksi
        $data = [
            'tanggal' => $tanggal,
            'sampah_id' => $nama_sampah,
            'jumlah' => $jumlah_jual,
            'metode_bayar' => $metode_bayar,
        ];

         // Jika edit
        if ($id) {
            $data['id'] = $id;
            $text = 'diupdate';
        } else {
             // Jika tambah data baru
            $text = 'ditambahkan';
            $data['client_id'] = session('clientId');
            $data['jenis'] = 'out';
        }

       // Jika pembayaran via Midtrans
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
                     // Informasi utama transaksi
                    'transaction_details' => [
                    // ID pesanan yang unik
                        'order_id' => $orderId,
                    // Total nominal transaksi (harus bertipe integer)
                        'gross_amount' => (int) $totalHarga,
                    ],
                    // Detail item yang dijual
                    'item_details' => [
                        [
                             // ID item (dibuat manual agar unik)
                            'id' => 'SMPH-' . $nama_sampah,
                             // Harga satuan item
                            'price' => (int) $sampahData['harga_jual'],
                             // Jumlah item yang dijual
                            'quantity' => (int) $jumlah_jual,
                             // Nama item yang tampil di halaman pembayaran Midtrans
                            'name' => $sampahData['nama_sampah'] . ' (' . $jumlah_jual . ' kg)',
                        ]
                    ],
                    // Data pelanggan yang melakukan transaksi
                    'customer_details' => [
                         // Nama pelanggan
                        'first_name' => $clientData['nama_lengkap'] ?? 'Customer',
                        // Email pelanggan
                        'email' => $clientData['email'] ?? 'customer@example.com',
                         // Nomor telepon pelanggan
                        'phone' => $clientData['no_telp'] ?? '',
                    ],
                    // URL callback Midtrans setelah proses pembayaran
                    'callbacks' => [
                        // Callback jika pembayaran selesai
                        'finish' => base_url('penjualan/midtrans-finish'),
                        // Callback jika pembayaran belum selesai
                        'unfinish' => base_url('penjualan/midtrans-unfinish'),
                        // Callback jika terjadi error
                        'error' => base_url('penjualan/midtrans-error'),
                    ]
                ];

                // Buat transaksi Midtrans
                $midtransTransaction = $this->midtransSnap->createTransaction($midtransParams);
                // Mengambil token pembayaran Midtrans Snap
                $token = $midtransTransaction->token;

                // Menyelesaikan transaksi database
                $this->transaksiModel->db->transComplete();

               // Jika request dikirim menggunakan AJAX
                if ($this->request->isAJAX()) {
                     // Kembalikan response JSON berisi token Midtrans
                    return $this->response->setJSON([
                        'success' => true,
                        'token' => $token,
                        'transaksi_id' => $transaksiId,
                        'order_id' => $orderId
                    ]);
                }

               // ======================================================
              // REDIRECT JIKA BUKAN AJAX
             // ======================================================
                
                // Redirect ke halaman pembayaran Midtrans dengan token
                return redirect()->to('penjualan/midtrans-payment?token=' . $token);
            } catch (\Throwable $th) {
                 // Rollback transaksi database jika terjadi error
                $this->transaksiModel->db->transRollback();

                // Jika request AJAX, kirim response error dalam bentuk JSON
                if ($this->request->isAJAX()) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage()
                    ]);
                }

                 // Jika bukan AJAX, tampilkan pesan error
                $message = [
                    'title' => 'Error',
                    'text' => 'Gagal membuat transaksi Midtrans: ' . $th->getMessage(),
                    'icon' => 'error'
                ];
                session()->setFlashdata($message);
                return redirect()->back();
            }
        }

        // Kurangi stok sampah berdasarkan jumlah yang dijual
        $stokTersedia = $stokTersedia - $jumlah_jual;

        try {
             // Mulai transaksi database
            $this->transaksiModel->db->transException(true)->transStart();
             // Simpan data transaksi ke database
            $this->transaksiModel->save($data);
             // Update stok sampah
            $this->sampahModel->update($nama_sampah, ['satuan' => $stokTersedia]);
             // Selesaikan transaksi database
            $this->transaksiModel->db->transComplete();

             // Jika request dari AJAX (QRIS)
            if ($this->request->isAJAX()) {
                // Ambil ID transaksi terakhir
                $transaksiId = $this->transaksiModel->getInsertID();
                  // Kembalikan response JSON
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil ' . $text,
                    'transaksi_id' => $transaksiId,
                    'redirect_url' => base_url('penjualan/qrcode/' . $transaksiId)
                ]);
            }

             // Jika bukan AJAX
            $message = [
                'title' => 'Success',
                'text' => 'Data berhasil ' . $text,
                'icon' => 'success'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        } catch (\Throwable $th) {
            // Rollback transaksi jika terjadi error
            $this->transaksiModel->db->transRollback();
            
            // Jika request dari AJAX, kembalikan JSON error
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data gagal ' . $text
                ]);
            }

             // Jika bukan AJAX
            $message = [
                'title' => 'Error',
                'text' => 'Data gagal ' . $text,
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->back();
        }
    }

    // Menghapus Transaksi & Mengembalikan Stok
    public function delete()
{
      // Ambil ID transaksi dari request POST
    $id = $this->request->getPost('id');

    // Ambil data transaksi sebelum dihapus
    $transaksi = $this->transaksiModel->find($id);

       // Jika transaksi tidak ditemukan
    if (!$transaksi) {
        return $this->response->setJSON([
            "title" => "Gagal",
            "text" => "Transaksi tidak ditemukan",
            "icon" => "error"
        ]);
    }

   // Ambil stok sampah saat ini
    $stokSekarang = $this->sampahModel->getStokTersedia($transaksi['sampah_id']);

   // Hitung stok baru (stok dikembalikan)
    $stokBaru = $stokSekarang + $transaksi['jumlah'];

    try {
         // Mulai transaksi database
        $this->transaksiModel->db->transStart();

        // Update stok sampah
        $this->sampahModel->update($transaksi['sampah_id'], [
            'satuan' => $stokBaru
        ]);

        // Hapus transaksi
        $this->transaksiModel->delete($id);

         // Update stok sampah
        $this->transaksiModel->db->transComplete();

        // Response sukses
        return $this->response->setJSON([
            "title" => "Berhasil",
            "text" => "Transaksi berhasil dihapus, stok dikembalikan",
            "icon" => "success"
        ]);

    } catch (\Throwable $th) {

        // Rollback jika terjadi error
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
    * Generate QR Code untuk pembayaran QRIS
    * Fungsi ini digunakan untuk menghasilkan QR Code dalam bentuk gambar (PNG)
    * berdasarkan ID transaksi, lalu langsung ditampilkan ke browser.
    *
    * @param int $id ID transaksi
    */
    public function generateQrCodeSimple($id)
    {
        try {
            // Mengambil data transaksi berdasarkan ID
            $transaksi = $this->transaksiModel->find($id);

            // Jika transaksi tidak ditemukan, tampilkan error 404
            if (!$transaksi) {
                throw new \CodeIgniter\Exceptions\PageNotFoundException('Transaksi tidak ditemukan');
            }

            // Validasi keamanan: memastikan transaksi milik client yang sedang login
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

            // Generate QR Code
            $qrCode = new QrCode($testData);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);

            // Kembalikan QR Code sebagai image PNG
            $this->response->setHeader('Content-Type', 'image/png');
            return $this->response->setBody($result->getString());
        } catch (\Exception $e) {
            // Jika error, tampilkan detail error dalam JSON
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    /**
 * Menampilkan halaman pembayaran Midtrans Snap
 * Token Midtrans diterima dari proses transaksi sebelumnya
 */
    public function midtransPayment()
    {
         // Ambil token Midtrans dari URL
        $token = $this->request->getGet('token');

        // Jika token tidak ada
        if (!$token) {
            $message = [
                'title' => 'Error',
                'text' => 'Token pembayaran tidak ditemukan',
                'icon' => 'error'
            ];
            session()->setFlashdata($message);
            return redirect()->to('penjualan');
        }

        // Ambil konfigurasi Midtrans
        $config = config('Midtrans');

        // Data yang dikirim ke view pembayaran
        $data = [
            'title' => 'Pembayaran Midtrans',
            'token' => $token,
            'client_key' => $config->clientKey
        ];

        return view('penjualan/midtrans_payment', $data);
    }

   /**
 * Menerima notifikasi (webhook) dari Midtrans
 * Digunakan untuk update status transaksi secara otomatis
 */
    public function midtransNotification()
    {
        try {
             // Ambil payload JSON dari Midtrans
            $notification = json_decode(file_get_contents('php://input'), true);

             // Validasi payload
            if (!$notification) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid notification']);
            }

             // Ambil informasi penting dari notifikasi
            $orderId = $notification['order_id'] ?? null;
            $transactionStatus = $notification['transaction_status'] ?? null;
            $fraudStatus = $notification['fraud_status'] ?? null;

             // Validasi order ID
            if (!$orderId) {
                return $this->response->setJSON([
                    'status' => 'error', 
                    'message' => 'Order ID not found'
                ]);
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
        // Ambil order_id dari parameter URL (GET)
        $orderId = $this->request->getGet('order_id');

        // Jika order_id tersedia
        if ($orderId) {
           // Cari data transaksi berdasarkan order_id yang disimpan di field 'bukti'
            $transaksi = $this->transaksiModel->where('bukti', $orderId)->first();

            // Jika transaksi ditemukan di database
            if ($transaksi) {
               // Ambil konfigurasi Midtrans dari file config
                $config = config('Midtrans');
                 // Set konfigurasi Midtrans secara manual
                \Midtrans\Config::$serverKey = $config->serverKey;
                \Midtrans\Config::$isProduction = $config->isProduction;
                \Midtrans\Config::$isSanitized = $config->isSanitized;
                \Midtrans\Config::$is3ds = $config->is3ds;

                try {
                     // Ambil status transaksi langsung dari server Midtrans
                    $status = \Midtrans\Transaction::status($orderId);
                    // Ambil status transaksi, baik dalam bentuk object atau array
                    $transactionStatus = is_object($status) ? $status->transaction_status : ($status['transaction_status'] ?? 'unknown');

                    // Jika pembayaran sudah sukses
                    if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                        
                        // Update stok sampah setelah pembayaran berhasil
                        $this->updateStokAfterPayment($transaksi);

                         // Pesan sukses untuk user
                        $message = [
                            'title' => 'Success',
                            'text' => 'Pembayaran berhasil!',
                            'icon' => 'success'
                        ];
                    } else {
                          // Jika pembayaran belum selesai (pending / status lain)
                        $message = [
                            'title' => 'Info',
                            'text' => 'Pembayaran sedang diproses. Status: ' . $transactionStatus,
                            'icon' => 'info'
                        ];
                    }
                } catch (\Exception $e) {
                     // Jika gagal mengambil status dari Midtrans
                    $message = [
                        'title' => 'Info',
                        'text' => 'Pembayaran sedang diproses',
                        'icon' => 'info'
                    ];
                }
            } else {
                // Jika transaksi tidak ditemukan di database
                $message = [
                    'title' => 'Error',
                    'text' => 'Transaksi tidak ditemukan',
                    'icon' => 'error'
                ];
            }
        } else {
            // Jika order_id tidak ada di URL
            $message = [
                'title' => 'Info',
                'text' => 'Terima kasih! Pembayaran sedang diproses',
                'icon' => 'info'
            ];
        }

         // Simpan pesan ke session flashdata
        session()->setFlashdata($message);

        // Redirect kembali ke halaman penjualan
        return redirect()->to('penjualan');
    }

    /**
 * Handle redirect jika pembayaran Midtrans belum selesai
 * Biasanya terjadi jika user menutup halaman pembayaran
 * sebelum transaksi selesai.
 */
    public function midtransUnfinish()
    {
        // Pesan informasi ke user
        $message = [
            'title' => 'Info',
            'text' => 'Pembayaran belum selesai. Silakan coba lagi atau hubungi customer service.',
            'icon' => 'info'
        ];
         // Simpan pesan ke session
        session()->setFlashdata($message);
        // Redirect ke halaman penjualan
        return redirect()->to('penjualan');
    }

    /**
 * Handle redirect jika terjadi error saat proses pembayaran Midtrans
 * Contoh: kegagalan sistem, error validasi, atau error server
 */
    public function midtransError()
    {
        // Pesan error ke user
        $message = [
            'title' => 'Error',
            'text' => 'Terjadi kesalahan saat memproses pembayaran. Silakan coba lagi.',
            'icon' => 'error'
        ];
        
        // Simpan pesan ke session
        session()->setFlashdata($message);

         // Redirect ke halaman penjualan
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

            // Ambil data sampah
            $sampah = $this->sampahModel->find($sampahId);

             // Update stok jika data sampah ditemukan
            if ($sampah) {
                $stokBaru = $sampah['satuan'] - $jumlahJual;
                $this->sampahModel->update($sampahId, ['satuan' => $stokBaru]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error updating stock after payment: ' . $e->getMessage());
        }
    }
}
