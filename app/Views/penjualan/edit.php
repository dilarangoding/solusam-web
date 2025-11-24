<?= $this->extend('template/index'); ?> // Memanggil template utama
<?= $this->section('content'); ?> // Membuka section konten halaman



<div class="card border-success mb-4 mt-4"> // Card pembungkus form dengan border hijau
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau transparan
        + Form <?= $title; ?> // Menampilkan judul form secara dinamis
    </div>
    <div class="card-body"> // Bagian isi card
        <form action="<?= base_url('penjualan/store') ?>" method="POST"> // Form submit ke route penjualan/store (POST)
            <div class="row g-3"> // Grid spacing antar elemen form
                <input
                    type="hidden" // Input hidden untuk menyimpan ID (edit)
                    name="id"
                    class="form-control"
                    required // Wajib diisi
                    value="<?= $data['id'] ?>"> // Nilai ID dari dataset yang sedang diedit

                <div class="col-md-4"> // Kolom untuk tanggal
                    <label class="form-label">Pilih Tanggal</label> // Label field tanggal
                    <input
                        type="date" // Input date
                        name="tanggal"
                        class="form-control"
                        value="<?= $data['tanggal'] ?>" // Nilai tanggal dari data edit
                        required> // Wajib diisi
                </div>

                <div class="col-md-4"> // Kolom nama sampah
                    <label class="form-label">Nama Sampah</label> // Label field
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required> // Dropdown sampah
                        <option value="" selected disabled>-- Pilih Sampah --</option> // Placeholder option
                        <?php foreach ($sampah as $row) : // Loop data sampah
                            $selected  = $row['id'] == $data['sampah_id'] ? 'selected' : ''; // Cek apakah data yang sedang diedit
                        ?>
                            <option value="<?= $row['id'] ?>" <?= $selected ?>> // Isi value ID dan tentukan selected
                                <?= $row['nama_sampah'] ?> // Tampilkan nama sampah
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-md-4"> // Kolom harga
                    <label class="form-label">Harga</label> // Label harga
                    <input
                        type="number" // Input angka
                        name="harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="harga" // ID untuk JS
                        readonly // Tidak bisa diubah manual
                        required>
                </div>


                <div class="col-md-4"> // Kolom jumlah jual
                    <label class="form-label">Jumlah (kg)</label> // Label field
                    <input
                        type="number" // Input angka
                        min="1" // Minimal 1
                        name="jumlah_jual"
                        class="form-control"
                        id="jumlah_jual"
                        value="<?= $data['jumlah'] ?>" // Nilai awal dari database
                        onkeyup="jumlah()" // Jalankan fungsi hitung total saat user mengetik
                        required>
                </div>

                <div class="col-md-4"> // Kolom total harga
                    <label class="form-label">Total Harga</label> // Label
                    <input
                        type="number" // Input angka
                        name="total_harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="total_harga" // ID digunakan JS
                        required>
                </div>

                <div class="col-md-4"> // Kolom metode bayar
                    <label class="form-label">Metode Bayar</label> // Label
                    <select name="metode_bayar" class="form-select" id="metode_bayar" required> // Dropdown metode pembayaran
                        <option value="" selected disabled>-- Pilih Metode --</option> // Placeholder
                        <option value="qris">QRIS</option> // Opsi QRIS
                        <option value="tunai">Tunai</option> // Opsi tunai
                    </select>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> // Container tombol aksi
                <button type="submit" class="btn btn-success"> // Tombol submit update
                    Update
                </button>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-secondary"> // Tombol batal kembali ke halaman penjualan
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?> // Menutup section konten

<?= $this->section('js'); ?> // Membuka section JavaScript
<script>
    let idsampah = '<?= $data['sampah_id'] ?>'; // Ambil ID sampah yang sedang diedit
    hargaSampah(idsampah); // Auto load harga saat halaman dibuka


    $('#nama_sampah').change(function() { // Event ketika dropdown sampah berubah
        let id = $(this).val(); // Ambil ID sampah

        hargaSampah(id); // Panggil function ambil harga
    });

    function hargaSampah(id) { // Function AJAX ambil harga jual sampah
        $.ajax({
            url: "<?= base_url('penjualan/sampah-ajax') ?>", // Route untuk ambil data harga
            type: "POST", // Method POST
            data: {
                id // Kirim ID sampah
            },
            success: function(response) { // Jika berhasil
                $('#harga').val(response.harga_jual); // Set nilai harga jual
                jumlah(); // Hitung ulang total
            },
            error: function(xhr, status, error) { // Jika error AJAX
                console.error(error); // Log error
                alert('Terjadi kesalahan saat mengambil data harga.'); // Tampilkan alert error
            }
        });
    }

    function jumlah() { // Function hitung total harga
        let harga = parseFloat($('#harga').val()) || 0; // Ambil harga, default 0
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0; // Ambil jumlah, default 0

        let totalHarga = harga * jumlahJual; // Rumus total

        $('#total_harga').val(totalHarga); // Set hasil total ke input
    }
</script>
<?= $this->endSection(); ?> // Menutup section JS
