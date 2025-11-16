<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1>
<p class="text-muted">Kelola data sampah</p>

<a href="<?= base_url('penjualan/create') ?>" class="btn btn-success btn-sm mb-3">
    <i class="ti ti-plus"></i> Tambah Data
</a>


<div class="card shadow-sm border-0">
    <div class="card-body">
        <h5 class="card-title mb-3"><?= $title; ?></h5>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-success">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nama Sampah</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Total</th>
                        <th scope="col">Metode Bayar</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function formatRupiah($number)
                    {
                        return 'Rp ' . number_format($number, 0, ',', '.');
                    }

                    $no = 1;
                    foreach ($data as $row) {
                        $total = $row['harga_jual'] * $row['jumlah'];
                        $bukti = $row['bukti'] ? '<br><a href="' . base_url('bukti/' . $row['bukti']) . '" target="_blank" class="badge bg-info text-decoration-none">Lihat Bukti</a>' : '';
                    ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= $row['tanggal']; ?></td>
                            <td><?= $row['nama_sampah']; ?></td>
                            <td><?= $row['jumlah']; ?></td>
                            <td><?= formatRupiah($row['harga_jual']); ?></td>
                            <td class="text-success fw-semibold"><?= formatRupiah($total); ?></td>
                            <td><?= $row['metode_bayar'] . ' ' . $bukti; ?></td>
                            <td>
                                <a href="<?= base_url('penjualan/edit/' . $row['id']) ?>" class="btn btn-outline-primary btn-sm" title="Edit">
                                    <i class="ti ti-pencil"></i>
                                </a>
                                <?php if ($row['metode_bayar'] === 'qris'): ?>
                                    <a href="<?= base_url('penjualan/qrcode/' . $row['id']) ?>" class="btn btn-outline-success btn-sm" title="Lihat QR Code">
                                        <i class="ti ti-qrcode"></i>
                                    </a>
                                <?php endif; ?>
                                <button type="button" data-nama="<?= $row['nama_sampah'] ?>" data-id="<?= $row['id'] ?>"
                                    onclick="hapus(this)" class="btn btn-outline-danger btn-sm" title="Hapus">
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
<?= $this->endSection(); ?>


<?= $this->section('js'); ?>
<script>
    function hapus(event) {
        let nama = $(event).data('nama')
        let id = $(event).data('id')

        Swal.fire({
            title: 'Apakah anda yakin?',
            html: `Ingin menghapus data <strong>${nama}</strong>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes',
        }).then((result) => {
            if (result.value) {
                $.ajax({
                        url: "<?= base_url('penjualan/delete'); ?>",
                        type: 'POST',
                        data: {
                            id,
                        }
                    })
                    .done(function(res) {
                        swal.fire({
                            title: res.title,
                            text: res.text,
                            icon: res.icon,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    })
                    .fail(function() {
                        swal.fire('Oops...', 'Something went wrong with ajax !', 'error');
                    });
            }

        })
    }
</script>
<?= $this->endSection(); ?>