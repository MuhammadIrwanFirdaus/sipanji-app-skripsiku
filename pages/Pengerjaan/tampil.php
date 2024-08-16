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
            <h1 class="m-0">Pengerjaan</h1>
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
                <h3 class="card-title">Data Pengerjaan</h3>
                    <a href="?page=serah-terima"
                        class="btn btn-secondary btn-sm float-right" style="margin-left: 10px;">
                            <i class="fa fa-file"></i> Serah Terima</a>
                    <!-- <a href="?page=cetak-pdf-pengerjaan"
                        class="btn btn-secondary btn-sm float-right">
                            <i class="fa fa-file"></i> Cetak PDF</a> -->
            </div>
            <div class="card-body">
                <table id="mytable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tempat</th>
                            <th>Tanggal</th>
                            <th>Nama Pemasang</th>
                            <th>Alat</th>
                            <th>Stok Terpakai</th>
                            <th>Foto Pengerjaan</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Tempat</th>
                            <th>Tanggal</th>
                            <th>Nama Pemasang</th>
                            <th>Alat</th>
                            <th>Stok Terpakai</th>
                            <th>Foto Pengerjaan</th>
                            <th>Opsi</th>
                        </tr>
                    </tfoot>
            <tbody>
                <?php
                            $database = new Database();
                            $db = $database->getConnection();
                            function indoDate($datetime) {
                              $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
                              $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                              
                              $day = date('w', strtotime($datetime));
                              $month = date('n', strtotime($datetime));
                              $date = date('j', strtotime($datetime)); // Mendapatkan angka tanggal
                          
                              return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));
                          }
                            $selectSql = "SELECT * FROM pengerjaan";
                            $stmt = $db->prepare($selectSql);
                            $stmt->execute();
                            $no = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                      <td><?php echo $no++ ?></td>
                      <td><?php echo $row['tempat'] ?></td>
                      <td><?php echo indoDate($row['tanggal']) ?></td>
                      <td><?php echo $row['nama_pemasang'] ?></td>
                      <td><?php echo $row['alat'] ?></td>
                      <td><?php echo $row['stok_terpakai'] ?></td>
                      <td>
                        <a href="uploaded_images/<?php echo $row['foto_pengerjaan']; ?>" target="_blank">
                            <img src="uploaded_images/<?php echo $row['foto_pengerjaan']; ?>" alt="Foto Pengerjaan" width="100">
                        </a>
                    </td>
                <td>
                <a href="?page=edit-data-pengerjaan&id=<?php echo $row['id_pengerjaan'] ?>"class="btn btn-warning btn-sm mr-1" style="margin : 10px"><i class="fa fa-edit"></i> Ubah</a>
                <a href="?page=hapus-data-pengerjaan&id=<?php echo $row['id_pengerjaan'] ?>"class="btn btn-danger btn-sm"onClick="javascript: return confirm('Konfirmasi data akan dihapus?');"  style="margin : 10px"><i class="fa fa-trash"></i> Hapus</a>
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