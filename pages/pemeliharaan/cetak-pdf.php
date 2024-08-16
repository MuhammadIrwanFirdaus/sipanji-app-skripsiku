<?php
ob_end_clean();
require('fpdf/fpdf.php');
require_once('database/Database.php');

class PDF extends FPDF {
    function Header() {
        // Logo dan Kop Surat
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 40); // Ganti dengan path logo Anda dan atur ukuran gambar
        $this->SetFont('Arial', 'B', 16); // Atur ukuran font nama perusahaan
        $this->Cell(0, 5, 'Pemerintah Kota Banjarbaru', 0, 1, 'C');
        $this->Cell(0, 5, 'Dinas Komunikasi dan Informatika Kota Banjarbaru', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 8); // Atur ukuran font nama perusahaan
        $this->Cell(0, 5, 'Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', 0, 1, 'C');
        $this->Cell(0, 5, 'Telepon: 0811-5289-090', 0, 1, 'C');

        // Tambahkan sedikit jarak antara kop dan garis
        $this->Ln(5);

        // Tambahkan garis di bawah kop surat
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY() + 1, $this->GetPageWidth() - 10, $this->GetY() + 1);

        $this->Ln(10); // Menambahkan jarak antara judul dan gambar
    }   
    
    function Footer() {
        // Footer content, including page number
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, false, 'C');
    }
}

function createPDF() {
    ob_end_clean();
    // Membuat koneksi ke database
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk mengambil data dari tabel events tanpa filter tanggal
    $selectSql = "SELECT * FROM alat";
    $stmt = $db->prepare($selectSql);
    $stmt->execute();
    $pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inisialisasi PDF
    $pdf = new PDF('L');
    $pdf->AddPage();

    // Judul
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Data Status Alat', 0, 1, 'C');

    // Header Tabel
    $pdf->SetFont('Arial', 'B', 12);

    // Hitung lebar tabel
    $tableWidth = 15 + 80 + 60 + 60 + 60;

    // Geser posisi X agar header tabel berada di tengah halaman
    $marginLeftHeader = ($pdf->GetPageWidth() - $tableWidth) / 2;

    $pdf->SetX($marginLeftHeader); // Geser posisi X untuk header tabel
    $pdf->Cell(15, 10, 'No', 1, 0, 'C'); // Kolom Nomor
    $pdf->Cell(60, 10, 'Tempat', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Ip Address', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Status', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Kerusakan', 1, 1, 'C');

    // Isi Tabel
    $pdf->SetFont('Arial', '', 12);
    $nomor = 1; // Inisialisasi nomor urut
    foreach ($pengajuan as $event) {
        // Geser posisi X untuk setiap sel di dalam baris tabel
        $pdf->SetX($marginLeftHeader);
        
        $pdf->Cell(15, 10, $nomor, 1, 0, 'C'); // Menampilkan nomor urut
        $pdf->Cell(60, 10, $event['tempat'], 1, 0, 'L');
        $pdf->Cell(60, 10, $event['device_id'], 1, 0, 'L'); // Pastikan nama kolom sesuai dengan database
        $pdf->Cell(60, 10, $event['status'], 1, 0, 'L'); // Pastikan nama kolom sesuai dengan database
        $pdf->Cell(60, 10, $event['kerusakan'], 1, 1, 'L'); // Pastikan nama kolom sesuai dengan database
        $nomor++; // Tambahkan nomor urut setelah satu baris selesai
    }

    // Reset posisi X ke awal
    $pdf->SetX(10);

    $pdf->Cell(0, 10, 'Banjarbaru, ' . date('d F Y'), 0, 1, 'R');
    // Informasi Pimpinan
    $pdf->SetFont('Arial', 'I', 14);
    $pdf->Cell(0, 10, 'Mengetahui, ', 0, 1, 'R');
    $pdf->Cell(0, 5, 'Kepala Dinas', 0, 1, 'R');
    $pdf->Cell(0, 10, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'U', 14);
    $pdf->Cell(0, 10, ' Asep Saputra, S. Kom, MM ', 0, 1, 'R');
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 5, 'NIP. 19770909 200604 1 006', 0, 1, 'R');
    $pdf->Ln(10); // Menambahkan jarak antara informasi pimpinan dan tabel

    // Outputkan file PDF langsung ke browser
    $pdf->Output('Laporan Status Jaringan.pdf', 'D');
    exit();
}

// Memanggil fungsi untuk membuat PDF
createPDF();
?>
