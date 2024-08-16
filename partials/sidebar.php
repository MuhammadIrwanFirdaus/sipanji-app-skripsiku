<?php
ob_start();
// Konfigurasi koneksi ke basis data
$host = "localhost"; // Ganti dengan host Anda
$dbname = "sipanji"; // Ganti dengan nama database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda

try {
    // Buat koneksi ke basis data menggunakan PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil ID pengguna dari sesi
    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : '';

    // Ambil peran dan username dari sesi
    $peran = isset($_SESSION['peran']) ? $_SESSION['peran'] : '';
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : 'User';

    // Ambil foto profil dari basis data
    if ($userId) {
        $query = "SELECT foto FROM admin WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $userId);
        $stmt->execute();

        // Ambil hasil query
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Tentukan path foto
        $foto = $user && !empty($user['foto']) ? 'uploaded_images/' . $user['foto'] : 'uploaded_images/default.jpg'; // Default jika foto tidak ada
    } else {
        $foto = 'uploaded_images/default.jpg'; // Default jika tidak ada ID pengguna
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="#" class="brand-link">
        <img src="dist/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">SIPANJI</span>
    </a>
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?php echo htmlspecialchars($foto); ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
            <a href="?page=edit-profil&id=<?php echo htmlspecialchars($userId); ?>" class="d-block"><?php echo htmlspecialchars($username); ?></a>
            </div>
        </div>
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="#"></a>
                </li>
                <?php if ($peran == 'admin'): ?>
                    <li class="nav-item"><a href="?page=halaman-admin" class="nav-link active bg-info"><i class="nav-icon fas fa-home"></i> Home</a></li>
                <?php elseif ($peran == 'kominfo'): ?>
                    <li class="nav-item"><a href="?page=halaman-kominfo" class="nav-link active bg-info"><i class="nav-icon fas fa-home"></i> Home</a></li>
                <?php elseif ($peran == 'instansi'): ?>
                    <li class="nav-item"><a href="?page=halaman-instansi" class="nav-link active bg-info"><i class="nav-icon fas fa-home"></i> Home</a></li>
                <?php elseif ($peran == 'umum'): ?>
                    <li class="nav-item"><a href="?page=halaman-umum" class="nav-link active bg-info"><i class="nav-icon fas fa-home"></i> Home</a></li>
                <?php else: ?>
                    <li class="nav-item"><a href="pages/login/view.php" class="nav-link"><i class="nav-icon fas fa-sign-in-alt"></i> Login</a></li>
                <?php endif; ?>

                <?php if ($peran == 'admin'): ?>
                <li class="nav-item">
                    <a href="?page=#" class="nav-link">
                        <i class="fas fa-file nav-icon"></i>
                        <p>Cetak Laporan</p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="?page=cetak-data-pengajuan" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Pengajuan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=cetak-data-stok" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Stok</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=view-pdf-gabungan" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan per Instansi</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=view-jadwal" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Jadwal</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=view-biaya-operasional" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Operasional</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=cetak-pemeliharaan" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Status alat</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                            <a href="?page=pdf-pengguna" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Statistik Pengguna</p>
                            </a>
                        </li> -->
                        <li class="nav-item">
                            <a href="?page=cetak-pdf-pelayanan" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Kepuasan Pelayanan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=view-gangguan-pdf" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Gangguan</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="?page=pdf-alat-sering" class="nav-link">
                                <i class="fas nav-icon"></i>
                                <p>Laporan Alat yang paling sering digunakan</p>
                            </a>
                        </li>
                    </ul>
                </li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</aside>