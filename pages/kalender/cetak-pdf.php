<?php
ob_end_clean();
require('fpdf/fpdf.php'); 
include_once "database/database.php";

// Fungsi untuk memformat tanggal dalam bahasa Indonesia
function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime));

    return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y', strtotime($datetime));
}

class PDF extends FPDF {
    function Header() {
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 40);
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
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

$database = new Database();
$db = $database->getConnection();

$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';

if (empty($start_date) || empty($end_date)) {
    die('Tanggal awal dan akhir harus disertakan.');
}

$query = "
    SELECT *
    FROM events
    WHERE (tanggal BETWEEN :start_date AND :end_date)
";

$stmt = $db->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();
$jadwalData = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($jadwalData)) {
    die('Tidak ada data yang tersedia untuk rentang tanggal ini.');
}
ob_end_clean();
$pdf = new PDF();
$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Laporan Jadwal', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Periode: ' . indoDate($start_date) . ' hingga ' . indoDate($end_date), 0, 1, 'C');
$pdf->Ln(10);

// Header Tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(60, 10, 'Tanggal', 1);
$pdf->Cell(60, 10, 'Tempat', 1);
$pdf->Cell(90, 10, 'Keterangan', 1);
$pdf->Ln();

// Isi Tabel
$pdf->SetFont('Arial', '', 12);
foreach ($jadwalData as $jadwal) {
    $pdf->Cell(60, 10, indoDate($jadwal['tanggal']), 1);
    $pdf->Cell(60, 10, $jadwal['title'], 1);
    $pdf->Cell(90, 10, $jadwal['keterangan'], 1);
    $pdf->Ln();
}

// Output PDF
$pdf->Output('D', 'Laporan_Jadwal_Pemasangan_Survey.pdf');
?>
