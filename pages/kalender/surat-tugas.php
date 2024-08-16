<?php
ob_end_clean();
require('fpdf/fpdf.php');
require_once('database/Database.php');

function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime)); // Mendapatkan angka tanggal

    return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));
}

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
        // Footer content, including barcode signature
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
    }
}

function createPDF($id) {
    ob_end_clean();
    // Membuat koneksi ke database
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk mengambil data dari tabel events dengan filter ID
    $selectSql = "SELECT * FROM events WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    $pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Periksa apakah data ada
    if (empty($pengajuan)) {
        die('Data tidak ditemukan.');
    }

    // Inisialisasi PDF
    $pdf = new PDF('L');
    $pdf->AddPage();

    // Judul
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Surat Tugas Bidang Jaringan', 0, 1, 'C');

    // Header Tabel
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(15, 10, 'No', 1, 0, 'C');
    $pdf->Cell(80, 10, 'Tanggal', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Tempat', 1, 0, 'C');
    $pdf->Cell(60, 10, 'Keterangan', 1, 1, 'C');

    // Isi Tabel
    $pdf->SetFont('Arial', '', 12);
    $nomor = 1; // Inisialisasi nomor urut
    foreach ($pengajuan as $event) {
        $pdf->Cell(15, 10, $nomor, 1, 0, 'C');
        $pdf->Cell(80, 10, indoDate($event['tanggal']), 1, 0, 'L');
        $pdf->Cell(60, 10, $event['title'], 1, 0, 'L');
        $pdf->Cell(60, 10, $event['keterangan'], 1, 1, 'L');
        $nomor++;
    }

    $pdf->Ln(10);

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

    // Outputkan file PDF langsung ke browser
    $pdf->Output('D', 'Surat Tugas.pdf');
    exit();
}

// Memeriksa apakah ID sudah dikirim melalui GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Memanggil fungsi untuk membuat PDF dengan filter ID
    createPDF($id);
} else {
    echo "ID tidak ditemukan.";
}
?>
