<?php
ob_start();
session_start();

if (!isset($_SESSION['pesan'])) {
    $_SESSION['pesan'] = ""; // Set initial message to empty
}

// Konfigurasi koneksi ke basis data
$host = "localhost"; // Ganti dengan host Anda
$dbname = "sipanji"; // Ganti dengan nama database Anda
$username = "root"; // Ganti dengan username database Anda
$password = ""; // Ganti dengan password database Anda

try {
    // Buat koneksi ke basis data menggunakan PDO
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_POST['button_create'])) {
        // Tangkap nilai dari form
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $alamat = $_POST['alamat'];
        $tempat_lahir = $_POST['tempat_lahir'];
        $tanggal_lahir = $_POST['tanggal_lahir'];
        $telepon = $_POST['telepon'];

        // Cek apakah username sudah ada
        $checkUsernameQuery = "SELECT COUNT(*) FROM admin WHERE username = :username";
        $stmtCheck = $db->prepare($checkUsernameQuery);
        $stmtCheck->bindParam(':username', $username);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count > 0) {
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "Username sudah digunakan. Silakan pilih username lain.";
        } else {
            // Proses upload foto profil
            $target_dir = "../../uploaded_images/";
            $file_name = basename($_FILES["foto"]["name"]);
            $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $unique_code = date("YmdHis"); // Tambahkan kode unik berdasarkan tanggal penyimpanan
            $new_file_name = $unique_code . '_' . uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $new_file_name;
            $uploadOk = 1;

            // Check if image file is a actual image or fake image
            $check = getimagesize($_FILES["foto"]["tmp_name"]);
            if ($check === false) {
                $uploadOk = 0;
                $error_message = "File bukan gambar.";
            }

            // Allow certain file formats
            $allowed_formats = ["jpg", "png", "jpeg", "gif"];
            if (!in_array($imageFileType, $allowed_formats)) {
                $uploadOk = 0;
                $error_message = "Format file tidak diizinkan.";
            }

            if ($uploadOk == 0) {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Upload Foto: " . $error_message;
            } else {
                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                    // Query untuk menyimpan data pengguna
                    $query = "INSERT INTO admin (username, password, email, alamat, tempat_lahir, tanggal_lahir, foto, peran, telepon, status) 
                              VALUES (:username, :password, :email, :alamat, :tempat_lahir, :tanggal_lahir, :foto, 'umum', :telepon, 'pending')";
                    $stmt = $db->prepare($query);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', $password);
                    $stmt->bindParam(':email', $email);
                    $stmt->bindParam(':alamat', $alamat);
                    $stmt->bindParam(':tempat_lahir', $tempat_lahir);
                    $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
                    $stmt->bindParam(':foto', $new_file_name);
                    $stmt->bindParam(':telepon', $telepon);

                    // Eksekusi query dan cek hasilnya
                    if ($stmt->execute()) {
                        $_SESSION['hasil'] = true;
                        $_SESSION['pesan'] = "Berhasil Simpan Data.";
                        header("Location: view.php");
                        exit();
                    } else {
                        $_SESSION['hasil'] = false;
                        $_SESSION['pesan'] = "Gagal Simpan Data.";
                    }
                } else {
                    $_SESSION['hasil'] = false;
                    $_SESSION['pesan'] = "Gagal Upload Foto.";
                }
            }
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Data Pengguna</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>
<body>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Data Pengguna</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Tambah Data Pengguna</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" required>
                        <?php if (isset($_SESSION['pesan']) && strpos($_SESSION['pesan'], 'Username') !== false) : ?>
                            <div class="text-danger"><?php echo $_SESSION['pesan']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggle-password">
                                    <i class="fa fa-eye" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" required>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="text" class="form-control" name="telepon" id="telepon" required>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto Profil</label>
                        <input type="file" class="form-control-file" name="foto" id="foto" required>
                    </div>
                    <a href="view.php" class="btn btn-danger btn-sm float-right">
                        <i class="fa fa-times"></i> Batal
                    </a>
                    <button type="submit" name="button_create" class="btn btn-success btn-sm float-right ml-2">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>


<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('toggle-password');
    const passwordField = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    togglePassword.addEventListener('click', function() {
        // Toggle password visibility
        const type = passwordField.type === 'password' ? 'text' : 'password';
        passwordField.type = type;

        // Toggle eye icon
        if (type === 'password') {
            eyeIcon.classList.remove('fa-eye-slash');
            eyeIcon.classList.add('fa-eye');
        } else {
            eyeIcon.classList.remove('fa-eye');
            eyeIcon.classList.add('fa-eye-slash');
        }
    });
});
</script>
