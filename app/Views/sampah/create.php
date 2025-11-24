<?= $this->extend('template/index'); ?> // Menggunakan template utama 'index' dari folder template
<?= $this->section('content'); ?> // Membuka blok section bernama 'content' untuk menempatkan isi halaman



<div class="card border-success mb-4 mt-4"> // Membuat card dengan border hijau dan margin atas-bawah
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau transparan dan teks tebal
        + Form Input Pembelian // Judul pada header card
    </div>
    <div class="card-body"> // Area isi card
        <form action="<?= base_url('sampah/store') ?>" method="POST"> // Form HTTP POST menuju route sampah/store
            <div class="row g-3"> // Membuat grid dengan jarak antar elemen 3

                <!-- Nama Sampah -->
                <div class="col-md-3"> // Kolom dengan lebar 3 pada ukuran layar medium
                    <label class="form-label">Nama Sampah</label> // Label input
                    <input
                        type="text" // Input teks
                        name="nama_sampah" // Nama field dikirim ke server
                        class="form-control" // Styling bootstrap
                        required> // Wajib diisi
                </div>

                <!-- Harga Beli -->
                <div class="col-md-3"> // Kolom 3
                    <label class="form-label">Harga Beli (Rp)</label> // Label harga beli
                    <input
                        type="number" // Input angka
                        name="harga_beli" // Field harga beli
                        min="0" // Minimal angka adalah 0
                        placeholder="0" // Placeholder default
                        class="form-control" // Bootstrap form style
                        required> // Wajib diisi
                </div>

                <!-- Harga Jual -->
                <div class="col-md-3"> // Kolom 3
                    <label class="form-label">Harga Jual (Rp)</label> // Label harga jual
                    <input
                        type="number" // Input angka
                        name="harga_jual" // Field harga jual
                        min="0" // Minimal angka 0
                        placeholder="0" // Placeholder
                        class="form-control" // Styling
                        required> // Harus diisi
                </div>

                <!-- Satuan -->
                <div class="col-md-3"> // Kolom 3
                    <label class="form-label">Satuan (kg)</label> // Label satuan
                    <input
                        type="number" // Input angka
                        name="satuan" // Field satuan
                        class="form-control" // Styling
                        min="1" // Minimal angka 1
                        required> // Wajib
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> // Wrapper tombol dengan margin atas & jarak antar tombol
                <button type="submit" class="btn btn-success"> // Tombol submit
                    Simpan // Teks tombol
                </button>
                <a href="<?= base_url('sampah') ?>" class="btn btn-secondary"> // Tombol batal menuju halaman index sampah
                    Batal // Teks tombol
                </a>
            </div>
        </form>
    </div>
</div>


<?= $this->endSection(); ?> // Menutup section 'content'
