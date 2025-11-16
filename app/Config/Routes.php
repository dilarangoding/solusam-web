<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'LoginController::index');
$routes->get('login', 'LoginController::index');
$routes->post('login', 'LoginController::attempLogin');
$routes->get('logout', 'LoginController::logout');
$routes->get('daftar', 'DaftarController::index');
$routes->post('register', 'DaftarController::register');

$routes->get('forgot-password', 'ForgotPasswordController::index');
$routes->post('forgot-password/send', 'ForgotPasswordController::sendResetLink');
$routes->get('reset-password/(:any)', 'ForgotPasswordController::resetPassword/$1');
$routes->post('reset-password/update', 'ForgotPasswordController::updatePassword');

// Google Login
$routes->get('auth/google', 'AuthController::redirectToGoogle');
$routes->get('auth/google/callback', 'AuthController::handleGoogleCallback');


$routes->get('dashboard', 'Dashboard::index', ['filter' => 'auth']);

// Sampah
$routes->group('sampah', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'SampahController::index');
    $routes->get('create', 'SampahController::create');
    $routes->post('store', 'SampahController::store');
    $routes->get('edit/(:num)', 'SampahController::edit/$1');
    $routes->post('delete', 'SampahController::delete');
});

// Klien
$routes->group('data-klien', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'DataKlienController::index');
    $routes->get('create', 'DataKlienController::create');
    $routes->post('store', 'DataKlienController::store');
    $routes->get('edit/(:num)', 'DataKlienController::edit/$1');
    $routes->post('delete', 'DataKlienController::delete');
});

// Penjualan
$routes->group('penjualan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PenjualanController::index');
    $routes->get('create', 'PenjualanController::create');
    $routes->post('sampah-ajax', 'PenjualanController::sampahAjax');
    $routes->post('store', 'PenjualanController::store');
    $routes->get('edit/(:num)', 'PenjualanController::edit/$1');
    $routes->post('delete', 'PenjualanController::delete');
    $routes->get('qrcode/(:num)', 'PenjualanController::showQrCode/$1');
    $routes->get('qrcode-image/(:num)', 'PenjualanController::generateQrCode/$1');
    $routes->get('qrcode-simple/(:num)', 'PenjualanController::generateQrCodeSimple/$1');
    $routes->get('test-qrcode', 'PenjualanController::testQrCode');
});

// Pembelian
$routes->group('pembelian', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'PembelianController::index');
    $routes->get('create', 'PembelianController::create');
    $routes->post('sampah-ajax', 'PembelianController::sampahAjax');
    $routes->post('store', 'PembelianController::store');
    $routes->get('edit/(:num)', 'PembelianController::edit/$1');
    $routes->post('delete', 'PembelianController::delete');
});

// Data Laporan
$routes->group('laporan', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'LaporanController::index');
    $routes->post('getLaporanData', 'LaporanController::getLaporanData');
    $routes->post('store', 'LaporanController::store');
    $routes->get('edit/(:num)', 'LaporanController::edit/$1');
    $routes->post('delete', 'LaporanController::delete');
    $routes->get('pemasukan', 'LaporanController::pemasukan');
    $routes->get('export', 'LaporanController::exportLaporan');
});

// Pemasukan dan Pengeluaran
$routes->get('pemasukan', 'LaporanController::pemasukan', ['filter' => 'auth']);
$routes->get('pengeluaran', 'LaporanController::pengeluaran', ['filter' => 'auth']);
$routes->post('getDataInOut', 'LaporanController::getDataInOut', ['filter' => 'auth']);
$routes->get('export-pemasukan', 'LaporanController::exportPemasukan', ['filter' => 'auth']);
$routes->get('export-pengeluaran', 'LaporanController::exportPengeluaran', ['filter' => 'auth']);


// Metode Bayar
$routes->group('metode-bayar', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'MetodePembayaranController::index');
    $routes->get('create', 'MetodePembayaranController::create');
    $routes->post('store', 'MetodePembayaranController::store');
    $routes->get('edit/(:num)', 'MetodePembayaranController::edit/$1');
    $routes->post('delete', 'MetodePembayaranController::delete');
});

// Reset Password
$routes->group('reset', ['filter' => 'auth'], function ($routes) {
    $routes->get('/', 'ResetController::index');
    $routes->post('update', 'ResetController::update');
});

// Public Routes (tidak perlu login)
$routes->group('public', function ($routes) {
    $routes->get('transaksi/(:num)', 'PublicTransaksiController::detail/$1');
    $routes->get('qrcode/(:num)', 'PublicTransaksiController::generateQrCode/$1');
});
