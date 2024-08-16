<?php
include "pages/login/function.php";
check_access('umum');

if (isset($_POST['button_create'])) {
    $database = new Database();
    $db = $database->getConnection();

    $foto = $_FILES['foto']['name'];
    $tmp_file = $_FILES['foto']['tmp_name'];
    $surat_pengajuan = $_FILES['surat_pengajuan']['name'];
    $tmp_surat = $_FILES['surat_pengajuan']['tmp_name'];

    // Generate unique number for no_pengajuan
    $newNumber = "PNJ-" . time(); 

    $insertSQL = "INSERT INTO data_pengajuan (no_pengajuan, kategori, tempat, alamat, nama_perwakilan, no_telpon, tgl_masuk, surat_pengajuan, koordinat, foto, status, user_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $db->prepare($insertSQL);
    $stmt->bindParam(1, $newNumber);
    $stmt->bindParam(2, $_POST['kategori']);
    $stmt->bindParam(3, $_POST['tempat']);
    $stmt->bindParam(4, $_POST['alamat']);
    $stmt->bindParam(5, $_POST['nama_perwakilan']);
    $stmt->bindParam(6, $_POST['no_telpon']);
    $stmt->bindParam(7, $_POST['tgl_masuk']);
    $stmt->bindParam(8, $_FILES['surat_pengajuan']['name']);
    $stmt->bindParam(9, $_POST['koordinat']);
    $stmt->bindParam(10, $_FILES['foto']['name']);
    $stmt->bindParam(11, $status);
    $stmt->bindParam(12, $user_id);

    // Upload file
    $targetDir = "uploaded_images/";
    $targetFile = $targetDir . basename($foto);
    $target_dir = "uploads_surat/";
    $target_file = $target_dir . basename($_FILES["surat_pengajuan"]["name"]);
    move_uploaded_file($_FILES["surat_pengajuan"]["tmp_name"], $target_file);
    move_uploaded_file($tmp_file, $targetFile);

    $user_id = $_SESSION['id']; // Ambil user_id dari session

    // Tetapkan nilai default untuk status jika tidak ditentukan
    $status = 'sedang proses'; // Ganti dengan nilai yang sesuai

    if ($stmt->execute()) {
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil Simpan Data";

        // Kirim pesan ke Telegram
        $message = "Pengajuan baru telah masuk:\n";
        $message .= "No Pengajuan: $newNumber\n";
        $message .= "Kategori: " . $_POST['kategori'] . "\n";
        $message .= "Tempat: " . $_POST['tempat'] . "\n";
        $message .= "Alamat: " . $_POST['alamat'] . "\n";
        $message .= "Nama Perwakilan: " . $_POST['nama_perwakilan'] . "\n";
        $message .= "No Telpon: " . $_POST['no_telpon'] . "\n";
        $message .= "Tanggal Masuk: " . $_POST['tgl_masuk'] . "\n";
        $message .= "Link Koordinat: " . $_POST['koordinat'];

        sendTelegramMessage($message);
    } else {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal Simpan Data";
    }
    echo "<meta http-equiv='refresh' content='0; url=?page=monitoring-pengajuan-umum'>";
}
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>

<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Data Pengajuan</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                    <li class="breadcrumb-item"><a href="?page=tampil-data-pengajuan">Data Pengajuan</a></li>
                    <li class="breadcrumb-item active">Tambah Data</li>
                </ol>
            </div>
        </div>
    </div>
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data Pengajuan</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="kategori">Kategori</label>
                    <select class="form-control" name="kategori" required>
                        <option value="">Pilih Kategori</option>
                        <option value="perkantoran">Perkantoran</option>
                        <option value="sekolahan">Sekolahan</option>
                        <option value="puskesmas">Puskesmas</option>
                        <option value="publik">Ruang Publik</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tempat">Nama Tempat</label>
                    <input type="text" class="form-control" name="tempat" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" class="form-control" name="alamat" required>
                </div>
                <div class="form-group">
                    <label for="nama_perwakilan">Nama Perwakilan</label>
                    <input type="text" class="form-control" name="nama_perwakilan" required>
                </div>
                <div class="form-group">
                    <label for="no_telpon">Nomor Telepon</label>
                    <input type="text" class="form-control" name="no_telpon" required>
                </div>
                <div class="form-group">
                    <label for="tgl_masuk">Tanggal Masuk</label>
                    <input type="datetime-local" class="form-control" name="tgl_masuk" required>
                </div>
                <div class="form-group">
                    <label for="surat_pengajuan">Surat Pengajuan (PDF)</label>
                    <input type="file" class="form-control-file" name="surat_pengajuan" accept=".pdf" required>
                </div>
                <div class="form-group">
                    <label for="foto">Unggah Foto</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="koordinat">Link Koordinat</label>
                    <input type="text" class="form-control" id="koordinat" name="koordinat" readonly>
                </div>
                <button onclick="goBack()" class="btn btn-secondary mt-3">Batal</button>
                <button type="submit" name="button_create" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</section>

<?php include_once "partials/scripts.php" ?>

<script>
            function goBack() {
            window.history.back();
        }

function initMap() {
    var map = L.map('map').setView([0, 0], 13); // Tentukan koordinat dan level zoom awal

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19, // Tentukan zoom maksimum
    }).addTo(map);

    // Variabel global untuk menyimpan lokasi
    var marker = L.marker([0, 0], { draggable: true }).addTo(map); // Tambahkan marker pada peta

    // Memanggil fungsi getLocation saat halaman dimuat
    getLocation();

    function getLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(showPosition);
        } else {
            alert("Geolocation tidak didukung oleh peramban ini.");
        }
    }

    function showPosition(position) {
        var userLocation = [position.coords.latitude, position.coords.longitude];

        map.setView(userLocation, 13); // Mengatur peta pada lokasi pengguna
        marker.setLatLng(userLocation); // Memindahkan marker ke lokasi pengguna

        updateCoordinates(userLocation); // Memperbarui koordinat pada input

        // Menampilkan koordinat pada kolom "Link Koordinat"
        document.getElementById('koordinat').value = "https://www.google.com/maps?q=" + userLocation[0] + "," + userLocation[1];
    }

    // Event listener saat marker dipindahkan
    marker.on('dragend', function (event) {
        var newPosition = marker.getLatLng();
        updateCoordinates([newPosition.lat, newPosition.lng]);
    });

    // Memperbarui koordinat pada input dan kolom "Link Koordinat"
    function updateCoordinates(location) {
        document.getElementById('koordinat').value = "https://www.google.com/maps?q=" + location[0] + "," + location[1];
    }
}

window.onload = initMap;
</script>
