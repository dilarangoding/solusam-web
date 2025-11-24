<?= $this->extend('template/index'); ?>
<?php // Memanggil template utama 'index' ?>

<?= $this->section('content'); ?>
<?php // Membuka section 'content' untuk konten halaman ini ?>



<div class="card border-success mb-4 mt-4">
    <!-- Card container dengan border hijau dan margin -->

    <div class="card-header bg-success bg-opacity-10 text-success fw-bold">
        <!-- Header card dengan background hijau transparan -->
        + Form <?= $title; ?>
        <?php // Menampilkan judul form berdasarkan variabel $title ?>
    </div>

    <div class="card-body">
        <!-- Isi dari card -->

        <form action="<?= base_url('metode-bayar/store') ?>" method="POST">
            <!-- Form dikirim ke route 'metode-bayar/store' dengan metode POST -->

            <div class="row g-3">
                <!-- Row utama dengan jarak antar elemen -->

                <div class="col-md-12">
                    <!-- Kolom lebar penuh -->

                    <label class="form-label">Metode</label>
                    <!-- Label untuk input nama metode bayar -->

                    <input
                        type="text"
                        name="nama"
                        class="form-control"
                        required>
                    <!-- Input teks untuk nama metode pembayaran -->
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-4 d-flex gap-2">
                    <!-- Flexbox untuk tombol dengan jarak antar elemen -->

                    <button type="submit" class="btn btn-success">
                        <!-- Tombol submit berwarna hijau -->
                        Simpan
                    </button>

                    <a href="<?= base_url('metode-bayar') ?>" class="btn btn-secondary">
                        <!-- Tombol batal untuk kembali ke halaman daftar metode bayar -->
                        Batal
                    </a>
                </div>

            </div>
        </form>
        <!-- Penutup form -->
    </div>
</div>


<?= $this->endSection(); ?>
<?php // Menutup section 'content' ?>
