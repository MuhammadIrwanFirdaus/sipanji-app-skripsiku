<!DOCTYPE html>
<html>
<head>
    <!-- Sertakan link CSS Bootstrap -->
    <link rel="stylesheet" href="assets/bootstrap.css">
</head>
<body>
    <?php
    // Sertakan file koneksi ke database (gantilah sesuai dengan file koneksi Anda)
    include "koneksi.php";

    if (isset($_GET['id'])) {
        $eventId = $_GET['id'];

        // Lakukan validasi data jika diperlukan
        // ...

        if (isset($_GET['confirm'])) {
            if ($_GET['confirm'] === 'yes') {
                // Persiapkan pernyataan SQL
                $sql = "DELETE FROM events WHERE id = ?";
                
                // Persiapkan pernyataan
                $stmt = $koneksi->prepare($sql);
                $stmt->bind_param("i", $eventId);

                // Eksekusi pernyataan
                if ($stmt->execute()) {
                    // Penghapusan berhasil
                    header("Location: index.php?page=Jadwal"); // Redirect kembali ke halaman utama setelah menghapus
                    exit;
                } else {
                    // Penghapusan gagal
                    echo "Penghapusan gagal: " . $stmt->error;
                }
            } elseif ($_GET['confirm'] === 'no') {
                // Pengguna memilih "No," kembali ke halaman utama atau lakukan tindakan lain
                header("Location: index.php?page=Jadwal");
                exit;
            }
        } else {
            // Menampilkan pesan konfirmasi dengan gaya Bootstrap
            echo "<div class='container mt-5'>";
            echo "<div class='alert alert-danger' role='alert'>";
            echo "Apakah Anda yakin ingin menghapus item ini?";
            echo "</div>";
            echo "<a href=\"hapus.php?id=$eventId&confirm=yes\" class='btn btn-danger'>Yes</a> ";
            echo "<a href=\"hapus.php?id=$eventId&confirm=no\" class='btn btn-primary'>No</a>";
            echo "</div>";
        }
    } else {
        // Jika ID tidak ditemukan di URL
        echo "ID tidak valid.";
    }
    ?>

    <!-- Sertakan script Bootstrap -->
    <script src="plugins/js/bootstrap.min.js"></script>
</body>
</html>
