<?= $this->extend('template/index'); ?> // Menggunakan template utama "index"
<?= $this->section('content'); ?> // Membuka section "content" agar konten ini dimasukkan ke layout

<div class="card border-success mb-4 mt-4"> // Card utama dengan border warna hijau dan margin atas/bawah
    <div class="card-header bg-success bg-opacity-10 text-success fw-bold"> // Header card dengan background hijau transparan
        + Form <?= $title; ?> // Menampilkan judul form sesuai variabel $title
    </div>
    <div class="card-body"> // Bagian isi card
        <form action="<?= base_url('metode-bayar/store') ?>" method="POST"> // Form dengan method POST menuju URL store
            <div class="row g-3"> // Grid row dengan gap antar elemen 3

                <input
                    type="hidden" // Input hidden agar tidak terlihat di form
                    name="id" // Nama field yang dikirim ke controller
                    class="form-control" // Class styling bootstrap
                    required // Wajib diisi (meski hidden, tetap validasi)
                    value="<?= $data['id'] ?>"> // Mengisi nilai ID dari data untuk proses update

                <div class="col-md-12"> // Kolom lebar penuh
                    <label class="form-label">Metode</label> // Label input untuk nama metode
                    <input
                        type="text" // Input teks
                        name="nama_sampah" // Nama field (Catatan: mungkin seharusnya "nama", tapi ini tetap kita biarkan sesuai kode asli)
                        class="form-control" // Styling bootstrap
                        value="<?= $data['nama'] ?>" // Menampilkan nilai nama metode untuk diedit
                        required> // Input wajib diisi
                </div>

                <!-- Tombol Aksi -->
                <div class="mt-4 d-flex gap-2"> // Container tombol dengan margin atas dan jarak antar tombol
                    <button type="submit" class="btn btn-success"> // Tombol submit berwarna hijau
                        Simpan // Teks tombol
                    </button>
                    <a href="<?= base_url('metode-bayar') ?>" class="btn btn-secondary"> // Tombol kembali ke halaman metode bayar
                        Batal // Teks tombol batal
                    </a>
                </div>
            </div>
        </form> // Penutup form
    </div>
</div>

<?= $this->endSection(); ?> // Menutup section content
