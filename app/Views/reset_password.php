<!DOCTYPE html>
<html lang="id"> <!-- Menentukan bahasa dokumen adalah bahasa Indonesia -->
<head>
    <meta charset="UTF-8"> <!-- Set karakter encoding ke UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Responsive untuk semua device -->
    <title>Reset Password - SOLUSAM</title> <!-- Judul halaman -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> <!-- Load Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet"> <!-- Load ikon Bootstrap -->
    <style>
        .input-group-text {
            background-color: transparent; /* Warna latar belakang tombol mata transparan */
            border-left: none; /* Hapus border kiri */
            cursor: pointer; /* Pointer mouse berubah saat hover */
        }
        .form-control:focus {
            box-shadow: none; /* Hilangkan shadow saat fokus */
            border-color: #198754; /* Warna border hijau saat fokus */
        }
        .input-group .form-control {
            border-right: none; /* Hapus border kanan input agar menyatu dengan tombol */
        }
        .input-group:focus-within .input-group-text {
            border-color: #198754; /* Border tombol ikut hijau saat fokus pada input */
        }
    </style>
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100"> <!-- Body dengan background light, center content vertikal & horizontal, tinggi 100vh -->

<div class="card shadow-sm p-4" style="width: 380px;"> <!-- Card utama dengan padding dan shadow, lebar tetap -->
    <div class="text-center mb-3"> <!-- Container untuk logo & judul, teks center -->
        <div class="rounded-circle bg-success text-white mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 28px;">S</div> <!-- Logo berbentuk lingkaran dengan huruf S -->
        <h4 class="fw-bold">SOLUSAM</h4> <!-- Nama aplikasi -->
        <p class="text-muted">Solusi Sampah - Sistem Manajemen Sampah</p> <!-- Deskripsi aplikasi -->
    </div>

    <h5 class="text-center mb-3">Reset Password</h5> <!-- Judul form -->

    <?php if(session()->getFlashdata('error')): ?> <!-- Cek apakah ada pesan error flashdata -->
        <div class="alert alert-danger py-2"><?= session()->getFlashdata('error'); ?></div> <!-- Tampilkan pesan error -->
    <?php endif; ?>

    <form action="<?= base_url('reset-password/update'); ?>" method="post"> <!-- Form untuk reset password -->
        <input type="hidden" name="token" value="<?= $token; ?>"> <!-- Input hidden untuk token verifikasi -->

        <!-- Password Baru -->
        <div class="mb-3"> <!-- Margin bawah untuk spacing -->
            <label for="password" class="form-label">Password Baru</label> <!-- Label password baru -->
            <div class="input-group"> <!-- Grup input & tombol show/hide -->
                <input type="password" class="form-control" id="password" name="password" placeholder="Masukkan password baru" required> <!-- Input password baru -->
                <span class="input-group-text" id="togglePassword1" onclick="togglePassword('password', 'togglePassword1')"> <!-- Tombol show/hide password -->
                    <i class="bi bi-eye-slash"></i> <!-- Ikon mata tertutup awalnya -->
                </span>
            </div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-3"> <!-- Margin bawah untuk spacing -->
            <label for="confirm_password" class="form-label">Konfirmasi Password</label> <!-- Label konfirmasi password -->
            <div class="input-group"> <!-- Grup input & tombol show/hide -->
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="Ulangi password baru" required> <!-- Input konfirmasi password -->
                <span class="input-group-text" id="togglePassword2" onclick="togglePassword('confirm_password', 'togglePassword2')"> <!-- Tombol show/hide password -->
                    <i class="bi bi-eye-slash"></i> <!-- Ikon mata tertutup awalnya -->
                </span>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100">Perbarui Password</button> <!-- Tombol submit form -->
    </form>

    <div class="text-center mt-3"> <!-- Link kembali ke login -->
        <a href="<?= base_url('login'); ?>" class="text-decoration-none">Kembali ke Login</a>
    </div>
</div>

<script>
function togglePassword(inputId, toggleId) { // Fungsi toggle show/hide password
    const input = document.getElementById(inputId); // Ambil elemen input berdasarkan id
    const icon = document.querySelector(`#${toggleId} i`); // Ambil ikon di tombol
    if (input.type === "password") { // Jika tipe input password
        input.type = "text"; // Ubah menjadi teks (tampil)
        icon.classList.replace("bi-eye-slash", "bi-eye"); // Ubah ikon menjadi mata terbuka
    } else {
        input.type = "password"; // Ubah kembali menjadi password (sembunyi)
        icon.classList.replace("bi-eye", "bi-eye-slash"); // Ubah ikon menjadi mata tertutup
    }
}
</script>

</body>
</html>
