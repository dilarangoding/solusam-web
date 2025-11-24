<?= $this->extend('template/index'); ?> 
<?php // Memanggil template utama 'index' dari folder template ?>

<?= $this->section('content'); ?> 
<?php // Membuka section 'content' untuk diisi konten halaman ini ?>



<div class="card border-success mb-4 mt-4"> 
    <!-- Card container dengan border hijau dan margin atas/bawah -->
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        <!-- Header card dengan background hijau transparan -->
        + Form <?= $title; ?> 
        <?php // Menampilkan judul form berdasarkan variabel $title ?>
    </div>

    <div class="card-body">
        <!-- Bagian isi card -->
        <form action="<?= base_url('data-klien/store') ?>" method="POST">
            <?php // Form dikirim ke route 'data-klien/store' menggunakan metode POST ?>
            
            <div class="row g-3">
                <!-- Row pertama dengan gap antar elemen -->
                <div class="row g-3">
                    <!-- Nested row untuk input form -->

                    <!-- Nama Lengkap -->
                    <div class="col-md-6">
                        <!-- Kolom lebar 6 pada layar medium ke atas -->
                        <label class="form-label">Nama Lengkap</label>
                        <!-- Label input -->
                        <input type="text" name="nama_lengkap" class="form-control">
                        <!-- Input teks untuk nama lengkap -->
                    </div>

                    <!-- No Telp -->
                    <div class="col-md-6">
                        <!-- Kolom lebar 6 -->
                        <label class="form-label">No Telp</label>
                        <!-- Label input -->
                        <input type="text" name="no_telp" class="form-control">
                        <!-- Input teks untuk nomor telepon -->
                    </div>

                    <!-- Alamat -->
                    <div class="col-md-12">
                        <!-- Kolom full width -->
                        <label class="form-label">Alamat</label>
                        <!-- Label input -->
                        <input type="text" name="alamat" class="form-control">
                        <!-- Input teks untuk alamat -->
                    </div>

                    <!-- Jenis Usaha -->
                    <div class="col-md-12">
                        <!-- Kolom penuh -->
                        <label class="form-label">Jenis Usaha</label>
                        <!-- Label input -->
                        <input type="text" name="jenis_usaha" class="form-control">
                        <!-- Input teks untuk jenis usaha -->
                    </div>

                </div>
            </div>

            <!-- Tombol Aksi -->
            <div class="mt-4 d-flex gap-2">
                <!-- Margin atas + flexbox dengan jarak antar tombol -->
                <button type="submit" class="btn btn-success">
                    <!-- Tombol submit berwarna hijau -->
                    Simpan
                </button>

                <a href="<?= base_url('data-klien') ?>" class="btn btn-secondary">
                    <!-- Tombol batal, kembali ke halaman data klien -->
                    Batal
                </a>
            </div>

        </form>
        <!-- Penutup form -->
    </div>
</div>


<?= $this->endSection(); ?> 
<?php // Menutup section 'content' ?>

