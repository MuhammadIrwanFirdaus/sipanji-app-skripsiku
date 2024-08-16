<?php 
if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    $findSql = "SELECT * FROM gangguan where id = ?";
    $stmtSelect = $db->prepare($findSql);
    $stmtSelect->bindParam(1, $_GET['id']);
    $stmtSelect->execute();
    $row = $stmtSelect->fetch();
    
    if (isset($row['id'])) {
        if (isset($_POST['button_update'])) {
            $updateSQL = "UPDATE gangguan SET gangguan = ?, nama_tempat = ?, perwakilan = ?, nomor_telepon = ?, tgl_masuk = ? WHERE id=?";
            $stmtUpdate = $db->prepare($updateSQL);
            $stmtUpdate->bindParam(1, $_POST['gangguan']);
            $stmtUpdate->bindParam(2, $_POST['nama_tempat']);
            $stmtUpdate->bindParam(3, $_POST['perwakilan']);
            $stmtUpdate->bindParam(4, $_POST['nomor_telepon']);
            $stmtUpdate->bindParam(5, $_POST['tgl_masuk']);
            $stmtUpdate->bindParam(6, $_POST['id']);

            if ($stmtUpdate->execute()) {
                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Ubah data";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Ubah data";
            }
            echo "<meta http-equiv='refresh' content='0; url=?page=tampil-gangguan'>";
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
            <h3 class="card-title">Ubah Data</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="no_pengajuan">Nomor Gangguan </label>
                    <input type="hidden" class="form-control" name="id" value="<?php echo $row['id'] ?>" >
                    <input type="text" class="form-control" name="no_pengajuan" value="<?php echo $row['no_pengajuan'] ?>" required readonly>
                </div>
                <div class="form-group">
                    <label for="nama_tempat">nama_tempat </label>
                    <input type="text" class="form-control" name="nama_tempat" value="<?php echo $row['nama_tempat'] ?>" required>
                </div>
            <div class="form-group">
                    <label for="perwakilan">Nama Perwakilan</label>
                    <input type="text" class="form-control" name="perwakilan" value="<?php echo $row['perwakilan'] ?>" required>
            <div class="form-group">
                    <label for="nomor_telepon">Nomor yang Bisa Dihubungi</label>
                    <input type="text" class="form-control" name="nomor_telepon" value="<?php echo $row['nomor_telepon'] ?>" required>
            </div>
                <div class="form-group">
                <label for="tgl_masuk">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tgl_masuk" name="tgl_masuk" value="<?php echo date('Y-m-d\TH:i', strtotime($row['tgl_masuk'])); ?>" required>
            </div>
            <a href="?page=tampil-gangguan" class="btn btn-secondary">Batal</a>
                <button type="submit" name="button_update" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

</section>

<?php include_once "partials/scripts.php" ?>
