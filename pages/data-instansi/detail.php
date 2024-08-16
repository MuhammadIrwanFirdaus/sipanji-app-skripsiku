<?php
// Pastikan ada koneksi ke database
$database = new Database();
$db = $database->getConnection();


// Periksa apakah ada parameter ID yang diterima
if(isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil detail acara berdasarkan ID
    $selectSql = "SELECT * FROM data_instansi WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $id);
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
    <title>Detail Tempat</title>
    <!-- Panggil sumber daya Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Detail Tempat</h1>
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><?php echo $row['nama_instansi']; ?></h5>
                <p class="card-text"><strong>Nama Perwakilan: </strong> <?php echo $row['nama_perwakilan']; ?></p>
                <p class="card-text"><strong>Alamat: </strong> <?php echo $row['alamat']; ?></p>
                <p class="card-text"><strong>Nomor yang bisa dihubungi:</strong> <?php echo $row['no_telpon']; ?></p>
                <p><a href="<?php echo $row['koordinat'] ?>" target="_blank">Lihat Lokasi</p>
            </div>
        </div>
        <a href="index.php?page=tampil-data-instansi" class="btn btn-secondary">Kembali</a>
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
