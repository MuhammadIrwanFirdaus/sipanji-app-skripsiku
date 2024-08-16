<?php
// Pastikan ada koneksi ke database
$database = new Database();
$db = $database->getConnection();

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
    $id = $_GET['id'];

    // Query untuk mengambil detail acara berdasarkan ID
    $selectSql = "SELECT * FROM data_pengajuan WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();

    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
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
                <h5 class="card-title"><?php echo $row['tempat']; ?></h5>
                <p class="card-text"><strong>Nama Perwakilan: </strong> <?php echo $row['nama_perwakilan']; ?></p>
                <p class="card-text"><strong>Alamat: </strong> <?php echo $row['alamat']; ?></p>
                <p class="card-text"><strong>Nomor yang bisa dihubungi:</strong> <?php echo $row['no_telpon']; ?></p>
                <td><?php echo indoDate($row['tgl_masuk']) ?></td>
                <br>
                <a href="<?php echo $row['koordinat']; ?>" class="btn btn-success btn-sm" target="blank_"><i class="fa fa-map-marker-alt"></i> Lihat Lokasi</a>
                <td>
                            <a href="uploads_surat/<?php echo $row['surat_pengajuan']; ?>" target="blank_" class="btn btn-primary btn-sm"><i class="fa fa-file-pdf"></i> Lihat PDF</a>
                            <!-- Tambahkan kolom untuk menampilkan file PDF -->
                        </td>
                        <td>
                                <a href="uploaded_images/<?php echo $row['foto']; ?>" target="_blank">
                                    <img src="uploaded_images/<?php echo $row['foto']; ?>" alt="foto" width="100">
                                </a>
                            </td>
            </div>
        </div>
        <a href="index.php?page=tampil-pengajuan" class="btn btn-secondary">Kembali</a>
    </div>


    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "tidak ditemukan.";
    }
} else {
    echo "ID tidak valid.";
}
?>
