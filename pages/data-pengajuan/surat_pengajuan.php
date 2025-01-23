<?php
ob_end_clean();
require('fpdf/fpdf.php');

class PDF extends FPDF {
    // Header halaman
    function Header() {
        // Logo dan Kop Surat
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 30); // Sesuaikan path dan ukuran logo
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'PEMERINTAH KOTA BANJARBARU', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 7, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C');
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 5, 'Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', 0, 1, 'C');
        $this->Cell(0, 5, 'Telepon: 0811-5289-090', 0, 1, 'C');
        $this->Ln(5);

        // Garis horizontal
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY(), $this->GetPageWidth() - 10, $this->GetY());
        $this->Ln(10);
    }

    // Footer halaman
    function Footer() {
        // Posisi 1.5 cm dari bawah
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        // Nomor halaman
        $this->Cell(0, 10, 'Halaman ' . $this->PageNo(), 0, 0, 'C');
    }

    // Membuat garis untuk isian
    function DrawLine($label, $yPosition, $width = 0) {
        $this->SetFont('Arial', '', 12);
        $this->SetXY(20, $yPosition);
        $this->Cell(40, 10, $label, 0, 0, 'L');
        $this->SetXY(70, $yPosition);
        $this->Cell($width, 10, '', 'B', 1, 'L');
    }
}

// Membuat instance PDF
ob_end_clean();
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AddPage();

// Judul Surat
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'SURAT PENGAJUAN', 0, 1, 'C');

// Informasi Pelanggan
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(10);

// Nama Pelanggan
$pdf->DrawLine('Nama Pelanggan:', $pdf->GetY(), 100);

// Tanggal Pengajuan
$pdf->DrawLine('Tanggal Pengajuan:', $pdf->GetY() + 10, 60);

// Deskripsi Pengajuan
$pdf->SetXY(20, $pdf->GetY() + 20);
$pdf->Cell(0, 10, 'Deskripsi Pengajuan:', 0, 1, 'L');
$pdf->Rect(20, $pdf->GetY(), 170, 50); // Kotak untuk deskripsi
$pdf->SetXY(20, $pdf->GetY());
$pdf->SetFont('Arial', 'I', 10);
$pdf->MultiCell(170, 10, 'Tuliskan deskripsi pengajuan Anda di sini...', 0, 'L');

// Tanda Tangan
$pdf->Ln(60);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Banjarbaru, ....................................', 0, 1, 'R');
$pdf->Cell(0, 10, 'Yang Mengajukan,', 0, 1, 'R');
$pdf->Ln(20);
$pdf->Cell(0, 10, '............................................', 0, 1, 'R');
$pdf->Cell(0, 5, 'Nama Lengkap', 0, 1, 'R');

// Outputkan file PDF langsung ke browser
$pdf->Output('D', 'Template_Surat_Pengajuan.pdf');
exit();
?>
