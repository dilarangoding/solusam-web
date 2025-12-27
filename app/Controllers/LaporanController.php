<?php

namespace App\Controllers;

// Menggunakan BaseController sebagai parent
use App\Controllers\BaseController;
// Model Transaksi untuk pengambilan data laporan
use App\Models\Transaksi;
// Library PhpSpreadsheet untuk export Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanController extends BaseController
{
    // Properti untuk menyimpan instance model Transaksi
    protected $transaksiModel;

     // Constructor untuk inisialisasi model
    public function __construct()
    {
        $this->transaksiModel = new Transaksi();
    }

    // Menampilkan halaman utama laporan
    public function index()
    {
        $data = [
            "title" => "Data Laporan",
        ];

        return view('laporan/index', $data);
    }

    // Mengambil data laporan berdasarkan filter (AJAX)
    public function getLaporanData()
    {
        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $client_id = session()->get('clientId');

        $laporanData = $this->transaksiModel->getLaporan(
            $client_id, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai
        );

        // Mengembalikan data dalam format JSON
        return $this->response->setJSON($laporanData);
    }

    // Menampilkan halaman laporan pemasukan
    public function pemasukan()
    {
        $data = [
            "title" => "Data Laporan Pemasukan",
        ];
        
        return view('laporan/pemasukan', $data);
    }

     // Menampilkan halaman laporan pengeluaran
    public function pengeluaran()
    {
        $data = [
            "title" => "Data Laporan Pengeluaran",
        ];

        return view('laporan/pengeluaran', $data);
    }

    // Mengambil data pemasukan atau pengeluaran (AJAX)
    public function getDataInOut()
    {
        // Mengambil filter dari request
        $tahun = $this->request->getPost('tahun');
        $bulan = $this->request->getPost('bulan');
        $tanggalMulai = $this->request->getPost('tanggal_mulai');
        $tanggalSelesai = $this->request->getPost('tanggal_selesai');
        $client_id = session()->get('clientId');
        $jenis = $this->request->getPost('jenis'); // in / out

         // Memanggil fungsi helper
        $laporanData = $this->getDataResult(
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai, 
            $jenis, 
            $client_id
        );
    
        return $this->response->setJSON($laporanData);
    }

    // Fungsi helper untuk mengambil data laporan pemasukan/pengeluaran
    protected function getDataResult(
        $tahun, 
        $bulan, 
        $tanggalMulai, 
        $tanggalSelesai, 
        $jenis, 
        $client_id) {
    
        $laporanData = $this->transaksiModel->getDataInOutLaporan(
            $client_id, 
            $jenis, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai);

        return $laporanData;
    }

    // Export laporan pemasukan ke Excel
    public function exportPemasukan()
    {
         // Mengambil filter dari URL
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $client_id = session()->get('clientId');
        
        // Mengambil data pemasukan (jenis = out)
        $data = $this->transaksiModel->getDataInOutLaporan(
            $client_id, 
            'out', 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai);

         // Membuat spreadsheet baru
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul sheet
        $sheet->setTitle('Laporan Pemasukan');

         // Styling header Excel
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '28A745']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

         // Header kolom Excel
        $headers = ['No', 'Tanggal', 'Nama Sampah', 'Jumlah (Kg)', 'Harga Jual', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

       // Mengisi data Excel/set data
        $row = 2;
        $totalBerat = 0;
        $totalPendapatan = 0;
        
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_sampah']);
            $sheet->setCellValue('D' . $row, $item['jumlah']);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['harga_jual'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_pendapatan'], 0, ',', '.'));
            
            $totalBerat += $item['jumlah'];
            $totalPendapatan += $item['total_pendapatan'];
            $row++;
        }

         // Baris total
        $summaryRow = $row + 1;
        $sheet->setCellValue('C' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('D' . $summaryRow, $totalBerat);
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalPendapatan, 0, ',', '.'));
        
        // Style summary row
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E8F5E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('C' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        // Auto size kolom
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

       // Download file
        $filename = 'Laporan_Pemasukan_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportPengeluaran()
    {
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
        $client_id = session()->get('clientId');

        $data = $this->transaksiModel->getDataInOutLaporan($client_id, 'in', $tahun, $bulan, $tanggalMulai, $tanggalSelesai);

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Set title
        $sheet->setTitle('Laporan Pengeluaran');

        // Header styling
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'DC3545']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

        // Set headers
        $headers = ['No', 'Tanggal', 'Nama Sampah', 'Jumlah (Kg)', 'Harga Beli', 'Total'];
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

        // Set data
        $row = 2;
        $totalBerat = 0;
        $totalPengeluaran = 0;
        
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['tanggal']);
            $sheet->setCellValue('C' . $row, $item['nama_sampah']);
            $sheet->setCellValue('D' . $row, $item['jumlah']);
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['harga_beli'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_pengeluaran'], 0, ',', '.'));
            
            $totalBerat += $item['jumlah'];
            $totalPengeluaran += $item['total_pengeluaran'];
            $row++;
        }

        // Add summary row
        $summaryRow = $row + 1;
        $sheet->setCellValue('C' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('D' . $summaryRow, $totalBerat);
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        
        // Style summary row
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'F8E8E8']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('C' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        // Auto size columns
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Generate filename
        $filename = 'Laporan_Pengeluaran_' . date('Y-m-d_H-i-s') . '.xlsx';

        // Set headers for download
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

    public function exportLaporan()
    {
        // Mengambil parameter filter dari URL (GET)
        $tahun = $this->request->getGet('tahun');
        $bulan = $this->request->getGet('bulan');
        $tanggalMulai = $this->request->getGet('tanggal_mulai');
        $tanggalSelesai = $this->request->getGet('tanggal_selesai');
         // Mengambil client_id dari session (klien yang sedang login)
        $client_id = session()->get('clientId');

        // Mengambil data laporan dari database melalui model Transaksi
        // Data sudah difilter berdasarkan client dan parameter tanggal
        $data = $this->transaksiModel->getLaporan(
            $client_id, 
            $tahun, 
            $bulan, 
            $tanggalMulai, 
            $tanggalSelesai
        );

        // Membuat objek Spreadsheet baru (PhpSpreadsheet)
        $spreadsheet = new Spreadsheet();
        // Mengambil sheet aktif
        $sheet = $spreadsheet->getActiveSheet();
        
        // Mengatur judul sheet Excel
        $sheet->setTitle('Laporan Keuangan');

        // Style untuk header tabel (baris pertama)
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '007BFF']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];

       // Daftar judul kolom pada tabel Excel
        $headers = [
            'No', 
            'Periode', 
            'Jumlah Transaksi', 
            'Total Pemasukan', 
            'Total Pengeluaran', 
            'Keuntungan/Kerugian'
        ];

        // Menuliskan header ke baris pertama Excel
        $col = 'A';
        foreach ($headers as $header) {
            $sheet->setCellValue($col . '1', $header);
            $sheet->getStyle($col . '1')->applyFromArray($headerStyle);
            $col++;
        }

       // Baris awal untuk data (setelah header)
        $row = 2;
        // Variabel penampung total keseluruhan
        $totalTransaksi = 0;
        $totalPemasukan = 0;
        $totalPengeluaran = 0;
        $totalKeuntungan = 0;

          // Mengisi data laporan ke dalam Excel
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['periode']);
            $sheet->setCellValue('C' . $row, $item['jumlah']);
            $sheet->setCellValue('D' . $row, 'Rp ' . number_format($item['total_pendapatan'], 0, ',', '.'));
            $sheet->setCellValue('E' . $row, 'Rp ' . number_format($item['total_pengeluaran'], 0, ',', '.'));
            $sheet->setCellValue('F' . $row, 'Rp ' . number_format($item['total_keuntungan'], 0, ',', '.'));

            // Menjumlahkan total keseluruhan
            $totalTransaksi += $item['jumlah'];
            $totalPemasukan += $item['total_pendapatan'];
            $totalPengeluaran += $item['total_pengeluaran'];
            $totalKeuntungan += $item['total_keuntungan'];
            $row++;
        }

       // Menentukan baris untuk total akhirw
        $summaryRow = $row + 1;
         // Menampilkan total keseluruhan di baris akhir
        $sheet->setCellValue('B' . $summaryRow, 'TOTAL');
        $sheet->setCellValue('C' . $summaryRow, $totalTransaksi);
        $sheet->setCellValue('D' . $summaryRow, 'Rp ' . number_format($totalPemasukan, 0, ',', '.'));
        $sheet->setCellValue('E' . $summaryRow, 'Rp ' . number_format($totalPengeluaran, 0, ',', '.'));
        $sheet->setCellValue('F' . $summaryRow, 'Rp ' . number_format($totalKeuntungan, 0, ',', '.'));
        
         // Style khusus untuk baris TOTAL
        $summaryStyle = [
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'E3F2FD']
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ];
        $sheet->getStyle('B' . $summaryRow . ':F' . $summaryRow)->applyFromArray($summaryStyle);

        // Mengatur lebar kolom otomatis agar rapi
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Membuat nama file Excel dengan timestamp
        $filename = 'Laporan_Keuangan_' . date('Y-m-d_H-i-s') . '.xlsx';

         // Header HTTP agar browser mengunduh file Excel
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Menulis file Excel ke output browser
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');

        // Menghentikan eksekusi setelah file dikirim
        exit;
    }
}
