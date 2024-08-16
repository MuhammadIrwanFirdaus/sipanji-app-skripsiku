<?php
include "pages/login/function.php";


if (isset($_POST['button_create'])) {
    $database = new Database();
    $db = $database->getConnection();

    $foto = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];

    // Generate unique number for no_pengajuan
    $newNumber = "GGN-" . time(); 

    $user_id = $_SESSION['id']; // Ambil user_id dari session
    $status = 'sedang proses'; // Tetapkan nilai default untuk status

    $insertSQL = "INSERT INTO gangguan (no_pengajuan, nama_tempat, perwakilan, nomor_telepon, keterangan, tgl_masuk, foto_kerusakan, status, user_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertSQL);
    $stmt->bindParam(1, $newNumber);
    $stmt->bindParam(2, $_POST['nama_tempat']);
    $stmt->bindParam(3, $_POST['perwakilan']);
    $stmt->bindParam(4, $_POST['nomor_telepon']);
    $stmt->bindParam(5, $_POST['keterangan']);
    $stmt->bindParam(6, $_POST['tgl_masuk']);
    $stmt->bindParam(7, $foto);
    $stmt->bindParam(8, $status);
    $stmt->bindParam(9, $user_id);
    $fotoFilename = $foto;

    // Upload file
    $targetDir = "uploaded_images/";
    $targetFile = $targetDir . basename($foto);
    move_uploaded_file($tmp_file, $targetFile);

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Simpan Data";

// Path foto berdasarkan nama file dari database
$fotoPath = $targetFile; // Ganti dengan path folder tempat foto disimpan

// Pesan yang akan dikirim
$message = "Aduan gangguan telah masuk:\n";
$message .= "No Gangguan: $newNumber\n";
$message .= "Nama Tempat: " . $_POST['nama_tempat'] . "\n";
$message .= "Perwakilan: " . $_POST['perwakilan'] . "\n";
$message .= "Nomor Telepon: " . $_POST['nomor_telepon'] . "\n";
$message .= "Keterangan: " . $_POST['keterangan'] . "\n";
$message .= "Tanggal Masuk: " . $_POST['tgl_masuk'] . "\n";
$message .= "Foto Kerusakan: ada dibawah ini" ; // Optional

// Panggil fungsi untuk mengirim pesan dan foto
sendTelegramMessageWithPhoto($message, $fotoPath);

        // Redirect to the monitoring page
        echo "<meta http-equiv='refresh' content='0; url=?page=monitoring-pengajuan-umum'>";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Simpan Data";
    }
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Ajukan Gangguan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">Gangguan</a></li>
                    <li class="breadcrumb-item active">Tambah Data</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ajukan Gangguan</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama_tempat">Nama Tempat</label>
                    <input type="text" class="form-control" name="nama_tempat" required>
                </div>
                <div class="form-group">
                    <label for="perwakilan">Nama Perwakilan</label>
                    <input type="text" class="form-control" name="perwakilan" required>
                </div>
                <div class="form-group">
                    <label for="nomor_telepon">Nomor Telepon</label>
                    <input type="text" class="form-control" name="nomor_telepon" required>
                </div>
                <div class="form-group">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" class="form-control" name="keterangan" required>
                </div>
                <div class="form-group">
                    <label for="tgl_masuk">Tanggal Masuk</label>
                    <input type="datetime-local" class="form-control" name="tgl_masuk" required>
                </div>
                <div class="form-group">
                    <label for="foto">Unggah Foto</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>
                <button onclick="goBack()" class="btn btn-secondary mt-3">Batal</button>
                <button type="submit" name="button_create" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</section>

<?php include_once "partials/scripts.php" ?>

<script>
    function goBack() {
        window.history.back();
    }
</script>
