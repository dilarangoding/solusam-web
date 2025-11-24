<?= $this->extend('template/index'); ?> # Memanggil template utama 'template/index'
<?= $this->section('content'); ?> # Membuka section 'content' untuk diisi konten halaman ini



<div class="card border-success mb-4 mt-4"> # Card Bootstrap dengan border hijau dan margin
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> # Header card dengan warna hijau lembut
        + Form <?= $title; ?> # Menampilkan judul form sesuai variabel $title
    </div>
    <div class="card-body"> # Bagian isi card
        <form action="<?= base_url('data-klien/store') ?>" method="POST"> # Form submit ke route 'data-klien/store' dengan method POST
            <div class="row g-3"> # Grid Bootstrap dengan jarak antar elemen
                <div class="row g-3"> # Row tambahan untuk layout form
                    <!-- Nama Lengkap -->
                    <div class="col-md-6"> # Kolom 6 grid untuk input nama
                        <label class="form-label">Nama Lengkap</label> # Label field
                        <input type="text" name="nama_lengkap" class="form-control"> # Input nama lengkap
                    </div>

                    <!-- No Telp -->
                    <div class="col-md-6"> # Kolom 6 grid untuk no telp
                        <label class="form-label">No Telp</label> # Label field
                        <input type="text" name="no_telp" class="form-control"> # Input nomor telepon
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12"> # Kolom full width
                        <label class="form-label">Alamat</label> # Label field
                        <input type="text" name="alamat" class="form-control"> # Input alamat lengkap
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="col-md-12"> # Kolom full width
                        <label class="form-label">Jenis Usaha</label> # Label field
                        <input type="text" name="jenis_usaha" class="form-control"> # Input jenis usaha
                    </div>
                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2"> # Row untuk tombol dengan gap dan margin top
                <button type="submit" class="btn btn-success"> # Tombol submit (warna hijau)
                    Simpan
                </button>
                <a href="<?= base_url('data-klien') ?>" class="btn btn-secondary"> # Tombol batal kembali ke halaman data-klien
                    Batal
                </a>
            </div>
        </form> # Penutup form
    </div> # Penutup card-body
</div> # Penutup card utama


<?= $this->endSection(); ?> # Menutup section 'content'
