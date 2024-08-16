<?php 
if (isset($_GET['id_stok'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id_stok'];
    $findSql = "SELECT * FROM stok_alat WHERE id_stok = ?";
    $stmt = $db->prepare($findSql);
    $stmt->bindParam(1, $_GET['id_stok']);
    $stmt->execute();
    $row = $stmt->fetch();
    
    if (isset($row['id_stok'])) {
        if (isset($_POST['button_update'])) {
            $updateSQL = "UPDATE stok_alat SET nama_alat = ?, jumlah = ?, harga = ?, foto = ? WHERE id_stok=?";
            $stmt = $db->prepare($updateSQL);
            $stmt->bindParam(1, $_POST['nama_alat']);
            $stmt->bindParam(2, $_POST['harga']);
            $stmt->bindParam(3, $_POST['jumlah']);
            
            // Unggah foto hanya jika file baru diunggah
            if ($_FILES['foto']['size'] > 0) {
                $fotoFileName = $_FILES['foto']['name'];
                $tmp_file = $_FILES['foto']['tmp_name'];
                $targetDir = "uploaded_images/";
                $targetFile = $targetDir . basename($fotoFileName);
                $stmt->bindParam(4, $fotoFileName);
            } else {
                // Jika tidak ada file yang diunggah, gunakan nilai sebelumnya
                $stmt->bindParam(4, $row['foto']);
            }

            $stmt->bindParam(5, $_POST['id_stok']);
            
            if ($stmt->execute()) {
                if ($_FILES['foto']['size'] > 0) {
                    // Proses unggah foto hanya jika ada file yang diunggah
                    if (move_uploaded_file($tmp_file, $targetFile)) {
                        // Foto berhasil diunggah
                    } else {
                        // Foto gagal diunggah
                    }
                }

                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Ubah data";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Ubah data";
            }
            echo "<meta http-equiv='refresh' content='0; url=?page=tampil-stok-alat'>";
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
            <h3 class="card-title">Ubah Data Alat</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Alat</label>
                    <input type="hidden" class="form-control" name="id_stok" value="<?php echo $row['id_stok'] ?>" >
                    <input type="text" class="form-control" name="nama_alat" value="<?php echo $row['nama_alat'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="text" class="form-control" name="jumlah" value="<?php echo $row['jumlah'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga</label>
                    <input type="decimal" class="form-control" name="harga" value="<?php echo $row['harga'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="foto">Unggah Foto Alat</label>
                    <input type="file" class="form-control" name="foto" accept="image/*">
                </div>
                <a href="?page=tampil-stok-alat" class="btn btn-danger btn-sm float-right" style="margin-left: 10px;">
                    <i class="fa fa-times"></i> Batal
                </a>
                <button type="submit" name="button_update" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>
</section>

<?php include_once "partials/scripts.php" ?>
