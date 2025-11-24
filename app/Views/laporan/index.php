<?= $this->extend('template/index'); ?> // Memanggil template utama dari folder template/index
<?= $this->section('content'); ?> // Membuka section bernama 'content' untuk mengisi layout

<h1 class="h3 fw-bold text-dark"><?= $title; ?></h1> // Menampilkan judul halaman dengan style besar dan tebal
<p class="text-muted">Kelola data laporan</p> // Subjudul deskriptif halaman

<div class="row g-4"> // Membuat grid row dengan gap 4

    <div class="col-12 col-sm-6 col-lg-3"> // Kolom responsive untuk summary card
        <div class="card shadow-sm h-100"> // Card dengan shadow dan tinggi penuh
            <div class="card-body"> // Area isi card
                <p class="text-muted small mb-1">Total Berat Sampah</p> // Label card
                <h5 class="fw-bold" id="total-berat"></h5> // Nilai total berat (diisi via JS)
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Card kedua
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Masuk</p> // Label
                <h5 class="fw-bold text-success" id="total-uang-masuk"></h5> // Nilai pemasukan (JS)
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Card ketiga
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Uang Keluar</p> // Label
                <h5 class="fw-bold text-danger" id="total-uang-keluar"></h5> // Nilai pengeluaran (JS)
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3"> // Card keempat
        <div class="card shadow-sm h-100">
            <div class="card-body">
                <p class="text-muted small mb-1">Total Keuntungan / Kerugian</p> // Label
                <h5 class="fw-bold" id="total-keuntungan"></h5> // Nilai profit/loss (JS)
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0 mt-4"> // Card tabel laporan
    <div class="card-body"> // Isi card
        <div class="d-flex justify-content-between align-items-center mb-3"> // Header card
            <h5 class="card-title mb-0"><?= $title; ?></h5> // Judul tabel
            <button class="btn btn-success" id="export-excel"> // Tombol export Excel
                <i class="ti ti-file-export"></i> Export Excel
            </button>
        </div>

        <!-- Filter Laporan -->
        <div class="row mb-3"> // Row filter laporan
            <div class="col-md-3"> // Kolom filter utama
                <label class="form-label">Pilih Filter</label> // Label dropdown filter
                <select class="form-select" id="filter-type"> // Dropdown untuk memilih filter
                    <option value="tahun">Tahun</option> // Opsi filter berdasarkan tahun
                    <option value="bulan">Bulan</option> // Opsi filter berdasarkan bulan
                    <option value="harian">Harian</option> // Opsi filter harian
                </select>
            </div>

            <!-- Filter Tahun -->
            <div class="col-md-3" id="tahun-filter"> // Input angka untuk filter tahun
                <label class="form-label">Tahun</label>
                <input type="number" class="form-control" id="tahun"> // Input tahun
            </div>

            <!-- Filter Bulan -->
            <div class="col-md-6" id="bulan-filter" style="display: none;"> // Filter bulan (hidden default)
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Bulan</label> // Label bulan
                        <select class="form-select" id="bulan"> // Dropdown pilih bulan
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
                            $currentMonth = date('n'); // Mendapatkan bulan saat ini (1–12)

                            for ($i = 1; $i <= 12; $i++) { // Loop 12 bulan
                                $monthName = date('F', mktime(0, 0, 0, $i, 1)); // Mendapatkan nama bulan
                                $selected = ($i == $currentMonth) ? 'selected' : ''; // Auto-select bulan saat ini
                                echo '<option value="' . $i . '" ' . $selected . '>' . $monthName . '</option>'; // Output opsi bulan
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tahun</label> // Tahun untuk filter bulan
                        <input type="number" class="form-control" id="tahun-bulan"> // Input tahun bulan
                    </div>
                </div>
            </div>

            <!-- Filter Harian -->
            <div class="col-md-6" id="harian-filter" style="display: none;"> // Filter harian (hidden default)
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Mulai</label> // Label tanggal mulai
                        <input type="date" class="form-control" id="tanggal-mulai"> // Input date start
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Tanggal Selesai</label> // Label tanggal selesai
                        <input type="date" class="form-control" id="tanggal-selesai"> // Input date end
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive"> // Membuat tabel jadi responsive
            <table class="table table-bordered table-hover align-middle dataTable"> // Tabel data laporan
                <thead class="table-success"> // Header tabel warna hijau
                    <tr>
                        <th scope="col">No</th> // Nomor urut
                        <th scope="col">Periode</th> // Periode laporan
                        <th scope="col">Jumlah</th> // Jumlah sampah/berat
                        <th scope="col">Total Pemasukan</th> // Pendapatan
                        <th scope="col">Total Pengeluaran</th> // Pengeluaran
                        <th scope="col">Keuntungan / Kerugian</th> // Profit / Loss
                    </tr>
                </thead>
                <tbody> // Isi tabel akan di-render oleh JavaScript

                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection(); ?> // Menutup section konten

<?= $this->section('js'); ?> // Section khusus untuk JavaScript
<script>
    $('#tahun-bulan').val(new Date().getFullYear()); // Set input tahun-bulan ke tahun sekarang
    $('#tahun').val(new Date().getFullYear()); // Set input tahun ke tahun sekarang
    let tahun = new Date().getFullYear(); // Simpan tahun saat ini ke variabel
    loadLaporan('tahun', tahun); // Load laporan default berdasarkan tahun
    // Function untuk menampilkan data laporan
    function loadLaporan(filterType, tahun = null, bulan = null, tanggalMulai = null, tanggalSelesai = null) { // Mendefinisikan fungsi loadLaporan dengan parameter filter
        $.ajax({
            url: "<?= base_url('laporan/getLaporanData'); ?>", // URL endpoint untuk mengambil data laporan via AJAX
            type: "POST", // Menggunakan metode POST
            data: { // Data yang dikirim ke server
                filter_type: filterType, // Jenis filter (tahun/bulan/harian)
                tahun: tahun, // Parameter tahun
                bulan: bulan, // Parameter bulan
                tanggal_mulai: tanggalMulai, // Parameter tanggal mulai
                tanggal_selesai: tanggalSelesai // Parameter tanggal selesai
            },
            success: function(response) { // Callback saat request berhasil
                console.log(response); // Logging data untuk debugging

                // Update summary cards
                // Inisialisasi variabel untuk menyimpan total
                let totalBerat = 0; // Total berat sampah
                let totalUangMasuk = 0; // Total pendapatan
                let totalUangKeluar = 0; // Total pengeluaran
                let totalKeuntungan = 0; // Total keuntungan/kerugian

                // Pastikan response adalah array
                if (Array.isArray(response)) { // Mengecek apakah data yang diterima berbentuk array
                    // Lakukan looping terhadap setiap elemen dalam array
                    response.forEach(function(data) { // Iterasi setiap elemen dalam response
                        // Tambahkan nilai dari setiap elemen ke total
                        totalBerat += parseFloat(data.jumlah) || 0; // Menambahkan total berat
                        totalUangMasuk += parseFloat(data.total_pendapatan) || 0; // Menambahkan total pemasukan
                        totalUangKeluar += parseFloat(data.total_pengeluaran) || 0; // Menambahkan total pengeluaran
                        totalKeuntungan += parseFloat(data.total_keuntungan) || 0; // Menambahkan total keuntungan
                    });

                    // Format total uang
                    totalUangMasukFormatted = 'Rp ' + totalUangMasuk.toLocaleString('id-ID'); // Format pemasukan ke Rupiah
                    totalUangKeluarFormatted = 'Rp ' + totalUangKeluar.toLocaleString('id-ID'); // Format pengeluaran ke Rupiah
                    totalKeuntunganFormatted = 'Rp ' + totalKeuntungan.toLocaleString('id-ID'); // Format keuntungan ke Rupiah

                    // Update summary cards dengan total yang sudah dihitung
                    $('#total-berat').text(totalBerat.toLocaleString('id-ID') + ' kg'); // Menampilkan total berat
                    $('#total-uang-masuk').text(totalUangMasukFormatted); // Menampilkan total uang masuk
                    $('#total-uang-keluar').text(totalUangKeluarFormatted); // Menampilkan total uang keluar
                    $('#total-keuntungan').text(totalKeuntunganFormatted); // Menampilkan total keuntungan

                } else {
                    // Jika response bukan array, tampilkan nilai default
                    $('#total-berat').text('0 kg'); // Default berat
                    $('#total-uang-masuk').text('Rp 0'); // Default pemasukan
                    $('#total-uang-keluar').text('Rp 0'); // Default pengeluaran
                    $('#total-keuntungan').text('Rp 0'); // Default keuntungan
                }
                // $('#total-berat').text(response.jumlah);
                // $('#total-uang-masuk').text('Rp ' + response.total_pendapatan);
                // $('#total-uang-keluar').text('Rp ' + response.total_pengeluaran);
                // $('#total-keuntungan').text(response.total_keuntungan + ' Kg');
                // Kosongkan data tabel sebelum mengisi ulang
                $('table tbody').empty(); // Menghapus semua data dalam tbody

                // Isi data ke table
                $.each(response, function(i, item) { // Loop setiap item dalam response
                    let totalPendapatan = parseFloat(item.total_pendapatan) || 0; // Parsing pendapatan
                    let totalPengeluaran = parseFloat(item.total_pengeluaran) || 0; // Parsing pengeluaran
                    let totalKeuntungan = parseFloat(item.total_keuntungan) || 0; // Parsing keuntungan

                    // Append baris baru ke tabel
                    $('table tbody').append(`
                        <tr>
                            <td>${i+1}</td> // Nomor urut
                            <td>${item.periode}</td> // Periode laporan
                            <td>${item.jumlah}</td> // Jumlah sampah
                            <td>Rp ${totalPendapatan.toLocaleString('id-ID')}</td> // Total pendapatan
                            <td>Rp ${totalPengeluaran.toLocaleString('id-ID')}</td> // Total pengeluaran
                            <td>Rp ${totalKeuntungan.toLocaleString('id-ID')}</td> // Total keuntungan
                        </tr>
                    `);
                });
            },
            error: function(xhr, status, error) { // Callback ketika terjadi error
                console.error(error); // Menampilkan error di console
            }
        });
    }

    // Event handler saat filter type berubah
    $('#filter-type').change(function() {
        var filterType = $(this).val(); // Mengambil nilai filter yang dipilih

        // Sembunyikan semua filter
        $('#tahun-filter').hide(); // Sembunyikan filter tahun
        $('#bulan-filter').hide(); // Sembunyikan filter bulan
        $('#harian-filter').hide(); // Sembunyikan filter harian

        // Tampilkan filter yang sesuai
        if (filterType == 'tahun') { // Jika filter tahun
            $('#tahun-filter').show(); // Tampilkan input tahun
            loadLaporan('tahun', $('#tahun').val(), null, null, null); // Muat laporan berdasarkan tahun

        } else if (filterType == 'bulan') { // Jika filter bulan
            $('#bulan-filter').show(); // Tampilkan filter bulan
            loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null); // Muat laporan bulan

        } else if (filterType == 'harian') { // Jika filter harian
            $('#harian-filter').show(); // Tampilkan filter harian
            $('table tbody').empty(); // Kosongkan tabel karena belum ada data
        }
    });

    // Event handler ketika tahun diinput
    $('#tahun').keyup(function() { // Ketika input tahun berubah
        loadLaporan('tahun', $(this).val(), null, null, null); // Reload laporan tahun
    });

    // Event handler untuk filter bulan
    $('#bulan, #tahun-bulan').change(function() { // Ketika bulan/tahun bulan berubah
        loadLaporan('bulan', $('#tahun-bulan').val(), $('#bulan').val(), null, null); // Reload laporan bulan
    });

    // Event handler untuk filter harian
    $('#tanggal-selesai').change(function() { // Load ketika tanggal selesai dipilih
        loadLaporan('harian', null, null, $('#tanggal-mulai').val(), $('#tanggal-selesai').val()); // Load laporan harian
    });

    // Export Excel functionality
    $('#export-excel').click(function() { // Event klik tombol export
        var filterType = $('#filter-type').val(); // Ambil jenis filter
        var tahun = $('#tahun').val(); // Tahun filter
        var bulan = $('#bulan').val(); // Bulan filter
        var tahunBulan = $('#tahun-bulan').val(); // Tahun untuk filter bulan
        var tanggalMulai = $('#tanggal-mulai').val(); // Tanggal mulai filter hari
        var tanggalSelesai = $('#tanggal-selesai').val(); // Tanggal selesai filter hari

        var url = '<?= base_url('laporan/export'); ?>?'; // Base URL export

        // Tentukan parameter URL berdasarkan filter
        if (filterType === 'tahun') {
            url += 'tahun=' + tahun; // Tambahkan tahun
        } else if (filterType === 'bulan') {
            url += 'tahun=' + tahunBulan + '&bulan=' + bulan; // Tambahkan bulan & tahun
        } else if (filterType === 'harian') {
            url += 'tanggal_mulai=' + tanggalMulai + '&tanggal_selesai=' + tanggalSelesai; // Tambahkan tanggal
        }

        window.open(url, '_blank'); // Membuka URL export di tab baru
    });
</script>

<?= $this->endSection(); ?> // Menutup section JS
