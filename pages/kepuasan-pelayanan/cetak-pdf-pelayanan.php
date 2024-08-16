<?php
ob_end_clean();
require('fpdf/fpdf.php');
require_once('database/Database.php');

// Function to format date in Indonesian
function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime)); // Mendapatkan angka tanggal

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y', strtotime($datetime));
    return $formattedDate;
}

// PDF class definition
class PDF extends FPDF {
    function Header() {
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 40); // Ganti dengan path logo Anda dan atur ukuran gambar
        $this->SetFont('Arial', 'B', 16); // Atur ukuran font nama perusahaan
        $this->Cell(0, 5, 'Pemerintah Kota Banjarbaru', 0, 1, 'C');
        $this->Cell(0, 5, 'Dinas Komunikasi dan Informatika Kota Banjarbaru', 0, 1, 'C');
        $this->SetFont('Arial', 'B', 8); // Atur ukuran font nama perusahaan
        $this->Cell(0, 5, 'Loktabat Utara, Kec. Banjarbaru Utara, Kota Banjar Baru, Kalimantan Selatan 70714', 0, 1, 'C');
        $this->Cell(0, 5, 'Telepon: 0811-5289-090', 0, 1, 'C');

        $this->Ln(5); // Tambahkan sedikit jarak antara kop dan garis
        $this->SetLineWidth(0.5);
        $this->Line(10, $this->GetY() + 1, $this->GetPageWidth() - 10, $this->GetY() + 1);
        $this->Ln(10); // Menambahkan jarak antara judul dan gambar
    }

    function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, false, 'C');
    }
}

// Function to create a bar chart
function createBarChart($data, $filename) {
    $width = 400;
    $height = 200;
    $barWidth = 60;
    $padding = 10;
    $chartPadding = 30;
    $chartWidth = $width - 2 * $chartPadding;
    $chartHeight = $height - 2 * $chartPadding;

    $image = imagecreatetruecolor($width, $height);
    $backgroundColor = imagecolorallocate($image, 255, 255, 255);
    $barColor = imagecolorallocate($image, 0, 123, 255);
    $borderColor = imagecolorallocate($image, 0, 0, 0);
    $textColor = imagecolorallocate($image, 0, 0, 0);

    imagefill($image, 0, 0, $backgroundColor);
    imagerectangle($image, $chartPadding - 1, $chartPadding - 1, $width - $chartPadding, $height - $chartPadding, $borderColor);

    $x = $chartPadding;
    $maxCount = max($data);
    if ($maxCount == 0) $maxCount = 1;

    foreach ($data as $key => $count) {
        $barHeight = ($count / $maxCount) * $chartHeight;

        imagefilledrectangle($image, $x, $height - $chartPadding - $barHeight, $x + $barWidth, $height - $chartPadding, $barColor);
        imagestring($image, 5, $x + ($barWidth / 2) - 10, $height - $chartPadding + 5, ucfirst($key), $textColor);
        imagestring($image, 5, $x + ($barWidth / 2) - 10, $height - $chartPadding - $barHeight - 15, $count, $textColor);

        $x += $barWidth + $padding;
    }

    // Ensure the directory exists
    if (!is_dir('uploaded_images')) {
        mkdir('uploaded_images', 0755, true);
    }

    $filepath = 'uploaded_images/' . $filename;
    imagepng($image, $filepath);
    imagedestroy($image);
}

function createPDF() {
    ob_end_clean();
    $database = new Database();
    $db = $database->getConnection();

    // Query to fetch data from kepuasan_pelayanan table
    $selectSql = "SELECT * FROM kepuasan_pelayanan ORDER BY tanggal DESC";
    $stmt = $db->prepare($selectSql);
    $stmt->execute();
    $kepuasan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count ratings for each value (1-5)
    $penilaianCounts = array_fill(1, 5, 0);
    foreach ($kepuasan as $row) {
        if (isset($penilaianCounts[$row['penilaian']])) {
            $penilaianCounts[$row['penilaian']]++;
        }
    }

    // Initialize PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Title
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Kepuasan Pelayanan', 0, 1, 'C');

    // Table Header
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(30, 10, 'Username', 1);
    $pdf->Cell(50, 10, 'Email', 1);
    $pdf->Cell(20, 10, 'Penilaian', 1);
    $pdf->Ln();

    $totalPenilaian = 0;
    $jumlahData = count($kepuasan);

    // Table Content
    $pdf->SetFont('Arial', '', 12);
    foreach ($kepuasan as $row) {
        $pdf->Cell(30, 10, $row['username'], 1);
        $pdf->Cell(50, 10, $row['email'], 1);
        $pdf->Cell(20, 10, $row['penilaian'], 1);

        $totalPenilaian += $row['penilaian'];
        $pdf->Ln();
    }

    // Calculate average rating
    $rataRataPenilaian = $jumlahData > 0 ? $totalPenilaian / $jumlahData : 0;

    // Display total and average rating at the bottom
    $pdf->Ln(10);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(35, 10, 'Total Penilaian:', 0);
    $pdf->Cell(50, 10, $totalPenilaian, 0, 1, 'L');
    $pdf->Cell(40, 10, 'Rata-rata Penilaian:', 0);
    $pdf->Cell(50, 10, number_format($rataRataPenilaian, 2), 0, 1, 'L');

    // Create and add rating chart
    createBarChart($penilaianCounts, 'penilaian_chart.png');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Grafik Penilaian Kepuasan', 0, 1, 'C');
    $pdf->Image('uploaded_images/penilaian_chart.png', 10, $pdf->GetY() + 10, 150);

    // Output PDF
    $pdf->Output('Laporan_Kepuasan_Pelayanan.pdf', 'D');
    exit();
}

// Call the function to create the PDF
createPDF();
?>
