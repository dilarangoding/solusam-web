<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" /> <!-- Set encoding UTF-8 -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0" /> <!-- Responsif untuk mobile -->
    <title><?= $title; ?></title> <!-- Judul halaman dinamis -->
    
    <!-- Tabler Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.35.0/dist/tabler-icons.min.css">
    
    <!-- CSS custom -->
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    
    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/datatable-bs5/dataTables.bootstrap5.css') ?>" />

    <style>
        /* Custom inline CSS tambahan jika diperlukan */
    </style>
</head>

<body class="bg-light">
    <div class="d-flex">
        <!-- Sidebar -->
        <?= $this->include('template/sidebar'); ?> <!-- Memasukkan sidebar dari file terpisah -->

        <!-- Main Content -->
        <div id="content" class="flex-grow-1 d-flex flex-column">
            <!-- Header -->
            <header class="d-flex justify-content-between align-items-center shadow-sm px-3 py-2 bg-white">
                <!-- Tombol open sidebar untuk mobile -->
                <button class="btn btn-sm d-lg-none" onclick="toggleSidebar()">☰</button>
                <h2 class="h6 fw-semibold mb-0">SOLUSAM Dashboard</h2> <!-- Judul header -->
            </header>

            <!-- Content utama -->
            <main class="p-4 overflow-auto">
                <?= $this->renderSection('content'); ?> <!-- Section content akan diganti dengan view spesifik -->
            </main>
        </div>
    </div>

    <!-- JS Libraries -->
    <script src="<?= base_url('assets/js/jquery.js') ?>"></script>
    <script src="<?= base_url('assets/js/sweetalert2.js') ?>"></script>
    <script src="<?= base_url('assets/datatable-bs5/dataTables.js') ?>"></script>
    <script src="<?= base_url('assets/datatable-bs5/dataTables.bootstrap5.js') ?>"></script>

    <script>
        // Menampilkan SweetAlert jika ada flashdata session
        <?php if (session('title')): ?>
            Swal.fire({
                title: "<?= session('title') ?>",
                text: '<?= session('text') ?>',
                icon: "<?= session('icon') ?>",
                showConfirmButton: false,
                timer: 2000
            })
        <?php endif ?>

        // Fungsi toggle sidebar (untuk mobile)
        function toggleSidebar() {
            document.getElementById("sidebar").classList.toggle("show");
        }

        // Inisialisasi DataTable untuk semua tabel dengan class 'dataTable'
        $(document).ready(function() {
            $('.dataTable').DataTable();
        });
    </script>

    <?= $this->renderSection('js'); ?> <!-- Section JS untuk masing-masing view -->
</body>

</html>
