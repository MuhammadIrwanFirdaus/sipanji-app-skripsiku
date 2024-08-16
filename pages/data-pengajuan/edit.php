<?php 
if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    $findSql = "SELECT * FROM data_pengajuan where id = ?";
    $stmtSelect = $db->prepare($findSql);
    $stmtSelect->bindParam(1, $_GET['id']);
    $stmtSelect->execute();
    $row = $stmtSelect->fetch();
    
    if (isset($row['id'])) {
        if (isset($_POST['button_update'])) {
            $updateSQL = "UPDATE data_pengajuan SET no_pengajuan = ?, kategori = ?, tempat = ?, alamat = ?, nama_perwakilan = ?, no_telpon = ?, tgl_masuk = ?, surat_pengajuan = ? WHERE id=?";
            $stmtUpdate = $db->prepare($updateSQL);
            $stmtUpdate->bindParam(1, $_POST['no_pengajuan']);
            $stmtUpdate->bindParam(2, $_POST['kategori']);
            $stmtUpdate->bindParam(3, $_POST['tempat']);
            $stmtUpdate->bindParam(4, $_POST['alamat']);
            $stmtUpdate->bindParam(5, $_POST['nama_perwakilan']);
            $stmtUpdate->bindParam(6, $_POST['no_telpon']);
            $stmtUpdate->bindParam(7, $_POST['tgl_masuk']);
            $stmtUpdate->bindParam(8, $_FILES['surat_pengajuan']['name']);
            $stmtUpdate->bindParam(9, $_POST['id']);

            $fileUploadDir = 'uploads_surat/'; // Direktori untuk menyimpan file
            $fileUploadPath = $fileUploadDir . basename($_FILES['surat_pengajuan']['name']); // Path lengkap untuk menyimpan file

            // Cek apakah file PDF yang diunggah
            if (strtolower(pathinfo($fileUploadPath, PATHINFO_EXTENSION)) === 'pdf') {
                if (move_uploaded_file($_FILES['surat_pengajuan']['tmp_name'], $fileUploadPath)) {
                    // Proses upload berhasil
                    echo "File berhasil diunggah.";
                } else {
                    // Proses upload gagal
                    echo "Gagal mengunggah file.";
                }
            } else {
                echo "Hanya file PDF yang diizinkan.";
            }

            if ($stmtUpdate->execute()) {
                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Ubah data";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Ubah data";
            }
            echo "<meta http-equiv='refresh' content='0; url=?page=tampil-pengajuan'>";
        }
    }
}
?>

<section class="content-header">
    <!-- Header content -->
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ubah Data Instansi</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="hidden" class="form-control" name="id" value="<?php echo $row['id'] ?>" >
                <select class="form-control" name="kategori" value="<?php echo $row['kategori'] ?>" required>
                    <option value="">Pilih Kategori</option>
                    <option value="perkantoran">Perkantoran</option>
                    <option value="sekolahan">Sekolahan</option>
                    <option value="puskesmas">Puskesmas</option>
                </select>
                </div>
                <div class="form-group">
                    <label for="no_pengajuan">Nomor Pengajuan </label>
                    <input type="text" class="form-control" name="no_pengajuan" value="<?php echo $row['no_pengajuan'] ?>" required readonly>
                </div>
                <div class="form-group">
                    <label for="tempat">Tempat </label>
                    <input type="text" class="form-control" name="tempat" value="<?php echo $row['tempat'] ?>" required>
                </div>
            <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" class="form-control" name="alamat" value="<?php echo $row['alamat'] ?>" required>
            </div>
            <div class="form-group">
                    <label for="nama_perwakilan">Nama Perwakilan</label>
                    <input type="text" class="form-control" name="nama_perwakilan" value="<?php echo $row['nama_perwakilan'] ?>" required>
            <div class="form-group">
                    <label for="no_telpon">Nomor yang Bisa Dihubungi</label>
                    <input type="text" class="form-control" name="no_telpon" value="<?php echo $row['no_telpon'] ?>" required>
            </div>
                <div class="form-group">
                <label for="tgl_masuk">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?php echo date('Y-m-d\TH:i', strtotime($row['tgl_masuk'])); ?>" required>
            </div>
            <div class="form-group">
                <label for="surat_pengajuan">Surat Pengajuan (PDF)</label>
                <input type="file" class="form-control-file" id="surat_pengajuan" name="surat_pengajuan" accept=".pdf"  value="<?php echo $row['surat_pengajuan'] ?>" required>
            </div>
            <button onclick="goBack()" class="btn btn-secondary mt-3">Batal</button>
                <button type="submit" name="button_update" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</section>

<?php include_once "partials/scripts.php" ?>
