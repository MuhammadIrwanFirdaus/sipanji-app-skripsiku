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

    // Generate nomor unik untuk no_pengajuan
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

    // Mengupload file
    $targetDir = "uploaded_images/";
    $targetFile = $targetDir . basename($foto);
    $target_dir = "uploads_surat/";
    $target_file = $target_dir . basename($_FILES["surat_pengajuan"]["name"]);
    move_uploaded_file($_FILES["surat_pengajuan"]["tmp_name"], $target_file);
    move_uploaded_file($tmp_file, $targetFile);

    $user_id = $_SESSION['id']; // Mengambil user_id dari session

    // Tetapkan nilai default untuk status
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

<!-- HTML Formulir Pengajuan -->
<!-- Tambahkan CSS dan JS Leaflet -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

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
                <!-- Formulir Kategori, Tempat, Alamat, dan lainnya -->
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
                    <input type="text" class="form-control" name="tempat" id="tempat" required>
                </div>
                <!-- Input untuk alamat -->
                <div class="form-group">
                    <label for="alamat">Alamat (Format: Nama Jalan, Kelurahan, Kecamatan, Kota)</label>
                    <input type="text" class="form-control" name="alamat" id="alamat" required placeholder="Contoh: Jl. Ahmad Yani, Sungai Besar, Banjarbaru Selatan, Banjarbaru">
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
                    <input type="file" class="form-control-file" name="surat_pengajuan" accept=".pdf" required><br>
                </div>
                <div class="form-group">
                    <label for="foto">Unggah Foto</label>
                    <input type="file" class="form-control" name="foto" accept="image/*" required>
                </div>
                <div class="form-group">
                    <label for="koordinat">Link Koordinat</label>
                    <input type="text" class="form-control" id="koordinat" name="koordinat" readonly>
                </div>
                <button type="submit" name="button_create" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- Peta untuk menampilkan lokasi -->
    <div class="card">
        <div class="card-body">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</section>

<script>
    // Inisialisasi peta dengan Leaflet.js
    var map = L.map('map').setView([-6.9175, 107.6191], 13); // Lokasi awal: Bandung

    // Tambahkan tile layer dari OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Variabel untuk marker
    var marker;

    // Fungsi untuk geocoding menggunakan LocationIQ
    function geocodeAddress(address) {
        var apiKey = 'pk.82f7c6371953fbbac1c5c7040499eb84';  // Gantilah dengan API Key Anda dari LocationIQ
        var url = `https://us1.locationiq.com/v1/search.php?key=${apiKey}&q=${encodeURIComponent(address)}&format=json`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    var lat = data[0].lat; // Koordinat latitude
                    var lon = data[0].lon; // Koordinat longitude

                    // Pusatkan peta ke lokasi
                    map.setView([lat, lon], 15);

                    // Tambahkan marker ke peta
                    if (marker) map.removeLayer(marker); // Hapus marker sebelumnya jika ada
                    marker = L.marker([lat, lon]).addTo(map);

                    // Isi input koordinat dengan link Google Maps
                    const coordLink = `https://www.google.com/maps?q=${lat},${lon}`;
                    document.getElementById('koordinat').value = coordLink;
                } else {
                    alert("Alamat atau nama tempat tidak ditemukan. Silakan periksa kembali formatnya.");
                }
            })
            .catch(error => console.error('Error geocoding:', error));
    }

    // Event listener untuk input nama tempat
    document.getElementById('tempat').addEventListener('change', function () {
        var tempat = this.value.trim();
        if (tempat !== '') {
            geocodeAddress(tempat);
        }
    });

    // Event listener untuk input alamat
    document.getElementById('alamat').addEventListener('change', function () {
        var address = this.value.trim();
        if (address !== '') {
            geocodeAddress(address);
        }
    });

    // Trigger geocode when the page loads (in case the user already has an address or place)
    window.onload = function() {
        var initialAddress = document.getElementById('alamat').value.trim();
        if (initialAddress !== '') {
            geocodeAddress(initialAddress);
        }

        var initialTempat = document.getElementById('tempat').value.trim();
        if (initialTempat !== '') {
            geocodeAddress(initialTempat);
        }
    };
</script>
