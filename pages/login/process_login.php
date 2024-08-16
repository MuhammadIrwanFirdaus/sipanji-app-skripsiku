<?php
session_start();

// Konfigurasi koneksi ke basis data
$host = "localhost"; // Ganti dengan host Anda
$dbname = "sipanji"; // Ganti dengan nama database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda

try {
    // Buat koneksi ke basis data menggunakan PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek apakah form login telah dikirim
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Pastikan variabel POST tidak kosong
        if (!empty($_POST['username']) && !empty($_POST['password'])) {
            // Ambil nilai dari form login
            $username = $_POST['username'];
            $password = $_POST['password'];

            // Buat query untuk memeriksa kredensial pengguna di tabel admin
            $query = "SELECT * FROM admin WHERE username = :username";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();

            // Ambil hasil query
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifikasi password dan cek apakah pengguna ditemukan
            if ($user && $password === $user['password']) {
                // Cek status pengguna
                if ($user['status'] === 'diterima') {
                    $updateQuery = "UPDATE admin SET login_terakhir = NOW() WHERE id = :id";
                    $updateStmt = $db->prepare($updateQuery);
                    $updateStmt->bindParam(':id', $user['id']);
                    $updateStmt->execute();
                    // Set sesi pengguna dan arahkan ke halaman sesuai peran
                    $_SESSION['id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['peran'] = $user['peran'];
                    $_SESSION['foto'] = $foto; // Foto profil harus diambil dari database atau file sistem

                    switch ($user['peran']) {
                        case 'admin':
                            header("Location: ../../index.php?page=halaman-admin");
                            break;
                        case 'kominfo':
                            header("Location: ../../index.php?page=halaman-kominfo");
                            break;
                        case 'instansi':
                            header("Location: ../../index.php?page=halaman-instansi");
                            break;
                        case 'umum':
                            header("Location: ../../index.php?page=halaman-umum");
                            break;
                        default:
                            header("Location: login.php?error=invalid_role");
                            break;
                    }
                    exit();
                } else {
                    // Jika status bukan 'diterima', arahkan kembali dengan pesan kesalahan
                    header("Location: view.php?error=status_not_accepted");
                    exit();
                }
            } else {
                // Jika kredensial salah, arahkan kembali ke halaman login dengan notifikasi kesalahan
                header("Location: view.php?error=invalid_credentials");
                exit();
            }
        } else {
            // Jika username atau password kosong, arahkan kembali ke halaman login dengan notifikasi kesalahan
            header("Location: view.php?error=empty_fields");
            exit();
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
