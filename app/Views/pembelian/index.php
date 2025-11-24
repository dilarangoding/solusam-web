<?= $this->extend('template/index'); ?> // Menggunakan template utama
<?= $this->section('content'); ?> // Membuka section konten utama

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Judul halaman
<p class="text-muted">Kelola data sampah</p> // Subjudul

<a href="<?= base_url('pembelian/create') ?>" class="btn btn-success btn-sm mb-3"> // Tombol menuju halaman tambah data
    <i class="ti ti-plus"></i> Tambah Data // Icon dan tulisan tombol
</a>

<div class="card shadow-sm border-0"> // Card container data
    <div class="card-body"> // Body card
        <h5 class="card-title mb-3"><?= $title; ?></h5> // Judul card
        <div class="table-responsive"> // Membuat tabel responsif
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel data + DataTables
                <thead class="table-success"> // Header tabel dengan background hijau muda
                    <tr>
                        <th scope="col">No</th> // Kolom nomor
                        <th scope="col">Tanggal</th> // Kolom tanggal
                        <th scope="col">Nama Sampah</th> // Kolom nama sampah
                        <th scope="col">Jumlah</th> // Kolom jumlah pembelian
                        <th scope="col">Harga</th> // Kolom harga satuan
                        <th scope="col">Total</th> // Kolom harga total
                        <th scope="col">Klien</th> // Kolom nama klien
                        <th scope="col">Aksi</th> // Kolom tombol aksi
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function formatRupiah($number) // Fungsi untuk mengubah angka menjadi format Rupiah
                    {
                        return 'Rp ' . number_format($number, 0, ',', '.'); // Format: Rp 10.000
                    }

                    $no = 1; // Nomor urut tabel dimulai dari 1
                    foreach ($data as $row) { // Loop seluruh data pembelian
                        $total = $row['harga_beli'] * $row['jumlah']; // Hitung total harga
                    ?>
                        <tr>
                            <td><?= $no++; ?></td> // Menampilkan nomor urut
                            <td><?= $row['tanggal']; ?></td> // Menampilkan tanggal transaksi
                            <td><?= $row['nama_sampah']; ?></td> // Menampilkan nama sampah
                            <td><?= $row['jumlah']; ?></td> // Menampilkan jumlah pembelian
                            <td><?= formatRupiah($row['harga_beli']); ?></td> // Menampilkan harga beli dalam format Rupiah
                            <td class="text-success fw-semibold"><?= formatRupiah($total); ?></td> // Total harga (teks hijau tebal)
                            <td><?= $row['nama_lengkap']; ?></td> // Menampilkan nama klien
                            <td>
                                <a href="<?= base_url('pembelian/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm"> // Tombol edit
                                    <i class="ti ti-pencil"></i> // Icon pensil
                                </a>

                                <button 
                                    type="button"
                                    data-nama="<?= $row['nama_sampah'] ?>" // Menyimpan nama sampah untuk dialog hapus
                                    data-id="<?= $row['id'] ?>" // Menyimpan id untuk proses hapus
                                    onclick="hapus(this)" // Ketika tombol diklik, jalankan fungsi hapus()
                                    class="btn btn-outline-danger btn-sm">
                                    <i class="ti ti-trash"></i> // Icon tong sampah
                                </button>
                            </td>
                        </tr>
                    <?php } ?> // Akhir loop data
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?> // Penutup section konten

<?= $this->section('js'); ?> // Membuka section javascript
<script>
    function hapus(event) { // Fungsi hapus data
        let nama = $(event).data('nama') // Ambil nama sampah dari atribut data-nama
        let id = $(event).data('id') // Ambil id dari atribut data-id

        Swal.fire({ // Tampilkan popup SweetAlert konfirmasi hapus
            title: 'Apakah anda yakin?', 
            html: `Ingin menghapus data <strong>${nama}</strong>`, // Pesan yang menampilkan nama sampah
            icon: 'warning', // Icon warning
            showCancelButton: true, // Tampilkan tombol batal
            confirmButtonColor: '#3085d6', // Warna tombol YES
            cancelButtonColor: '#d33', // Warna tombol CANCEL
            confirmButtonText: 'Yes', // Tulisan tombol YES
        }).then((result) => { // Ketika pengguna memilih tombol
            if (result.value) { // Jika menekan YES
                $.ajax({ // Lakukan AJAX request untuk hapus
                        url: "<?= base_url('pembelian/delete'); ?>", // URL tujuan hapus
                        type: 'POST', // Metode POST
                        data: { id }, // Data dikirim berupa id data
                    })
                    .done(function(res) { // Jika request berhasil
                        swal.fire({
                            title: res.title, // Judul alert dari server
                            text: res.text, // Pesan alert dari server
                            icon: res.icon, // Icon (success / error)
                            showConfirmButton: false, // Tidak menampilkan tombol OK
                            timer: 2000 // Alert otomatis hilang dalam 2 detik
                        }).then(() => {
                            location.reload(); // Reload halaman setelah alert hilang
                        });
                    })
                    .fail(function() { // Jika AJAX error
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error'); // Tampilkan error
                    });
            }
        })
    }
</script>
<?= $this->endSection(); ?> // Menutup section javascript
