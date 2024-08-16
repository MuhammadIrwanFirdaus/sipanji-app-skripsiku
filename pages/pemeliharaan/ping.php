<?php
include_once "database/database.php";
header('Content-Type: application/json');

// Ambil daftar perangkat dari database
$database = new Database();
$db = $database->getConnection();
$query = "SELECT * FROM alat";
$result = $db->query($query);

$devices = [];

while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $device_ip = $row['device_id'];
    $ping_result = shell_exec("ping -n 1 $device_ip");

    $status = (strpos($ping_result, 'Reply from') !== false) ? 'terhubung' : 'terputus';
    $devices[] = [
        'ip_address' => $device_ip,
        'status' => $status
    ];
}

echo json_encode($devices);
?>
