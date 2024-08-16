<?php 
if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    $findSql = "SELECT * FROM admin where id = ?";
    $stmt = $db->prepare($findSql);
    $stmt->bindParam(1, $_GET['id']);
    $stmt->execute();
    $row = $stmt->fetch();
    
    if (isset($row['id'])) {
        if (isset($_POST['button_update'])) {
            $updateSQL = "UPDATE admin SET username = ?, password = ? WHERE id=?";
            $stmt = $db->prepare($updateSQL);
            $stmt->bindParam(1, $_POST['username']);
            $stmt->bindParam(2, $_POST['password']);
            $stmt->bindParam(3, $_POST['id']);
            
            if ($stmt->execute()) {
                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Ubah data";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Ubah data";
            }
            echo "<meta http-equiv='refresh' content='0; url=?page=tampil-admin'>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Admin</title>
    <?php include_once "partials/cssdatatables.php" ?>
</head>
<body>
    <div class="container">
        <section class="content-header">
            <div class="containerfluid">
                <div class="row mb2">
                    <div class="col-sm-6">
                        <h1>Edit Data Admin</h1>
                    </div>
                </div>
            </div>
        </section>
        <section class="content">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Edit Data Pengguna Admin</h3>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="hidden" class="form-control" name="id" value="<?php echo $row['id'] ?>" >
                            <input type="text" class="form-control" name="username" value="<?php echo $row['username'] ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="text" class="form-control" name="password" value="<?php echo $row['password'] ?>" required>
                        </div>
                        <a href="?page=tampil-admin" class="btn btn-danger btn-sm float-right" style="margin-left: 10px;">
                            <i class="fa fa-times"></i> Batal
                        </a>
                        <button type="submit" name="button_update" class="btn btn-success btn-sm float-right">
                            <i class="fa fa-save"></i> Simpan
                        </button>
                    </form>
                </div>
            </div>
        </section>
    </div>
    <?php include_once "partials/scripts.php" ?>
</body>
</html>
