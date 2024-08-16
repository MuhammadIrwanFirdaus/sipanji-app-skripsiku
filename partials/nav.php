<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
    </ul>
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="fas fa-th-large"></i>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-item dropdown-header">Pengaturan pengguna</span>
                <?php if (isset($_SESSION['peran']) && $_SESSION['peran'] === 'admin') { ?>
                    <a href="?page=tampil-admin" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Pengguna
                    </a>
                    <a href="?page=kelola-pengguna" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Kelola Pengguna
                    </a>
                    <a href="?page=statistik-pengguna" class="dropdown-item">
                        <i class="fas fa-user mr-2"></i> Statistik Pengguna
                    </a>
                <?php } ?>
                <a href="pages/login/view.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            </div>
        </li>
    </ul>
</nav>
