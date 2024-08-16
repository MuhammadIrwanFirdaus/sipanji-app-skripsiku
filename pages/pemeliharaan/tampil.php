<?php
include_once "partials/cssdatatables.php";
include_once "database/database.php";

$database = new Database();
$db = $database->getConnection();

// Query untuk mengambil data dari tabel 'alat'
$alatQuery = "
    SELECT id, tempat, device_id, status, kerusakan
    FROM alat
";
$alatResult = $db->query($alatQuery);

// Query untuk mengambil data dari tabel 'data_pengajuan'
$dataPengajuanQuery = "
    SELECT tempat, koordinat
    FROM data_pengajuan WHERE status = 'diterima'
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

<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Monitoring Alat</h3>
            <a href="?page=tambah-alat" class="btn btn-info btn-sm float-right" style="margin-left: 10px;">
                <i class="fa fa-plus-circle"></i> Tambah Data
            </a>
        </div>
        <div class="card-body">
            <!-- Tabel Alat -->
            <table id="mytable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Tempat</th>
                        <th>IP Address</th>
                        <th>Status</th>
                        <th>Kerusakan</th>
                        <th>Opsi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $alatResult->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['tempat']); ?></td>
                        <td><?php echo htmlspecialchars($row['device_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['status']); ?></td>
                        <td><?php echo htmlspecialchars($row['kerusakan']); ?></td>
                        <td>
                            <a href="?page=edit-alat&id=<?php echo $row['id']?>" class="btn btn-warning btn-sm float-right" style="margin: 10px;"><i class="fa fa-edit"></i> Ubah Data</a>
                            <a href="?page=hapus-alat&id=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm float-right" style="margin: 10px"><i class="fa fa-trash"></i> Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

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

    // Update status based on kerusakan
    const statusCells = document.querySelectorAll('td.status');
    statusCells.forEach(cell => {
        const kerusakan = cell.previousElementSibling.textContent.trim();
        if (kerusakan === '-' || kerusakan === '') {
            cell.textContent = 'Terhubung';
        } else {
            cell.textContent = 'Terputus';
        }
    });
});
</script>
