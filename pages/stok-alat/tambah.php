<?php
if (isset($_POST['button_create'])) {

    $database = new Database();
    $db = $database->getConnection();

    // Mengambil nilai dari form
    $nama_alat = $_POST['nama_alat'];
    $jumlah = $_POST['jumlah'];
    $harga = $_POST['harga'];
    $foto = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];

    // Proses konversi jika ekstensi file adalah .jfif
    if (strtolower(pathinfo($foto, PATHINFO_EXTENSION)) == 'jfif') {
        $tmp_file = convertJfifToJpg($tmp_file);
        $foto = str_replace('.jfif', '.jpg', $foto); // Update nama file
    }

    // Siapkan query untuk menyimpan data ke dalam tabel stok_alat
    $insertSQL = "INSERT INTO stok_alat (nama_alat, jumlah, harga, foto) VALUES (?, ?, ?, ?)";
    $stmt = $db->prepare($insertSQL);
    $stmt->bindParam(1, $nama_alat);
    $stmt->bindParam(2, $jumlah);
    $stmt->bindParam(3, $harga);
    $stmt->bindParam(4, $foto);

    if ($stmt->execute()) {
        // Proses unggah foto
        $targetDir = "uploaded_images/";
        $targetFile = $targetDir . basename($foto);

        if (move_uploaded_file($tmp_file, $targetFile)) {
            $_SESSION['hasil'] = true;
            $_SESSION['pesan'] = "Berhasil Simpan Data";
        } else {
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "Gagal Mengunggah Foto";
        }
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Simpan Data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=tampil-stok-alat'>";
}

function convertJfifToJpg($imagePath) {
    $image = imagecreatefromjpeg($imagePath); // Buat gambar dari file jfif
    $newPath = str_replace('.jfif', '.jpg', $imagePath); // Ganti ekstensi ke jpg
    imagejpeg($image, $newPath); // Simpan gambar sebagai jpg
    imagedestroy($image); // Hapus dari memori
    return $newPath; // Return path gambar baru
}
?>



<section class="content-header">
    <div class="containerfluid">
        <div class="row mb2">
            <div class="col-sm-6">
                <h1>Tambah Stok Alat</h1>
            </div>
            <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                <li class="breadcrumb-item"><a href="?page=tampil-stok-alat">Stok Alat</a></li>
                <li class="breadcrumb-item active">Tambah Data</li>
            </ol>
        </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Stok ALat</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nama">Nama Alat</label>
                    <input type="text" class="form-control" name="nama_alat" required>
                </div>
                <div class="form-group">
                    <label for="jumlah">Jumlah</label>
                    <input type="BigInt" class="form-control" name="jumlah" required>
                </div>
                <div class="form-group">
                    <label for="harga">Harga Satuan</label>
                    <input type="decimal" class="form-control" name="harga" required>
                </div>
                <div class="form-group">
                    <label for="foto">Unggah Foto</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>
                <a href="?page=tampil-stok-alat" class="btn btn-danger btn-sm float-right"  style="margin-left: 10px;">
                    <i class="fa fa-times"></i> Batal
                </a>
                <button type="submit" name="button_create" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>