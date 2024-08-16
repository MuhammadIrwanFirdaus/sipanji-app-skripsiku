<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate PDF with Date Filter</title>

    <script src="assets/jquery.min.js"></script>
    <script src="assets/moment.min.js"></script>
    <script src="assets/bootstrap/css/bootstrap.min.css"></script>
</head>
<body>
    <h2>Filter Tanggal</h2>
    <form action="?page=cetak-pdf-biaya-operasional" method="post">
        <label for="start_date">Tanggal Awal:</label>
        <input type="date" id="start_date" name="start_date" required>

        <label for="end_date">Tanggal Akhir:</label>
        <input type="date" id="end_date" name="end_date" required>

        <button type="submit">Download Laporan</button>
    </form>


        <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
        <script src="assets/jquery.min.js"></script>
        <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php include "partials/scripts.php" ?>
<?php include_once "partials/scripts.php" ?>
<?php include_once "partials/scriptsdatatables.php" ?>
