<?php
include "pages/login/function.php";
check_access('kadis');

// Konfigurasi koneksi ke basis data
$host = "localhost";
$dbname = "sipanji";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Periksa apakah ada permintaan untuk menghapus komentar
    if (isset($_POST['delete_comment'])) {
        $comment_id = $_POST['comment_id'];

        // Query untuk menghapus komentar
        $query = "DELETE FROM kepuasan_pelayanan WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $comment_id);

        if ($stmt->execute()) {
            $_SESSION['hasil'] = true;
            $_SESSION['pesan'] = "Komentar berhasil dihapus.";
        } else {
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "Gagal menghapus komentar.";
        }

        // Redirect untuk menghindari pengiriman ulang formulir
        header("Location: ?page=halaman-kadis");
        exit();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

// Query untuk mengambil data dari tabel data_pengajuan
$query = "
    SELECT 
        MONTH(tgl_masuk) as bulan, 
        COUNT(*) as jumlah 
    FROM 
        data_pengajuan 
    WHERE 
        tgl_masuk >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    GROUP BY 
        MONTH(tgl_masuk)
    ORDER BY 
        MONTH(tgl_masuk)
";
$stmt = $db->prepare($query);
$stmt->execute();

$pelayananData = array_fill(1, 12, 0); // Inisialisasi dengan 0 untuk setiap bulan

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $pelayananData[(int)$row['bulan']] = $row['jumlah'];
}
// Query untuk mengambil rata-rata kepuasan pelayanan per bulan selama 1 tahun terakhir
$querySatisfaction = "
    SELECT 
        MONTH(tanggal) as bulan, 
        AVG(penilaian) as rata_rata 
    FROM 
        kepuasan_pelayanan 
    WHERE 
        tanggal >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)
    GROUP BY 
        MONTH(tanggal)
    ORDER BY 
        MONTH(tanggal)
";
$stmtSatisfaction = $db->prepare($querySatisfaction);
$stmtSatisfaction->execute();

$satisfactionData = array_fill(1, 12, 0); // Inisialisasi dengan 0 untuk setiap bulan

while ($row = $stmtSatisfaction->fetch(PDO::FETCH_ASSOC)) {
    $satisfactionData[(int)$row['bulan']] = round($row['rata_rata'], 1);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Halaman Kadis</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>
    <section class="content">
        <div class="container-fuild">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <!-- ./col -->
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-calendar"></i> Jadwal</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-stats-bars"></i>
                        </div>
                        <a href="?page=Jadwal" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-file"></i> Pengerjaan</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-person-add"></i>
                        </div>
                        <a href="?page=tampil-pengerjaan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><i class="fas fa-bug"></i> Gangguan</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="?page=tampil-gangguan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>
<!-- Map Section and Grafik Pelayanan Section -->
<div class="row">
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lokasi DISKOMINFO KOTA BANJARBARU</h3>
            </div>
            <div class="card-body">
                <div id="map" style="height: 400px;"></div> <!-- Pastikan tinggi peta tetap sesuai -->
            </div>
        </div>
    </div>
    
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Pelayanan Selama 1 Tahun Terakhir</h3>
            </div>
            <div class="card-body">
                <canvas id="pelayananChart" style="height: 400px;"></canvas> <!-- Tinggi grafik disesuaikan -->
            </div>
        </div>
    </div>
</div>

<div class="col-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Grafik Kepuasan Pelayanan Selama 1 Tahun Terakhir</h3>
        </div>
        <div class="card-body">
            <canvas id="satisfactionChart" style="height: 400px;"></canvas> <!-- Tinggi grafik disesuaikan -->
        </div>
    </div>
</div>

<!-- Tampilkan Komentar -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Komentar</h3>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                <?php
                $query = "SELECT username, email, penilaian, komentar, tanggal FROM kepuasan_pelayanan ORDER BY tanggal DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='alert alert-secondary'>";
                    echo "<h5>{$row['username']} ({$row['email']}) - Penilaian: {$row['penilaian']}/5</h5>";
                    echo "<p>{$row['komentar']}</p>";
                    echo "<small><i>Dikirim pada: {$row['tanggal']}</i></small>";
                    echo "</div>";
                }
                ?>
            </div>
        </div>
    </div>
</div>


        </div>
    </section>

    <script>
        var map = L.map('map').setView([-3.440425,114.8324361], 15); // Koordinat DISKOMINFO KOTA BANJARBARU

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([-3.440425,114.8324361]).addTo(map);
        
        marker.bindPopup('<b>DISKOMINFO KOTA BANJARBARU</b><br><a href="https://www.google.com/maps?q=-3.440425,114.8324361" target="_blank">Lihat di Google Maps</a>');
        
        marker.on('click', function() {
            window.open('https://www.google.com/maps?q=-3.440425,114.8324361', '_blank');
        });

        var ctx = document.getElementById('pelayananChart').getContext('2d');
    var pelayananData = <?php echo json_encode(array_values($pelayananData)); ?>;

    var chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Jumlah Pelayanan',
                data: pelayananData,
                borderColor: 'rgba(75, 192, 192, 1)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    var ctxSatisfaction = document.getElementById('satisfactionChart').getContext('2d');
    var satisfactionData = <?php echo json_encode(array_values($satisfactionData)); ?>;

    var satisfactionChart = new Chart(ctxSatisfaction, {
        type: 'line',
        data: {
            labels: ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
            datasets: [{
                label: 'Rata-Rata Kepuasan Pelayanan',
                data: satisfactionData,
                borderColor: 'rgba(153, 102, 255, 1)',
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    </script>
</body>
</html>
<?php include_once "partials/scripts.php" ?>
