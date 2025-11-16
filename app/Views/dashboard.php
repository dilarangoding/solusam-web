<?= $this->extend('template/index'); ?>
<?php echo $this->section('content');

function formatRupiah($number)
{
    return 'Rp ' . number_format($number, 0, ',', '.');
}
?>

<div class="container-fluid my-4">
    <p class="text-end text-muted"><i class="ti ti-calendar-week"></i><?= $tanggal; ?></p>

    <!-- Judul -->
    <h1 class="h3 fw-bold text-dark">Dashboard SOLUSAM</h1>
    <p class="text-muted">
        Ringkasan data dan statistik sistem
        <small class="text-muted">(Data yang ditampilkan per bulan ini)</small>
    </p>

    <!-- Cards -->
    <div class="row g-4 mt-2">
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Transaksi</p>
                    <h5 class="fw-bold"><?= $totalSemua['jumlah']; ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Berat Sampah</p>
                    <h5 class="fw-bold"><?= $totalSemua['total_jml']; ?> kg</h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Uang Masuk</p>
                    <h5 class="fw-bold text-success"><?= formatRupiah($totalSemua['total_pendapatan']); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-lg-3">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <p class="text-muted small mb-1">Total Uang Keluar</p>
                    <h5 class="fw-bold text-danger"><?= formatRupiah($totalSemua['total_pengeluaran']); ?></h5>
                </div>
            </div>
        </div>
    </div>

    <!-- Transactions & Summary -->
    <div class="row g-4 mt-3">
        <!-- Transaksi Terbaru -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold text-dark mb-3">Transaksi Terbaru</h5>
                    <ul class="list-unstyled">
                        <?php

                        foreach ($lastTransaksi as $row) {
                            $harga = $row['jenis'] == 'in' ? $row['harga_jual'] : $row['harga_beli'];
                            $jenis = $row['jenis'] == 'in' ? 'Penjualan' : 'Pembelian';
                            $total = $harga * $row['jumlah'];
                        ?>
                            <li class="d-flex justify-content-between align-items-start bg-success bg-opacity-10 p-2 rounded mb-2">
                                <div>
                                    <p class="fw-medium text-dark mb-0"><?= $jenis . ' - ' . $row['nama_sampah']; ?> </p>
                                    <small class="text-muted"><?= $row['tanggal']; ?></small>
                                </div>
                                <span class="text-success fw-medium"><?= formatRupiah($total); ?></span>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Ringkasan Bulan Ini -->
        <div class="col-12 col-lg-6">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="fw-semibold text-dark mb-3">Ringkasan Bulan Ini <?= date('M'); ?></h5>
                    <ul class="list-unstyled small">
                        <li class="d-flex justify-content-between mb-2">
                            <span>Penjualan</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_pendapatan']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Pembelian</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_pengeluaran']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between mb-2">
                            <span>Keuntungan</span> <span class="text-success"><?= formatRupiah($ringkasanBulan['total_keuntungan']); ?></span>
                        </li>
                        <li class="d-flex justify-content-between">
                            <span>Total Berat</span> <span class="fw-medium"><?= $ringkasanBulan['total_jml']; ?> kg</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<?= $this->endSection(); ?>