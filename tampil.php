<?php
// tampil.php

// Sertakan file konfigurasi database atau inisialisasi database sesuai kebutuhan
include 'database/database.php';

// Ambil data acara dari database atau sumber data lainnya
$database = new Database();
$db = $database->getConnection();

$selectSql = "SELECT * FROM events";
$stmt = $db->prepare($selectSql);
$stmt->execute();

$eventData = array();

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // Tentukan warna berdasarkan nilai keterangan
    $color = 'blue'; // Default color
    if ($row['keterangan'] == 'survey') {
        $color = 'green';
    } elseif ($row['keterangan'] == 'pemeliharaan') {
        $color = 'red';
    }
    
    // Tambahkan data acara ke dalam array
    $eventData[] = array(
        'id' => $row['id'],
        'no_pengajuan' => $row['no_pengajuan'],
        'title' => $row['title'],
        'start' => $row['tanggal'],
        'end' => $row['tanggal'],
        'color' => $color,
    );
}

// Keluarkan data acara sebagai JSON
header('Content-Type: application/json');
echo json_encode($eventData);
?>
