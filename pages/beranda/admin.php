<?php
include "pages/login/function.php";
check_access('admin');

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
        header("Location: ?page=halaman-admin");
        exit();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Halaman Admin</title>
    <style>
        #map {
            height: 400px;
            width: 100%;
        }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>
<body>
    <section class="content">
        <div class="container-fuild">
            <!-- Small boxes (Stat box) -->
            <div class="row">
            <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-file"></i> Kelola</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=kelola-pengajuan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-info">
                        <div class="inner">
                            <h3><i class="nav-icon fas fa-file"></i> Pengajuan</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-bag"></i>
                        </div>
                        <a href="?page=tampil-pengajuan" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
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
                    <div class="small-box bg-danger">
                        <div class="inner">
                            <h3><i class="fas fa-cogs nav-icon"></i> Stok Alat</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="?page=tampil-stok-alat" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                    </div>
                </div>
                <!-- ./col -->
                <div class="col-lg-3 col-6">
                    <!-- small box -->
                    <div class="small-box bg-secondary">
                        <div class="inner">
                            <h3><i class="fas fa-screwdriver"></i> maintanance</h3>
                        </div>
                        <div class="icon">
                            <i class="ion ion-pie-graph"></i>
                        </div>
                        <a href="?page=monitoring-alat" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
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

<!-- Tampilkan Komentar -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Komentar</h3>
            </div>
            <div class="card-body">
                <?php
                // Ambil data komentar dari database
                $query = "SELECT id, username, email, penilaian, komentar, tanggal FROM kepuasan_pelayanan ORDER BY tanggal DESC";
                $stmt = $db->prepare($query);
                $stmt->execute();

                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<div class='alert alert-secondary'>";
                    echo "<h5>{$row['username']} ({$row['email']}) - Penilaian: {$row['penilaian']}/5</h5>";
                    echo "<p>{$row['komentar']}</p>";
                    echo "<small><i>Dikirim pada: {$row['tanggal']}</i></small>";

                    // Tambahkan tombol hapus
                    echo "<form method='POST' style='display:inline;'>
                        <input type='hidden' name='comment_id' value='{$row['id']}'>
                        <button type='submit' name='delete_comment' class='btn btn-danger btn-sm'>Hapus</button>
                    </form>";

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
    </script>
</body>
</html>
<?php include_once "partials/scripts.php" ?>
