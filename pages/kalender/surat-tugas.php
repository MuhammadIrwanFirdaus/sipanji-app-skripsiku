<?php
ob_end_clean();
require('fpdf/fpdf.php');
require_once('database/Database.php');

function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime));

    return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));
}

class PDF extends FPDF {
    function Header() {
        // Logo dan Kop Surat
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 40); 
        $this->SetFont('Arial', 'B', 16); 
        $this->Cell(0, 5, 'PEMERINTAH KOTA BANJARBARU', 0, 1, 'C');
        $this->Cell(0, 5, 'DINAS KOMUNIKASI DAN INFORMATIKA', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(0, 5, 'Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjarbaru, Kalimantan Selatan 70714', 0, 1, 'C');
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

function createPDF($id) {
    ob_end_clean();
    $database = new Database();
    $db = $database->getConnection();

    $selectSql = "SELECT * FROM events WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $pengajuan = $stmt->fetch(PDO::FETCH_ASSOC);

    if (empty($pengajuan)) {
        die('Data tidak ditemukan.');
    }

    $pdf = new PDF('P', 'mm', 'A4');
    $pdf->AddPage();

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(30, 7, 'Nomor', 0, 0);
    $pdf->Cell(3, 7, ':', 0, 0);
    $pdf->Cell(0, 7, '......../...../...../2024', 0, 1);

    $pdf->Cell(30, 7, 'Lampiran', 0, 0);
    $pdf->Cell(3, 7, ':', 0, 0);
    $pdf->Cell(0, 7, '-', 0, 1);

    $pdf->Cell(30, 7, 'Hal', 0, 0);
    $pdf->Cell(3, 7, ':', 0, 0);
    $pdf->Cell(0, 7, 'Surat Tugas', 0, 1);

    $pdf->Ln(10);

    $pdf->MultiCell(0, 7, "Dalam rangka memenuhi kebutuhan operasional jaringan di lokasi " . $pengajuan['title'] . ", dengan ini kami menugaskan Saudara untuk melaksanakan tugas sebagai berikut:");
    
    $pdf->Ln(5);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(15, 10, 'No', 1, 0, 'C');
    $pdf->Cell(165, 10, 'Pemasang', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 12);

    // Pisahkan nama pemasang
    $pemasang_list = explode(', ', $pengajuan['pemasang']);
    $nomor = 1;

    foreach ($pemasang_list as $pemasang) {
        $pdf->Cell(15, 10, $nomor, 1, 0, 'C');
        $pdf->Cell(165, 10, trim($pemasang), 1, 1, 'L');
        $nomor++;
    }

    $pdf->Ln(10);
    $pdf->MultiCell(0, 7, "Demikian surat tugas ini dibuat untuk dapat dilaksanakan dengan sebaik-baiknya. Segala biaya yang timbul sebagai akibat dari pelaksanaan tugas ini akan ditanggung sesuai dengan peraturan yang berlaku.");

    $pdf->Ln(15);
    $pdf->Cell(0, 10, 'Banjarbaru, ' . date('d F Y'), 0, 1, 'R');
    $pdf->SetFont('Arial', 'I', 14);
    $pdf->Cell(0, 10, 'Mengetahui, ', 0, 1, 'R');
    $pdf->Cell(0, 5, 'Kepala Dinas', 0, 1, 'R');
    $pdf->Cell(0, 20, '', 0, 1, 'R');
    $pdf->SetFont('Arial', 'U', 14);
    $pdf->Cell(0, 10, ' Asep Saputra, S. Kom, MM ', 0, 1, 'R');
    $pdf->SetFont('Arial', '', 14);
    $pdf->Cell(0, 5, 'NIP. 19770909 200604 1 006', 0, 1, 'R');

    $pdf->Output('D', 'Surat Tugas.pdf');
    exit();
}


if (isset($_GET['id'])) {
    $id = $_GET['id'];
    createPDF($id);
} else {
    echo "ID tidak ditemukan.";
}
?>
