<?= $this->extend('template/index'); ?> # Memanggil template utama 'template/index'
<?= $this->section('content'); ?> # Membuka section konten

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> # Judul halaman
<p class="text-muted">Kelola data klien</p> # Deskripsi kecil

<a href="<?= base_url('data-klien/create') ?>" class="btn btn-success btn-sm mb-3"> # Tombol tambah data
    <i class="ti ti-plus"></i> Tambah Data # Ikon + tulisan
</a>

<!-- Data Klien -->
<div class="card shadow-sm border-0"> # Card tampilan tabel
    <div class="card-body"> # Isi card
        <h5 class="card-title mb-3"><?= $title; ?></h5> # Judul card
        <div class="table-responsive"> # Agar tabel bisa discroll jika overflow
            <table class="table table-bordered table-hover align-middle dataTable"> # Tabel dengan border dan hover
                <thead class="table-success"> # Header tabel berwarna hijau
                    <tr>
                        <th scope="col">No</th> # Kolom nomor urut
                        <th scope="col">Nama Lengkap</th> # Kolom nama
                        <th scope="col">No Telp</th> # Kolom telepon
                        <th scope="col">Alamat</th> # Kolom alamat
                        <th scope="col">Jenis Usaha</th> # Kolom jenis usaha
                        <th scope="col">Aksi</th> # Kolom action edit/hapus
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function formatRupiah($number) # Fungsi format rupiah, sebenarnya tidak digunakan di sini
                    {
                        return 'Rp ' . number_format($number, 0, ',', '.'); # Mengembalikan format uang
                    }

                    $no = 1; # Inisialisasi nomor urut tabel
                    foreach ($data as $row) { # Looping setiap data klien
                    ?>
                        <tr>
                            <td><?= $no++; ?></td> # Menampilkan nomor otomatis
                            <td><?= $row['nama_lengkap']; ?></td> # Kolom nama
                            <td><?= $row['no_telp']; ?></td> # Kolom telepon
                            <td><?= $row['alamat']; ?></td> # Kolom alamat
                            <td><?= $row['jenis_usaha']; ?></td> # Kolom jenis usaha
                            <td>
                                <a href="<?= base_url('data-klien/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm"> # Tombol edit
                                    <i class="ti ti-pencil"></i> # Ikon edit
                                </a>
                                <button type="button" data-nama="<?= $row['nama_lengkap'] ?>" data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)" class="btn btn-outline-danger btn-sm"> # Tombol hapus dengan data-id dan data-nama
                                    <i class="ti ti-trash"></i> # Ikon hapus
                                </button>
                            </td>
                        </tr>
                    <?php } ?> # Penutup foreach
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> # Menutup section konten

<?= $this->section('js'); ?> # Membuka section khusus untuk javascript
<script>
    function hapus(event) { # Fungsi hapus dipanggil ketika tombol hapus ditekan
        let nama = $(event).data('nama') # Mengambil nama dari atribut data-nama
        let id = $(event).data('id') # Mengambil id dari atribut data-id

        Swal.fire({ # Popup konfirmasi menggunakan SweetAlert
            title: 'Apakah anda yakin?', # Judul popup
            html: `Ingin menghapus data <strong>${nama}</strong>`, # Isi pesan popup
            icon: 'warning', # Ikon warning
            showCancelButton: true, # Tampilkan tombol batal
            confirmButtonColor: '#3085d6', # Warna tombol confirm
            cancelButtonColor: '#d33', # Warna tombol cancel
            confirmButtonText: 'Yes', # Tulisan tombol confirm
        }).then((result) => { # Jika user menekan Yes
            if (result.value) { # Jika konfirmasi benar
                $.ajax({
                        url: "<?= base_url('data-klien/delete'); ?>", # URL endpoint delete
                        type: 'POST', # Method POST
                        data: {
                            id, # Mengirim ID klien
                        }
                    })
                    .done(function(res) { # Jika request berhasil
                        swal.fire({
                            title: res.title, # Judul pesan dari backend
                            text: res.text, # Isi pesan
                            icon: res.icon, # Ikon (success/error)
                            showConfirmButton: false,
                            timer: 2000 # Auto close setelah 2 detik
                        }).then(() => {
                            location.reload(); # Reload halaman setelah hapus
                        });
                    })
                    .fail(function() { # Jika error AJAX
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error'); # Pesan error
                    });
            }

        })
    }
</script>
<?= $this->endSection(); ?> # Menutup section javascript
