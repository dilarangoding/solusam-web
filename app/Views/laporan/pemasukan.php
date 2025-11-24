<?= $this->extend('template/index'); ?> // Menggunakan template utama 'index'
<?= $this->section('content'); ?> // Membuka section 'content' untuk mengisi konten halaman

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Menampilkan judul halaman
<p class="text-muted">Kelola data laporan pendapatan</p> // Subjudul halaman

<div class="row g-4"> // Baris untuk card ringkasan
    <div class="col-12 col-sm-6 col-lg-3"> // Kolom card pertama
        <div class="card shadow-sm h-100"> // Card dengan shadow
            <div class="card-body"> // Isi card
                <p class="text-muted small mb-1"><i class="ti ti-chart-line text-success"></i> Total Pemasukan</p> // Label card
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> // Tempat menampilkan total pemasukan
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Kolom card kedua
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p> // Label card
                <h5 class="fw-bold" id="total-berat"></h5> // Tempat menampilkan total berat sampah
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0 mt-4"> // Card utama tabel laporan
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3"> // Header card
            <h5 class="card-title mb-0"><?= $title; ?></h5> // Judul card
            <button class="btn btn-success" id="export-excel"> // Tombol export excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3"> // Row filter
            <div class="col-md-3"> // Pilih jenis filter
                <label class="form-label">Pilih Filter</label>
                <select class="form-select" id="filter-type"> // Dropdown filter jenis
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> // Filter tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> // Input tahun
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> // Filter bulan (hide default)
                <div class="row">
                    <div class="col-md-6"> // Pilih bulan
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan"> // Dropdown bulan
                        <?php
                            $currentMonth = date('n'); // Ambil bulan saat ini

                            for ($i = 1; $i <= 12; $i++) { // Loop 12 bulan
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Tandai bulan sekarang
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>'; // Cetak opsi
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6"> // Input tahun bulan
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-bulan"> // Input tahun untuk filter bulan
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> // Filter harian (hidden default)
                <div class="row">
                    <div class="col-md-6"> // Tanggal mulai
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai">
                    </div>
                    <div class="col-md-6"> // Tanggal selesai
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai">
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive"> // Table laporan
            <table class="table table-bordered table-hover align-middle dataTable"> // DataTable
                <thead class="table-success"> // Header tabel
                    <tr>
                        <th scope="col">No</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Nama Sampah</th>
                        <th scope="col">Jumlah (Kg)</th>
                        <th scope="col">Harga</th>
                        <th scope="col">Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody> // Body diisi Ajax
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?> // Tutup section content


<?= $this->section('js'); ?> // Buka section JS khusus halaman
<script>
    $('#tahun-bulan').val(new Date().getFullYear()); // Set default tahun-bulan = tahun ini
    $('#tahun').val(new Date().getFullYear()); // Set default tahun = tahun ini
    let tahun = new Date().getFullYear(); // Simpan tahun saat ini
    loadLaporan('tahun', tahun); // Load laporan berdasarkan tahun default

    // FUNCTION UTAMA UNTUK MEMUAT LAPORAN
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {
        $.ajax({
            url: "<?= base_url('getDataInOut'); ?>", // API untuk ambil data laporan
            type: "POST",
            data: {
                filter_type: filterType, // Jenis filter (tahun/bulan/harian)
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                jenis: 'out' // Jenis laporan: pemasukan/pendapatan
            },
            success: function(response) {
                console.log(response); // Debug

                let totalBerat = 0; // Total berat sampah
                let totalUangMasuk = 0; // Total pendapatan uang

                if (Array.isArray(response)) { // Jika response array valid
                    response.forEach(function(data) { // Loop data
                        totalBerat += parseFloat(data.jumlah) || 0; // Akumulasi jumlah (kg)
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0; // Akumulasi uang masuk
                    });

                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); // Format uang

                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); // Tampilkan total berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted); // Tampilkan total uang

                } else { // Jika array tidak valid
                    $('#total-berat').text('0 kg');
                    $('#total-uang-masuk').text('Rp 0');
                }

                $('table tbody').empty(); // Kosongkan tabel

                // Masukkan data ke tabel
                $.each(response, function(i, item) {
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0; // Total pendapatan
                    let hargaJual = parseFloat(item.harga_jual) || 0; // Harga jual per kg

                    $('table tbody').append(`
                        <tr>
                            <td>${i+1}</td> // Nomor
                            <td>${item.tanggal}</td> // Tanggal transaksi
                            <td>${item.nama_sampah}</td> // Nama sampah
                            <td>${item.jumlah}</td> // Jumlah (kg)
                            <td>Rp ${hargaJual.toLocaleString('id-ID')}</td> // Harga jual
                            <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td> // Total pendapatan
                        </tr>
                    `);
                });
            },
            error: function(xhr, status, error) {
                console.error(error); // Tampilkan error jika gagal
            }
        });
    }

    // EVENT: ketika filter jenis berubah
    $('#filter-type').change(function() {
        var filterType = $(this).val();

        $('#tahun-filter').hide(); // Sembunyikan semua
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') { // Jika filter tahun
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val());

        } else if (filterType == 'bulan') { // Jika filter bulan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());

        } else if (filterType == 'harian') { // Jika filter harian
            $('#harian-filter').show();
            $('table tbody').empty(); // Kosongkan tabel dulu
        }
    });

    // EVENT: Filter tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val());
    });

    // EVENT: Filter bulan & tahun-bulan
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
    });

    // EVENT: Filter harian (tanggal selesai dipilih)
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // EXPORT EXCEL
    $('#export-excel').click(function() {
        var filterType = $('#filter-type').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var tahunBulan = $('#tahun-bulan').val();
        var tanggalMulai = $('#tanggal-mulai').val();
        var tanggalSelesai = $('#tanggal-selesai').val();

        var url = '<?= base_url('export-pemasukan'); ?>?'; // Base URL export excel
        
        if (filterType === 'tahun') { // Export filter tahun
            url += 'tahun=' + tahun;

        } else if (filterType === 'bulan') { // Export filter bulan
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;

        } else if (filterType === 'harian') { // Export filter harian
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank'); // Buka file excel
    });
</script>
<?= $this->endSection(); ?> // Tutup section JS
