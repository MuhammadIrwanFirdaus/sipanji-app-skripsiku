<?php
// Mulai output buffering
ob_start();
session_start();

// Pastikan user_id ada di session
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['id'];

// Query SQL untuk mengambil data dari data_pengajuan
$database = new Database();
$db = $database->getConnection();
$queryPengajuan = "SELECT * FROM data_pengajuan WHERE user_id = :user_id";
$stmtPengajuan = $db->prepare($queryPengajuan);
$stmtPengajuan->bindParam(':user_id', $user_id);
$stmtPengajuan->execute();
$resultPengajuan = $stmtPengajuan->fetchAll(PDO::FETCH_ASSOC);


// Query SQL untuk mengambil data dari gangguan
$queryGangguan = "SELECT * FROM gangguan WHERE user_id = :user_id";
$stmtGangguan = $db->prepare($queryGangguan);
$stmtGangguan->bindParam(':user_id', $user_id);
$stmtGangguan->execute();
$resultGangguan = $stmtGangguan->fetchAll(PDO::FETCH_ASSOC);

$db = null; // Tutup koneksi database

// Hapus buffer output jika ada
ob_end_clean();

// Daftar nama hari dan bulan dalam bahasa Indonesia
$hari = array(
    'Sunday' => 'Minggu',
    'Monday' => 'Senin',
    'Tuesday' => 'Selasa',
    'Wednesday' => 'Rabu',
    'Thursday' => 'Kamis',
    'Friday' => 'Jumat',
    'Saturday' => 'Sabtu'
);

$bulan = array(
    'January' => 'Januari',
    'February' => 'Februari',
    'March' => 'Maret',
    'April' => 'April',
    'May' => 'Mei',
    'June' => 'Juni',
    'July' => 'Juli',
    'August' => 'Agustus',
    'September' => 'September',
    'October' => 'Oktober',
    'November' => 'November',
    'December' => 'Desember'
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Monitoring Pengajuan dan gangguan</title>
    <!-- Link ke Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
     <!-- Link ke Font Awesome untuk ikon -->
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .status-sedang-proses {
            background-color: #ffeb3b !important; /* Kuning */
            color: black !important;
        }
        .status-diterima {
            background-color: #4caf50 !important; /* Hijau */
            color: white !important;
        }
        .status-ditolak {
            background-color: #f44336 !important; /* Merah */
            color: white !important;
        }
        .status-icon {
            margin-right: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Monitoring Pengajuan</h1>
        <h2>Data Pengajuan</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Tempat</th>
                    <th>Tanggal Masuk</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultPengajuan as $row): ?>
                <?php
                // Format tanggal
                $date = new DateTime($row['tgl_masuk']);
                $dayName = $date->format('l'); // Nama hari dalam bahasa Inggris
                $monthName = $date->format('F'); // Nama bulan dalam bahasa Inggris
                $formattedDate = $hari[$dayName] . ', ' . $date->format('d') . ' ' . $bulan[$monthName] . ' ' . $date->format('Y');

                // Set class and icon based on status
                $statusClass = '';
                $statusIcon = '';
                if ($row['status'] == 'sedang proses') {
                    $statusClass = 'status-sedang-proses';
                    $statusIcon = '<i class="fas fa-clock status-icon"></i>';
                } elseif ($row['status'] == 'diterima') {
                    $statusClass = 'status-diterima';
                    $statusIcon = '<i class="fas fa-check status-icon"></i>';
                } elseif ($row['status'] == 'ditolak') {
                    $statusClass = 'status-ditolak';
                    $statusIcon = '<i class="fas fa-times status-icon"></i>';
                }
                ?>
                <tr class="<?php echo $statusClass; ?>">
                    <td><?php echo htmlspecialchars($row['tempat']); ?></td>
                    <td><?php echo htmlspecialchars($formattedDate); ?></td>
                    <td><?php echo $statusIcon . htmlspecialchars($row['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <h2>Data Gangguan</h2>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Nama Tempat</th>
                    <th>Keterangan</th>
                    <th>Tanggal Masuk</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($resultGangguan as $row): 
                    // Format tanggal
                $date = new DateTime($row['tgl_masuk']);
                $dayName = $date->format('l'); // Nama hari dalam bahasa Inggris
                $monthName = $date->format('F'); // Nama bulan dalam bahasa Inggris
                $formattedDate = $hari[$dayName] . ', ' . $date->format('d') . ' ' . $bulan[$monthName] . ' ' . $date->format('Y');
                // Set class and icon based on status
                $statusClass = '';
                $statusIcon = '';
                if ($row['status'] == 'sedang proses') {
                    $statusClass = 'status-sedang-proses';
                    $statusIcon = '<i class="fas fa-clock status-icon"></i>';
                } elseif ($row['status'] == 'diterima') {
                    $statusClass = 'status-diterima';
                    $statusIcon = '<i class="fas fa-check status-icon"></i>';
                } elseif ($row['status'] == 'ditolak') {
                    $statusClass = 'status-ditolak';
                    $statusIcon = '<i class="fas fa-times status-icon"></i>';
                }
                    ?>
                <tr class="<?php echo $statusClass; ?>">
                    <td><?php echo htmlspecialchars($row['nama_tempat']); ?></td>
                    <td><?php echo htmlspecialchars($row['keterangan']); ?></td>
                    <td><?php echo htmlspecialchars($formattedDate); ?></td>
                    <td><?php echo $statusIcon . htmlspecialchars($row['status']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php?page=halaman-umum" class="btn btn-secondary">Kembali</a>
    </div>

    <!-- Script Bootstrap JS -->
    <?php include_once "partials/scripts.php" ?>
</body>
</html>
