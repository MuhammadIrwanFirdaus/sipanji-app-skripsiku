<?php 
if (isset($_GET['id'])) {
    $database = new Database();
    $db = $database->getConnection();

    $id = $_GET['id'];
    $findSql = "SELECT * FROM data_instansi where id = ?";
    $stmt = $db->prepare($findSql);
    $stmt->bindParam(1, $_GET['id']);
    $stmt->execute();
    $row = $stmt->fetch();
    
    if (isset($row['id'])) {
        if (isset($_POST['button_update'])) {
            $updateSQL = "UPDATE data_instansi SET kategori = ?, nama_instansi = ?, nama_perwakilan = ?, alamat = ?, no_telpon = ?, koordinat = ? WHERE id=?";
            $stmt = $db->prepare($updateSQL);
            $stmt->bindParam(1, $_POST['kategori']);
            $stmt->bindParam(2, $_POST['nama_instansi']);
            $stmt->bindParam(3, $_POST['nama_perwakilan']);
            $stmt->bindParam(4, $_POST['alamat']);
            $stmt->bindParam(5, $_POST['no_telpon']);
            $stmt->bindParam(6, $_POST['koordinat']);
            $stmt->bindParam(7, $_POST['id']);
            
            if ($stmt->execute()) {
                $_SESSION['hasil'] = true;
                $_SESSION['pesan'] = "Berhasil Ubah data";
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Ubah data";
            }
            echo "<meta http-equiv='refresh' content='0; url=?page=tampil-data-instansi'>";
        }
    }
}
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/leaflet@1.7.1/dist/leaflet.js"></script>

<section class="content-header">
    <!-- Header content -->
</section>
<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Ubah Data Instansi</h3>
        </div>
        <div class="card-body">
            <form method="POST">
            <div class="form-group">
                <label for="kategori">Kategori</label>
                <input type="hidden" class="form-control" name="id" value="<?php echo $row['id'] ?>" >
                <select class="form-control" name="kategori" value="<?php echo $row['kategori'] ?>" required>
                    <option value="">Pilih Kategori</option>
                    <option value="perkantoran">Perkantoran</option>
                    <option value="sekolahan">Sekolahan</option>
                    <option value="puskesmas">Puskesmas</option>
                </select>
            </div>
                <div class="form-group">
                    <label for="nama_instansi">Nama Instansi</label>
                    <input type="text" class="form-control" name="nama_instansi" value="<?php echo $row['nama_instansi'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="nama_perwakilan">Nama Perwakilan</label>
                    <input type="text" class="form-control" name="nama_perwakilan" value="<?php echo $row['nama_perwakilan'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" class="form-control" name="alamat" value="<?php echo $row['alamat'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="no_telpon">Nomor yang Bisa Dihubungi</label>
                    <input type="text" class="form-control" name="no_telpon" value="<?php echo $row['no_telpon'] ?>" required>
                </div>
                <div class="form-group">
                    <label for="koordinat">Link Koordinat:</label>
                    <input type="text" class="form-control" id="koordinat" name="koordinat" value="<?php echo $row['koordinat'] ?>"readonly>
                </div>
                <a href="?page=tampil-data-instansi" class="btn btn-danger btn-sm float-right" style="margin-left: 10px;">
                    <i class="fa fa-times"></i> Batal
                </a>
                <button type="submit" name="button_update" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div id="addressResult"></div>
            <input id="pac-input" class="controls" type="text" placeholder="Cari lokasi">
            <div id="map" style="height: 400px;"></div>
        </div>
    </div>
</section>

<?php include_once "partials/scripts.php" ?>

<script>
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

        var url = document.getElementById('koordinat').value;
        var coordinates = getCoordinatesFromURL(url);
        if (coordinates) {
            marker.setLatLng(coordinates); // Atur marker pada koordinat yang diekstrak
            map.setView(coordinates, 13); // Atur peta pada koordinat yang diekstrak
        }
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

        // Fungsi untuk mengekstrak koordinat dari URL
        function getCoordinatesFromURL(url) {
        var match = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (match) {
            var lat = parseFloat(match[1]);
            var lng = parseFloat(match[2]);
            return [lat, lng];
        }
        return null;
    }
}

window.onload = initMap;
</script>