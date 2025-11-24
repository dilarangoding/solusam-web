<?php

namespace App\Libraries; // Mendefinisikan namespace library agar bisa dipanggil dari controller atau model

class MidtransSnap
{
    // Konstruktor otomatis dipanggil saat library diinstansiasi
    public function __construct()
    {
        $config = config('Midtrans'); // Mengambil konfigurasi Midtrans dari file Config/Midtrans.php

        // Mengatur server key Midtrans dari konfigurasi
        \Midtrans\Config::$serverKey    = $config->serverKey;

        // Menentukan environment, true untuk production, false untuk sandbox
        \Midtrans\Config::$isProduction = $config->isProduction;

        // Mengaktifkan sanitasi data transaksi agar aman dari manipulasi
        \Midtrans\Config::$isSanitized  = $config->isSanitized;

        // Mengaktifkan fitur 3DS untuk kartu kredit (3D Secure)
        \Midtrans\Config::$is3ds        = $config->is3ds;
    }

    /**
     * Membuat transaksi baru menggunakan Midtrans Snap API
     * @param array $params Data transaksi (order_id, gross_amount, customer_details, dll)
     * @return array Respon transaksi dari Midtrans
     */
    public function createTransaction(array $params)
    {
        // Memanggil Snap::createTransaction dari Midtrans SDK dengan parameter yang diberikan
        return \Midtrans\Snap::createTransaction($params);
    }
}
