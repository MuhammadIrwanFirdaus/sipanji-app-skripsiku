<?php
include "pages/login/function.php";
check_access('umum');

// Koneksi database
include_once 'database/database.php';
$database = new Database();
$db = $database->getConnection();

// Ambil data pengguna
$username = $_SESSION['username'];

// Ambil email dari tabel admin berdasarkan username
$query = "SELECT email FROM admin WHERE username = :username";
$stmt = $db->prepare($query);
$stmt->bindParam(':username', $username);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$email = $user['email'];

// Proses form penilaian
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
// Query for fetching alat data from stok_alat table
$queryAlat = "SELECT nama_alat, jumlah FROM stok_alat";
$stmtAlat = $db->prepare($queryAlat);
$stmtAlat->execute();
$alatData = $stmtAlat->fetchAll(PDO::FETCH_ASSOC);


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

// Query untuk mengambil data alat yang tersedia
$queryAlat = "SELECT nama_alat, jumlah FROM stok_alat";
$stmtAlat = $db->prepare($queryAlat);
$stmtAlat->execute();
$alatData = $stmtAlat->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
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
                        <a href="?page=monitoring-pengajuan-umum" class="small-box-footer">Lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
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
                        <a href="?page=tambah-pengajuan-umum" class="small-box-footer">Lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
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
                        <a href="?page=tambah-gangguan" class="small-box-footer">Lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
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
                        <a href="?page=tampil-peta" class="small-box-footer">Lebih lanjut <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
            </div>

            <div class="row">
    <!-- Peta -->
    <div class="col-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Lokasi DISKOMINFO KOTA BANJARBARU</h3>
            </div>
            <div class="card-body">
                <div id="map" style="height: 400px;"></div> <!-- Peta dengan tinggi tetap -->
            </div>
        </div>
    </div>

<!-- Tabel Alat Tersedia -->
<div class="col-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Alat Tersedia</h3>
        </div>
        <div class="card-body">
            <!-- Tabel Alat Tersedia -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Nama Alat</th>
                        <th>Jumlah Tersedia</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($alatData as $alat): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($alat['nama_alat']); ?></td>
                            <td>
                                <?php 
                                    echo number_format($alat['jumlah'], 0, ',', '.') . ' buah'; 
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <!-- Grafik Pelayanan -->
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

    <!-- Grafik Kepuasan -->
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
</div>


<!-- Komentar Pelayanan Section -->
<div class="container-fluid">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">Komentar Pelayanan</h3>
                </div>
                <div class="card-body">
                    <?php if (!empty($message)) : ?>
                        <div class="alert alert-info"><?php echo $message; ?></div>
                    <?php endif; ?>
                    <form method="post" action="">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="nama">Nama:</label>
                                <input type="text" class="form-control" id="nama" name="nama" value="<?php echo $username; ?>" readonly>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="email">Email:</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo $email; ?>" readonly>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="penilaian">Penilaian:</label>
                                <div>
                                    <?php for ($i = 1; $i <= 5; $i++) : ?>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="penilaian" id="penilaian<?php echo $i; ?>" value="<?php echo $i; ?>" required>
                                            <label class="form-check-label" for="penilaian<?php echo $i; ?>">
                                                <i class="fas fa-star text-warning"></i> <?php echo $i; ?>
                                            </label>
                                        </div>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div class="col-12 mb-3">
                                <label for="komentar">Komentar:</label>
                                <textarea class="form-control" id="komentar" name="komentar" rows="4" placeholder="Tulis komentar Anda..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success w-100">Kirim</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tampilkan Komentar Section -->
    <div class="row">
        <div class="col-12">
            <div class="card w-100">
                <div class="card-header bg-secondary text-white text-center">
                    <h3 class="card-title">Komentar Terbaru</h3>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <?php
                    $query = "SELECT username, email, penilaian, komentar, tanggal FROM kepuasan_pelayanan ORDER BY tanggal DESC";
                    $stmt = $db->prepare($query);
                    $stmt->execute();

                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) : ?>
                        <div class="card mb-3 border-0 shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title">
                                    <strong><?php echo htmlspecialchars($row['username']); ?></strong>
                                    <span class="text-muted">(<?php echo htmlspecialchars($row['email']); ?>)</span>
                                </h5>
                                <h6 class="card-subtitle mb-2 text-warning"> 
                                    <?php for ($i = 1; $i <= $row['penilaian']; $i++) : ?>
                                        <i class="fas fa-star"></i>
                                    <?php endfor; ?>
                                </h6>
                                <p class="card-text"><?php echo htmlspecialchars($row['komentar']); ?></p>
                                <small class="text-muted">Dikirim pada: <?php echo htmlspecialchars($row['tanggal']); ?></small>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>
    </div>
</div>

        </div>
    </section>

    <script>
        // Peta Lokasi DISKOMINFO KOTA BANJARBARU
        var map = L.map('map').setView([-3.4269, 114.7922], 13); // Koordinat DISKOMINFO KOTA BANJARBARU

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        L.marker([-3.4269, 114.7922]).addTo(map)
            .bindPopup("<b>DISKOMINFO KOTA BANJARBARU</b>")
            .openPopup();
    </script>

    <script>
        // Grafik Pelayanan
        var ctx = document.getElementById('pelayananChart').getContext('2d');
        var pelayananChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Jumlah Pengajuan',
                    data: <?php echo json_encode($pelayananData); ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Grafik Kepuasan
        var ctx2 = document.getElementById('satisfactionChart').getContext('2d');
        var satisfactionChart = new Chart(ctx2, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Rata-rata Kepuasan',
                    data: <?php echo json_encode($satisfactionData); ?>,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
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
