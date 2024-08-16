<?php
include_once "partials/cssdatatables.php";
include_once "database/database.php";

$database = new Database();
$db = $database->getConnection();


// Query untuk mengambil data dari tabel 'data_pengajuan'
$dataPengajuanQuery = "
    SELECT tempat, koordinat
    FROM data_pengajuan where status = 'diterima'
";
$dataPengajuanResult = $db->query($dataPengajuanQuery);

// Fungsi untuk mengekstrak latitude dan longitude dari URL Google Maps
function extractCoordinates($url) {
    // Coba regex untuk URL Google Maps
    $pattern = '/q=(-?\d+\.\d+),(-?\d+\.\d+)/';
    if (preg_match($pattern, $url, $matches)) {
        return [$matches[1], $matches[2]];
    }
    return [null, null];
}

$deviceArray = [];
while ($row = $dataPengajuanResult->fetch(PDO::FETCH_ASSOC)) {
    list($latitude, $longitude) = extractCoordinates($row['koordinat']);
    if ($latitude && $longitude) {
        $deviceArray[] = [
            'tempat' => $row['tempat'],
            'latitude' => $latitude,
            'longitude' => $longitude,
            'koordinat' => $row['koordinat']
        ];
    }
}

// Debugging: Print array to check data
echo '<script>console.log(' . json_encode($deviceArray) . ');</script>';
?>


<div id="map" style="height: 500px; width: 100%;"></div>

<?php include_once "partials/scripts.php" ?>
<?php include_once "partials/scriptsdatatables.php" ?>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<script>
document.addEventListener('DOMContentLoaded', (event) => {
    // Inisialisasi peta
    var map = L.map('map').setView([-3.440425, 114.8324361], 15); // Koordinat awal peta (DISKOMINFO KOTA BANJARBARU)

    // Tambahkan layer tile
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Ambil data koordinat dari PHP
    const deviceData = <?php
        // Generate a JavaScript array with the extracted coordinates
        echo json_encode($deviceArray);
    ?>;

    console.log('Device Data:', deviceData); // Debugging: Log data

    // Tambahkan marker ke peta
    deviceData.forEach(device => {
        if (device.latitude && device.longitude) {
            L.marker([device.latitude, device.longitude])
                .addTo(map)
                .bindPopup(`<b>${device.tempat}</b><br><a href="${device.koordinat}" target="_blank">Lihat di Google Maps</a>`)
                .on('click', function() {
                    window.open(device.koordinat, '_blank');
                });
        } else {
            console.log('Invalid coordinates for device:', device); // Debugging: Log invalid coordinates
        }
    });

    // Sesuaikan tampilan peta berdasarkan marker
    if (deviceData.length > 0) {
        const bounds = L.latLngBounds(deviceData.map(device => {
            return [device.latitude, device.longitude];
        }));
        map.fitBounds(bounds);
    }
});
</script>
