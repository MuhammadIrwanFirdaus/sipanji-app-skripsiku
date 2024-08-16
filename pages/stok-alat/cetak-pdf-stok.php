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

function createPDF() {
    ob_end_clean();
    // Membuat koneksi ke database
    $database = new Database();
    $db = $database->getConnection();

    // Query untuk mengambil data stok barang
    $selectSql = "SELECT * FROM stok_alat";
    $stmt = $db->prepare($selectSql);
    $stmt->execute();
    $stokBarang = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Inisialisasi PDF
    $pdf = new PDF();
    $pdf->AddPage();

    // Judul laporan
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'Laporan Stok Barang', 0, 1, 'C');

    // Isi Tabel
    $pdf->SetFont('Arial', '', 12);
    $nomor = 1; // Inisialisasi nomor urut
    foreach ($stokBarang as $barang) {
        $pdf->Cell(0, 10, $nomor . '. ' . $barang['nama_alat'] . '  Jumlah : ' . $barang['jumlah'] . ' Buah', 0, 1, 'L'); // Menampilkan nomor urut, nama alat, dan jumlah
        $pdf->Ln(); // Baris baru

        // Menampilkan foto alat
        $imagePath = 'uploaded_images/' . $barang['foto']; // Sesuaikan dengan lokasi penyimpanan foto
        if (file_exists($imagePath)) {
            $pdf->Image($imagePath, 10, $pdf->GetY(), 60, 0); // Menampilkan gambar dengan posisi dan ukuran yang sesuai
            $pdf->Ln(60); // Baris baru setelah gambar
        } else {
            $pdf->Cell(0, 10, 'Foto Tidak Tersedia', 0, 1, 'L'); // Menampilkan teks jika foto tidak tersedia
        }

        $nomor++; // Tambahkan nomor urut setiap kali loop berjalan
    }

    // Informasi Pimpinan
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
    $pdf->Output('Laporan Stok Barang.pdf', 'D');
    exit();
}

// Memanggil fungsi untuk membuat PDF
createPDF();
?>
