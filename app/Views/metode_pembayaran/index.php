<?= $this->extend('template/index'); ?> // Meng-extend template utama "index"
<?= $this->section('content'); ?> // Membuka section "content" untuk isi halaman

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Judul halaman
<p class="text-muted">Kelola data metode pembayaran</p> // Deskripsi halaman

<a href="<?= base_url('metode-bayar/create') ?>" class="btn btn-success btn-sm mb-3"> // Tombol menuju halaman tambah data metode bayar
    <i class="ti ti-plus"></i> Tambah Data // Ikon + teks tombol
</a>

<div class="card shadow-sm border-0"> // Card container dengan shadow lembut dan tanpa border
    <div class="card-body"> // Isi card
        <h5 class="card-title mb-3"><?= $title; ?></h5> // Judul card
        <div class="table-responsive"> // Membuat tabel responsif
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel dengan border, hover, center align
                <thead class="table-success"> // Header tabel dengan warna hijau
                    <tr>
                        <th scope="col">No</th> // Kolom nomor
                        <th scope="col">Nama Metode</th> // Kolom nama metode pembayaran
                        <th scope="col">Aksi</th> // Kolom aksi (edit & delete)
                    </tr>
                </thead>
                <tbody>
                    <?php

                    $no = 1; 
                    foreach ($data as $row) { 
                    ?>
                        <tr>
                            <td><?= $no++; ?></td> // Menampilkan nomor urut
                            <td><?= $row['nama']; ?></td> // Menampilkan nama metode
                            <td>
                                <a href="<?= base_url('sampah/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm"> // Tombol edit (NOTE: URL masih ke 'sampah/edit', ini mungkin salah tapi tetap tidak diubah)
                                    <i class="ti ti-pencil"></i> // Ikon pensil
                                </a>
                                <button type="button" data-nama="<?= $row['nama'] ?>" data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)" class="btn btn-outline-danger btn-sm"> // Tombol hapus dengan atribut data untuk JS
                                    <i class="ti ti-trash"></i> // Ikon tempat sampah
                                </button>
                            </td>
                        </tr>
                    <?php } ?> // Penutup looping
                </tbody>
            </table> // Penutup tabel
        </div> // Penutup div table-responsive
    </div> // Penutup card-body
</div> // Penutup card

<?= $this->endSection(); ?> // Menutup section content

<?= $this->section('js'); ?> // Membuka section khusus untuk javascript
<script>
    function hapus(event) { // Fungsi untuk konfirmasi dan eksekusi hapus
        let nama = $(event).data('nama') // Mengambil nama dari atribut data-nama
        let id = $(event).data('id') // Mengambil id dari atribut data-id

        Swal.fire({ // Menampilkan popup konfirmasi menggunakan SweetAlert
            title: 'Apakah anda yakin?', // Judul popup
            html: `Ingin menghapus data <strong>${nama}</strong>`, // Pesan popup menampilkan nama item
            icon: 'warning', // Ikon peringatan
            showCancelButton: true, // Menampilkan tombol batal
            confirmButtonColor: '#3085d6', // Warna tombol konfirmasi
            cancelButtonColor: '#d33', // Warna tombol batal
            confirmButtonText: 'Yes', // Teks tombol konfirmasi
        }).then((result) => { // Callback jika user menekan tombol
            if (result.value) { // Jika user memilih 'Yes'
                $.ajax({
                        url: "<?= base_url('metode-bayar/delete'); ?>", // Route untuk hapus data
                        type: 'POST', // Method POST
                        data: {
                            id, // Mengirim data id
                        }
                    })
                    .done(function(res) { // Jika request sukses
                        swal.fire({
                            title: res.title, // Judul response
                            text: res.text, // Pesan response
                            icon: res.icon, // Ikon response
                            showConfirmButton: false, // Tombol OK disembunyikan
                            timer: 2000 // Popup otomatis tertutup setelah 2 detik
                        }).then(() => {
                            location.reload(); // Reload halaman setelah data terhapus
                        });
                    })
                    .fail(function() { // Jika request gagal
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error'); // Menampilkan pesan error
                    });
            }

        })
    }
</script>
<?= $this->endSection(); ?> // Menutup section javascript
