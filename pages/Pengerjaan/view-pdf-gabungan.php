<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF with Date Filter</title>

    <!-- Tautan ke file JavaScript dan CSS yang diperlukan -->
    <script src="assets/jquery.min.js"></script>
    <script src="assets/moment.min.js"></script>
    <script src="assets/bootstrap/css/bootstrap.min.css"></script>
</head>
<body>
    <h2>Filter Pemasangan</h2>
    <form action="?page=cetak-pdf-gabungan" method="post">
        <label for="tempat">Pilih Instansi:</label>
        <select name="tempat" id="tempat" required>
            <option value="">Pilih Instansi</option>
            <?php
            // Ambil daftar nama instansi dari database
            $database = new Database();
            $db = $database->getConnection();
            $selectSql = "SELECT DISTINCT tempat FROM pengerjaan";
            $stmt = $db->prepare($selectSql);
            $stmt->execute();
            $instansiList = $stmt->fetchAll(PDO::FETCH_COLUMN);
            foreach ($instansiList as $instansi) {
                echo "<option value=\"$instansi\">$instansi</option>";
            }
            ?>
        </select>

        <button type="submit">Download Laporan</button>
    </form>

    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
    <?php include "partials/scripts.php" ?>
</body>
</html>
