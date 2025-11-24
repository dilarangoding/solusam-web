<?= $this->extend('template/index'); ?> // Memanggil template utama 'index'
<?= $this->section('content'); ?> // Membuka section content

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Judul halaman
<p class="text-muted">Kelola data sampah</p> // Subjudul/deskripsi halaman

<a href="<?= base_url('sampah/create') ?>" class="btn btn-success btn-sm mb-3"> // Tombol tambah data
    <i class="ti ti-plus"></i> Tambah Data
</a>

<!-- Data Sampah -->
<div class="card shadow-sm border-0"> // Card untuk tabel data sampah
    <div class="card-body">
        <h5 class="card-title mb-3"><?= $title; ?></h5> // Judul card
        <div class="table-responsive"> // Wrapper untuk scroll horizontal pada tabel
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel bootstrap dengan DataTable
                <thead class="table-success"> // Header tabel
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Nama Sampah</th>
                        <th scope="col">Harga Beli</th>
                        <th scope="col">Harga Jual</th>
                        <th scope="col">Satuan (kg)</th>
                        <th scope="col">Margin</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Fungsi untuk format mata uang rupiah
                    function formatRupiah($number)
                    {
                        return 'Rp ' . number_format($number, 0, ',', '.');
                    }

                    $no = 1; // Inisialisasi nomor urut
                    foreach ($data as $row) { // Looping data sampah
                        $margin = $row['harga_jual'] - $row['harga_beli']; // Hitung margin
                    ?>
                        <tr>
                            <td><?= $no++; ?></td> // Nomor urut
                            <td><?= $row['nama_sampah']; ?></td> // Nama sampah
                            <td><?= formatRupiah($row['harga_beli']); ?></td> // Harga beli
                            <td><?= formatRupiah($row['harga_jual']); ?></td> // Harga jual
                            <td><?= $row['satuan']; ?></td> // Satuan (kg)
                            <td class="text-success fw-semibold"><?= formatRupiah($margin); ?></td> // Margin keuntungan
                            <td>
                                <a href="<?= base_url('sampah/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm"> // Tombol edit
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <button type="button" data-nama="<?= $row['nama_sampah'] ?>" data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)" class="btn btn-outline-danger btn-sm"> // Tombol hapus
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> // Menutup section content

<?= $this->section('js'); ?> // Membuka section JS
<script>
    // Fungsi hapus data
    function hapus(event) {
        let nama = $(event).data('nama'); // Ambil nama dari atribut data
        let id = $(event).data('id'); // Ambil id dari atribut data

        Swal.fire({ // SweetAlert untuk konfirmasi
            title: 'Apakah anda yakin?',
            html: `Ingin menghapus data <strong>${nama}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
        }).then((result) => {
            if (result.value) {
                $.ajax({ // AJAX request untuk hapus data
                        url: "<?= base_url('sampah/delete'); ?>", // URL delete
                        type: 'POST', // Metode POST
                        data: { id } // Data yang dikirim (id)
                    })
                    .done(function(res) { // Jika berhasil
                        swal.fire({ // Tampilkan notifikasi hasil
                            title: res.title,
                            text: res.text,
                            icon: res.icon,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload(); // Reload halaman setelah 2 detik
                        });
                    })
                    .fail(function() { // Jika gagal
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            }
        });
    }
</script>
<?= $this->endSection(); ?> // Menutup section JS
