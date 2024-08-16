<?php
include "partials/scripts.php";

$idPengajuan = isset($_GET['id']) ? $_GET['id'] : null;
$noPengajuan = isset($_GET['no_pengajuan']) ? $_GET['no_pengajuan'] : null;
$tempat = isset($_GET['tempat']) ? $_GET['tempat'] : null;

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_pengajuan = isset($_POST['no_pengajuan']) ? $_POST['no_pengajuan'] : null;
    $title = isset($_POST['title']) ? $_POST['title'] : null;
    $tanggal = isset($_POST['tanggal']) ? $_POST['tanggal'] : null;
    $keterangan = isset($_POST['keterangan']) ? $_POST['keterangan'] : null;

    $insertSQL = "INSERT INTO events (no_pengajuan, title, tanggal, keterangan) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($insertSQL);
    $stmt->bindParam(1, $no_pengajuan);
    $stmt->bindParam(2, $title);
    $stmt->bindParam(3, $tanggal);
    $stmt->bindParam(4, $keterangan);

    if ($stmt->execute()) {
        echo "Berhasil Simpan Data";
    } else {
        echo "Gagal Simpan Data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=Jadwal'>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Jadwal Pemasangan</title>
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Tambah Jadwal</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="no_pengajuan">No. Pengajuan</label>
                <input type="text" class="form-control" id="no_pengajuan" name="no_pengajuan" value="<?php echo htmlspecialchars($noPengajuan); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="title">Tempat</label>
                <input type="text" class="form-control" id="tempat" name="title" value="<?php echo htmlspecialchars($tempat); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <select class="form-control" id="keterangan" name="keterangan" required>
                    <option value="">Pilih Keterangan</option>
                    <option value="survey">Survey</option>
                    <option value="pemasangan">Pemasangan</option>
                    <option value="pemeliharaan">Pemeliharaan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Jadwal</button>
            <a href="index.php?page=Jadwal" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
