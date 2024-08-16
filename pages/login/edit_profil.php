<?php
ob_start();


// Konfigurasi koneksi ke basis data
$host = "localhost";
$dbname = "sipanji";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_SESSION['id'])) {
        $user_id = $_SESSION['id'];
        
        // Ambil data pengguna saat ini
        $query = "SELECT * FROM admin WHERE id = :id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':id', $user_id);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (isset($_POST['button_update'])) {
            $username = $_POST['username'];
            $password = $_POST['password'];
            $email = $_POST['email'];
            $alamat = $_POST['alamat'];
            $tempat_lahir = $_POST['tempat_lahir'];
            $tanggal_lahir = $_POST['tanggal_lahir'];
            $telepon = $_POST['telepon'];
            $foto = $user['foto'];

            // Cek apakah username sudah digunakan oleh pengguna lain
            $query_check = "SELECT COUNT(*) FROM admin WHERE username = :username AND id != :id";
            $stmt_check = $db->prepare($query_check);
            $stmt_check->bindParam(':username', $username);
            $stmt_check->bindParam(':id', $user_id);
            $stmt_check->execute();
            $username_exists = $stmt_check->fetchColumn();

            if ($username_exists) {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Username sudah digunakan. Silakan pilih username lain.";
            } else {
                if (!empty($_FILES['foto']['name'])) {
                    $target_dir = "uploaded_images/";
                    $new_file_name = uniqid() . '_' . basename($_FILES["foto"]["name"]);
                    $target_file = $target_dir . $new_file_name;

                    if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                        if (!empty($user['foto']) && file_exists($target_dir . $user['foto'])) {
                            unlink($target_dir . $user['foto']);
                        }
                        $foto = $new_file_name;
                    } else {
                        $_SESSION['hasil'] = false;
                        $_SESSION['pesan'] = "Gagal Upload Foto.";
                    }
                }

                // Update data pengguna
                $query_update = "UPDATE admin SET username = :username, password = :password, email = :email, 
                                 alamat = :alamat, tempat_lahir = :tempat_lahir, tanggal_lahir = :tanggal_lahir, 
                                 foto = :foto, telepon = :telepon WHERE id = :id";
                $stmt_update = $db->prepare($query_update);
                $stmt_update->bindParam(':username', $username);
                $stmt_update->bindParam(':password', $password);
                $stmt_update->bindParam(':email', $email);
                $stmt_update->bindParam(':alamat', $alamat);
                $stmt_update->bindParam(':tempat_lahir', $tempat_lahir);
                $stmt_update->bindParam(':tanggal_lahir', $tanggal_lahir);
                $stmt_update->bindParam(':foto', $foto);
                $stmt_update->bindParam(':telepon', $telepon);
                $stmt_update->bindParam(':id', $user_id);

                if ($stmt_update->execute()) {
                    $_SESSION['hasil'] = true;
                    $_SESSION['pesan'] = "Berhasil Memperbarui Data.";
                    header("Location: ?page=halaman-" . strtolower($user['peran']));
                    exit();
                } else {
                    $_SESSION['hasil'] = false;
                    $_SESSION['pesan'] = "Gagal Memperbarui Data";
                }
            }
        }
    } else {
        header("Location: pages/login/view.php");
        exit();
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

<!-- HTML Form remains the same -->


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil Pengguna</title>
    <?php include_once "partials/cssdatatables.php" ?>
</head>
<body>
<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Edit Profil Pengguna</h1>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Profil Pengguna</h3>
            </div>
            <div class="card-body">
                <form method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" class="form-control" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                        <?php if (isset($_SESSION['pesan']) && strpos($_SESSION['pesan'], 'Username') !== false) : ?>
                            <div class="text-danger"><?php echo $_SESSION['pesan']; ?></div>
                        <?php endif; ?>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" name="password" id="password" value="<?= htmlspecialchars($user['password']) ?>" required>
                            <div class="input-group-append">
                                <span class="input-group-text" id="toggle-password">
                                    <i class="fa fa-eye" id="eye-icon"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" name="alamat" id="alamat" value="<?= htmlspecialchars($user['alamat']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tempat_lahir">Tempat Lahir</label>
                        <input type="text" class="form-control" name="tempat_lahir" id="tempat_lahir" value="<?= htmlspecialchars($user['tempat_lahir']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="tanggal_lahir">Tanggal Lahir</label>
                        <input type="date" class="form-control" name="tanggal_lahir" id="tanggal_lahir" value="<?= htmlspecialchars($user['tanggal_lahir']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="telepon">Nomor Telepon</label>
                        <input type="text" class="form-control" name="telepon" id="telepon" value="<?= htmlspecialchars($user['telepon']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto Profil</label>
                        <input type="file" class="form-control-file" name="foto" id="foto">
                        <?php if (!empty($user['foto'])): ?>
                            <img src="uploaded_images/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil" width="100" class="mt-2">
                        <?php endif; ?>
                    </div>
                    <button type="submit" name="button_update" class="btn btn-success btn-sm float-right ml-2">
                        <i class="fa fa-save"></i> Simpan
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
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


<?php include_once "partials/scripts.php" ?>
