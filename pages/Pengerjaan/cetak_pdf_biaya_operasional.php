<?php
ob_end_clean();
require('fpdf/fpdf.php');
include_once 'database/database.php';

// Define the PDF class extending FPDF
class PDF extends FPDF {
    var $widths;
    var $aligns;

    function SetWidths($w) {
        // Set the array of column widths
        $this->widths = $w;
    }

    function SetAligns($a) {
        // Set the array of column alignments
        $this->aligns = $a;
    }

    function Row($data) {
        // Calculate the height of the row
        $nb = 0;
        for ($i = 0; $i < count($data); $i++) {
            $nb = max($nb, $this->NbLines($this->widths[$i], $data[$i]));
        }
        $h = 5 * $nb;

        // Issue a page break first if needed
        $this->CheckPageBreak($h);

        // Draw the cells of the row
        for ($i = 0; $i < count($data); $i++) {
            $w = $this->widths[$i];
            $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';

            // Save the current position
            $x = $this->GetX();
            $y = $this->GetY();

            // Draw the border
            $this->Rect($x, $y, $w, $h);

            // Print the text
            $this->MultiCell($w, 5, $data[$i], 0, $a);

            // Put the position to the right of the cell
            $this->SetXY($x + $w, $y);
        }

        // Go to the next line
        $this->Ln($h);
    }

    function CheckPageBreak($h) {
        // If the height h would cause an overflow, add a new page immediately
        if ($this->GetY() + $h > $this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
        }
    }

    function NbLines($w, $txt) {
        // Calculates the number of lines a MultiCell of width w will take
        $cw = &$this->CurrentFont['cw'];
        if ($w == 0) {
            $w = $this->w - $this->rMargin - $this->x;
        }
        $wmax = ($w - 2 * $this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 && $s[$nb - 1] == "\n") {
            $nb--;
        }
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ') {
                $sep = $i;
            }
            $l += $cw[$c];
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j) {
                        $i++;
                    }
                } else {
                    $i = $sep + 1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            } else {
                $i++;
            }
        }
        return $nl;
    }

    function Header() {
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 35); // Logo path and size
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

// Get date range from POST
$start_date = $_POST['start_date'];
$end_date = $_POST['end_date'];

// Initialize database connection
$database = new Database();
$db = $database->getConnection();

// Query to fetch data
$query = "
    SELECT p.tempat, p.tanggal, p.nama_pemasang, p.alat, p.stok_terpakai, p.foto_pengerjaan, 
           s.harga AS harga, 
           (p.stok_terpakai * s.harga) AS biaya_operasional,
           p.biaya_tambahan,
           (p.stok_terpakai * s.harga + p.biaya_tambahan) AS total_biaya_operasional
    FROM pengerjaan p
    JOIN stok_alat s ON p.alat = s.nama_alat
    WHERE p.tanggal BETWEEN :start_date AND :end_date
";
$stmt = $db->prepare($query);
$stmt->bindParam(':start_date', $start_date);
$stmt->bindParam(':end_date', $end_date);
$stmt->execute();

// Calculate total operational cost
$total_cost = 0;
$data = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_cost += $row['total_biaya_operasional'];
    $data[] = $row;
}

// Create PDF
ob_end_clean();
$pdf = new PDF('L');
$pdf->SetWidths(array(40, 30, 50, 30, 25, 25, 30, 30, 40));
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Laporan Biaya Operasional', 0, 1, 'C');
$pdf->Cell(0, 10, "Tanggal: $start_date s/d $end_date", 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', 'B', 10);
$pdf->Row(array('Tempat', 'Tanggal', 'Nama Pemasang', 'Alat', 'Stok Terpakai', 'Harga', 'Biaya Operasional', 'Biaya Tambahan', 'Total Biaya'));

$pdf->SetFont('Arial', '', 10);
foreach ($data as $row) {
    $pdf->Row(array(
        $row['tempat'],
        $row['tanggal'],
        $row['nama_pemasang'],
        $row['alat'],
        $row['stok_terpakai'],
        number_format($row['harga'], 2),
        number_format($row['biaya_operasional'], 2),
        number_format($row['biaya_tambahan'], 2),
        number_format($row['total_biaya_operasional'], 2)
    ));
}

// Add total operational cost
$pdf->Ln(10);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(240, 10, 'Total Biaya Operasional:', 0, 0, 'R');
$pdf->Cell(40, 10, number_format($total_cost, 2), 1, 1, 'R');

$pdf->Cell(0, 10, 'Banjarbaru, ' . date('d F Y'), 0, 1, 'R');
$pdf->SetFont('Arial', 'I', 14);
$pdf->Cell(0, 10, 'Mengetahui, ', 0, 1, 'R');
$pdf->Cell(0, 5, 'Kepala Dinas', 0, 1, 'R');
$pdf->Cell(0, 10, '', 0, 1, 'R');
$pdf->SetFont('Arial', 'U', 14);
$pdf->Cell(0, 10, 'Asep Saputra, S. Kom, MM', 0, 1, 'R');
$pdf->SetFont('Arial', '', 14);
$pdf->Cell(0, 0, 'NIP. 1234567890', 0, 1, 'R');

// Output PDF
$pdf->Output('D', "laporan_biaya_operasional_" . date('Y-m-d') . ".pdf");
?>
