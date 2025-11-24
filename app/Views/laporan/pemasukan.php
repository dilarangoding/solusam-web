<?= $this->extend('template/index'); ?> // Meng-extend template utama
<?= $this->section('content'); ?> // Membuka section konten

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Menampilkan judul halaman
<p class="text-muted">Kelola data laporan pendapatan</p> // Subjudul halaman

<div class="row g-4"> // Row untuk card ringkasan
    <div class="col-12 col-sm-6 col-lg-3"> // Kolom card
        <div class="card shadow-sm h-100"> // Card tampilan
            <div class="card-body"> // Isi card
                <p class="text-muted small mb-1"><i class="ti ti-chart-line text-success"></i> Total Pemasukan</p> // Label
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> // Total pemasukan dinamis
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Kolom card kedua
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Berat Sampah</p> // Label berat sampah
                <h5 class="fw-bold" id="total-berat"></h5> // Total berat sampah dinamis
            </div>
        </div>
    </div>
</div>


<div class="card shadow-sm border-0 mt-4"> // Card utama untuk tabel laporan
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3"> // Header card
            <h5 class="card-title mb-0"><?= $title; ?></h5> // Judul card
            <button class="btn btn-success" id="export-excel"> // Tombol export Excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3"> // Row filter
            <div class="col-md-3"> // Kolom filter utama
                <label class="form-label">Pilih Filter</label>
                <select class="form-select" id="filter-type"> // Dropdown jenis filter
                    <option value="tahun">Tahun</option>
                    <option value="bulan">Bulan</option>
                    <option value="harian">Harian</option>
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> // Input filter tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> // Input tahun
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> // Filter bulan disembunyikan dulu
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label>
                        <select class="form-select" id="bulan"> // Dropdown bulan
                        <?php
                            $currentMonth = date('n'); // Mendapatkan bulan saat ini 1-12

                            for ($i = 1; $i <= 12; $i++) { // Loop 12 bulan
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Menandai bulan sekarang
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>'; // Cetak opsi bulan
                            }
                        ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label>
                        <input type="number" class="form-control" id="tahun-bulan"> // Input tahun untuk bulan
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> // Filter harian disembunyikan dulu
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label>
                        <input type="date" class="form-control" id="tanggal-mulai"> // Input tanggal mulai
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label>
                        <input type="date" class="form-control" id="tanggal-selesai"> // Input tanggal selesai
                    </div>
                </div>
            </div>
        </div>


        <div class="table-responsive"> // Wrapper table responsif
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel data
                <thead class="table-success"> // Header tabel warna hijau
                    <tr>
                        <th scope="col">No</th> // Kolom nomor
                        <th scope="col">Tanggal</th> // Kolom tanggal
                        <th scope="col">Nama Sampah</th> // Kolom nama sampah
                        <th scope="col">Jumlah (Kg)</th> // Kolom jumlah
                        <th scope="col">Harga</th> // Kolom harga
                        <th scope="col">Total</th> // Kolom total pendapatan
                    </tr>
                </thead>
                <tbody> // Isi tabel
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection(); ?> // Menutup section konten


<?= $this->section('js'); ?> // Membuka section javascript
<script>
    $('#tahun-bulan').val(new Date().getFullYear()); // Set tahun default untuk filter bulan
    $('#tahun').val(new Date().getFullYear()); // Set tahun default untuk filter tahun
    let tahun = new Date().getFullYear(); // Simpan tahun saat ini
    loadLaporan('tahun', tahun); // Load laporan berdasarkan tahun saat halaman dibuka

    // Function untuk menampilkan data laporan
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) {
        $.ajax({
            url: "<?= base_url('getDataInOut'); ?>", // Endpoint API untuk ambil data pemasukan/pengeluaran
            type: "POST", // Metode POST
            data: {
                filter_type: filterType, // Menentukan filter
                tahun: tahun, // Tahun
                bulan: bulan, // Bulan
                tanggal_mulai: tanggalMulai, // Tanggal mulai
                tanggal_selesai: tanggalSelesai, // Tanggal selesai
                jenis: 'out' // Jenis data: 'out' = pemasukan
            },
            success: function(response) { // Ketika berhasil
                console.log(response); // Debug log

                // Inisialisasi total
                let totalBerat = 0; // Total berat sampah
                let totalUangMasuk = 0; // Total pendapatan

                // Jika response array
                if (Array.isArray(response)) {
                    response.forEach(function(data) { // Loop data
                        totalBerat += parseFloat(data.jumlah) || 0; // Menambah berat
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0; // Menambah pendapatan
                    });

                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); // Format Rupiah

                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); // Update card berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted); // Update card pemasukan

                } else {
                    $('#total-berat').text('0 kg'); // Default jika kosong
                    $('#total-uang-masuk').text('Rp 0'); // Default jika kosong
                }

                $('table tbody').empty(); // Kosongkan tabel

                // Isi tabel
                $.each(response, function(i, item) {
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0; // Hitung total
                    let hargaJual = parseFloat(item.harga_jual) || 0; // Harga jual
                    $('table tbody').append(`
                            <tr>
                                <td>${i+1}</td> // Nomor urut
                                <td>${item.tanggal}</td> // Tanggal transaksi
                                <td>${item.nama_sampah}</td> // Nama sampah
                                <td>${item.jumlah}</td> // Berat
                                <td>Rp ${hargaJual.toLocaleString('id-ID')}</td> // Harga satuan
                                <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td> // Total pendapatan
                            </tr>
                        `);
                });

            },
            error: function(xhr, status, error) {
                console.error(error); // Log error
            }
        });
    }

    // Event handler saat jenis filter diganti
    $('#filter-type').change(function() {
        var filterType = $(this).val(); // Ambil value terpilih

        $('#tahun-filter').hide(); // Sembunyikan semua
        $('#bulan-filter').hide();
        $('#harian-filter').hide();

        if (filterType == 'tahun') { // Tampilkan filter tahun
            $('#tahun-filter').show();
            loadLaporan('tahun', $('#tahun').val(), null, null, null);

        } else if (filterType == 'bulan') { // Filter bulan
            $('#bulan-filter').show();
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null);

        } else if (filterType == 'harian') { // Filter harian
            $('#harian-filter').show();
            $('table tbody').empty(); // Kosongkan tabel sebelum pilih tanggal
        }
    });

    // Input tahun
    $('#tahun').keyup(function() {
        loadLaporan('tahun', $(this).val(), null, null, null); // Reload laporan
    });

    // Input bulan dan tahun bulan
    $('#bulan, #tahun-bulan').change(function() {
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null);
    });

    // Input tanggal selesai
    $('#tanggal-selesai').change(function() {
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val());
    });

    // Export Excel
    $('#export-excel').click(function() {
        var filterType = $('#filter-type').val(); // Ambil filter
        var tahun = $('#tahun').val(); // Tahun filter
        var bulan = $('#bulan').val(); // Bulan filter
        var tahunBulan = $('#tahun-bulan').val(); // Tahun untuk bulan
        var tanggalMulai = $('#tanggal-mulai').val(); // Tgl mulai
        var tanggalSelesai = $('#tanggal-selesai').val(); // Tgl selesai

        var url = '<?= base_url('export-pemasukan'); ?>?'; // Base URL export
        
        if (filterType === 'tahun') {
            url += 'tahun=' + tahun;
        } else if (filterType === 'bulan') {
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan;
        } else if (filterType === 'harian') {
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai;
        }

        window.open(url, '_blank'); // Buka file hasil export
    });
</script>
<?= $this->endSection(); ?> // Menutup section js
