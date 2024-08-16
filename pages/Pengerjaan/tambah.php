<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$idJadwal = isset($_GET['id']) ? $_GET['id'] : null;
$noPengajuan = isset($_GET['no_pengajuan']) ? $_GET['no_pengajuan'] : null;
$tempat = isset($_GET['title']) ? $_GET['title'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();

    // Ambil nilai dari form
    $no_pengajuan = $_POST['no_pengajuan'];
    $tempat = $_POST['tempat'];
    $tanggal = $_POST['tanggal'];
    $nama_pemasang = $_POST['nama_pemasang'];
    $alat_array = $_POST['alat'];
    $stok_terpakai_array = $_POST['stok_terpakai'];
    $foto_array = $_FILES['foto'];
    $uang_bensin = $_POST['uang_bensin'];
    $uang_makan = $_POST['uang_makan'];

    // Total biaya tambahan
    $biaya_tambahan = $uang_bensin + $uang_makan;

    for ($i = 0; $i < count($alat_array); $i++) {
        $alat = $alat_array[$i];
        $stok_terpakai = $stok_terpakai_array[$i];
        $foto = $foto_array['name'][$i];
        $tmp_file = $foto_array['tmp_name'][$i];
    
        if (!empty($alat) && !empty($stok_terpakai) && !empty($foto)) {
            // Ambil stok alat yang ada
            $stokAlatQuery = "SELECT jumlah FROM stok_alat WHERE nama_alat = :nama_alat";
            $stmtStokAlat = $db->prepare($stokAlatQuery);
            $stmtStokAlat->bindValue(':nama_alat', $alat);
            $stmtStokAlat->execute();
            $stokAlat = $stmtStokAlat->fetch(PDO::FETCH_ASSOC)['jumlah'];
    
            if ($stokAlat < $stok_terpakai) {
                echo "<script>alert('Jumlah stok alat tidak mencukupi untuk alat {$alat}!');</script>";
                continue;
            }
    
            $targetDir = "uploaded_images/";
            $targetFile = $targetDir . basename($foto);
    
            if (move_uploaded_file($tmp_file, $targetFile)) {
                // Siapkan query insert untuk masing-masing alat
                $insertSQL = "INSERT INTO pengerjaan (no_pengajuan, tempat, tanggal, nama_pemasang, alat, stok_terpakai, foto_pengerjaan, biaya_tambahan) 
                              VALUES (:no_pengajuan, :tempat, :tanggal, :nama_pemasang, :alat, :stok_terpakai, :foto_pengerjaan, :biaya_tambahan)";
                $stmt = $db->prepare($insertSQL);
    
                $stmt->bindValue(':no_pengajuan', $no_pengajuan);
                $stmt->bindValue(':tempat', $tempat);
                $stmt->bindValue(':tanggal', $tanggal);
                $stmt->bindValue(':nama_pemasang', $nama_pemasang);
                $stmt->bindValue(':alat', $alat);
                $stmt->bindValue(':stok_terpakai', $stok_terpakai);
                $stmt->bindValue(':foto_pengerjaan', $foto);
                $stmt->bindValue(':biaya_tambahan', $biaya_tambahan);
    
                // Simpan data ke database
                if ($stmt->execute()) {
                    // Update stok alat
                    $updateStokSQL = "UPDATE stok_alat SET jumlah = jumlah - :jumlah_terpakai WHERE nama_alat = :nama_alat";
                    $stmtStok = $db->prepare($updateStokSQL);
                    $stmtStok->bindValue(':jumlah_terpakai', $stok_terpakai, PDO::PARAM_INT);
                    $stmtStok->bindValue(':nama_alat', $alat);
                    $stmtStok->execute();
                } else {
                    echo "<script>alert('Gagal menyimpan data ke database untuk alat {$alat}');</script>";
                }
            } else {
                echo "<script>alert('Gagal mengunggah file foto untuk alat {$alat}');</script>";
            }
        } else {
            echo "<script>alert('Data alat, stok terpakai, atau foto tidak lengkap untuk alat {$alat}');</script>";
        }
    }

    // Redirect setelah semua data diproses
    echo "<meta http-equiv='refresh' content='0; url=?page=tampil-pengerjaan'>";
}

// Ambil data alat dari tabel alat
$database = new Database();
$db = $database->getConnection();
$alatQuery = "SELECT nama_alat FROM stok_alat";
$stmtAlat = $db->prepare($alatQuery);
$stmtAlat->execute();
$alatList = $stmtAlat->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pengerjaan</title>
    <!-- Panggil sumber daya Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <div class="container">
        <h1 class="mt-5">Tambah Data Pengerjaan</h1>
        <form method="POST" enctype="multipart/form-data" id="formPengerjaan">
            <!-- Kolom Tempat (terisi otomatis dari tempat yang dikirimkan) -->
            <!-- Kolom No. Pengajuan (terisi otomatis dari no_pengajuan yang dikirimkan) -->
            <div class="form-group">
                <label for="no_pengajuan">No. Pengajuan</label>
                <input type="text" class="form-control" id="no_pengajuan" name="no_pengajuan" value="<?php echo htmlspecialchars($noPengajuan); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tempat">Tempat</label>
                <input type="text" class="form-control" id="tempat" name="tempat" value="<?php echo htmlspecialchars($tempat); ?>" readonly>
            </div>
            <div class="form-group">
                <label for="tanggal">Tanggal dan Waktu</label>
                <input type="datetime-local" class="form-control" id="tanggal" name="tanggal" required>
            </div>
            <div class="form-group">
                <label for="nama_pemasang">Nama Pemasang</label>
                <input type="text" class="form-control" id="nama_pemasang" name="nama_pemasang" required>
            </div>
            <div class="form-group">
                <label for="uang_bensin">Uang Bensin (Rp 50,000)</label>
                <input type="hidden" class="form-control" id="uang_bensin" name="uang_bensin" value="50000" readonly>
            </div>
            <div class="form-group">
                <label for="uang_makan">Uang Makan (Rp 100,000)</label>
                <input type="hidden" class="form-control" id="uang_makan" name="uang_makan" value="100000" readonly>
            </div>
            <div id="alat_stok_terpakai">
                <div class="form-group">
                    <label for="alat">Alat</label>
                    <select class="form-control" name="alat[]" required>
                        <?php foreach ($alatList as $alat): ?>
                            <option value="<?php echo htmlspecialchars($alat['nama_alat']); ?>"><?php echo htmlspecialchars($alat['nama_alat']); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="stok_terpakai">Stok Terpakai</label>
                    <input type="number" class="form-control" name="stok_terpakai[]" required>
                </div>
            </div>
            <div class="form-group">
                <label for="foto">Unggah Foto Pengerjaan</label>
                <input type="file" class="form-control" id="foto" name="foto[]" accept="image/*" multiple required>
            </div>

            <button type="submit" class="btn btn-primary">Tambah Data</button>
            <a href="index.php?page=tampil-pengerjaan" class="btn btn-secondary">Kembali</a>
            <button type="button" class="btn btn-info" onclick="tambahBaris()">Tambah Baris alat dan stok</button>
        </form>
    </div>

    <!-- Panggil sumber daya jQuery dan Bootstrap JS -->
    <script src="assets/jquery.min.js"></script>
    <script src="plugin-fa/bootstrap/js/bootstrap.bundle.min.js"></script>
    
    <script>
    function tambahBaris() {
        var alatStok = document.getElementById("alat_stok_terpakai");

        var divAlat = document.createElement("div");
        divAlat.classList.add("form-group");
        divAlat.innerHTML = `
            <label for="alat">Alat</label>
            <select class="form-control" name="alat[]" required>
                <?php foreach ($alatList as $alat): ?>
                    <option value="<?php echo htmlspecialchars($alat['nama_alat']); ?>"><?php echo htmlspecialchars($alat['nama_alat']); ?></option>
                <?php endforeach; ?>
            </select>
        `;
        alatStok.appendChild(divAlat);

        var divStok = document.createElement("div");
        divStok.classList.add("form-group");
        divStok.innerHTML = `
            <label for="stok_terpakai">Stok Terpakai</label>
            <input type="number" class="form-control" name="stok_terpakai[]" required>
        `;
        alatStok.appendChild(divStok);

        var divFoto = document.createElement("div");
        divFoto.classList.add("form-group");
        divFoto.innerHTML = `
            <label for="foto">Unggah Foto Pengerjaan</label>
            <input type="file" class="form-control" name="foto[]" accept="image/*" multiple required>
        `;
        alatStok.appendChild(divFoto);
    }
    </script>
</body>
</html>
