<?php
include "pages/login/function.php";
check_access('umum');

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
                        <a href="?page=monitoring-pengajuan-umum" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
                        <a href="?page=tambah-pengajuan-umum" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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
            <!-- Map Section -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Lokasi DISKOMINFO KOTA BANJARBARU</h3>
                        </div>
                        <div class="card-body">
                            <div id="map"></div>
                        </div>
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
    </script>
</body>
</html>
<?php include_once "partials/scripts.php" ?>
