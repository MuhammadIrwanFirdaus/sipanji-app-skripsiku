<?php
ob_end_clean();
require('fpdf/fpdf.php'); // Pastikan path ke file fpdf.php benar
include_once 'database/database.php';

// Define the PDF class extending FPDF
class PDF extends FPDF {
    function Header() {
        // Logo
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 35);
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 5, 'Pemerintah Kota Banjarbaru', 0, 1, 'C');
        $this->Cell(0, 5, 'Dinas Komunikasi dan Informatika Kota Banjarbaru', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 8);
        $this->Cell(0, 5, 'Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', 0, 1, 'C');
        $this->Cell(0, 5, 'Telepon: 0811-5289-090', 0, 1, 'C');
        $this->Ln(5);
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY() + 1, $this->GetPageWidth() - 10, $this->GetY() + 1);
        $this->Ln(10);
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
        $this->SetY(-10);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Banjarbaru, ' . date('d F Y'), 0, 0, 'R');
    }
}

// Fungsi untuk menghasilkan laporan PDF
function generatePDF() {
    ob_end_clean();
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk menghitung total stok terpakai setiap alat
    $selectSql = "
        SELECT alat, SUM(stok_terpakai) as total_stok
        FROM pengerjaan
        GROUP BY alat
        ORDER BY total_stok DESC
    ";

    $stmt = $db->prepare($selectSql);
    $stmt->execute();

    // Membuat instance FPDF
    $pdf = new PDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 12);
    
    // Header
    $pdf->Cell(0, 10, 'Laporan Alat yang Paling Sering Digunakan', 0, 1, 'C');
    $pdf->Cell(0, 10, '', 0, 1); // Tambahkan baris kosong

    // Table Header
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(100, 10, 'Alat', 1);
    $pdf->Cell(90, 10, 'Total Stok Terpakai', 1);
    $pdf->Ln();

    // Table Data
    $pdf->SetFont('Arial', '', 10);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $pdf->Cell(100, 10, $row['alat'], 1);
        $pdf->Cell(90, 10, $row['total_stok'], 1);
        $pdf->Ln();
    }

    // Output PDF
    $pdf->Output('D', 'laporan_alat_sering_digunakan.pdf');
}

// Panggil fungsi untuk menghasilkan PDF
generatePDF();
?>
