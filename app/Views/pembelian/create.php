<?= $this->extend('template/index'); ?> // Menggunakan template utama "index"
<?= $this->section('content'); ?> // Membuka section konten utama halaman



<div class="card border-success mb-4 mt-4"> // Card utama dengan border hijau dan margin
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau transparan
        + Form <?= $title; ?> // Judul form sesuai variabel $title
    </div>
    <div class="card-body"> // Isi card
        <form action="<?= base_url('pembelian/store') ?>" method="POST"> // Form submit ke route pembelian/store dengan metode POST
            <div class="row g-3"> // Grid dengan jarak antar elemen
                <div class="col-md-4"> // Kolom input tanggal
                    <label class="form-label">Pilih Tanggal</label> // Label input
                    <input
                        type="date" // Input tanggal
                        name="tanggal" // Name untuk kirim data
                        class="form-control" // Style bootstrap
                        required> // Wajib diisi
                </div>

                <div class="col-md-4"> // Kolom select nama sampah
                    <label class="form-label">Nama Sampah</label>
                    <select name="nama_sampah" class="form-select" id="nama_sampah" required> // Dropdown pilih sampah
                        <option value="" selected disabled>-- Pilih Sampah --</option> // Placeholder pilihan
                        <?php foreach ($sampah as $row) : ?> // Loop tampilkan semua data sampah
                            <option value="<?= $row['id'] ?>"> // ID sampah sebagai value
                                <?= $row['nama_sampah'] ?> // Nama sampah yang ditampilkan
                            </option>
                        <?php endforeach; ?> // Penutup foreach
                    </select>
                </div>


                <div class="col-md-4"> // Kolom harga
                    <label class="form-label">Harga</label>
                    <input
                        type="number" // Input angka
                        name="harga" // Nama field
                        min="0" // Minimal 0
                        placeholder="0" // Placeholder
                        class="form-control"
                        id="harga" // ID digunakan JS
                        readonly // Harga tidak bisa diinput manual
                        required> // Wajib
                </div>


                <div class="col-md-4"> // Kolom jumlah beli
                    <label class="form-label">Jumlah</label>
                    <input
                        type="number" // Input angka
                        min="1" // Minimal jumlah 1
                        name="jumlah_beli" // Nama field
                        class="form-control"
                        id="jumlah_beli" // ID digunakan untuk kalkulasi otomatis
                        onkeyup="jumlah()" // Memanggil fungsi kalkulasi ketika diketik
                        required> // Wajib diisi
                </div>

                <div class="col-md-4"> // Kolom total harga
                    <label class="form-label">Total Harga</label>
                    <input
                        type="number"
                        name="total_harga" // Nama field
                        min="0"
                        placeholder="0"
                        class="form-control"
                        id="total_harga" // Diisi otomatis melalui JS
                        required>
                </div>

                <div class="col-md-4"> // Kolom pilih klien
                    <label class="form-label">Klien</label>
                    <select name="pembeli" class="form-select" id="pembeli" required> // Dropdown pembeli
                        <option value="" selected disabled>-- Pilih Pembeli --</option>
                        <?php foreach ($klien as $row) : ?> // Loop data klien
                            <option value="<?= $row['id'] ?>"> // ID klien
                                <?= $row['nama_lengkap'] ?> // Nama lengkap ditampilkan
                            </option>
                        <?php endforeach; ?> // Penutup foreach
                    </select>
                </div>

                
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> // Wrapper tombol dengan jarak antar tombol
                <button type="submit" class="btn btn-success">
                    Simpan // Tombol simpan data
                </button>
                <a href="<?= base_url('sampah') ?>" class="btn btn-secondary"> // Tombol batal kembali ke halaman sampah
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?> // Menutup section content

<?= $this->section('js'); ?> // Section khusus untuk script JS
<script>
    $('#nama_sampah').change(function() { // Ketika pilihan sampah berubah
        let sampahId = $(this).val(); // Ambil ID sampah terpilih

        $.ajax({
            url: "<?= base_url('penjualan/sampah-ajax') ?>", // Route untuk ambil harga sampah
            type: "POST", // Metode POST
            data: {
                id: sampahId // Mengirim ID sampah
            },
            success: function(response) { // Jika request berhasil
                $('#harga').val(response.harga_beli); // Isi input harga dengan harga beli yang diterima
            },
            error: function(xhr, status, error) { // Jika request gagal
                console.error(error); // Tampilkan error di console
                alert('Terjadi kesalahan saat mengambil data harga.'); // Notifikasi error
            }
        });
    });

    function jumlah() { // Fungsi menghitung total harga
        let harga = parseFloat($('#harga').val()) || 0; // Ambil harga, default 0 jika kosong
        let jumlahJual = parseFloat($('#jumlah_beli').val()) || 0; // Ambil jumlah beli, default 0

        let totalHarga = harga * jumlahJual; // Rumus total

        $('#total_harga').val(totalHarga); // Tampilkan total harga ke input
    }
</script>
<?= $this->endSection(); ?> // Menutup section JS
