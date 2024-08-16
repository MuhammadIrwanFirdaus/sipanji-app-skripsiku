<!-- Main content -->
<div class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Stok Alat</h3>
            <a href="?page=tambah-stok-alat"
                        class="btn btn-info btn-sm float-right" style="margin-left: 10px;">
                            <i class="fa fa-plus-circle"></i> Tambah Data</a>
                    <!-- <a href="?page=cetak-pdf-alat"
                        class="btn btn-secondary btn-sm float-right">
                            <i class="fa fa-file"></i> Cetak PDF</a> -->
        </div>
        <div class="card-body">
            <table id="stokAlatTable" class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Alat</th>
                        <th>Jumlah</th>
                        <th>Harga Satuan</th>
                        <th>Foto</th>
                        <th>Opsi</th>
                        <!-- Tambahkan kolom sesuai kebutuhan, misalnya: deskripsi, tanggal pembelian, dll -->
                    </tr>
                </thead>
                <tbody>
                    <?php
                    function formatRupiah($angka) {
                        return 'Rp ' . number_format($angka, 2, ',', '.');
                    }
                    // Di sini, kamu dapat mengambil data stok alat dari database
                    // Misalnya, dengan mengganti query dan pengambilan data berdasarkan struktur tabel yang ada di database
                    $database = new Database();
                    $db = $database->getConnection();
                    $selectSql = "SELECT * FROM stok_alat";
                    $stmt = $db->prepare($selectSql);
                    $stmt->execute();
                    $no = 1;
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                            <td><?php echo $no++ ?></td>
                            <td><?php echo $row['nama_alat'] ?></td>
                            <td><?php echo $row['jumlah'] ?></td>
                            <td><?php echo formatRupiah($row['harga']) ?></td>
                            <td>
                                <a href="uploaded_images/<?php echo $row['foto']; ?>" target="_blank">
                                    <img src="uploaded_images/<?php echo $row['foto']; ?>" alt="foto" width="100">
                                </a>
                            </td>
                            <td>
                                <a href="?page=hapus-stok-alat&id_stok=<?php echo $row['id_stok'] ?>"class="btn btn-danger btn-sm"onClick="javascript: return confirm('Konfirmasi data akan dihapus?');"><i class="fa fa-trash"></i> Hapus</a>
                                <a href="?page=update-stok-alat&id_stok=<?php echo $row['id_stok'] ?>"class="btn btn-secondary btn-sm mr-1"><i class="fa fa-plus"></i> Tambah Stok</a>
                                <a href="?page=ubah-stok-alat&id_stok=<?php echo $row['id_stok'] ?>"class="btn btn-warning btn-sm mr-1"><i class="fa fa-edit"></i> Ubah Data</a>
                            </td>
                            <!-- Tambahkan kolom lain sesuai kebutuhan -->
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