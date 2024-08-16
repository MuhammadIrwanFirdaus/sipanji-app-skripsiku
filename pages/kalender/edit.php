<?php
include "partials/scripts.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['event_id']) && isset($_POST['title']) && isset($_POST['tanggal']) && isset($_POST['keterangan'])) {
        // Kode dari file pertama untuk melakukan update
        // ...

        $event_id = $_POST['event_id'];
        $title = $_POST['title'];
        $tanggal = $_POST['tanggal'];
        $keterangan = $_POST['keterangan'];

        // Lakukan koneksi ke database
        $database = new Database();
        $db = $database->getConnection();

        // Query untuk melakukan update data
        $updateSql = "UPDATE events SET title = :title, tanggal = :tanggal, keterangan = :keterangan WHERE id = :id";
        $stmt = $db->prepare($updateSql);

        // Bind parameter
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':tanggal', $tanggal);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->bindParam(':id', $event_id);

        // Eksekusi statement
        if ($stmt->execute()) {
            // Redirect ke halaman index.php jika berhasil diupdate
            echo"<meta http-equiv='refresh' content='0;url=?page=Jadwal'>";
            exit();
        } else {
            echo "Gagal melakukan update.";
        }
    } else {
        echo "Data tidak lengkap.";
    }
}

include "partials/scripts.php";

if(isset($_GET['id'])) {
    $event_id = $_GET['id'];
    
    // Mengambil data acara berdasarkan ID
    $database = new Database();
    $db = $database->getConnection();
    
    $selectSql = "SELECT * FROM events WHERE id = :id";
    $stmt = $db->prepare($selectSql);
    $stmt->bindParam(':id', $event_id);
    $stmt->execute();
    
    if($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        // Data acara ditemukan, tampilkan formulir edit
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Jadwal</title>
    <!-- Panggil sumber daya Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Edit Jadwal</h1>
        <form method="POST">
            <!-- Hidden input untuk menyimpan ID acara -->
            <input type="hidden" name="event_id" value="<?php echo $row['id']; ?>">
            <div class="form-group">
                <label for="title">Tempat</label>
                <input type="text" class="form-control" id="title" name="title" value="<?php echo $row['title']; ?>" required>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" value="<?php echo date('Y-m-d\TH:i', strtotime($row['tanggal'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <select class="form-control" name="keterangan" value="<?php echo $row['keterangan']; ?>"required>
                    <option value="">Pilih Keterangan</option>
                    <option value="survey">Survey</option>
                    <option value="pemasangan">Pemasangan</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            <a href="index.php?page=Jadwal" class="btn btn-secondary">Batal</a>
        </form>
    </div>

    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
    } else {
        echo "Acara tidak ditemukan.";
    }
} else {
    echo "ID acara tidak valid.";
}
?>
