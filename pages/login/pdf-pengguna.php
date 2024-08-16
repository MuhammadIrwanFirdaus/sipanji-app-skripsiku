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

    $oldLocale = setlocale(LC_TIME, '0');
    setlocale(LC_TIME, 'id_ID.utf8');

    $formattedDate = $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));

    setlocale(LC_TIME, $oldLocale);
    return $formattedDate;
}

function createBarChart($data, $filename) {
    $width = 500;
    $height = 200;
    $barWidth = 60;
    $padding = 15;
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
    foreach ($data as $key => $count) {
        $barHeight = ($count / $maxCount) * $chartHeight;

        imagefilledrectangle($image, $x, $height - $chartPadding - $barHeight, $x + $barWidth, $height - $chartPadding, $barColor);
        imagestring($image, 5, $x + ($barWidth / 2) - 10, $height - $chartPadding + 5, ucfirst($key), $textColor);
        imagestring($image, 5, $x + ($barWidth / 2) - 10, $height - $chartPadding - $barHeight - 15, $count, $textColor);

        $x += $barWidth + $padding;
    }

    // Save image to the 'uploaded_images' folder
    $filepath = 'uploaded_images/' . $filename;
    imagepng($image, $filepath);
    imagedestroy($image);
}


function getUserRoleCounts() {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT peran, COUNT(*) as count FROM admin GROUP BY peran";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $roleCounts = [];
    foreach ($data as $row) {
        $roleCounts[$row['peran']] = $row['count'];
    }

    return $roleCounts;
}

function getSubmissionData() {
    $database = new Database();
    $db = $database->getConnection();

    $query = "SELECT admin.peran, COUNT(*) as count FROM data_pengajuan JOIN admin ON data_pengajuan.user_id = admin.id GROUP BY admin.peran";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $submissionCounts = [];
    foreach ($data as $row) {
        $submissionCounts[$row['peran']] = $row['count'];
    }

    return $submissionCounts;
}

function getUsernames($userIds) {
    $database = new Database();
    $db = $database->getConnection();

    $placeholders = implode(',', array_fill(0, count($userIds), '?'));
    $query = "SELECT id, username FROM admin WHERE id IN ($placeholders)";
    $stmt = $db->prepare($query);
    $stmt->execute($userIds);
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $usernames = [];
    foreach ($data as $row) {
        $usernames[$row['id']] = $row['username'];
    }

    return $usernames;
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

function createPDF() {
    ob_end_clean();
    $database = new Database();
    $db = $database->getConnection();

    // Fetch user role data
    $roleCounts = getUserRoleCounts();

    // Fetch submission data
    $submissionCounts = getSubmissionData();

    // Fetch user details for most frequent submissions
    if (!empty($submissionCounts)) {
        $mostFrequentRole = array_keys($submissionCounts, max($submissionCounts))[0];
        $mostFrequentCount = max($submissionCounts);
    } else {
        $mostFrequentRole = 'N/A';
        $mostFrequentCount = 0;
    }

    // Create PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Header Table
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Statistik Pengguna', 0, 1, 'C');

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(90, 10, 'Statistik', 1, 0, 'C');
    $pdf->Cell(50, 10, 'Nilai', 1, 1, 'C');

    // Table Content
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(90, 10, 'Jumlah Total Pengguna', 1, 0, 'L');
    $pdf->Cell(50, 10, array_sum($roleCounts), 1, 1, 'L');

    foreach ($roleCounts as $role => $count) {
        $pdf->Cell(90, 10, 'Jumlah Pengguna dengan Peran ' . ucfirst($role), 1, 0, 'L');
        $pdf->Cell(50, 10, $count, 1, 1, 'L');
    }

    // Create and add user role chart
    createBarChart($roleCounts, 'user_role_chart.png');
    $pdf->Image('uploaded_images/user_role_chart.png', 10, $pdf->GetY() + 10, 150);

    // Create and add submission chart
    createBarChart($submissionCounts, 'submission_chart.png');
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Jumlah Pengajuan', 0, 1, 'C');
    $pdf->Image('uploaded_images/submission_chart.png', 10, $pdf->GetY() + 10, 150);

    // Add most frequent submitter information
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Pengajuan Paling Sering', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, 'Peran dengan jumlah pengajuan terbanyak adalah ' . ucfirst($mostFrequentRole) . ' dengan jumlah pengajuan ' . $mostFrequentCount, 0, 1, 'L');

    // Output PDF
    $pdf->Output('Laporan Statistik Pengguna.pdf', 'D');
    exit();
}

createPDF();
?>

