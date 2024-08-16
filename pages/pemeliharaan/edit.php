<?php
include_once "partials/cssdatatables.php";
include_once "database/database.php";

$database = new Database();
$db = $database->getConnection();

// Dapatkan ID alat yang akan diedit
$id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: ID tidak ditemukan.');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tempat = $_POST['tempat'];
    $ip_address = $_POST['ip_address'];
    $kerusakan = isset($_POST['kerusakan']) ? $_POST['kerusakan'] : '';

    // Tentukan status berdasarkan kerusakan
    $status = (trim($kerusakan) === '-' || empty(trim($kerusakan))) ? 'terhubung' : 'terputus';

    // Update data perangkat yang diubah
    $query = "UPDATE alat SET tempat = ?, device_id = ?, kerusakan = ?, status = ? WHERE id = ?";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $tempat);
    $stmt->bindParam(2, $ip_address);
    $stmt->bindParam(3, $kerusakan);
    $stmt->bindParam(4, $status);
    $stmt->bindParam(5, $id);

    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Data berhasil diperbarui.</div>";
        echo "<meta http-equiv='refresh' content='0; url=?page=monitoring-alat'>";
    } else {
        echo "<div class='alert alert-danger'>Gagal memperbarui data: " . print_r($stmt->errorInfo(), true) . "</div>";
    }
} else {
    // Ambil data alat yang akan diedit
    $query = "SELECT * FROM alat WHERE id = ? LIMIT 1";
    $stmt = $db->prepare($query);
    $stmt->bindParam(1, $id);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $tempat = $row['tempat'];
    $ip_address = $row['device_id'];
    $kerusakan = $row['kerusakan'];
}

// Query untuk mendapatkan daftar nama tempat dari data_pengajuan
$placesQuery = "SELECT DISTINCT tempat FROM data_pengajuan";
$placesResult = $db->query($placesQuery);
?>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Edit Alat</h1>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Edit Alat</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="?page=edit-alat&id=<?php echo htmlspecialchars($id); ?>">
                <div class="form-group">
                    <label for="tempat">Tempat:</label>
                    <select class="form-control" id="tempat" name="tempat" required>
                        <option value="">Pilih Tempat</option>
                        <?php while ($place = $placesResult->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo htmlspecialchars($place['tempat']); ?>" <?php echo $place['tempat'] == $tempat ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($place['tempat']); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="ip_address">IP Address:</label>
                    <input type="text" class="form-control" id="ip_address" name="ip_address" value="<?php echo htmlspecialchars($ip_address, ENT_QUOTES); ?>" required>
                </div>
                <div class="form-group">
                    <label for="kerusakan">Kerusakan:</label>
                    <input type="text" class="form-control" id="kerusakan" name="kerusakan" value="<?php echo htmlspecialchars($kerusakan, ENT_QUOTES); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Update Data</button>
            </form>
        </div>
    </div>
</div>

<?php include "partials/scripts.php" ?>
