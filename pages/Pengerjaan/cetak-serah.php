<?php
ob_end_clean();
require('fpdf/fpdf.php');

function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime));
    $year = date('Y', strtotime($datetime));

    $oldLocale = setlocale(LC_TIME, '0');
    setlocale(LC_TIME, 'id_ID.utf8');

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . ' ' . $year;

    setlocale(LC_TIME, $oldLocale);
    return $formattedDate;
}

class PDF extends FPDF {
    function Header() {
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 30);
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

if (isset($_POST['instansi']) && isset($_POST['alamat']) && isset($_POST['nama_barang']) && isset($_POST['jumlah']) && isset($_POST['kondisi']) && isset($_POST['penerima']) && isset($_POST['nip_penerima'])) {
    $instansi = $_POST['instansi'];
    $alamat = $_POST['alamat'];
    $nama_barang = $_POST['nama_barang'];
    $jumlah = $_POST['jumlah'];
    $kondisi = $_POST['kondisi'];
    $penerima = $_POST['penerima'];
    $nip_penerima = $_POST['nip_penerima'];

    ob_end_clean();
    $pdf = new PDF('P');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Serah Terima Barang', 0, 1, 'C');

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Instansi: ' . $instansi, 0, 1);
    $pdf->Cell(0, 10, 'Alamat: ' . $alamat, 0, 1);
    $pdf->Cell(0, 10, 'Nama Barang: ' . $nama_barang, 0, 1);
    $pdf->Cell(0, 10, 'Jumlah: ' . $jumlah, 0, 1);
    $pdf->Cell(0, 10, 'Kondisi: ' . $kondisi, 0, 1);
    $pdf->Cell(0, 10, 'Penerima: ' . $penerima, 0, 1);
    $pdf->Cell(0, 10, 'NIP Penerima: ' . $nip_penerima, 0, 1);

    $pdf->Ln(20);

    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Banjarbaru, ' . indoDate(date('Y-m-d')), 0, 1, 'R');
    $pdf->Cell(0, 10, 'Mengetahui, ', 0, 1, 'R');
    $pdf->Cell(0, 10, 'Kepala Dinas', 0, 1, 'R');
    $pdf->Ln(20);
    $pdf->SetFont('Arial', 'U', 12);
    $pdf->Cell(0, 10, 'Asep Saputra, S. Kom, MM', 0, 1, 'R');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'NIP. 19770909 200604 1 006', 0, 1, 'R');

    $pdf->Output('Serah_Terima.pdf', 'D');
    exit();
}
?>
