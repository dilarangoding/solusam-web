<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>



<div class="card border-success mb-4 mt-4">
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        + Form <?= $title; ?>
    </div>
    <div class="card-body">
        <form action="<?= base_url('penjualan/store') ?>" method="POST" enctype="multipart/form-data" id="form_penjualan">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pilih Tanggal</label>
                    <input
                        type="date"
                        name="tanggal"
                        class="form-control"
                        required>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required>
                        <option value="" selected disabled>-- Pilih Sampah --</option>
                        <?php foreach ($sampah as $row) : ?>
                            <option value="<?= $row['id'] ?>">
                                <?= $row['nama_sampah'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-md-4">
                    <label class="form-label">Harga</label>
                    <input
                        type="number"
                        name="harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="harga"
                        readonly
                        required>
                </div>


                <div class="col-md-4">
                    <label class="form-label">Jumlah (kg)</label>
                    <input
                        type="number"
                        min="1"
                        name="jumlah_jual"
                        class="form-control"
                        id="jumlah_jual"
                        onkeyup="jumlah()"
                        required>
                    <div class="form-text" id="stok_info" style="display: none;">
                        <span class="text-muted">Stok tersedia setelah penjualan: </span>
                        <span id="stok_tersedia" class="fw-bold text-primary">0</span>
                        <span class="text-muted"> kg</span>
                    </div>
                    <div class="invalid-feedback" id="stok_error" style="display: none;">
                        Jumlah melebihi stok tersedia!
                    </div>
                </div>



                <div class="col-md-4">
                    <label class="form-label">Total Harga</label>
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
                    <label class="form-label">Metode Bayar</label>
                    <select name="metode_bayar" class="form-select" id="metode_bayar" required>
                        <option value="" selected disabled>-- Pilih Metode --</option>
                        <option value="qris">QRIS</option>
                        <option value="tunai">Tunai</option>
                    </select>
                </div>

                <div class="col-md-4" id="upload_qris" style="display: none;">
                    <label for="bukti_qris" class="form-label">Upload Bukti QRIS</label>
                    <input type="file" name="bukti_qris" id="bukti_qris" class="form-control">
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <button type="submit" class="btn btn-success">
                    Simpan
                </button>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-secondary">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script>
    // Variabel global untuk menyimpan stok awal
    let stokAwal = 0;

    $('#nama_sampah').change(function() {
        let sampahId = $(this).val();

        if (sampahId) {
            $.ajax({
                url: "<?= base_url('penjualan/sampah-ajax') ?>",
                type: "POST",
                data: {
                    id: sampahId
                },
                success: function(response) {
                    $('#harga').val(response.harga_jual);

                    // Simpan stok awal dan tampilkan informasi stok
                    stokAwal = parseFloat(response.stok_tersedia) || 0;
                    $('#stok_tersedia').text(stokAwal);
                    $('#stok_info').show();

                    // Reset validasi stok
                    $('#jumlah_jual').removeClass('is-invalid');
                    $('#stok_error').hide();
                    $('#jumlah_jual').val('');
                    $('#total_harga').val('');

                    // Reset warna stok
                    $('#stok_tersedia').removeClass('text-danger').addClass('text-primary');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('Terjadi kesalahan saat mengambil data harga.');
                }
            });
        } else {
            // Reset semua field jika tidak ada sampah yang dipilih
            $('#harga').val('');
            $('#jumlah_jual').val('');
            $('#total_harga').val('');
            $('#stok_info').hide();
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            stokAwal = 0;
        }
    });

    function jumlah() {
        let harga = parseFloat($('#harga').val()) || 0;
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0;

        let totalHarga = harga * jumlahJual;
        $('#total_harga').val(totalHarga);

        // Hitung stok tersedia setelah dikurangi jumlah yang akan dijual
        let stokSetelahJual = stokAwal - jumlahJual;

        // Update tampilan stok tersedia
        $('#stok_tersedia').text(stokSetelahJual);

        // Validasi stok dan ubah warna
        if (jumlahJual > stokAwal) {
            $('#jumlah_jual').addClass('is-invalid');
            $('#stok_error').show();
            $('#stok_tersedia').removeClass('text-primary').addClass('text-danger');
        } else if (jumlahJual > 0) {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-primary').addClass('text-warning');
        } else {
            $('#jumlah_jual').removeClass('is-invalid');
            $('#stok_error').hide();
            $('#stok_tersedia').removeClass('text-warning text-danger').addClass('text-primary');
        }
    }

    // $("#metode_bayar").on("change", function() {
    //     let metode = $(this).find("option:selected").text();
    //     let valMetode = metode.toLowerCase().trim()


    //     if (valMetode == "qris") {
    //         $("#upload_qris").show();
    //     } else {
    //         $("#upload_qris").hide();
    //     }
    // });

    // Validasi form sebelum submit
    $('#form_penjualan').on('submit', function(e) {
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0;
        let metodeBayar = $('#metode_bayar').val();

        if (jumlahJual > stokAwal) {
            e.preventDefault();
            alert('Jumlah yang dijual (' + jumlahJual + ' kg) melebihi stok tersedia (' + stokAwal + ' kg). Silakan periksa kembali.');
            return false;
        }

        // Jika metode pembayaran QRIS, tampilkan loading dan redirect ke QR Code
        if (metodeBayar === 'qris') {
            e.preventDefault();

            // Tampilkan loading
            let submitBtn = $(this).find('button[type="submit"]');
            let originalText = submitBtn.html();
            submitBtn.html('<i class="spinner-border spinner-border-sm me-2"></i>Memproses...').prop('disabled', true);

            // Submit form via AJAX
            let formData = new FormData(this);

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        // Parse JSON response
                        let data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (data.success) {
                            // Redirect ke halaman QR Code
                            window.location.href = data.redirect_url;
                        } else {
                            alert(data.message || 'Terjadi kesalahan saat menyimpan data.');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    } catch (e) {
                        // Jika response bukan JSON, cek apakah berhasil
                        if (response.includes('success') || response.includes('berhasil')) {
                            alert('Data berhasil disimpan! Silakan buka halaman penjualan untuk melihat QR Code.');
                            window.location.href = '<?= base_url('penjualan'); ?>';
                        } else {
                            alert('Terjadi kesalahan saat menyimpan data.');
                            submitBtn.html(originalText).prop('disabled', false);
                        }
                    }
                },
                error: function() {
                    alert('Terjadi kesalahan saat menyimpan data.');
                    submitBtn.html(originalText).prop('disabled', false);
                }
            });
        }
    });
</script>
<?= $this->endSection(); ?>