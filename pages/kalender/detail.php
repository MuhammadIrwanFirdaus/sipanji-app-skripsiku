<?php
// Pastikan ada koneksi ke database
$database = new Database();
$db = $database->getConnection();

// Fungsi untuk mengubah format hari dan bulan ke Bahasa Indonesia
function indoDate($datetime) {
    $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
    $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
    
    $day = date('w', strtotime($datetime));
    $month = date('n', strtotime($datetime));
    $date = date('j', strtotime($datetime)); // Mendapatkan angka tanggal

    return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));
}


// Periksa apakah ada parameter ID yang diterima
if(isset($_GET['id'])) {
    $event_id = $_GET['id'];

    // Query untuk mengambil detail acara berdasarkan ID
    $selectSql = "SELECT * FROM events WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $event_id);
    $stmt->execute();

    // Cek apakah acara ditemukan
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Tampilkan detail acara
?>
<?php include "partials/scripts.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Acara</title>
    <!-- Panggil sumber daya Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Detail Jadwal</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $row['title']; ?></h5>
                <p class="card-text"><strong>Tanggal dan Waktu:</strong> <?php echo indoDate($row['tanggal']); ?></p>
                <p class="card-text"><strong>Keterangan:</strong> <?php echo $row['keterangan']; ?></p>
            </div>
        </div>
        <a href="index.php?page=Jadwal" class="btn btn-secondary">Kembali</a>
    </div>


    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "Acara tidak ditemukan.";
    }
} else {
    echo "ID acara tidak valid.";
}
?>
