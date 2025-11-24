<?= $this->extend('template/index'); ?> # Memanggil template utama
<?= $this->section('content'); ?> # Membuka section konten

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> # Judul halaman laporan
<p class="text-muted">Kelola data laporan</p> # Subjudul / deskripsi

<div class="row g-4"> # Grid untuk card summary statistik

    <div class="col-12 col-sm-6 col-lg-3"> # Kolom card
        <div class="card shadow-sm h-100"> # Card
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p> # Label
                <h5 class="fw-bold" id="total-berat"></h5> # Nilai total berat (di-update JS)
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Masuk</p>
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> # Total pemasukan
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Keluar</p>
                <h5 class="fw-bold text-danger" id="total-uang-keluar"></h5> # Total pengeluaran
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Keuntungan / Kerugian</p>
                <h5 class="fw-bold" id="total-keuntungan"></h5> # Total profit/loss
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0 mt-4"> # Card utama tabel laporan
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3"> # Header tabel + tombol export
            <h5 class="card-title mb-0"><?= $title; ?></h5> # Judul card
            <button class="btn btn-success" id="export-excel"> # Tombol export Excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3">

            <div class="col-md-3">
                <label class="form-label">Pilih Filter</label>
                <select class="form-select" id="filter-type"> # Pilihan mode filter
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> # Filter berdasarkan tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> # Input tahun
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> # Filter bulan (hidden dulu)
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan"> # Dropdown bulan (auto select current month)
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
                            $currentMonth = date('n'); # Ambil bulan saat ini
                            for ($i = 1; $i <= 12; $i++) {
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); # Nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; # Jika bulan sekarang → selected
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-bulan"> # Tahun untuk filter bulan
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> # Filter harian (tanggal range)
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai"> # Date start
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai"> # Date end
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive"> # Tabel laporan
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
                <tbody> # Data diisi oleh AJAX
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> # Menutup section konten


<?= $this->section('js'); ?> # Buka section JS
<script>

    $('#tahun-bulan').val(new Date().getFullYear()); # Set input tahun-bulan = tahun sekarang
    $('#tahun').val(new Date().getFullYear()); # Set filter tahun = tahun sekarang

    let tahun = new Date().getFullYear(); # Simpan tahun sekarang
    loadLaporan('tahun', tahun); # Load laporan default by tahun

    // Function untuk load laporan via AJAX
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {

        $.ajax({
            url: "<?= base_url('laporan/getLaporanData'); ?>", # Endpoint ambil data
            type: "POST",
            data: { # Kirim data filter ke controller
                filter_type: filterType,
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai
            },

            success: function(response) { # Jika data berhasil diterima
                console.log(response);

                // Inisialisasi total summary
                let totalBerat = 0;
                let totalUangMasuk = 0;
                let totalUangKeluar = 0;
                let totalKeuntungan = 0;

                if (Array.isArray(response)) { # Pastikan response adalah array
                    response.forEach(function(data) { # Loop setiap baris data
                        totalBerat += parseFloat(data.jumlah) || 0;
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0;
                        totalUangKeluar += parseFloat(data.total_pengeluaran) || 0;
                        totalKeuntungan += parseFloat(data.total_keuntungan) || 0;
                    });

                    # Format angka ke rupiah
                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID');
                    totalUangKeluarFormatted = 'Rp ' + totalUangKeluar.toLocaleString('id-ID');
                    totalKeuntunganFormatted = 'Rp ' + totalKeuntungan.toLocaleString('id-ID');

                    # Tampilkan summary
                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg');
                    $('#total-uang-masuk').text(totalUangMasukFormatted);
                    $('#total-uang-keluar').text(totalUangKeluarFormatted);
                    $('#total-keuntungan').text(totalKeuntunganFormatted);

                } else {
                    # Jika response tidak valid, tampilkan default
                    $('#total-berat').text('0 kg');
                    $('#total-uang-masuk').text('Rp 0');
                    $('#total-uang-keluar').text('Rp 0');
                    $('#total-keuntungan').text('Rp 0');
                }

                $('table tbody').empty(); # Kosongkan tabel

                $.each(response, function(i, item) { # Loop data table
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0;
                    let totalPengeluaran = parseFloat(item.total_pengeluaran) || 0;
                    let totalKeuntungan = parseFloat(item.total_keuntungan) || 0;

                    $('table tbody').append(` # Tambahkan row baru
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

            error: function(xhr, status, error) { # Jika AJAX error
                console.error(error);
            }
        });
    }

    // Event filter tipe laporan
    $('#filter-type').change(function() {

        var filterType = $(this).val(); # Ambil value dropdown filter

        $('#tahun-filter').hide(); # Hide semua dulu
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') { # Mode tahun
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val());

        } else if (filterType == 'bulan') { # Mode bulan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());

        } else if (filterType == 'harian') { # Mode harian
            $('#harian-filter').show();
            $('table tbody').empty(); # Kosongkan table karena perlu range tanggal
        }
    });

    // Event filter tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val());
    });

    // Event filter bulan & tahun-bulan
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
    });

    // Event filter harian (ketika end date dipilih)
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // Export Excel
    $('#export-excel').click(function() {

        var filterType = $('#filter-type').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var tahunBulan = $('#tahun-bulan').val();
        var tanggalMulai = $('#tanggal-mulai').val();
        var tanggalSelesai = $('#tanggal-selesai').val();

        var url = '<?= base_url('laporan/export'); ?>?'; # URL dasar export

        if (filterType === 'tahun') {
            url += 'tahun=' + tahun; # Export per tahun
        } 
        else if (filterType === 'bulan') {
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan; # Export per bulan
        } 
        else if (filterType === 'harian') {
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai; # Export range hari
        }

        window.open(url, '_blank'); # Buka file Excel
    });

</script>
<?= $this->endSection(); ?> # Tutup section JS
