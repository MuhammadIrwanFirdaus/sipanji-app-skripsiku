<?php include_once "partials/cssdatatables.php" ?>
<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <?php
        if (isset($_SESSION["hasil"])) {
            if ($_SESSION["hasil"]) {
                ?>
                <div class="alert alert-success alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                    <h5><i class="icon fas fa-check"></i> Berhasil</h5>
                    <?php echo $_SESSION["pesan"] ?>
                </div>
                <?php
            } else {
                ?>
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                    <h5><i class="icon fas fa-ban"></i> Gagal</h5>
                    <?php echo $_SESSION["pesan"] ?>
                </div>
                <?php
            }
            unset($_SESSION['hasil']);
            unset($_SESSION['pesan']);
        }
        ?>
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Data Pengguna</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->

<!-- Main content -->
<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Pengguna</h3>
            <a href="?page=tambah-admin" class="btn btn-info btn-sm float-right" style="margin-left: 10px;">
                <i class="fa fa-plus-circle"></i> Tambah Data
            </a>
        </div>
        <div class="card-body">
            <table id="mytable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Alamat</th>
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
                        <th>Foto Profil</th>
                        <th>Peran</th>
                        <th>Opsi</th>
                    </tr>
                </tfoot>
                <tbody>
                    <?php
                    $database = new Database();
                    $db = $database->getConnection();

                    $selectSql = "SELECT * FROM admin";
                    $stmt = $db->prepare($selectSql);
                    $stmt->execute();
                    $no = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['username'] ?></td>
                            <td><?php echo $row['email'] ?></td>
                            <td><?php echo $row['alamat'] ?></td>
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
    </div>
</div>

<?php include "partials/scripts.php" ?>
<?php include_once "partials/scriptsdatatables.php" ?>
<script>
    $(function() {
        $('#mytable').DataTable({
            // Additional options if needed
        })
    });
</script>
