<?= $this->extend('template/index'); ?> 
<?php // Memanggil template utama "index" ?>

<?= $this->section('content'); ?> 
<?php // Membuka section "content" ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> 
<!-- Judul halaman menggunakan variabel $title -->

<p class="text-muted">Kelola data klien</p> 
<!-- Deskripsi kecil halaman -->

<a href="<?= base_url('data-klien/create') ?>" class="btn btn-success btn-sm mb-3">
    <!-- Tombol menuju halaman tambah data klien -->
    <i class="ti ti-plus"></i> Tambah Data
</a>

<!-- Data Klien -->
<div class="card shadow-sm border-0">
    <!-- Card container untuk tabel klien -->
    <div class="card-body">
        <!-- Body card -->

        <h5 class="card-title mb-3"><?= $title; ?></h5>
        <!-- Judul kecil tabel data -->

        <div class="table-responsive">
            <!-- Membuat tabel responsif -->

            <table class="table table-bordered table-hover align-middle dataTable">
                <!-- Tabel utama dengan border dan hover -->

                <thead class="table-success">
                    <!-- Header tabel berwarna hijau -->
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Lengkap</th>
                        <th scope="col">No Telp</th>
                        <th scope="col">Alamat</th>
                        <th scope="col">Jenis Usaha</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php
                    function formatRupiah($number)
                    {
                        // Fungsi untuk format angka menjadi Rupiah
                        return 'Rp ' . number_format($number, 0, ',', '.');
                    }

                    $no = 1; 
                    // Variabel nomor urut untuk tabel

                    foreach ($data as $row) { 
                        // Looping seluruh data klien
                    ?>
                        <tr>
                            <!-- Baris data tabel -->
                            <td><?= $no++; ?></td> 
                            <!-- Nomor urut -->

                            <td><?= $row['nama_lengkap']; ?></td>
                            <!-- Nama lengkap klien -->

                            <td><?= $row['no_telp']; ?></td>
                            <!-- Nomor telepon klien -->

                            <td><?= $row['alamat']; ?></td>
                            <!-- Alamat klien -->

                            <td><?= $row['jenis_usaha']; ?></td>
                            <!-- Jenis usaha klien -->

                            <td>
                                <!-- Kolom aksi (edit & hapus) -->

                                <a href="<?= base_url('data-klien/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm">
                                    <!-- Tombol edit -->
                                    <i class="ti ti-pencil"></i>
                                </a>

                                <button type="button"
                                    data-nama="<?= $row['nama_lengkap'] ?>" 
                                    // Menyimpan nama klien di atribut data untuk modal konfirmasi

                                    data-id="<?= $row['id'] ?>" 
                                    // Menyimpan ID klien untuk keperluan delete

                                    onclick="hapus(this)" 
                                    // Memanggil fungsi hapus dan mengirim tombol sebagai parameter

                                    class="btn btn-outline-danger btn-sm">
                                    <i class="ti ti-trash"></i> 
                                    <!-- Icon hapus -->
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                    <!-- Penutup foreach -->
                </tbody>

            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> 
<?php // Menutup section "content" ?>


<?= $this->section('js'); ?> 
<?php // Membuka section Javascript ?>


<script>
    function hapus(event) {
        // Fungsi untuk menghapus data menggunakan SweetAlert konfirmasi

        let nama = $(event).data('nama')
        // Mengambil nama dari atribut data-nama pada tombol

        let id = $(event).data('id')
        // Mengambil ID klien dari atribut data-id

        Swal.fire({
            // Menampilkan popup konfirmasi
            title: 'Apakah anda yakin?',
            html: `Ingin menghapus data <strong>${nama}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6', // Warna tombol Yes
            cancelButtonColor: '#d33', // Warna tombol Cancel
            confirmButtonText: 'Yes',
        }).then((result) => {

            if (result.value) {
                // Jika user menekan tombol YES

                $.ajax({
                    // Request AJAX ke server

                    url: "<?= base_url('data-klien/delete'); ?>", 
                    // URL endpoint delete

                    type: 'POST', 
                    // Metode POST

                    data: {
                        id,
                        // Mengirim ID klien sebagai payload
                    }
                })
                .done(function(res) {
                    // Jika AJAX berhasil

                    swal.fire({
                        title: res.title, 
                        // Judul dari response server

                        text: res.text, 
                        // Pesan dari response server

                        icon: res.icon, 
                        // Icon success/error 

                        showConfirmButton: false,
                        timer: 2000
                        // Popup otomatis hilang setelah 2 detik
                    }).then(() => {
                        location.reload();
                        // Reload halaman setelah sukses delete
                    });
                })
                .fail(function() {
                    // Jika AJAX gagal

                    swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                });
            }

        })
    }
</script>

<?= $this->endSection(); ?> 
<?php // Menutup section Javascript ?>
