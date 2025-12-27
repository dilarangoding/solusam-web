<?php

namespace App\Controllers;

/**
 * Class Dashboard
 *
 * Controller ini bertanggung jawab
 * untuk menampilkan halaman dashboard user
 */
class Dashboard extends BaseController
{
    /**
     * Method index()
     *
     * Method utama yang dipanggil saat user membuka halaman dashboard
     * Mengambil data transaksi dan mengirimkannya ke view
     */
    public function index()
    {
        // Membuat instance model Transaksi
        $transaksi = new \App\Models\Transaksi();
        // Mengambil bulan saat ini (format angka 01–12)
        $bulan = date('m');
        // Mengambil tahun saat ini (format YYYY)
        $tahun = date('Y');
        /**
        * Mengambil transaksi terakhir user
        * berdasarkan clientId yang tersimpan di session
        */
        $lastTransaksi = $transaksi->getLastTransaction(session('clientId'), $bulan);
          /**
         * Mengambil ringkasan transaksi pada bulan dan tahun berjalan
         * berdasarkan clientId user
         */
        $ringkasanBulan = $transaksi->getRingkasanBulan(session('clientId'), $bulan, $tahun);
        /**
         * Mengambil total seluruh transaksi user
         * dari awal sampai saat ini
         */
        $totalSemua = $transaksi->getTotalAll(session('clientId'));

        /**
         * Data yang akan dikirim ke view dashboard
        */
        $data = [
            "title" => "Dashboard", // Judul halaman
            "tanggal" => $this->tanggal_indo(date('Y-m-d')),  // Tanggal hari ini format Indonesia
            "lastTransaksi" => $lastTransaksi, // Data transaksi terakhir
            "ringkasanBulan" => $ringkasanBulan, // Ringkasan transaksi bulanan
            "totalSemua" => $totalSemua, // Total seluruh transaksi
        ];

        // Menampilkan view dashboard dan mengirimkan data
        return view('dashboard', $data);
    }

    /**
     * Method tanggal_indo()
     * 
     * Berfungsi untuk mengubah format tanggal
     * dari format standar (Y-m-d) menjadi
     * format Bahasa Indonesia
     * 
    */
    public function tanggal_indo($source_date)
    {
        // Mengubah string tanggal menjadi timestamp
        $d = strtotime($source_date);

        // Mengambil komponen tanggal
        $year = date('Y', $d); // Tahun
        $month = date('n', $d); // Bulan (1–12)
        $day = date('d', $d); // Tanggal
        $day_name = date('D', $d); // Nama hari versi Inggris (Sun, Mon, dst)

        // Array mapping nama hari ke Bahasa Indonesia
        $day_names = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jum\'at',
            'Sat' => 'Sabtu'
        );
        // Array mapping bulan ke Bahasa Indonesia
        $month_names = array(
            '1' => 'Januari',
            '2' => 'Februari',
            '3' => 'Maret',
            '4' => 'April',
            '5' => 'Mei',
            '6' => 'Juni',
            '7' => 'Juli',
            '8' => 'Agustus',
            '9' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember'
        );
        // Mengubah nama hari ke Bahasa Indonesia
        $day_name = $day_names[$day_name];
        // Mengubah nama bulan ke Bahasa Indonesia
        $month_name = $month_names[$month];
          // Format akhir tanggal Indonesia
        $date = "$day_name, $day $month_name $year";

        // Mengembalikan hasil tanggal
        return $date;
    }
}
