<?= $this->extend('template/index'); ?> // Memanggil template utama
<?= $this->section('content'); ?> // Memulai section konten

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Menampilkan judul halaman
<p class="text-muted">Kelola data sampah</p> // Subjudul halaman

<a href="<?= base_url('penjualan/create') ?>" class="btn btn-success btn-sm mb-3"> // Tombol tambah data penjualan
    <i class="ti ti-plus"></i> Tambah Data
</a>

<div class="card shadow-sm border-0"> // Card container
    <div class="card-body"> // Body card
        <h5 class="card-title mb-3"><?= $title; ?></h5> // Menampilkan ulang judul pada card

        <div class="table-responsive"> // Membuat tabel responsif
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel dengan border dan hover
                <thead class="table-success"> // Header tabel berwarna hijau muda
                    <tr>
                        <th>No</th> // Kolom nomor urut
                        <th>Tanggal</th> // Kolom tanggal transaksi
                        <th>Nama Sampah</th> // Kolom nama sampah
                        <th>Jumlah</th> // Kolom jumlah sampah
                        <th>Harga</th> // Kolom harga jual per item
                        <th>Total</th> // Kolom total harga (jumlah * harga)
                        <th>Metode Bayar</th> // Kolom metode pembayaran
                        <th>Aksi</th> // Kolom aksi edit/hapus
                    </tr>
                </thead>

                <tbody>
                    <?php
                    function formatRupiah($number) // Fungsi format angka menjadi Rupiah
                    {
                        return 'Rp ' . number_format($number, 0, ',', '.'); // Format angka sesuai format Rupiah
                    }

                    $no = 1; // Inisialisasi nomor urut
                    foreach ($data as $row) { // Loop semua data penjualan
                        $total = $row['harga_jual'] * $row['jumlah']; // Hitung total harga
                    ?>
                        <tr>
                            <td><?= $no++; ?></td> // Menampilkan nomor urut
                            <td><?= $row['tanggal']; ?></td> // Menampilkan tanggal
                            <td><?= $row['nama_sampah']; ?></td> // Menampilkan nama sampah
                            <td><?= $row['jumlah']; ?></td> // Menampilkan jumlah
                            <td><?= formatRupiah($row['harga_jual']); ?></td> // Menampilkan harga yang sudah diformat
                            <td class="text-success fw-semibold"><?= formatRupiah($total); ?></td> // Menampilkan total harga
                            <td><?= $row['metode_bayar']; ?></td> // Menampilkan metode bayar
                            <td>
                                <a href="<?= base_url('penjualan/edit/' . $row['id']) ?>" 
                                   class="btn btn-outline-primary btn-sm" title="Edit"> // Tombol edit
                                    <i class="ti ti-pencil"></i>
                                </a>

                                <button type="button"
                                        data-nama="<?= $row['nama_sampah'] ?>" // Menyimpan nama sampah untuk alert
                                        data-id="<?= $row['id'] ?>" // Menyimpan ID untuk dihapus
                                        onclick="hapus(this)" // Memanggil fungsi hapus
                                        class="btn btn-outline-danger btn-sm"
                                        title="Hapus"> // Tombol hapus
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php } ?> // Tutup foreach
                </tbody>
            </table>
        </div>

    </div>
</div>

<?= $this->endSection(); ?> // Menutup section konten


<?= $this->section('js'); ?> // Memulai section khusus script JS

<script>
    function hapus(event) { // Fungsi hapus data
        let nama = $(event).data('nama'); // Ambil nama sampah dari atribut HTML
        let id = $(event).data('id'); // Ambil ID dari atribut HTML

        Swal.fire({ // Tampilkan popup konfirmasi
            title: 'Apakah anda yakin?', // Judul popup
            html: `Ingin menghapus data <strong>${nama}</strong>`, // Pesan popup
            icon: 'warning', // Icon peringatan
            showCancelButton: true, // Tampilkan tombol batal
            confirmButtonColor: '#3085d6', // Warna tombol konfirmasi
            cancelButtonColor: '#d33', // Warna tombol batal
            confirmButtonText: 'Yes', // Tulisan tombol konfirmasi
        }).then((result) => { // Event setelah popup disetujui/ditolak
            if (result.value) { // Jika user menekan YES

                $.ajax({ // Request AJAX untuk hapus data
                    url: "<?= base_url('penjualan/delete'); ?>", // URL hapus data
                    type: 'POST', // Metode POST
                    data: { id } // Data ID yang dikirim ke server
                })

                .done(function(res) { // Jika request berhasil
                    Swal.fire({
                        title: res.title, // Judul response
                        text: res.text, // Pesan response
                        icon: res.icon, // Icon response
                        showConfirmButton: false, // Hilangkan tombol OK
                        timer: 2000 // Popup tertutup otomatis 2 detik
                    }).then(() => {
                        location.reload(); // Refresh halaman setelah hapus
                    });
                })

                .fail(function() { // Jika AJAX error
                    Swal.fire('Oops...', 'Something went wrong with ajax !', 'error'); // Tampilkan error
                });

            }
        });
    }
</script>

<?= $this->endSection(); ?> // Menutup section JS
