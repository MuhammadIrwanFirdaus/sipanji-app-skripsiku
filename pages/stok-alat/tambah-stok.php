<!-- Form untuk menambah stok alat -->
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Stok Alat</h3>
        </div>
        <div class="card-body">
            <form method="POST">
                <div class="form-group">
                    <label for="jumlah_stok_masuk">Jumlah Stok Masuk</label>
                    <input type="number" class="form-control" name="jumlah_stok_masuk" required>
                </div>
                <button type="submit" name="submit_stok" class="btn btn-success">Tambah Stok</button>
            </form>
        </div>
    </div>
</section>

<?php
// Mengatur aksi jika formulir dikirimkan
if (isset($_POST['submit_stok'])) {
    $database = new Database();
    $db = $database->getConnection();

    $jumlah_stok_masuk = $_POST['jumlah_stok_masuk'];

    // Lakukan validasi data, contoh: apakah jumlah stok masuk valid, dll.

    // Lakukan penambahan stok ke database
    $updateSql = "UPDATE stok_alat SET jumlah = jumlah + :jumlah_stok_masuk";
    $stmt = $db->prepare($updateSql);
    $stmt->bindParam(':jumlah_stok_masuk', $jumlah_stok_masuk, PDO::PARAM_INT);

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Ubah data";
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Ubah data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=tampil-stok-alat'>";
}
?>
