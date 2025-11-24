<?= $this->extend('template/index'); ?> // Memanggil template utama
<?= $this->section('content'); ?> // Membuka section konten utama



<div class="card border-success mb-4 mt-4"> // Card container utama
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau
        + Form <?= $title; ?> // Judul form berdasarkan variabel title
    </div>
    <div class="card-body"> // Body card
        <form action="<?= base_url('penjualan/store') ?>" method="POST" enctype="multipart/form-data" id="form_penjualan"> // Form submit menuju penjualan/store
            <div class="row g-3"> // Grid dengan gap 3

                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label> // Label tanggal
                    <input
                        type="date" // Input tanggal
                        name="tanggal" // Name input tanggal
                        class="form-control"
                        required> // Wajib diisi
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required> // Dropdown pilih sampah
                        <option value="" selected disabled>-- Pilih Sampah --</option>
                        <?php foreach ($sampah as $row) : ?> // Loop data sampah
                            <option value="<?= $row['id'] ?>"> // Value ID sampah
                                <?= $row['nama_sampah'] ?> // Nama sampah yang ditampilkan
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Harga</label>
                    <input
                        type="number" // Input harga
                        name="harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="harga" // Akan otomatis terisi via AJAX
                        readonly // Tidak bisa diubah manual
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Jumlah (kg)</label> // Input jumlah sampah yang dijual
                    <input
                        type="number"
                        min="1"
                        name="jumlah_jual"
                        class="form-control"
                        id="jumlah_jual"
                        onkeyup="jumlah()" // Akan hitung total harga + validasi stok
                        required>

                    <div class="form-text" id="stok_info" style="display: none;"> // Informasi stok tersisa
                        <span class="text-muted">Stok tersedia setelah penjualan: </span>
                        <span id="stok_tersedia" class="fw-bold text-primary">0</span> // Visual stok sisa
                        <span class="text-muted"> kg</span>
                    </div>

                    <div class="invalid-feedback" id="stok_error" style="display: none;"> // Pesan error jika stok tidak cukup
                        Jumlah melebihi stok tersedia!
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Total Harga</label> // Input total harga otomatis
                    <input
                        type="number"
                        name="total_harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="total_harga"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Metode Bayar</label> // Pilihan metode bayar
                    <select name="metode_bayar" class="form-select" id="metode_bayar" required>
                        <option value="" selected disabled>-- Pilih Metode --</option>
                        <option value="midtrans">Midtrans</option> // Pembayaran online
                        <option value="tunai">Tunai</option> // Pembayaran cash
                    </select>
                </div>
            </div>

            <div class="mt-4 d-flex gap-2"> // Tombol aksi
                <button type="submit" class="btn btn-success">
                    Bayar // Tombol submit
                </button>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-secondary">
                    Batal // Tombol batal
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?> // Menutup section konten

<?= $this->section('js'); ?> // Membuka section JavaScript
<script>
    // Variabel global untuk menyimpan stok awal
    let stokAwal = 0;

    $('#nama_sampah').change(function() { // Event ketika dropdown sampah berubah
        let sampahId = $(this).val(); // Ambil ID sampah

        if (sampahId) {
            $.ajax({
                url: "<?= base_url('penjualan/sampah-ajax') ?>", // Endpoint AJAX untuk ambil harga & stok
                type: "POST",
                data: { id: sampahId },
                success: function(response) { // Jika berhasil
                    $('#harga').val(response.harga_jual); // Set harga jual otomatis

                    stokAwal = parseFloat(response.stok_tersedia) || 0; // Simpan stok awal
                    $('#stok_tersedia').text(stokAwal); // Tampilkan stok
                    $('#stok_info').show(); // Tampilkan info stok

                    // Reset input dan validasi
                    $('#jumlah_jual').removeClass('is-invalid');
                    $('#stok_error').hide();
                    $('#jumlah_jual').val('');
                    $('#total_harga').val('');

                    $('#stok_tersedia').removeClass('text-danger').addClass('text-primary'); // Reset warna stok
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat mengambil data harga.');
                }
            });
        } else {
            // Reset jika tidak memilih sampah
            $('#harga').val('');
            $('#jumlah_jual').val('');
            $('#total_harga').val('');
            $('#stok_info').hide();
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            stokAwal = 0;
        }
    });

    function jumlah() { // Fungsi untuk hitung total harga & validasi stok
        let harga = parseFloat($('#harga').val()) || 0; // Ambil harga
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0; // Ambil jumlah jual

        let totalHarga = harga * jumlahJual; // Hitung total harga
        $('#total_harga').val(totalHarga); // Set total harga

        let stokSetelahJual = stokAwal - jumlahJual; // Hitung sisa stok
        $('#stok_tersedia').text(stokSetelahJual); // Update UI stok

        // Validasi stok
        if (jumlahJual > stokAwal) {
            $('#jumlah_jual').addClass('is-invalid'); // Kasih warna merah input
            $('#stok_error').show(); // Tampilkan pesan error
            $('#stok_tersedia').removeClass('text-primary').addClass('text-danger'); // Warna merah di stok
        } else if (jumlahJual > 0) {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-primary').addClass('text-warning'); // Warna kuning jika masih aman
        } else {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-warning text-danger').addClass('text-primary'); // Reset ke biru
        }
    }

    // Validasi sebelum submit form
    $('#form_penjualan').on('submit', function(e) {
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0;
        let metodeBayar = $('#metode_bayar').val();

        if (jumlahJual > stokAwal) { // Jika stok tidak cukup
            e.preventDefault();
            alert(
                'Jumlah yang dijual (' + jumlahJual +
                ' kg) melebihi stok tersedia (' + stokAwal +
                ' kg). Silakan periksa kembali.'
            );
            return false;
        }

        // Jika metode midtrans → proses pembayaran online
        if (metodeBayar === 'midtrans') {
            e.preventDefault(); // Stop submit default

            let submitBtn = $(this).find('button[type="submit"]'); // Tombol submit
            let originalText = submitBtn.html(); // Simpan teks asli
            submitBtn.html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...').prop('disabled', true); // Loading

            let formData = new FormData(this); // Ambil semua input form

            $.ajax({
                url: $(this).attr('action'), // Endpoint penjualan/store
                type: 'POST',
                data: formData,
                processData: false, // Untuk FormData harus false
                contentType: false,
                success: function(response) {
                    try {
                        let data = typeof response === 'string' ? JSON.parse(response) : response; // Parse JSON

                        if (data.success && data.token) { // Jika token midtrans diterima
                            submitBtn.html(originalText).prop('disabled', false); // Kembalikan tombol

                            if (typeof snap === 'undefined') { // Jika Snap belum ada di halaman
                                let script = document.createElement('script');

                                <?php
                                $midtransConfig = config('Midtrans');
                                $snapUrl = $midtransConfig->isProduction
                                    ? 'https://app.midtrans.com/snap/snap.js'
                                    : 'https://app.sandbox.midtrans.com/snap/snap.js';
                                ?>

                                script.src = '<?= $snapUrl ?>'; // Load script Snap
                                script.setAttribute('data-client-key', '<?= $midtransConfig->clientKey ?? "" ?>');
                                document.body.appendChild(script);

                                script.onload = function() {
                                    snap.pay(data.token, { // Jalankan popup Snap
                                        onSuccess: function(result) {
                                            window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                        },
                                        onPending: function(result) {
                                            window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                        },
                                        onError: function() {
                                            window.location.href = '<?= base_url('penjualan/midtrans-error') ?>';
                                        },
                                        onClose: function() {
                                            console.log('Popup pembayaran ditutup.');
                                        }
                                    });
                                };
                            } else {
                                snap.pay(data.token, { // Jika Snap sudah ada
                                    onSuccess: function(result) {
                                        window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                    },
                                    onPending: function(result) {
                                        window.location.href = '<?= base_url('penjualan/midtrans-finish') ?>?order_id=' + result.order_id;
                                    },
                                    onError: function() {
                                        window.location.href = '<?= base_url('penjualan/midtrans-error') ?>';
                                    },
                                    onClose: function() {
                                        console.log('Popup pembayaran ditutup.');
                                    }
                                });
                            }
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat membuat transaksi Midtrans.');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        alert('Terjadi kesalahan saat memproses pembayaran.');
                        submitBtn.html(originalText).prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert('Terjadi kesalahan saat menyimpan data.');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?> // Menutup section JS
