<?php
// include koneksi ke database (gantilah dengan koneksi ke database Anda)
include "koneksi.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $eventId = $_POST["id"];
    $newStartDate = $_POST["newStartDate"];
    $newEndDate = $_POST["newEndDate"];

    // Lakukan validasi data jika diperlukan
    // ...

    // Lakukan update tanggal event di dalam database
    $sql = "UPDATE events SET tanggal = '$newStartDate' WHERE id = $eventId";
    if ($koneksi->query($sql) === TRUE) {
        // Pembaruan berhasil
        $response = "Pembaruan berhasil!";
    } else {
        // Pembaruan gagal
        $response = "Pembaruan gagal: " . $koneksi->error;
    }
} else {
    // Jika metode bukan POST
    $response = "Permintaan tidak valid!";
}

echo json_encode(array("message" => $response));
?>
