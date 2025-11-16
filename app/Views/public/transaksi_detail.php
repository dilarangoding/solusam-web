<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@tabler/icons@1.0.0/icons-sprite.svg" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background: linear-gradient(45deg, #28a745, #20c997);
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
        }

        .badge {
            font-size: 0.9em;
            padding: 0.5em 0.8em;
        }

        .info-item {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
            border-left: 4px solid #28a745;
        }

        .qr-code-container {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-custom {
            border-radius: 25px;
            padding: 0.7rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-badge {
            font-size: 1.1em;
            padding: 0.6rem 1.2rem;
            border-radius: 25px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="text-center mb-4">
                    <h1 class="text-white fw-bold mb-2">
                        <i class="ti ti-receipt me-3"></i>
                        Detail Transaksi
                    </h1>
                    <p class="text-white-50">Informasi lengkap transaksi pembelian sampah</p>
                </div>

                <!-- Card Detail Transaksi -->
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="mb-0">
                                <i class="ti ti-shopping-cart me-2"></i>
                                Transaksi #<?= $transaksi['id']; ?>
                            </h4>
                            <span class="status-badge bg-success">
                                <i class="ti ti-check me-1"></i>
                                Selesai
                            </span>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <!-- Informasi Transaksi -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-primary fw-bold mb-2">
                                        <i class="ti ti-calendar me-2"></i>
                                        Tanggal Transaksi
                                    </h6>
                                    <p class="mb-0 fs-5"><?= date('d F Y', strtotime($transaksi['tanggal'])); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-primary fw-bold mb-2">
                                        <i class="ti ti-clock me-2"></i>
                                        Waktu Transaksi
                                    </h6>
                                    <p class="mb-0 fs-5"><?= date('H:i', strtotime($transaksi['created_at'])); ?> WIB</p>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Produk -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-success fw-bold mb-2">
                                        <i class="ti ti-package me-2"></i>
                                        Nama Sampah
                                    </h6>
                                    <p class="mb-0 fs-5"><?= $transaksi['nama_sampah']; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-success fw-bold mb-2">
                                        <i class="ti ti-scale me-2"></i>
                                        Jumlah
                                    </h6>
                                    <p class="mb-0 fs-5"><?= $transaksi['jumlah']; ?> kg</p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Harga -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-warning fw-bold mb-2">
                                        <i class="ti ti-currency-dollar me-2"></i>
                                        Harga per kg
                                    </h6>
                                    <p class="mb-0 fs-5">Rp <?= number_format($transaksi['harga_jual'], 0, ',', '.'); ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item bg-success bg-opacity-10">
                                    <h6 class="text-success fw-bold mb-2">
                                        <i class="ti ti-calculator me-2"></i>
                                        Total Harga
                                    </h6>
                                    <p class="mb-0 fs-4 fw-bold text-success">Rp <?= number_format($total_harga, 0, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Informasi Pembayaran -->
                        <div class="row g-4 mb-4">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <h6 class="text-info fw-bold mb-2">
                                        <i class="ti ti-credit-card me-2"></i>
                                        Metode Pembayaran
                                    </h6>
                                    <p class="mb-0 fs-5">
                                        <?php if ($transaksi['metode_bayar'] === 'qris'): ?>
                                            <span class="badge bg-primary">
                                                <i class="ti ti-qrcode me-1"></i>
                                                QRIS
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-success">
                                                <i class="ti ti-cash me-1"></i>
                                                Tunai
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <?php if ($transaksi['bukti']): ?>
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <h6 class="text-info fw-bold mb-2">
                                            <i class="ti ti-file-text me-2"></i>
                                            Bukti Pembayaran
                                        </h6>
                                        <a href="<?= base_url('bukti/' . $transaksi['bukti']); ?>"
                                            target="_blank"
                                            class="btn btn-outline-primary btn-sm">
                                            <i class="ti ti-eye me-1"></i>
                                            Lihat Bukti
                                        </a>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Informasi Penjual -->
                        <?php if ($transaksi['nama_lengkap']): ?>
                            <div class="row g-4 mb-4">
                                <div class="col-12">
                                    <div class="info-item">
                                        <h6 class="text-secondary fw-bold mb-3">
                                            <i class="ti ti-building-store me-2"></i>
                                            Informasi Penjual
                                        </h6>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>Nama:</strong> <?= $transaksi['nama_lengkap']; ?></p>
                                                <p class="mb-1"><strong>Jenis Usaha:</strong> <?= $transaksi['jenis_usaha']; ?></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="mb-1"><strong>No. Telepon:</strong> <?= $transaksi['no_telp']; ?></p>
                                                <p class="mb-1"><strong>Alamat:</strong> <?= $transaksi['alamat']; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- QR Code untuk Verifikasi -->
                        <div class="row justify-content-center">
                            <div class="col-md-6">
                                <div class="qr-code-container">
                                    <h6 class="text-muted mb-3">
                                        <i class="ti ti-qrcode me-2"></i>
                                        QR Code Verifikasi
                                    </h6>
                                    <img src="<?= base_url('public/qrcode/' . $transaksi['id']); ?>"
                                        alt="QR Code Verifikasi"
                                        class="img-fluid"
                                        style="max-width: 200px;">
                                    <p class="text-muted mt-3 small">
                                        Scan QR Code ini untuk memverifikasi transaksi
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="text-center mt-4">
                    <p class="text-white-50">
                        <i class="ti ti-shield-check me-2"></i>
                        Transaksi ini telah diverifikasi dan tercatat dalam sistem
                    </p>
                    <div class="mt-3">
                        <button onclick="window.print()" class="btn btn-light btn-custom me-2">
                            <i class="ti ti-printer me-2"></i>
                            Cetak Detail
                        </button>
                        <button onclick="window.close()" class="btn btn-outline-light btn-custom">
                            <i class="ti ti-x me-2"></i>
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Print styles
        window.addEventListener('beforeprint', function() {
            document.body.style.background = 'white';
            document.querySelectorAll('.card').forEach(card => {
                card.style.boxShadow = 'none';
                card.style.border = '1px solid #ddd';
            });
        });

        window.addEventListener('afterprint', function() {
            document.body.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
        });
    </script>
</body>

</html>