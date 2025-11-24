<?= $this->extend('template/index'); ?> // Memanggil layout utama template/index
<?= $this->section('content'); ?> // Membuka section 'content' agar isi halaman ditempatkan di layout

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Menampilkan judul halaman
<p class="text-muted">Kelola data laporan pengeluaran</p> // Sub-judul halaman

<div class="row g-4"> // Row untuk card summary
    <div class="col-12 col-sm-6 col-lg-3"> // Kolom card pertama
        <div class="card shadow-sm h-100"> // Card pertama
            <div class="card-body"> // Body card
                <p class="text-muted small mb-1"><i class="ti ti-chart-line text-success"></i> Total Pengeluaran</p> // Label
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> // Tempat menampilkan total uang
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Card kedua
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p> // Label berat sampah
                <h5 class="fw-bold" id="total-berat"></h5> // Tempat total berat
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4"> // Card utama tabel laporan
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-center mb-3"> // Header card
            <h5 class="card-title mb-0"><?= $title; ?></h5> // Menampilkan judul lagi
            <button class="btn btn-danger" id="export-excel"> // Tombol export excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3"> // Row filter bagian atas

            <div class="col-md-3">
                <label class="form-label">Pilih Filter</label> // Label filter utama
                <select class="form-select" id="filter-type"> // Dropdown memilih jenis filter
                    <option value="tahun">Tahun</option> // Filter tahunan
                    <option value="bulan">Bulan</option> // Filter bulanan
                    <option value="harian">Harian</option> // Filter harian
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> // Input filter tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> // Input tahun
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> // Area filter bulan awalnya disembunyikan
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label> // Input bulan
                        <select class="form-select" id="bulan"> // Dropdown bulan
                        <?php
                            $currentMonth = date('n'); // Mendapatkan bulan saat ini (1-12)

                            for ($i = 1; $i <= 12; $i++) { // Loop bulan 1-12
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Ambil nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Tandai bulan sekarang
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>'; // Tampilkan opsi bulan
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label> // Input tahun untuk filter bulan
                        <input type="number" class="form-control" id="tahun-bulan"> // Input tahun filter bulan
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> // Filter harian, juga disembunyikan default
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label> // Input tanggal awal
                        <input type="date" class="form-control" id="tanggal-mulai">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label> // Input tanggal akhir
                        <input type="date" class="form-control" id="tanggal-selesai">
                    </div>
                </div>
            </div>

        </div>

        <div class="table-responsive"> // Wrapper tabel agar bisa scroll
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel laporan
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
                <tbody> // Data tabel akan diisi AJAX
                </tbody>
            </table>
        </div>

    </div>
</div>

<?= $this->endSection(); ?> // Menutup section content

<?= $this->section('js'); ?> // Membuka section JS
<script>
    $('#tahun-bulan').val(new Date().getFullYear()); // Set default tahun filter bulan = tahun sekarang
    $('#tahun').val(new Date().getFullYear()); // Set tahun default filter tahunan
    let tahun = new Date().getFullYear(); // Simpan tahun sekarang ke variabel
    loadLaporan('tahun', tahun); // Load laporan default = berdasarkan tahun

    // Function untuk menampilkan data laporan
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {
        $.ajax({
            url: "<?= base_url('getDataInOut'); ?>", // Endpoint backend
            type: "POST", // Method
            data: { // Data dikirim ke backend
                filter_type: filterType,
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                jenis: 'in' // Jenis laporan (pengeluaran)
            },
            success: function(response) { // Jika request berhasil
                console.log(response); // Debug hasil response

                let totalBerat = 0; // Variabel total berat
                let totalUangMasuk = 0; // Variabel total pengeluaran

                if (Array.isArray(response)) { // Pastikan response adalah array
                    response.forEach(function(data) { // Loop data
                        totalBerat += parseFloat(data.jumlah) || 0; // Tambah berat ke total
                        totalUangMasuk += parseFloat(data.total_pengeluaran) || 0; // Tambah total uang
                    });

                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); // Format uang

                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); // Tampilkan total berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted); // Tampilkan total uang
                } else {
                    $('#total-berat').text('0 kg'); // Jika tidak array, default 0
                    $('#total-uang-masuk').text('Rp 0');
                }

                $('table tbody').empty(); // Kosongkan tabel sebelum isi data baru

                $.each(response, function(i, item) { // Loop data tabel
                    let totalPendapatan = parseFloat(item.total_pengeluaran) || 0;
                    let hargaBeli = parseFloat(item.harga_beli) || 0;

                    // Append baris baru ke tabel
                    $('table tbody').append(`
                        <tr>
                            <td>${i+1}</td> 
                            <td>${item.tanggal}</td>
                            <td>${item.nama_sampah}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp ${hargaBeli.toLocaleString('id-ID')}</td>
                            <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td>
                        </tr>
                    `);
                });
            },
            error: function(xhr, status, error) { // Jika ERROR server
                console.error(error); // Tampilkan error
            }
        });
    }

    // Jika user memilih jenis filter
    $('#filter-type').change(function() {
        var filterType = $(this).val(); // Ambil value filter

        $('#tahun-filter').hide(); // Sembunyikan semua filter
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') { // Jika filter tahunan
            $('#tahun-filter').show(); // Tampilkan input tahun
            loadLaporan('tahun', $('#tahun').val());

        } else if (filterType == 'bulan') { // Filter bulanan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());

        } else if (filterType == 'harian') { // Filter harian
            $('#harian-filter').show();
            $('table tbody').empty(); // Kosongkan tabel dulu
        }
    });

    // Ketika user mengetik tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val());
    });

    // Ketika ganti bulan atau tahun (filter bulanan)
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
    });

    // Ketika ganti tanggal selesai (filter harian)
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // Tombol export excel
    $('#export-excel').click(function() {
        var filterType = $('#filter-type').val();
        var tahun = $('#tahun').val();
        var bulan = $('#bulan').val();
        var tahunBulan = $('#tahun-bulan').val();
        var tanggalMulai = $('#tanggal-mulai').val();
        var tanggalSelesai = $('#tanggal-selesai').val();

        var url = '<?= base_url('export-pengeluaran'); ?>?'; // URL export

        if (filterType === 'tahun') { // Export tahunan
            url += 'tahun=' + tahun;
        } else if (filterType === 'bulan') { // Export bulanan
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;
        } else if (filterType === 'harian') { // Export harian
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank'); // Buka file export
    });
</script>
<?= $this->endSection(); ?> // Menutup section JS
