<?= $this->extend('template/index'); ?> // Menggunakan template utama 'index'
<?= $this->section('content'); ?> // Membuka section konten untuk mengisi layout

<div class="card border-primary mb-4 mt-4"> // Membuat card berbingkai warna biru
    <div class="card-header bg-primary bg-opacity-10 text-primary fw-bold"> // Header card dengan warna biru transparan
        <i class="ti ti-qrcode me-2"></i> // Icon QR code
        <?= $title; ?> 
    </div>

    <div class="card-body text-center"> // Body card dengan teks rata tengah

        <!-- Informasi Transaksi -->
        <div class="row mb-4"> // Membuat row dengan margin bawah
            <div class="col-md-6 mx-auto"> // Kolom 6 dan berada di tengah
                <div class="card border-info"> // Card detail transaksi warna biru muda
                    <div class="card-header bg-info bg-opacity-10 text-info fw-bold"> // Header card
                        Detail Transaksi
                    </div>

                    <div class="card-body"> // Isi card detail transaksi
                        <div class="row g-3"> // Grid dengan jarak antar elemen

                            <div class="col-6"> // Kolom kiri
                                <label class="form-label fw-bold">Nama Sampah:</label> // Label
                                <p class="mb-0"><?= $transaksi['nama_sampah']; ?></p> // Menampilkan nama sampah
                            </div>

                            <div class="col-6"> // Kolom kanan
                                <label class="form-label fw-bold">Jumlah:</label> // Label
                                <p class="mb-0"><?= $transaksi['jumlah']; ?></p> // Menampilkan jumlah
                            </div>

                            <div class="col-6"> // Kolom kiri bawah
                                <label class="form-label fw-bold">Harga per kg:</label> // Label
                                <p class="mb-0">
                                    Rp <?= number_format($transaksi['harga_jual'], 0, ',', '.'); ?> // Format harga
                                </p>
                            </div>

                            <div class="col-6"> // Kolom kanan bawah
                                <label class="form-label fw-bold">Total Harga:</label> // Label
                                <p class="mb-0 fw-bold text-success">
                                    Rp <?= number_format($transaksi['jumlah'] * $transaksi['harga_jual'], 0, ',', '.'); ?> // Hitung total harga
                                </p>
                            </div>

                            <div class="col-12"> // Kolom full
                                <label class="form-label fw-bold">Tanggal:</label> // Label tanggal
                                <p class="mb-0">
                                    <?= date('d F Y', strtotime($transaksi['tanggal'])); ?> // Format tanggal
                                </p>
                            </div>

                        </div> <!-- end row g-3 -->
                    </div> <!-- end card-body -->

                </div> <!-- end card border-info -->
            </div> <!-- end col -->
        </div> <!-- end row detail transaksi -->

        <!-- QR Code -->
        <div class="row mb-4"> // Row untuk menempatkan kartu QR Code
            <div class="col-md-4 mx-auto"> // Kolom dengan lebar 4 dan berada di tengah
                <div class="card border-warning"> // Card dengan border kuning
                    <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold"> // Header card QR Code
                        <i class="ti ti-qrcode me-2"></i> // Icon QR Code
                        Scan QR Code
                    </div>

                    <div class="card-body"> // Body card QR

                        <div class="qr-code-container p-3 bg-white rounded"> // Container untuk QR Code dengan padding dan background putih

                            <?php if (isset($qr_code_base64)): ?> // Jika QR code disediakan dalam bentuk base64
                                
                                <img src="<?= $qr_code_base64; ?>" // Tampilkan QR Code base64
                                    alt="QR Code Pembayaran" // Alt text jika gagal load
                                    class="img-fluid" // Agar gambar responsive
                                    style="max-width: 200px;" // Batas ukuran QR Code
                                    onerror="this.src='<?= $qr_code_url; ?>'; this.onerror=function(){this.src='<?= base_url('penjualan/qrcode-simple/' . $transaksi['id']); ?>';};"> // Jika gagal load base64 → pakai URL → jika gagal lagi → pakai fallback lokal

                            <?php else: ?> // Jika base64 TIDAK tersedia

                                <img src="<?= $qr_code_url; ?>" // Tampilkan QR dari URL biasa
                                    alt="QR Code Pembayaran"
                                    class="img-fluid"
                                    style="max-width: 300px;" // Lebih besar dari base64 version
                                    
                                    onerror="this.src='<?= base_url('penjualan/qrcode-simple/' . $transaksi['id']); ?>'; 
                                    this.onerror=function(){
                                        this.style.display='none'; 
                                        this.nextElementSibling.style.display='block';
                                    };"> 
                                    // Jika gagal load → pakai fallback → jika tetap gagal → sembunyikan gambar dan tampilkan alert warning

                                <div style="display: none;" class="alert alert-warning"> // Pesan jika QR tidak bisa dimuat
                                    <i class="ti ti-alert-triangle me-2"></i> // Icon warning 
                                    QR Code tidak dapat dimuat. Silakan refresh halaman atau gunakan tombol refresh di bawah.
                                </div>

                            <?php endif; ?> // Akhir pengecekan base64

                        </div> <!-- end qr-code-container -->

                        <div class="mt-3"> // Info kecil bawah QR Code
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i> // Icon info
                                Scan QR Code ini untuk melihat detail transaksi // Pesan info
                            </small>
                        </div>

                    </div> <!-- end card-body -->

                </div> <!-- end card border-warning -->

            </div> <!-- end col -->
        </div> <!-- end row -->
        <!-- Tombol Aksi -->
        <div class="d-flex gap-2 justify-content-center"> // Wrapper tombol dengan jarak dan center-align
            <button type="button" class="btn btn-primary" onclick="window.print()"> // Tombol untuk print halaman
                <i class="ti ti-printer me-2"></i> // Icon printer
                Cetak QR Code // Text tombol
            </button>

            <a href="<?= base_url('penjualan'); ?>" class="btn btn-secondary"> // Tombol kembali ke halaman penjualan
                <i class="ti ti-arrow-left me-2"></i> // Icon panah kembali
                Kembali ke Daftar Penjualan
            </a>
            <!-- Tombol refresh QR Code disembunyikan -->
            <!-- <button type="button" class="btn btn-success" onclick="refreshQrCode()"> -->
            <!--     <i class="ti ti-refresh me-2"></i> -->
            <!--     Refresh QR Code -->
            <!-- </button> -->
        </div>
</div> <!-- Akhir dari card-body -->
</div> <!-- Akhir dari card utama -->

<!-- Modal untuk konfirmasi refresh -->
<div class="modal fade" id="refreshModal" tabindex="-1" aria-labelledby="refreshModalLabel" aria-hidden="true"> // Modal Bootstrap untuk konfirmasi refresh
    <div class="modal-dialog"> // Ukuran modal
        <div class="modal-content"> // Container isi modal

            <div class="modal-header"> // Header modal
                <h5 class="modal-title" id="refreshModalLabel">Refresh QR Code</h5> // Judul modal
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> // Tombol close modal
            </div>

            <div class="modal-body"> // Isi modal
                Apakah Anda yakin ingin memperbarui QR Code? QR Code lama tidak akan berlaku lagi. // Peringatan
            </div>

            <div class="modal-footer"> // Footer modal (tombol aksi)
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button> // Tombol batal
                <button type="button" class="btn btn-success" onclick="confirmRefresh()">Ya, Refresh</button> // Tombol konfirmasi
            </div>

        </div>
    </div>
</div>

<?= $this->endSection(); ?> // Menutup section utama "content"

<?= $this->section('js'); ?> // Membuka section JS untuk halaman

<script>
    function refreshQrCode() { // Fungsi membuka modal refresh
        $('#refreshModal').modal('show'); // Menampilkan modal
    }

    function confirmRefresh() { // Fungsi setelah user konfirmasi refresh
        window.location.reload(); // Reload halaman untuk generate QR Code baru
    }

    // Debug QR Code loading
    $(document).ready(function() { // Eksekusi setelah halaman selesai dimuat
        $('img[alt="QR Code Pembayaran"]').on('load', function() { // Jika gambar berhasil dimuat
            console.log('QR Code berhasil dimuat'); // Log info
        }).on('error', function() { // Jika gambar gagal dimuat
            console.error('QR Code gagal dimuat'); // Log error
            $(this).after('<div class="alert alert-danger mt-2"><i class="ti ti-alert-triangle me-2"></i>QR Code tidak dapat dimuat. Silakan refresh halaman atau hubungi administrator.</div>'); 
            // Tampilkan pesan error ke bawah gambar
        });
    });

    // Auto refresh setiap 5 menit untuk keamanan
    setInterval(function() { 
        if (confirm('QR Code akan diperbarui untuk keamanan. Lanjutkan?')) { // Konfirmasi user sebelum refresh otomatis
            window.location.reload(); // Reload halaman
        }
    }, 300000); // 300.000 ms = 5 menit

    // Print styles
    window.addEventListener('beforeprint', function() { // Event sebelum di-print
        document.body.classList.add('printing'); // Tambah class khusus
    });

    window.addEventListener('afterprint', function() { // Event setelah selesai print
        document.body.classList.remove('printing'); // Hapus class
    });
</script>
<style>
    @media print { // Styling khusus untuk mode print

        .btn,
        .modal,
        .card-header {
            display: none !important; // Sembunyikan tombol dan header card saat print
        }

        .card {
            border: none !important; // Hilangkan border card agar lebih bersih saat print
            box-shadow: none !important; // Hapus shadow saat print
        }

        .qr-code-container {
            border: 2px solid #000 !important; // Tambahkan border hitam tegas untuk QR Code
        }

        body {
            background: white !important; // Background putih saat print
        }
    }

    .qr-code-container {
        border: 1px solid #dee2e6; // Border default QR container
        border-radius: 8px; // Sudut melengkung
    }
</style>

<?= $this->endSection(); ?> // Menutup section JS
