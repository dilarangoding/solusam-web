<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $transaksi = new \App\Models\Transaksi();
        $bulan = date('m');
        $tahun = date('Y');
        $lastTransaksi = $transaksi->getLastTransaction(session('clientId'), $bulan);
        $ringkasanBulan = $transaksi->getRingkasanBulan(session('clientId'), $bulan, $tahun);
        $totalSemua = $transaksi->getTotalAll(session('clientId'));
        // dd($totalSemua);
        $data = [
            "title" => "Dashboard",
            "tanggal" => $this->tanggal_indo(date('Y-m-d')),
            "lastTransaksi" => $lastTransaksi,
            "ringkasanBulan" => $ringkasanBulan,
            "totalSemua" => $totalSemua,
        ];

        return view('dashboard', $data);
    }

    public function tanggal_indo($source_date)
    {
        $d = strtotime($source_date);

        $year = date('Y', $d);
        $month = date('n', $d);
        $day = date('d', $d);
        $day_name = date('D', $d);

        $day_names = array(
            'Sun' => 'Minggu',
            'Mon' => 'Senin',
            'Tue' => 'Selasa',
            'Wed' => 'Rabu',
            'Thu' => 'Kamis',
            'Fri' => 'Jum\'at',
            'Sat' => 'Sabtu'
        );
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
        $day_name = $day_names[$day_name];
        $month_name = $month_names[$month];

        $date = "$day_name, $day $month_name $year";

        return $date;
    }
}
