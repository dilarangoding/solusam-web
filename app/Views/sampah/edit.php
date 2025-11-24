<?= $this->extend('template/index'); ?> // Memanggil template utama 'index'
<?= $this->section('content'); ?> // Membuka section 'content' untuk isi halaman

<div class="card border-success mb-4 mt-4"> // Membuat card dengan border berwarna hijau
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau transparan
        + Form Input Pembelian // Judul form
    </div>
    <div class="card-body"> // Bagian dalam card
        <form action="<?= base_url('sampah/store') ?>" method="POST"> // Form submit menggunakan POST menuju route sampah/store (untuk update dan insert)
            <div class="row g-3"> // Grid layout dengan jarak antar elemen 3

                <input
                    type="hidden" // Field tersembunyi untuk membawa ID saat update
                    name="id" // Nama field ID
                    class="form-control" // Class bootstrap
                    required // Wajib ada (untuk memastikan update)
                    value="<?= $data['id'] ?>"> // Nilai ID diambil dari data yang dikirim controller

                <!-- Nama Sampah -->
                <div class="col-md-3"> // Kolom lebar 3/12
                    <label class="form-label">Nama Sampah</label> // Label nama sampah
                    <input
                        type="text" // Input teks
                        name="nama_sampah" // Nama field
                        class="form-control" // Styling
                        required // Wajib diisi
                        value="<?= $data['nama_sampah'] ?>"> // Pre-filled data nama sampah
                </div>

                <!-- Harga Beli -->
                <div class="col-md-3"> // Kolom lebar 3/12
                    <label class="form-label">Harga Beli (Rp)</label> // Label harga beli
                    <input
                        type="number" // Input angka
                        name="harga_beli" // Nama field harga beli
                        min="0" // Minimal nilai 0
                        placeholder="0" // Placeholder default
                        class="form-control" // Styling bootstrap
                        required value="<?= $data['harga_beli'] ?>"> // Pre-filled data harga beli
                </div>

                <!-- Harga Jual -->
                <div class="col-md-3"> // Kolom 3
                    <label class="form-label">Harga Jual (Rp)</label> // Label harga jual
                    <input
                        type="number" // Input angka
                        name="harga_jual" // Nama field harga jual
                        min="0" // Minimal angka 0
                        placeholder="0" // Placeholder default
                        class="form-control" // Styling
                        required value="<?= $data['harga_jual'] ?>"> // Pre-filled data harga jual
                </div>

                <!-- Satuan -->
                <div class="col-md-3"> // Kolom 3
                    <label class="form-label">Satuan (kg)</label> // Label satuan
                    <input
                        type="number" // Input angka
                        min="1" // Minimal angka 1
                        name="satuan" // Nama field satuan
                        class="form-control" // Styling bootstrap
                        required value="<?= $data['satuan'] ?>"> // Pre-filled data satuan
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> // Wrapper tombol dengan jarak antar tombol 2
                <button type="submit" class="btn btn-success"> // Tombol submit update
                    Update // Teks tombol
                </button>
                <a href="<?= base_url('sampah') ?>" class="btn btn-secondary"> // Tombol batal kembali ke halaman index
                    Batal // Teks tombol
                </a>
            </div>
        </form>
    </div>
</div>
<?= $this->endSection(); ?> // Menutup section content
