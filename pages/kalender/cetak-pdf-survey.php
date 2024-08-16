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

    $oldLocale = setlocale(LC_TIME, '0'); // Simpan setelan lokal sebelumnya
    setlocale(LC_TIME, 'id_ID.utf8'); // Atur lokal ke Bahasa Indonesia dengan UTF-8

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));

    setlocale(LC_TIME, $oldLocale); // Kembalikan setelan lokal sebelumnya
    return $formattedDate;
}

function indoDateWithoutTime($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime));
    $year = date('Y', strtotime($datetime));

    $oldLocale = setlocale(LC_TIME, '0'); // Simpan setelan lokal sebelumnya
    setlocale(LC_TIME, 'id_ID.utf8'); // Atur lokal ke Bahasa Indonesia dengan UTF-8

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . ' ' . $year;

    setlocale(LC_TIME, $oldLocale); // Kembalikan setelan lokal sebelumnya
    return $formattedDate;
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

function createPDF($start_date, $end_date) {
    ob_end_clean();
    // Membuat koneksi ke database
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk mengambil data dari tabel events dengan filter tanggal
    $selectSql = "SELECT * FROM events WHERE tanggal BETWEEN :start_date AND :end_date";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();
    $pengajuan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inisialisasi PDF
    $pdf = new PDF('L');
    $pdf->AddPage();

    // Judul
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Survey', 0, 1, 'C');

    // Keterangan Periode
    $pdf->SetFont('Arial', 'I', 12);
    $pdf->Cell(0, 10, 'Periode: ' . indoDateWithoutTime($start_date) . ' sampai ' . indoDateWithoutTime($end_date), 0, 1, 'C');


// Header Tabel
$pdf->SetFont('Arial', 'B', 12);

// Hitung lebar tabel
$tableWidth = 15 + 80 + 60 + 60;

// Geser posisi X agar header tabel berada di tengah halaman
$marginLeftHeader = ($pdf->GetPageWidth() - $tableWidth) / 2;

$pdf->SetX($marginLeftHeader); // Geser posisi X untuk header tabel
$pdf->Cell(15, 10, 'No', 1, 0, 'C'); // Kolom Nomor
$pdf->Cell(80, 10, 'Tanggal', 1, 0, 'C');
$pdf->Cell(60, 10, 'Tempat', 1, 0, 'C');
$pdf->Cell(60, 10, 'Keterangan', 1, 1, 'C');

// Isi Tabel
$pdf->SetFont('Arial', '', 12);
$nomor = 1; // Inisialisasi nomor urut
foreach ($pengajuan as $event) {
    if ($event['keterangan'] === 'survey') { // Memeriksa apakah keterangan adalah "survey"
        // Geser posisi X untuk setiap sel di dalam baris tabel
        $pdf->SetX($marginLeftHeader);
        
        $pdf->Cell(15, 10, $nomor, 1, 0, 'C'); // Menampilkan nomor urut
        // Ubah format tanggal
        setlocale(LC_ALL, 'id_ID.utf8'); // Atur lokal ke Bahasa Indonesia dengan UTF-8
        $indoDate = indoDate($event['tanggal']); // Menggunakan fungsi indoDate yang telah didefinisikan sebelumnya
        $pdf->Cell(80, 10, $indoDate, 1, 0, 'L');
        $pdf->Cell(60, 10, $event['title'], 1, 0, 'L');
        $pdf->Cell(60, 10, $event['keterangan'], 1, 1, 'L');
        $nomor++; // Tambahkan nomor urut setelah satu baris selesai
    }
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
    $pdf->Output('Laporan Survey.pdf', 'D');
    exit();
}

// Memeriksa apakah data tanggal sudah dikirim dari formulir
if (isset($_POST['start_date']) && isset($_POST['end_date'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Memanggil fungsi untuk membuat PDF dengan filter tanggal
    createPDF($start_date, $end_date);
} else {
    // Menampilkan formulir filter tanggal jika data belum dikirim

}
?>
