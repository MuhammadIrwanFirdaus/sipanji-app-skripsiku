<?php
include "database/database.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    $no_pengajuan = isset($_POST['no_pengajuan']) ? $_POST['no_pengajuan'] : null;

    // Query untuk mengambil tempat berdasarkan no_pengajuan
    $queryTempat = "SELECT tempat FROM data_pengajuan WHERE no_pengajuan = ?";
    $stmtTempat = $db->prepare($queryTempat);
    $stmtTempat->bindParam(1, $no_pengajuan);
    $stmtTempat->execute();

    // Ambil informasi tempat
    $tempat = $stmtTempat->fetch(PDO::FETCH_ASSOC);

    // Kembalikan sebagai JSON
    header('Content-Type: application/json');
    echo json_encode(['tempat' => $tempat['tempat']]);
    exit();
}
?>
