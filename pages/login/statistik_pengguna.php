<?php
include_once "partials/scripts.php";

$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM admin";
$stmt = $db->prepare($query);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalUsers = count($users);
$peranCounts = [
    'admin' => 0,
    'kominfo' => 0,
    'instansi' => 0,
    'umum' => 0,
];
$lastLogin = [];

foreach ($users as $user) {
    if (isset($peranCounts[$user['peran']])) {
        $peranCounts[$user['peran']]++;
    }
    if (!empty($user['login_terakhir'])) {
        $lastLogin[] = $user['login_terakhir'];
    }
}

$mostRecentLogin = !empty($lastLogin) ? max($lastLogin) : 'N/A';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Statistik Pengguna</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Laporan Statistik Pengguna</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Statistik</th>
                    <th>Nilai</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Jumlah Total Pengguna</td>
                    <td><?php echo $totalUsers; ?></td>
                </tr>
                <?php foreach ($peranCounts as $peran => $count) : ?>
                    <tr>
                        <td>Jumlah Pengguna dengan Peran <?php echo ucfirst($peran); ?></td>
                        <td><?php echo $count; ?></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td>Pengguna Terakhir Login</td>
                    <td><?php echo $mostRecentLogin; ?></td>
                </tr>
            </tbody>
        </table>

        <h2>Data Pengguna</h2>
        <table id="mytable" class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Foto Profil</th>
                    <th>Peran</th>
                    <th>Opsi</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th>No</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Alamat</th>
                    <th>Tempat Lahir</th>
                    <th>Tanggal Lahir</th>
                    <th>Foto Profil</th>
                    <th>Peran</th>
                    <th>Opsi</th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                $no = 1;
                foreach ($users as $row) {
                    ?>
                    <tr>
                        <td><?php echo $no++ ?></td>
                        <td><?php echo $row['username'] ?></td>
                        <td><?php echo $row['email'] ?></td>
                        <td><?php echo $row['alamat'] ?></td>
                        <td><?php echo $row['tempat_lahir'] ?></td>
                        <td><?php echo $row['tanggal_lahir'] ?></td>
                        <td>
                            <a href="uploaded_images/<?php echo htmlspecialchars($row['foto']); ?>" target="_blank">
                                <img src="uploaded_images/<?php echo htmlspecialchars($row['foto']); ?>" alt="foto" width="100">
                            </a>
                        </td>
                        <td><?php echo $row['peran'] ?></td>
                        <td>
                            <a href="?page=edit-admin&id=<?php echo $row['id'] ?>" class="btn btn-warning btn-sm float-right" style="margin: 10px;">
                                <i class="fa fa-edit"></i> Ubah Data
                            </a>
                            <a href="?page=hapus-admin&id=<?php echo $row['id'] ?>" class="btn btn-danger btn-sm float-right" style="margin: 10px;">
                                <i class="fa fa-trash"></i> Hapus
                            </a>
                        </td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>

    <script>
        $(document).ready(function() {
            $('#mytable').DataTable();
        });
    </script>
</body>
</html>

