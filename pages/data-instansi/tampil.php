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
              <h5><i class="icon fas fa-check"></i>Berhasil</h5>
              <?php echo $_SESSION["pesan"] ?>
            </div>
            <?php
          } else {
            ?>
            <div class="alert alert-danger alert-dismissible">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
              <h5><i class="icon fas fa-ban"></i>Gagal</h5>
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
            <h1 class="m-0">Data Intansi</h1>
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
                <h3 class="card-title">Data Instansi</h3>
                    <a href="?page=tambah-data-instansi"
                        class="btn btn-info btn-sm float-right" style="margin-left: 10px;">
                            <i class="fa fa-plus-circle"></i> Tambah Data</a>
                    <a href="?page=cetak-pdf-Intansi"
                        class="btn btn-secondary btn-sm float-right">
                            <i class="fa fa-file"></i> Cetak PDF</a>
            </div>
            <div class="card-body">
                <table id="mytable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Nama Intansi</th>
                            <th>Alamat Intansi</th>
                            <th>Titik Lokasi</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Kategori</th>
                            <th>Nama Intansi</th>
                            <th>Alamat Intansi</th>
                            <th>Titik Lokasi</th>
                            <th>Opsi</th>
                        </tr>
                    </tfoot>
            <tbody>
                <?php
                            $database = new Database();
                            $db = $database->getConnection();
                            $selectSql = "SELECT * FROM data_instansi";
                            $stmt = $db->prepare($selectSql);
                            $stmt->execute();
                            $no = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                      <td><?php echo $no++ ?></td>
                      <td><?php echo $row['kategori'] ?></td>
                      <td><?php echo $row['nama_instansi'] ?></td>
                      <td><?php echo $row['alamat'] ?></td>
                      <td><a href="<?php echo $row['koordinat'] ?>" target="_blank">Lihat Lokasi</a></td>

                <td>
                <a href="?page=detail-data-instansi&id=<?php echo $row['id'] ?>"class="btn btn-secondary btn-sm mr-1"><i class="fa fa-file"></i> Detail</a>
                <a href="?page=edit-data-instansi&id=<?php echo $row['id'] ?>"class="btn btn-warning btn-sm mr-1"><i class="fa fa-edit"></i> Ubah</a>
                <a href="?page=hapus-data-instansi&id=<?php echo $row['id'] ?>"class="btn btn-danger btn-sm"onClick="javascript: return confirm('Konfirmasi data akan dihapus?');"><i class="fa fa-trash"></i> Hapus</a>
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
<?php include_once "partials/scripts.php" ?>
<?php include_once "partials/scriptsdatatables.php" ?>
<script>
 $(function() {
 $('#mytable').DataTable({})
//  "searching": false // Disable search feature
 });
</script>