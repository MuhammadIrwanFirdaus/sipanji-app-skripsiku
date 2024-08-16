<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$id = isset($_GET['id']) ? $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    // Ambil nilai dari form
    $id = $_POST['id_pengerjaan'];
    $no_pengajuan = $_POST['no_pengajuan'];
    $tempat = $_POST['tempat'];
    $tanggal = $_POST['tanggal'];
    $nama_pemasang = $_POST['nama_pemasang'];
    $foto = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];

    // Siapkan query untuk menyimpan data ke dalam tabel pengerjaan
    $updateSQL = "UPDATE pengerjaan SET no_pengajuan = ?, tempat = ?, tanggal = ?, nama_pemasang = ?, foto_pengerjaan = ? WHERE id_pengerjaan = ?";
    $stmt = $db->prepare($updateSQL);

    // Bind nilai ke dalam statement
    $stmt->bindParam(1, $no_pengajuan);
    $stmt->bindParam(2, $tempat);
    $stmt->bindParam(3, $tanggal);
    $stmt->bindParam(4, $nama_pemasang);
    $stmt->bindParam(5, $foto);
    $stmt->bindParam(6, $id);

    if ($stmt->execute()) {
        // Proses unggah foto
        // Ubah "path/to/uploaded_images/" sesuai dengan direktori yang diinginkan
        $targetDir = "uploaded_images/";
        $targetFile = $targetDir . basename($foto);

        if (move_uploaded_file($tmp_file, $targetFile)) {
            // Foto berhasil diunggah
        } else {
            // Foto gagal diunggah
        }
        echo "<meta http-equiv='refresh' content='0; url=?page=tampil-pengerjaan'>";
    } else {
        // Handle kesalahan jika penyimpanan data pengerjaan gagal
    }
}

// Ambil data pengerjaan berdasarkan ID
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM pengerjaan WHERE id_pengerjaan = ?";
$stmt = $db->prepare($query);
$stmt->bindParam(1, $id);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);

// Pastikan data ditemukan sebelum menampilkan formulir
if (!$row) {
    echo "Data Pengerjaan tidak ditemukan.";
    exit;
}

// Ambil data dari hasil query
$no_pengajuan = $row['no_pengajuan'];
$tempat = $row['tempat'];
$tanggal = $row['tanggal'];
$nama_pemasang = $row['nama_pemasang'];
$foto_pengerjaan = $row['foto_pengerjaan'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Data Pengerjaan</title>
    <!-- Panggil sumber daya Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Edit Data Pengerjaan</h1>
        <form method="POST" enctype="multipart/form-data" id="formPengerjaan">
            <input type="hidden" name="id_pengerjaan" value="<?php echo $id; ?>">
            <div class="form-group">
                <label for="no_pengajuan">No. Pengajuan</label>
                <input type="text" class="form-control" id="no_pengajuan" name="no_pengajuan" value="<?php echo $no_pengajuan; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tempat">Tempat</label>
                <input type="text" class="form-control" id="tempat" name="tempat" value="<?php echo $tempat; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d\TH:i', strtotime($tanggal)); ?>" required>
            </div>
            <div class="form-group">
                <label for="nama_pemasang">Nama Pemasang</label>
                <input type="text" class="form-control" id="nama_pemasang" name="nama_pemasang" value="<?php echo $nama_pemasang; ?>" required>
            </div>
            <div class="form-group">
                <label for="foto_pengerjaan">Unggah Foto Pengerjaan</label>
                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php?page=tampil-pengerjaan" class="btn btn-secondary">Kembali</a>
        </form>
    </div>

    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
