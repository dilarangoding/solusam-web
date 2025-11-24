<?= $this->extend('template/index'); ?> // Memanggil template utama
<?= $this->section('content'); ?> // Membuka section konten utama

<!-- Judul halaman -->
<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> <!-- Menampilkan judul dari controller -->
<p class="text-muted">Kelola data laporan pengeluaran</p> <!-- Subjudul deskripsi -->

<!-- Row untuk card summary -->
<div class="row g-4">

    <!-- Card Total Pengeluaran -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">
                    <i class="ti ti-chart-line text-success"></i> Total Pengeluaran
                </p> <!-- Label card -->
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> <!-- Total pengeluaran akan diisi oleh JS -->
            </div>
        </div>
    </div>

    <!-- Card Total Berat Sampah -->
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p> <!-- Label card -->
                <h5 class="fw-bold" id="total-berat"></h5> <!-- Total berat akan diisi JS -->
            </div>
        </div>
    </div>
</div>

<!-- Card tabel utama -->
<div class="card shadow-sm border-0 mt-4">
    <div class="card-body">

        <!-- Header card -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0"><?= $title; ?></h5> <!-- Judul card -->
            <button class="btn btn-danger" id="export-excel"> <!-- Tombol export -->
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter laporan -->
        <div class="row mb-3">

            <!-- Dropdown pilih filter -->
            <div class="col-md-3">
                <label class="form-label">Pilih Filter</label> <!-- Label filter -->
                <select class="form-select" id="filter-type"> <!-- Select jenis filter -->
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> <!-- Filter tahun -->
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> <!-- Input tahun -->
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> <!-- Tersembunyi awal -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan"> <!-- Pilih bulan -->
                        <?php
                            $currentMonth = date('n'); // Mendapatkan bulan saat ini
                            for ($i = 1; $i <= 12; $i++) { // Loop 12 bulan
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Ambil nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Tandai bulan sekarang
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>'; // Output option
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-bulan"> <!-- Input tahun untuk filter bulan -->
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> <!-- Tersembunyi awal -->
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai"> <!-- Input tanggal mulai -->
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai"> <!-- Input tanggal selesai -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Table data -->
        <div class="table-responsive"> <!-- Agar table bisa di-scroll -->
            <table class="table table-bordered table-hover align-middle dataTable">
                <thead class="table-success"> <!-- Header tabel -->
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
                    <!-- Data akan diisi oleh JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> <!-- Menutup section konten -->



<?= $this->section('js'); ?> <!-- Section untuk JavaScript -->
<script>

    $('#tahun-bulan').val(new Date().getFullYear()); // Set input tahun-bulan menjadi tahun sekarang
    $('#tahun').val(new Date().getFullYear()); // Set filter tahun menjadi tahun sekarang
    let tahun = new Date().getFullYear(); // Simpan tahun sekarang ke variabel
    loadLaporan('tahun', tahun); // Load laporan default berdasarkan tahun berjalan

    // Function untuk mengambil & menampilkan data laporan
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {

        $.ajax({
            url: "<?= base_url('getDataInOut'); ?>", // URL endpoint backend
            type: "POST", // Kirim data via POST
            data: { // Data yang dikirim ke backend
                filter_type: filterType,
                tahun: tahun,
                bulan: bulan,
                tanggal_mulai: tanggalMulai,
                tanggal_selesai: tanggalSelesai,
                jenis: 'in' // Jenis laporan: pengeluaran
            },
            success: function(response) { // Jika request berhasil
                console.log(response); // Debug data dari server

                // Inisialisasi nilai total
                let totalBerat = 0;
                let totalUangMasuk = 0;

                if (Array.isArray(response)) { // Pastikan respons berupa array
                    response.forEach(function(data) { // Loop setiap item
                        totalBerat += parseFloat(data.jumlah) || 0; // Tambah total berat
                        totalUangMasuk += parseFloat(data.total_pengeluaran) || 0; // Tambah total uang
                    });

                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); // Format rupiah

                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); // Update card berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted); // Update card total uang
                } else {
                    $('#total-berat').text('0 kg'); // Jika data kosong
                    $('#total-uang-masuk').text('Rp 0');
                }

                $('table tbody').empty(); // Kosongkan tabel

                // Isi tabel dengan data dari server
                $.each(response, function(i, item) {
                    let totalPendapatan = parseFloat(item.total_pengeluaran) || 0; // Total pengeluaran
                    let hargaBeli = parseFloat(item.harga_beli) || 0; // Harga pembelian
                    $('table tbody').append(`
                        <tr>
                            <td>${i+1}</td>
                            <td>${item.tanggal}</td>
                            <td>${item.nama_sampah}</td>
                            <td>${item.jumlah}</td>
                            <td>Rp ${hargaBeli.toLocaleString('id-ID')}</td>
                            <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td>
                        </tr>
                    `); // Tambahkan row
                });

            },
            error: function(xhr, status, error) { // Jika terjadi error
                console.error(error); // Log error
            }
        });
    }

    // Event pada perubahan filter type
    $('#filter-type').change(function() {
        var filterType = $(this).val(); // Ambil value filter

        $('#tahun-filter').hide(); // Sembunyikan semua filter
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') { // Jika pilih tahun
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val());
        } else if (filterType == 'bulan') { // Jika pilih bulan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
        } else if (filterType == 'harian') { // Jika pilih harian
            $('#harian-filter').show();
            $('table tbody').empty(); // Kosongkan tabel dulu
        }
    });

    // Event input tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val());
    });

    // Event filter bulan
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val());
    });

    // Event filter harian
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // Fungsi export Excel
    $('#export-excel').click(function() {
        var filterType = $('#filter-type').val(); // Ambil jenis filter
        var tahun = $('#tahun').val(); // Tahun filter
        var bulan = $('#bulan').val(); // Bulan filter
        var tahunBulan = $('#tahun-bulan').val(); // Tahun filter bulan
        var tanggalMulai = $('#tanggal-mulai').val(); // Tanggal mulai
        var tanggalSelesai = $('#tanggal-selesai').val(); // Tanggal selesai

        var url = '<?= base_url('export-pengeluaran'); ?>?'; // Base URL export

        if (filterType === 'tahun') { // Export berdasarkan tahun
            url += 'tahun=' + tahun;
        } else if (filterType === 'bulan') { // Export berdasarkan bulan
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;
        } else if (filterType === 'harian') { // Export berdasarkan tanggal
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank'); // Buka di tab baru
    });
</script>

<?= $this->endSection(); ?> <!-- Menutup section JS -->
