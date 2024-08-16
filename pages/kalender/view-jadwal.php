<?php
include_once "partials/cssdatatables.php";
include_once "database/database.php";
include_once "partials/scripts.php";

$database = new Database();
$db = $database->getConnection();

$jadwalData = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Query untuk mengambil data yang sesuai dengan filter tanggal dan keterangan
    $query = "
        SELECT *
        FROM events
        WHERE (tanggal BETWEEN :start_date AND :end_date)
    ";

    $stmt = $db->prepare($query);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();
    $jadwalData = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Jadwal dengan Filter</title>
    <link rel="stylesheet" href="assets/bootstrap/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-4">
        <h2>Filter Jadwal</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="start_date">Tanggal Awal:</label>
                <input type="date" id="start_date" name="start_date" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="end_date">Tanggal Akhir:</label>
                <input type="date" id="end_date" name="end_date" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3">Tampilkan Jadwal</button>
        </form>

        <?php if (!empty($jadwalData)): ?>
            <h3 class="mt-5">Hasil Filter</h3>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Tempat</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($jadwalData as $jadwal): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($jadwal['tanggal']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['title']); ?></td>
                        <td><?php echo htmlspecialchars($jadwal['keterangan']); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <a href="?page=download-jadwal&start_date=<?php echo $start_date; ?>&end_date=<?php echo $end_date; ?>" class="btn btn-success mt-3">
                Download Laporan
            </a>
        <?php endif; ?>
    </div>

    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
