<?php
include "pages/login/function.php";
check_access('instansi');

// Database connection
include_once 'database/database.php';
$database = new Database();
$db = $database->getConnection();

// Fetch user details
$username = $_SESSION['username'];

// Get email from pengguna table based on the username
$query = "SELECT email FROM admin WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $user['email'];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $penilaian = htmlspecialchars($_POST['penilaian']);
    $komentar = htmlspecialchars($_POST['komentar']);

    $query = "INSERT INTO kepuasan_pelayanan (username, email, penilaian, komentar) VALUES (:username, :email, :penilaian, :komentar)";
    $stmt = $db->prepare($query);

    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':penilaian', $penilaian);
    $stmt->bindParam(':komentar', $komentar);

    if ($stmt->execute()) {
        $message = "Penilaian berhasil ditambahkan!";
    } else {
        $message = "Terjadi kesalahan, coba lagi.";
    }
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Umum</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <section class="content">
        <div class="container-fluid">
            <!-- Small boxes (Stat box) -->
            <div class="row">
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-desktop"></i> Status</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=monitoring-pengajuan-instansi" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-warning">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-file"></i> Pengajuan</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=tambah-pengajuan-instansi" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-bug"></i> Gangguan</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=tambah-gangguan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                                <!-- ./col -->
                                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-success">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-map-marked"></i> Info</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=tampil-peta" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                                <!-- ./col -->
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

            <!-- Komentar Pelayanan Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Komentar Pelayanan</h3>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($message)) { echo "<div class='alert alert-info'>$message</div>"; } ?>
                            <form method="post" action="">
                                <div class="form-group">
                                    <label for="nama">Nama:</label>
                                    <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $username; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email:</label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" readonly>
                                </div>
                                <div class="form-group">
                                    <label for="penilaian">Penilaian:</label><br>
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="penilaian" id="penilaian<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                            <label class="form-check-label" for="penilaian<?php echo $i; ?>"><?php echo $i; ?></label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                                <div class="form-group">
                                    <label for="komentar">Komentar:</label>
                                    <textarea class="form-control" id="komentar" name="komentar" rows="3"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary">Kirim</button>
                            </form>
                        </div>
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
                        <div class="card-body">
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
        var map = L.map('map').setView([-3.440425, 114.8324361], 15); // Koordinat DISKOMINFO KOTA BANJARBARU

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        var marker = L.marker([-3.440425, 114.8324361]).addTo(map);

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
