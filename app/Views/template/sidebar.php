<?php
// Fungsi untuk menandai menu sidebar aktif
function isActive($url, $strict = false)
{
    $uri = service('uri'); // Ambil instance URI CodeIgniter
    $segment = $uri->getSegment(1); // Ambil segmen pertama dari URL
    $active = 'bg-light text-active rounded'; // Class CSS untuk menu aktif
    if ($strict) {
        return $segment === $url ? $active : ''; // Cek jika mode strict
    }
    return $segment === $url ? $active : ''; // Default cek segmen pertama
}
?>

<div id="sidebar" class="p-3">
    <!-- Header Sidebar: Logo + Nama Aplikasi -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-2">
            <div style="background-color: white; border-radius: 50%; padding: 6px; display: inline-block;">
                <img src="<?= base_url('assets/img/logosolus.png') ?>" 
                     alt="Logo Solusam"
                     style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
            </div>
            <div class="text-light">
                <h1 class="h5 fw-bold mb-0">SOLUSAM</h1>
                <p class="small mb-0">Solusi Sampah</p>
            </div>
        </div>
        <!-- Tombol close sidebar untuk mobile -->
        <button class="btn btn-sm d-lg-none" onclick="toggleSidebar()">✖</button>
    </div>

    <!-- Menu Sidebar -->
    <nav class="nav flex-column small">
        <!-- Dashboard -->
        <a href="<?= base_url('dashboard') ?>" class="nav-link py-2 <?= isActive('dashboard') ?>">
            <i class="ti ti-home"></i> Dashboard
        </a>

        <!-- Menu Transaksi -->
        <p class="text-uppercase fw-semibold mt-3 mb-1">Transaksi</p>
        <a href="<?= base_url('penjualan') ?>" class="nav-link py-2 <?= isActive('penjualan') ?>">
            <i class="ti ti-shopping-bag-plus"></i> Penjualan
        </a>
        <a href="<?= base_url('pembelian') ?>" class="nav-link py-2 <?= isActive('pembelian') ?>">
            <i class="ti ti-shopping-bag-minus"></i> Pembelian
        </a>

        <!-- Menu Data -->
        <p class="text-uppercase fw-semibold mt-3 mb-1">Data</p>
        <!-- <a href="<?= base_url('metode-bayar') ?>" class="nav-link py-2 <?= isActive('metode-bayar') ?>"><i class="ti ti-cash"></i> Metode Bayar</a> -->
        <a href="<?= base_url('sampah') ?>" class="nav-link py-2 <?= isActive('sampah') ?>">
            <i class="ti ti-trash"></i> Data Sampah
        </a>
        <a href="<?= base_url('data-klien') ?>" class="nav-link py-2 <?= isActive('data-klien') ?>">
            <i class="ti ti-users"></i> Data Klien
        </a>
        <a href="<?= base_url('pemasukan') ?>" class="nav-link py-2 <?= isActive('pemasukan') ?>">
            <i class="ti ti-moneybag-plus"></i> Data Pemasukan
        </a>
        <a href="<?= base_url('pengeluaran') ?>" class="nav-link py-2 <?= isActive('pengeluaran') ?>">
            <i class="ti ti-moneybag-minus"></i> Data Pengeluaran
        </a>

        <!-- Menu Lainnya -->
        <p class="text-uppercase fw-semibold mt-3 mb-1">Lainnya</p>
        <a href="<?= base_url('laporan') ?>" class="nav-link py-2 <?= isActive('laporan') ?>">
            <i class="ti ti-file"></i> Data Laporan
        </a>
        <a href="<?= base_url('reset') ?>" class="nav-link py-2 <?= isActive('reset') ?>">
            <i class="ti ti-key"></i> Ganti Password
        </a>
    </nav>

    <!-- Tombol Logout -->
    <a href="<?= base_url('logout') ?>" class="btn btn-link text-light text-decoration-none mt-4">
        <i class="ti ti-logout-2"></i> Logout
    </a>
</div>
