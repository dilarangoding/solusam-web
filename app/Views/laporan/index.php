<?= $this->extend('template/index'); ?>
<?= $this->section('content'); ?>

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1>
<p class="text-muted">Kelola data laporan</p>

<div class="row g-4">

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p>
                <h5 class="fw-bold" id="total-berat"></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Masuk</p>
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Keluar</p>
                <h5 class="fw-bold text-danger" id="total-uang-keluar"></h5>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Keuntungan / Kerugian</p>
                <h5 class="fw-bold" id="total-keuntungan"></h5>
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0"><?= $title; ?></h5>
            <button class="btn btn-success" id="export-excel">
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3">
            <div class="col-md-3">
                <label class="form-label">Pilih Filter</label>
                <select class="form-select" id="filter-type">
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter">
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun">
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan">
                            <!-- <option value="1">Januari</option>
                            <option value="2">Februari</option>
                            <option value="3">Maret</option>
                            <option value="4">April</option>
                            <option value="5">Mei</option>
                            <option value="6">Juni</option>
                            <option value="7">Juli</option>
                            <option value="8">Agustus</option>
                            <option value="9">September</option>
                            <option value="10" selected>Oktober</option>
                            <option value="11">November</option>
                            <option value="12">Desember</option> -->
                            <?php
                            $currentMonth = date('n'); // Mendapatkan bulan saat ini (1-12)

                            for ($i = 1; $i <= 12; $i++) {
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Tandai sebagai selected jika sesuai dengan bulan saat ini
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-bulan">
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai">
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-success">
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Periode</th>
                        <th scope="col">Jumlah</th>
                        <th scope="col">Total Pemasukan</th>
                        <th scope="col">Total Pengeluaran</th>
                        <th scope="col">Keuntungan / Kerugian</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?>


<?= $this->section('js'); ?>
<script>
    $('#tahun-bulan').val(new Date().getFullYear());
    $('#tahun').val(new Date().getFullYear());
    let tahun = new Date().getFullYear();
    loadLaporan('tahun', tahun);
    // Function untuk menampilkan data laporan
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {
        $.ajax({
            url: "<?= base_url('laporan/getLaporanData'); ?>",
            type: "POST",
            data: {
                filter_type: filterType,
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai
            },
            success: function(response) {
                console.log(response);

                // Update summary cards
                // Inisialisasi variabel untuk menyimpan total
                let totalBerat = 0;
                let totalUangMasuk = 0;
                let totalUangKeluar = 0;
                let totalKeuntungan = 0;

                // Pastikan response adalah array
                if (Array.isArray(response)) {
                    // Lakukan looping terhadap setiap elemen dalam array
                    response.forEach(function(data) {
                        // Tambahkan nilai dari setiap elemen ke total
                        totalBerat += parseFloat(data.jumlah) || 0; // Pastikan nilainya angka
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0;
                        totalUangKeluar += parseFloat(data.total_pengeluaran) || 0;
                        totalKeuntungan += parseFloat(data.total_keuntungan) || 0;
                    });

                    // Format total uang
                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID');
                    totalUangKeluarFormatted = 'Rp ' + totalUangKeluar.toLocaleString('id-ID');
                    totalKeuntunganFormatted = 'Rp ' + totalKeuntungan.toLocaleString('id-ID');

                    // Update summary cards dengan total yang sudah dihitung
                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg');
                    $('#total-uang-masuk').text(totalUangMasukFormatted);
                    $('#total-uang-keluar').text(totalUangKeluarFormatted);
                    $('#total-keuntungan').text(totalKeuntunganFormatted);

                } else {
                    // Jika response bukan array, tampilkan nilai default
                    $('#total-berat').text('0 kg');
                    $('#total-uang-masuk').text('Rp 0');
                    $('#total-uang-keluar').text('Rp 0');
                    $('#total-keuntungan').text('Rp 0');
                }
                // $('#total-berat').text(response.jumlah);
                // $('#total-uang-masuk').text('Rp ' + response.total_pendapatan);
                // $('#total-uang-keluar').text('Rp ' + response.total_pengeluaran);
                // $('#total-keuntungan').text(response.total_keuntungan + ' Kg');

                // // Update table data (kosongkan dulu)
                $('table tbody').empty();

                // // Isi data ke table
                $.each(response, function(i, item) {
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0;
                    let totalPengeluaran = parseFloat(item.total_pengeluaran) || 0;
                    let totalKeuntungan = parseFloat(item.total_keuntungan) || 0;
                    $('table tbody').append(`
                            <tr>
                                <td>${i+1}</td>
                                <td>${item.periode}</td>
                                <td>${item.jumlah}</td>
                                <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td>
                                <td>Rp ${totalPengeluaran.toLocaleString('id-ID')}</td>
                                <td>Rp ${totalKeuntungan.toLocaleString('id-ID')}</td>
                            </tr>
                        `);
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
            }
        });
    }

    // Event handler untuk filter type
    $('#filter-type').change(function() {
        var filterType = $(this).val();

        // Sembunyikan semua filter
        $('#tahun-filter').hide();
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        // Tampilkan filter yang sesuai
        if (filterType == 'tahun') {
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val(), null, null, null);

        } else if (filterType == 'bulan') {
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null);

        } else if (filterType == 'harian') {
            $('#harian-filter').show();
            $('table tbody').empty();


        }
    });

    // Event handler untuk filter tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val(), null, null, null);
    });

    // Event handler untuk filter bulan
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null);
    });

    // Event handler untuk filter harian
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // Export Excel functionality
    $('#export-excel').click(function() {
        var filterType = $('#filter-type').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var tahunBulan = $('#tahun-bulan').val();
        var tanggalMulai = $('#tanggal-mulai').val();
        var tanggalSelesai = $('#tanggal-selesai').val();

        var url = '<?= base_url('laporan/export'); ?>?';
        
        if (filterType === 'tahun') {
            url += 'tahun=' + tahun;
        } else if (filterType === 'bulan') {
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;
        } else if (filterType === 'harian') {
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank');
    });
</script>
<?= $this->endSection(); ?>