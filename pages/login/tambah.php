<?php
if (isset($_POST['button_create'])) {
    // Tangkap nilai dari form
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email']; // Email tidak digunakan untuk verifikasi di sini
    $alamat = $_POST['alamat'];
    $tempat_lahir = $_POST['tempat_lahir'];
    $tanggal_lahir = $_POST['tanggal_lahir'];
    $peran = $_POST['peran'];
    $telepon = $_POST['telepon'];

    // Inisialisasi koneksi ke database
    $database = new Database();
    $db = $database->getConnection();

    // Cek apakah username sudah digunakan
    $query_check = "SELECT COUNT(*) FROM admin WHERE username = :username";
    $stmt_check = $db->prepare($query_check);
    $stmt_check->bindParam(':username', $username);
    $stmt_check->execute();
    $username_exists = $stmt_check->fetchColumn();

    if ($username_exists) {
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Username sudah digunakan. Silakan pilih username lain.";
    } else {
        // Proses upload foto profil
        $target_dir = "uploaded_images/";
        $file_name = basename($_FILES["foto"]["name"]);
        $imageFileType = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $new_file_name = uniqid() . '.' . $imageFileType;
        $target_file = $target_dir . $new_file_name;
        $uploadOk = 1;

        // Check if image file is an actual image or fake image
        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check === false) {
            $uploadOk = 0;
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "File bukan gambar.";
        }

        // Allow all file sizes (Remove size check)
        // Allow certain file formats
        $allowed_formats = ["jpg", "png", "jpeg", "gif"];
        if (!in_array($imageFileType, $allowed_formats)) {
            $uploadOk = 0;
            $_SESSION['hasil'] = false;
            $_SESSION['pesan'] = "Format file tidak diizinkan.";
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                // Query untuk menyimpan data pengguna
                $query = "INSERT INTO admin (username, password, email, alamat, tempat_lahir, tanggal_lahir, foto, peran, telepon, status) 
                          VALUES (:username, :password, :email, :alamat, :tempat_lahir, :tanggal_lahir, :foto, :peran, :telepon, 'diterima')";
                $stmt = $db->prepare($query);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':password', $password);
                $stmt->bindParam(':email', $email); // Email disimpan tapi tidak digunakan untuk verifikasi
                $stmt->bindParam(':alamat', $alamat);
                $stmt->bindParam(':tempat_lahir', $tempat_lahir);
                $stmt->bindParam(':tanggal_lahir', $tanggal_lahir);
                $stmt->bindParam(':foto', $new_file_name);
                $stmt->bindParam(':peran', $peran);
                $stmt->bindParam(':telepon', $telepon);

                // Eksekusi query dan cek hasilnya
                if ($stmt->execute()) {
                    $_SESSION['hasil'] = true;
                    $_SESSION['pesan'] = "Berhasil Simpan Data.";
                } else {
                    $_SESSION['hasil'] = false;
                    $_SESSION['pesan'] = "Gagal Simpan Data";
                }
            } else {
                $_SESSION['hasil'] = false;
                $_SESSION['pesan'] = "Gagal Upload Foto.";
            }
        }
    }

    echo "<meta http-equiv='refresh' content='0; url=?page=tampil-admin'>";
}
?>



<section class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Tambah Data Pengguna</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="?page=home">Home</a></li>
                    <li class="breadcrumb-item"><a href="?page=tampil-admin">Admin</a></li>
                    <li class="breadcrumb-item active">Tambah Data</li>
                </ol>
            </div>
        </div>
    </div>
</section>

<section class="content">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Tambah Data Pengguna</h3>
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" class="form-control" name="username" required>
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
                    <input type="email" class="form-control" name="email" required>
                </div>
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <input type="text" class="form-control" name="alamat" required>
                </div>
                <div class="form-group">
                    <label for="tempat_lahir">Tempat Lahir</label>
                    <input type="text" class="form-control" name="tempat_lahir" required>
                </div>
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" class="form-control" name="tanggal_lahir" required>
                </div>
                <div class="form-group">
                    <label for="telepon">Nomor Telepon</label>
                    <input type="text" class="form-control" name="telepon" required>
                </div>
                <div class="form-group">
                    <label for="foto">Foto Profil</label>
                    <input type="file" class="form-control" name="foto" required>
                </div>
                <div class="form-group">
                    <label for="peran">Peran</label>
                    <select class="form-control" name="peran" required>
                        <option value="admin">Admin</option>
                        <option value="kominfo">Kominfo</option>
                        <option value="instansi">Instansi</option>
                        <option value="umum">Umum</option>
                    </select>
                </div>
                <a href="?page=tampil-admin" class="btn btn-danger btn-sm float-right" style="margin-left: 10px;">
                    <i class="fa fa-times"></i> Batal
                </a>
                <button type="submit" name="button_create" class="btn btn-success btn-sm float-right">
                    <i class="fa fa-save"></i> Simpan
                </button>
            </form>
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