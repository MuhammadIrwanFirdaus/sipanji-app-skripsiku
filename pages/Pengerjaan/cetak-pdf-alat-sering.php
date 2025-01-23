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

// Fungsi untuk membuat grafik lingkaran (pie chart)
function createPieChart($data, $filename) {
    $width = 500;  // Perbesar ukuran gambar
    $height = 500;
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

    $labelColors = [
        imagecolorallocate($image, 255, 255, 255),  // Putih untuk label agar kontras
        imagecolorallocate($image, 0, 0, 0),        // Hitam
        imagecolorallocate($image, 255, 255, 255),  // Putih
        imagecolorallocate($image, 0, 0, 0),        // Hitam
        imagecolorallocate($image, 255, 255, 255)   // Putih
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

        // Menempatkan label di dalam bagian pie
        $midAngle = deg2rad(($startAngle + $endAngle) / 2);
        $labelX = $centerX + cos($midAngle) * ($radius / 2); // Letakkan label di tengah-tengah pie
        $labelY = $centerY + sin($midAngle) * ($radius / 2);
        $label = "$key: $value";
        imagestring($image, 5, $labelX - (strlen($label) * 3), $labelY - 7, $label, $labelColors[$colorIndex]);

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

    // Mengumpulkan data untuk grafik pie
    $data = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $data[$row['alat']] = $row['total_stok'];
    }

    // Membuat grafik pie
    createPieChart($data, 'pie_chart.png');

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
    foreach ($data as $alat => $total_stok) {
        $pdf->Cell(100, 10, $alat, 1);
        $pdf->Cell(90, 10, $total_stok, 1);
        $pdf->Ln();
    }

    // Tambahkan grafik ke dalam PDF
    $pdf->Image('uploaded_images/pie_chart.png', 60, $pdf->GetY() + 10, 100, 100);
    $pdf->Ln(110);

    // Output PDF
    $pdf->Output('D', 'laporan_alat_sering_digunakan.pdf');
}

// Panggil fungsi untuk menghasilkan PDF
generatePDF();
?>
