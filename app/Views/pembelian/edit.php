<?= $this->extend('template/index'); ?> // Menggunakan template utama dari folder template/index
<?= $this->section('content'); ?> // Membuka section khusus konten halaman



<div class="card border-success mb-4 mt-4"> // Card utama untuk tampilan form edit pembelian
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan warna hijau tipis
        + Form <?= $title; ?> // Menampilkan judul form dinamis dari variabel $title
    </div>
    <div class="card-body"> // Bagian isi card
        <form action="<?= base_url('pembelian/store') ?>" method="POST"> // Form submit ke route pembelian/store
            <div class="row g-3"> // Grid layout dengan jarak antar kolom
                
                <input
                    type="hidden" // Input tersembunyi untuk ID
                    name="id"
                    class="form-control"
                    required
                    value="<?= $data['id'] ?>"> // Mengisi ID pembelian yang sedang diedit

                <div class="col-md-4"> // Kolom input tanggal
                    <label class="form-label">Pilih Tanggal</label>
                    <input
                        type="date" // Input tanggal
                        name="tanggal"
                        class="form-control"
                        value="<?= $data['tanggal'] ?>" // Tanggal dari database
                        required>
                </div>

                <div class="col-md-4"> // Kolom dropdown Nama Sampah
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required> // Dropdown pilih sampah
                        <option value="" selected disabled>-- Pilih Sampah --</option>
                        <?php foreach ($sampah as $row) : // Loop data sampah
                            $selected  = $row['id'] == $data['sampah_id'] ? 'selected' : ''; // Menandai pilihan yang sesuai data edit
                        ?>
                            <option value="<?= $row['id'] ?>" <?= $selected ?>> // Jika matching, beri selected
                                <?= $row['nama_sampah'] ?> // Menampilkan nama sampah
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <div class="col-md-4"> // Kolom input harga
                    <label class="form-label">Harga</label>
                    <input
                        type="number"
                        name="harga"
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="harga" // ID digunakan untuk AJAX
                        readonly // Tidak bisa diketik manual
                        required>
                </div>


                <div class="col-md-4"> // Kolom jumlah jual
                    <label class="form-label">Jumlah</label>
                    <input
                        type="number"
                        min="1"
                        name="jumlah_jual" // Nama field
                        class="form-control"
                        id="jumlah_jual"
                        value="<?= $data['jumlah'] ?>" // Pre-fill jumlah
                        onkeyup="jumlah()" // Hitung total saat diketik
                        required>
                </div>

                <div class="col-md-4"> // Kolom total harga
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

                <div class="col-md-4"> // Kolom dropdown klien
                    <label class="form-label">Klien</label>
                    <select name="pembeli" class="form-select" id="pembeli" required> // Dropdown klien
                        <option value="" selected disabled>-- Pilih Pembeli --</option>
                        <?php foreach ($klien as $row) : // Loop data klien
                            $selected  = $row['id'] == $data['pembeli'] ? 'selected' : ''; // Menandai klien yang sesuai pada data edit
                        ?>
                            <option value="<?= $row['id'] ?>" <?= $selected ?>> // Pilihan yang cocok otomatis terpilih
                                <?= $row['nama_lengkap'] ?> // Menampilkan nama klien
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> // Layout tombol
                <button type="submit" class="btn btn-success">
                    Update // Tombol update data
                </button>
                <a href="<?= base_url('penjualan') ?>" class="btn btn-secondary">
                    Batal // Tombol kembali tanpa menyimpan
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?> // Menutup section konten

<?= $this->section('js'); ?> // Bagian khusus js
<script>
    let idsampah = '<?= $data['sampah_id'] ?>'; // Menyimpan ID sampah awal yang sedang diedit
    hargaSampah(idsampah); // Memanggil fungsi untuk load harga saat pertama kali

    $('#nama_sampah').change(function() { // Ketika dropdown sampah berubah
        let id = $(this).val(); // Ambil ID baru
        hargaSampah(id); // Update harga
    });

    function hargaSampah(id) { // Fungsi mengambil harga berdasarkan ID sampah
        $.ajax({
            url: "<?= base_url('pembelian/sampah-ajax') ?>", // URL AJAX untuk ambil harga
            type: "POST",
            data: {
                id // Data yang dikirim: id sampah
            },
            success: function(response) { // Jika berhasil
                $('#harga').val(response.harga_beli); // Isi input harga
                jumlah(); // Re-kalkulasi total
            },
            error: function(xhr, status, error) { // Jika error
                console.error(error); // Log error
                alert('Terjadi kesalahan saat mengambil data harga.'); // Alert error
            }
        });
    }

    function jumlah() { // Fungsi hitung total harga
        let harga = parseFloat($('#harga').val()) || 0; // Ambil harga, default 0 jika kosong
        let jumlahJual = parseFloat($('#jumlah_jual').val()) || 0; // Ambil jumlah, default 0

        let totalHarga = harga * jumlahJual; // Rumus total harga

        $('#total_harga').val(totalHarga); // Set hasil total harga
    }
</script>
<?= $this->endSection(); ?> // Menutup section js
