<?php
ob_end_clean();
require('fpdf/fpdf.php');
require_once('database/Database.php');

class PDF extends FPDF {
    function Header() {
        // Logo dan Kop Surat
        $this->Image('dist/img/Logo Banjarbaru.jpg', 10, 10, 35); // Ganti dengan path logo Anda dan atur ukuran gambar
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

ob_end_clean();
$pdf = new PDF('L'); // Menggunakan kelas PDF yang telah Anda definisikan

// Membuat koneksi ke database
$database = new Database();
$db = $database->getConnection();

// Query untuk mengambil data stok terpakai, alat, dan foto pengerjaan berdasarkan nama tempat
if (isset($_POST['tempat'])) {
    $nama_tempat = $_POST['tempat'];

    // Ambil data
    $selectSql = "SELECT no_pengajuan, alat, stok_terpakai, foto_pengerjaan, tanggal FROM pengerjaan WHERE tempat = :tempat";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':tempat', $nama_tempat);
    $stmt->execute();
    $data_pengerjaan = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Kelompokkan data berdasarkan no_pengajuan
    $dataGrouped = [];
    foreach ($data_pengerjaan as $pengerjaan) {
        $no_pengajuan = $pengerjaan['no_pengajuan'];
        if (!isset($dataGrouped[$no_pengajuan])) {
            $dataGrouped[$no_pengajuan] = [];
        }
        $dataGrouped[$no_pengajuan][] = $pengerjaan;
    }

    // Proses setiap no_pengajuan
    foreach ($dataGrouped as $no_pengajuan => $pengerjaanList) {
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 16);
        $pdf->Cell(0, 10, 'Laporan Pengerjaan - No Pengajuan: ' . $no_pengajuan, 0, 1, 'C');
        
        $pdf->SetFont('Arial', 'I', 12);
        $pdf->Cell(0, 10, 'Tanggal: ' . $pengerjaanList[0]['tanggal'], 0, 1, 'C');

        // Tulis teks dan foto pengerjaan untuk setiap kelompok
        foreach ($pengerjaanList as $pengerjaan) {
            $alat = $pengerjaan['alat'];
            $stok_terpakai = $pengerjaan['stok_terpakai'];
            $informasi = 'Alat: ' . $alat . ', Stok Terpakai: ' . $stok_terpakai;
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, $informasi, 0, 1);
        }

        // Tambahkan jarak antara informasi stok dan alat dengan kumpulan foto pengerjaan
        $pdf->Ln(10);

        // Batasi jumlah foto yang ditampilkan per halaman
        $maxPhotosPerRow = 2; // Maksimum foto per baris
        $maxWidth = 90; // Lebar maksimum gambar dalam mm
        $x = 10; // Posisi X awal
        $y = $pdf->GetY(); // Posisi Y awal

        // Gambar foto
        $photoCount = 0;
        foreach ($pengerjaanList as $pengerjaan) {
            $imagePath = 'uploaded_images/' . $pengerjaan['foto_pengerjaan'];
            if (file_exists($imagePath)) {
                // Cek ukuran gambar
                list($width, $height) = getimagesize($imagePath);

                // Menghitung skala
                $scale = $maxWidth / ($width / 25.4); // Menggunakan 25.4 untuk konversi mm ke inci

                // Mengatur ukuran gambar
                $newWidth = $width * $scale / 25.4;
                $newHeight = $height * $scale / 25.4;

                // Jika gambar melebihi lebar halaman, pindah ke baris berikutnya
                if ($x + $newWidth > $pdf->GetPageWidth() - 10) {
                    $x = 10;
                    $y += $newHeight + 10; // Tambahkan jarak antara gambar
                    if ($y + $newHeight > $pdf->GetPageHeight() - 30) { // Ganti 30 jika tinggi footer berbeda
                        $pdf->AddPage();
                        $x = 10;
                        $y = $pdf->GetY();
                    }
                }

                $pdf->Image($imagePath, $x, $y, $newWidth, $newHeight, 'JPEG'); // Menampilkan gambar
                $x += $newWidth + 10; // Tambahkan jarak antara gambar
                $photoCount++;

                // Jika sudah mencetak maksimal foto per halaman, tambahkan halaman baru dan reset foto counter
                if ($photoCount % $maxPhotosPerRow == 0) {
                    $x = 10;
                    $y += $newHeight + 10; // Tambahkan jarak antara baris gambar
                    if ($y + $newHeight > $pdf->GetPageHeight() - 30) { // Ganti 30 jika tinggi footer berbeda
                        $pdf->AddPage();
                        $x = 10;
                        $y = $pdf->GetY();
                    }
                }
            }
        }

        // Tambahkan footer di bawah foto
        $pdf->Ln(10); // Tambahkan jarak sebelum footer
        $pdf->Cell(0, 10, 'Banjarbaru, ' . date('d F Y'), 0, 1, 'R');
        $pdf->SetFont('Arial', 'I', 14);
        $pdf->Cell(0, 10, 'Mengetahui, ', 0, 1, 'R');
        $pdf->Cell(0, 5, 'Kepala Dinas', 0, 1, 'R');
        $pdf->Cell(0, 10, '', 0, 1, 'R');
        $pdf->SetFont('Arial', 'U', 14);
        $pdf->Cell(0, 10, 'Asep Saputra, S. Kom, MM', 0, 1, 'R');
        $pdf->SetFont('Arial', '', 14);
        $pdf->Cell(0, 5, 'NIP. 19770909 200604 1 006', 0, 1, 'R');
        $pdf->Ln(10); // Menambahkan jarak antara informasi pimpinan dan tabel
    }
}

// Outputkan file PDF langsung ke browser
$pdf->Output('Laporan Pengerjaan - ' . $nama_tempat . '.pdf', 'D');
exit();
?>
