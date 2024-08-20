<?php
// Memulai output buffering untuk menghindari output sebelum PDF dihasilkan
ob_end_clean();

// Memuat library FPDF dan menghubungkan ke database
require('fpdf/fpdf.php');
require_once('database/Database.php');

// Fungsi untuk format tanggal dalam Bahasa Indonesia
function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime));

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y', strtotime($datetime));
    return $formattedDate;
}

// Class untuk membuat PDF
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
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, false, 'C');
    }
}

// Fungsi untuk membuat grafik pie
function createPieChart($data, $filename) {
    $width = 400;
    $height = 400;
    $image = imagecreatetruecolor($width, $height);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    imagefill($image, 0, 0, $backgroundColor);

    // Warna untuk setiap bagian pie chart
    $colors = [
        imagecolorallocate($image, 255, 0, 0),  // Merah
        imagecolorallocate($image, 0, 255, 0),  // Hijau
        imagecolorallocate($image, 0, 0, 255),  // Biru
        imagecolorallocate($image, 255, 255, 0),  // Kuning
        imagecolorallocate($image, 255, 0, 255)   // Magenta
    ];

    $total = array_sum($data);
    $startAngle = 0;
    $centerX = $width / 2;
    $centerY = $height / 2;
    $radius = min($centerX, $centerY) - 50;

    // Menggambar setiap slice pie
    $colorIndex = 0;
    foreach ($data as $key => $value) {
        if ($value == 0) continue;  // Tidak menggambar bagian jika nilai 0

        $percentage = $value / $total;
        $endAngle = $startAngle + ($percentage * 360);

        // Menggambar bagian pie
        imagefilledarc($image, $centerX, $centerY, 2 * $radius, 2 * $radius, $startAngle, $endAngle, $colors[$colorIndex], IMG_ARC_PIE);

        // Menempatkan label
        $midAngle = deg2rad(($startAngle + $endAngle) / 2);
        $labelX = $centerX + cos($midAngle) * ($radius + 20);
        $labelY = $centerY + sin($midAngle) * ($radius + 20);
        $label = "$key: $value orang";  // Menambahkan kata "orang"
        imagestring($image, 3, $labelX, $labelY, $label, $colors[$colorIndex]);

        $startAngle = $endAngle;
        $colorIndex = ($colorIndex + 1) % count($colors);
    }

    if (!is_dir('uploaded_images')) {
        mkdir('uploaded_images', 0755, true);
    }

    $filepath = 'uploaded_images/' . $filename;
    imagepng($image, $filepath);
    imagedestroy($image);
}



// Fungsi untuk membuat PDF
// Fungsi untuk membuat PDF
function createPDF() {
    ob_end_clean();
    $database = new Database();
    $db = $database->getConnection();

    // Query data
    $selectSql = "SELECT * FROM kepuasan_pelayanan ORDER BY tanggal DESC";
    $stmt = $db->prepare($selectSql);
    $stmt->execute();
    $kepuasan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $penilaianCounts = array_fill(1, 5, 0);
    foreach ($kepuasan as $row) {
        $penilaianCounts[$row['penilaian']]++;
    }

    $pdf = new PDF();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Kepuasan Pelayanan', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'Username', 1);
    $pdf->Cell(50, 10, 'Email', 1);
    $pdf->Cell(20, 10, 'Penilaian', 1);
    $pdf->Ln();

    $pdf->SetFont('Arial', '', 12);
    $totalPenilaian = 0;
    foreach ($kepuasan as $row) {
        $pdf->Cell(30, 10, $row['username'], 1);
        $pdf->Cell(50, 10, $row['email'], 1);
        $pdf->Cell(20, 10, $row['penilaian'], 1);
        $pdf->Ln();
        $totalPenilaian += $row['penilaian'];
    }

    $rataRataPenilaian = count($kepuasan) > 0 ? $totalPenilaian / count($kepuasan) : 0;

    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, 'Rata-rata Penilaian: ' . round($rataRataPenilaian, 2), 0, 1, 'C');

    // Buat grafik pie
    createPieChart($penilaianCounts, 'pie_chart.png');

    // Tambahkan grafik ke dalam PDF
    $pdf->Image('uploaded_images/pie_chart.png', 60, $pdf->GetY() + 10, 100, 100);
    $pdf->Ln(110);

    // Atur header HTTP untuk download file PDF
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Laporan_Kepuasan_Pelayanan.pdf"');
    header('Cache-Control: must-revalidate');
    header('Expires: 0');

    // Output PDF
    $pdf->Output('I'); // 'I' untuk output inline di browser atau 'D' untuk download langsung
}

// Buat PDF saat halaman diakses
createPDF();


?>
