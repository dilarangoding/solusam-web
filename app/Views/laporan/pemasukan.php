<?= $this->extend('template/index'); ?>        # Memanggil template utama bernama index
<?= $this->section('content'); ?>             # Membuka section 'content' untuk isi halaman

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1>   # Menampilkan judul dinamis dari controller
<p class="text-muted">Kelola data laporan pendapatan</p>  # Deskripsi halaman

<div class="row g-4">                         # Row Bootstrap untuk menampung card

    <div class="col-12 col-sm-6 col-lg-3">    # Kolom responsif
        <div class="card shadow-sm h-100">    # Card box
            <div class="card-body">
                <p class="text-muted small mb-1"><i class="ti ti-chart-line text-success"></i> Total Pemasukan</p>
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> # Tempat update pemasukan via JS
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p>
                <h5 class="fw-bold" id="total-berat"></h5>  # Tempat update total berat via JS
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4">   # Card tabel
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0"><?= $title; ?></h5>  # Judul card
            <button class="btn btn-success" id="export-excel">  # Tombol export Excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3">

            <div class="col-md-3">
                <label class="form-label">Pilih Filter</label>
                <select class="form-select" id="filter-type">  # Filter kategori laporan
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter">  # Input untuk filter tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun">
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;">  # Default tersembunyi
                <div class="row">

                    <div class="col-md-6">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan">  # Dropdown bulan
                        <?php
                            $currentMonth = date('n');         # Ambil bulan saat ini
                            for ($i = 1; $i <= 12; $i++) {     # Loop 1 sampai 12
                                $monthName = date('F', mktime(0,0,0,$i,1)); # Nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; # Set default terpilih
                                echo '<option value="'.$i.'" '.$selected.'>'.$monthName.'</option>'; # Output option
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
            <div class="col-md-6" id="harian-filter" style="display: none;">  # Input tanggal harian
                <div class="row">

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai"> # Start date
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai"> # End date
                    </div>

                </div>
            </div>

        </div>

        <div class="table-responsive">             # Wrapper tabel agar scroll
            <table class="table table-bordered table-hover align-middle dataTable"> # Tabel data
                <thead class="table-success">
                    <tr>
                        <th>No</th>
                        <th>Tanggal</th>
                        <th>Nama Sampah</th>
                        <th>Jumlah (Kg)</th>
                        <th>Harga</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>                              # Data akan diisi via AJAX
                </tbody>
            </table>
        </div>

    </div>
</div>

<?= $this->endSection(); ?>                       # Menutup section content


<?= $this->section('js'); ?>                      # Membuka section javascript
<script>

    $('#tahun-bulan').val(new Date().getFullYear()); # Set default tahun (filter bulan)
    $('#tahun').val(new Date().getFullYear());       # Set default tahun (filter tahun)
    let tahun = new Date().getFullYear();            # Simpan tahun saat ini
    loadLaporan('tahun', tahun);                     # Load laporan default: per tahun

    function loadLaporan(filterType, tahun=null, bulan=null, tanggalMulai=null, tanggalSelesai=null) { # Fungsi ambil data laporan
        $.ajax({
            url: "<?= base_url('getDataInOut'); ?>", # Endpoint backend
            type: "POST",                           # Metode POST
            data: {                                 # Body request
                filter_type: filterType,
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                jenis: 'out'                        # Ambil data jenis OUT (penjualan)
            },

            success: function(response) {           # Jika request berhasil
                console.log(response);              # Debug response JSON

                let totalBerat = 0;                 # Variabel total berat
                let totalUangMasuk = 0;             # Variabel total pemasukan

                if (Array.isArray(response)) {      # Pastikan response berupa array
                    response.forEach(function(data){# Loop tiap baris data
                        totalBerat += parseFloat(data.jumlah) || 0;        # Jumlah kg
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0; # Total uang
                    });

                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); # Format Rp

                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); # Tampilkan total berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted);              # Tampilkan total pemasukan

                } else {                            # Jika tidak ada data
                    $('#total-berat').text('0 kg');
                    $('#total-uang-masuk').text('Rp 0');
                }

                $('table tbody').empty();           # Kosongkan tabel sebelumnya

                $.each(response, function(i, item) { # Loop isi tabel
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0;
                    let hargaJual = parseFloat(item.harga_jual) || 0;

                    $('table tbody').append(`
                        <tr>
                            <td>${i+1}</td>                       # Nomor urut
                            <td>${item.tanggal}</td>              # Tanggal transaksi
                            <td>${item.nama_sampah}</td>          # Nama sampah
                            <td>${item.jumlah}</td>               # Jumlah kg
                            <td>Rp ${hargaJual.toLocaleString('id-ID')}</td> # Harga
                            <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td> # Total
                        </tr>
                    `);
                });

            },

            error: function(xhr, status, error) {   # Jika gagal request
                console.error(error);
            }
        });
    }

    $('#filter-type').change(function() {           # Event saat filter diganti
        var filterType = $(this).val();

        $('#tahun-filter').hide();                  # Sembunyikan semua filter
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') {                # Jika filter tahun
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val());

        } else if (filterType == 'bulan') {         # Jika filter bulan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());

        } else if (filterType == 'harian') {        # Jika harian
            $('#harian-filter').show();
            $('table tbody').empty();               # Kosongkan tabel dulu
        }
    });

    $('#tahun').keyup(function() {                  # Filter tahun on typing
        loadLaporan('tahun', $(this).val());
    });

    $('#bulan, #tahun-bulan').change(function() {   # Filter bulan
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
    });

    $('#tanggal-selesai').change(function() {       # Filter harian jika tanggal selesai dipilih
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    $('#export-excel').click(function() {           # Tombol export Excel
        var filterType = $('#filter-type').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var tahunBulan = $('#tahun-bulan').val();
        var tanggalMulai = $('#tanggal-mulai').val();
        var tanggalSelesai = $('#tanggal-selesai').val();

        var url = '<?= base_url('export-pemasukan'); ?>?'; # URL export

        if (filterType === 'tahun') {
            url += 'tahun=' + tahun;
        } else if (filterType === 'bulan') {
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;
        } else if (filterType === 'harian') {
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank');                # Buka file Excel
    });

</script>
<?= $this->endSection(); ?>                       # Tutup section JS
