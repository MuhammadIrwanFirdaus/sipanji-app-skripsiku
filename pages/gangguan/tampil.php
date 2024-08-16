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
            <h1 class="m-0">Data Gangguan</h1>
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
                <h3 class="card-title">Data Gangguan</h3>
                    <!-- <a href="?page=tambah-data-pengajuan"
                        class="btn btn-info btn-sm float-right" style="margin-left: 10px;">
                            <i class="fa fa-plus-circle"></i> Tambah Data</a> -->
            </div>
            <div class="card-body">
                <table id="mytable" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                        <th>No</th>
                            <th>Nomor Gangguan</th>
                            <th>Nama Tempat</th>
                            <th>Perwakilan</th>
                            <th>Nomor Telepon</th>
                            <th>Keterangan</th>
                            <th>Tanggal Masuk</th>
                            <th>Foto Tempat</th>
                            <th>Opsi</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>No</th>
                            <th>Nomor Gangguan</th>
                            <th>Nama Tempat</th>
                            <th>Perwakilan</th>
                            <th>Nomor Telepon</th>
                            <th>Keterangan</th>
                            <th>Tanggal Masuk</th>
                            <th>Foto Tempat</th>
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
                              $selectSql = "SELECT * FROM gangguan WHERE status = 'diterima'";
                              $stmt = $db->prepare($selectSql);
                              $stmt->execute();
                              $no = 1;
                              while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <tr>
                      <td><?php echo $no++ ?></td>
                      <td><?php echo $row['no_pengajuan'] ?></td>
                      <td><?php echo $row['nama_tempat'] ?></td>
                      <td><?php echo $row['perwakilan'] ?></td>
                      <td><?php echo $row['nomor_telepon'] ?></td>
                      <td><?php echo $row['keterangan'] ?></td>
                      <td><?php echo indoDate($row['tgl_masuk']) ?></td>
                        <td>
                                <a href="uploaded_images/<?php echo $row['foto_kerusakan']; ?>" target="_blank">
                                    <img src="uploaded_images/<?php echo $row['foto_kerusakan']; ?>" alt="foto" width="100">
                                </a>
                            </td>
                <td>
                <a href="?page=edit-gangguan&id=<?php echo $row['id'] ?>" class="btn btn-warning btn-sm float-right" style="margin: 10px;"><i class="fa fa-edit"></i> Ubah Data</a>
                <a href="?page=hapus-gangguan&id=<?php echo $row['id'] ?>"class="btn btn-danger btn-sm float-right" style="margin: 10px"><i class="fa fa-trash"></i> Hapus</a>
                <a href="?page=tambahJadwal&id=<?php echo $row['id']; ?>&no_pengajuan=<?php echo $row['no_pengajuan']; ?>&tempat=<?php echo urlencode($row['nama_tempat']); ?>" class="btn btn-info btn-sm float-right" style="margin: 10px"><i class="fa fa-calendar"></i> Jadwal</a>
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


<?php include_once "partials/scripts.php" ?>
<?php include_once "partials/scriptsdatatables.php" ?>
<script>
 $(function() {
 $('#mytable').DataTable({})
//  "searching": false // Disable search feature
 });
</script>