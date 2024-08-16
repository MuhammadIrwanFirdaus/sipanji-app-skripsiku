<?php
include "pages/login/function.php";
check_access('admin');

// Proses form untuk menerima atau menolak pengajuan
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $action = $_POST['action'];

    if ($action == 'terima') {
        $status = 'diterima';
    } else {
        $status = 'ditolak';
    }

    // Koneksi database menggunakan PDO
    $database = new Database();
    $db = $database->getConnection();

    $query = "UPDATE admin SET status = :status WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':id', $user_id);
    
    if ($stmt->execute()) {
        echo "Pengajuan berhasil diperbarui.";
    } else {
        echo "Error: " . $stmt->errorInfo()[2];
    }

    $db = null; // Tutup koneksi database
}

// Ambil data pengajuan dari database
$database = new Database();
$db = $database->getConnection();

$query = "SELECT * FROM admin WHERE status = 'pending'";
$stmt = $db->prepare($query);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

$db = null; // Tutup koneksi database
?>
<?php include_once "partials/scripts.php" ?>
<!DOCTYPE html>
<html>
<head>
    <title>Kelola User</title>
    <!-- Link ke Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Kelola User</h1>
        <table class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>email</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['username']); ?></td>
                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                    <td>
                        <form method="post" action="">
                            <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                            <button type="submit" name="action" value="terima" class="btn btn-success">Terima</button>
                            <button type="submit" name="action" value="tolak" class="btn btn-danger">Tolak</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="index.php?page=halaman-admin" class="btn btn-secondary">Kembali</a>
    </div>
</body>
</html>
