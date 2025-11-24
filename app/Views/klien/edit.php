<?= $this->extend('template/index'); ?> # Menggunakan template utama 'template/index'
<?= $this->section('content'); ?> # Membuka section 'content' untuk diisi konten halaman ini

<div class="card border-success mb-4 mt-4"> # Card Bootstrap dengan border hijau dan margin
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> # Header card dengan warna hijau lembut
        + Form Edit Klien # Judul form edit data klien
    </div>
    <div class="card-body"> # Bagian isi card
        <form action="<?= base_url('data-klien/store') ?>" method="POST"> # Form submit ke route 'data-klien/store' dengan method POST (dipakai juga untuk edit)
            <div class="row g-3"> # Grid layout dengan gap antar elemen
                <input
                    type="hidden" # Input tidak ditampilkan
                    name="id" # Field untuk ID klien yang akan diupdate
                    class="form-control"
                    required # ID harus ada
                    value="<?= $data['id'] ?>"> # Mengisi ID klien dari data yang dikirim ke view
                <div class="row g-3"> # Row baru untuk field-field form
                    <!-- Nama Lengkap -->
                    <div class="col-md-6"> # Kolom 6 grid
                        <label class="form-label">Nama Lengkap</label> # Label field
                        <input type="text" name="nama_lengkap" class="form-control" required value="<?= $data['nama_lengkap'] ?>"> # Input dengan nilai lama
                    </div>

                    <!-- No Telp -->
                    <div class="col-md-6"> # Kolom 6 grid
                        <label class="form-label">No Telp</label> # Label field
                        <input type="text" name="no_telp" class="form-control" required value="<?= $data['no_telp'] ?>"> # Input berisi no telp lama
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12"> # Kolom full width
                        <label class="form-label">Alamat</label> # Label field
                        <input type="text" name="alamat" class="form-control" required value="<?= $data['alamat'] ?>"> # Input alamat lama
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="col-md-12"> # Kolom full width
                        <label class="form-label">Jenis Usaha</label> # Label field
                        <input type="text" name="jenis_usaha" class="form-control" required value="<?= $data['jenis_usaha'] ?>"> # Input jenis usaha lama
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> # Container fleksibel untuk tombol dengan jarak
                <button type="submit" class="btn btn-success"> # Tombol submit update data
                    Update
                </button>
                <a href="<?= base_url('data-klien') ?>" class="btn btn-secondary"> # Tombol batal kembali ke daftar klien
                    Batal
                </a>
            </div>
        </form> # Penutup form
    </div> # Penutup card-body
</div> # Penutup card utama

<?= $this->endSection(); ?> # Menutup section 'content'
