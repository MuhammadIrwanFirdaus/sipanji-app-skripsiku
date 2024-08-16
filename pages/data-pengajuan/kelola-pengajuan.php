<?php
include "pages/login/function.php";
check_access('admin');

// Proses form untuk menerima atau menolak pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pengajuan_id = $_POST['pengajuan_id'];
    $gangguan_id = $_POST['gangguan_id'];
    $action = $_POST['action'];

    $status = ($action == 'terima') ? 'diterima' : 'ditolak';

    // Koneksi database menggunakan PDO
    $database = new Database();
    $db = $database->getConnection();

    // Update status di tabel data_pengajuan
    $queryUpdatePengajuan = "UPDATE data_pengajuan SET status = :status WHERE id = :id";
    $stmtUpdatePengajuan = $db->prepare($queryUpdatePengajuan);
    $stmtUpdatePengajuan->bindParam(':status', $status);
    $stmtUpdatePengajuan->bindParam(':id', $pengajuan_id);

    // Update status di tabel gangguan
    $queryUpdateGangguan = "UPDATE gangguan SET status = :status WHERE no_pengajuan = :gangguan_id";
    $stmtUpdateGangguan = $db->prepare($queryUpdateGangguan);
    $stmtUpdateGangguan->bindParam(':status', $status);
    $stmtUpdateGangguan->bindParam(':gangguan_id', $gangguan_id);

    if ($stmtUpdatePengajuan->execute() && $stmtUpdateGangguan->execute()) {
        echo "<div class='alert alert-success'>Pengajuan dan gangguan berhasil diperbarui.</div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmtUpdatePengajuan->errorInfo()[2] . " / " . $stmtUpdateGangguan->errorInfo()[2] . "</div>";
    }

    $db = null; // Tutup koneksi database
}

// Ambil data pengajuan dan gangguan dari database
$database = new Database();
$db = $database->getConnection();

// Query untuk mengambil data pengajuan
$queryPengajuan = "SELECT * FROM data_pengajuan WHERE status = 'sedang proses'";
$stmtPengajuan = $db->prepare($queryPengajuan);
$stmtPengajuan->execute();
$resultPengajuan = $stmtPengajuan->fetchAll(PDO::FETCH_ASSOC);

// Query untuk mengambil data gangguan
$queryGangguan = "SELECT * FROM gangguan WHERE status = 'sedang proses'";
$stmtGangguan = $db->prepare($queryGangguan);
$stmtGangguan->execute();
$resultGangguan = $stmtGangguan->fetchAll(PDO::FETCH_ASSOC);

$db = null; // Tutup koneksi database
?>
<?php include_once "partials/scripts.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola Pengajuan</title>
    <!-- Link ke Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Kelola Pengajuan</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Tempat</th>
                    <th>Tanggal Masuk</th>
                    <th>Keterangan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <!-- Tampilkan data pengajuan -->
                <?php foreach ($resultPengajuan as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['tempat']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_masuk']); ?></td>
                    <td>-</td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="pengajuan_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <input type="hidden" name="gangguan_id" value="<?php echo htmlspecialchars($row['no_pengajuan']); ?>">
                            <button type="submit" name="action" value="terima" class="btn btn-success">Terima</button>
                            <button type="submit" name="action" value="tolak" class="btn btn-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>

                <!-- Tampilkan data gangguan -->
                <?php foreach ($resultGangguan as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nama_tempat']); ?></td>
                    <td><?php echo htmlspecialchars($row['tgl_masuk']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="pengajuan_id" value="<?php echo htmlspecialchars($row['no_pengajuan']); ?>">
                            <input type="hidden" name="gangguan_id" value="<?php echo htmlspecialchars($row['no_pengajuan']); ?>">
                            <button type="submit" name="action" value="terima" class="btn btn-success">Terima</button>
                            <button type="submit" name="action" value="tolak" class="btn btn-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php?page=halaman-admin" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
