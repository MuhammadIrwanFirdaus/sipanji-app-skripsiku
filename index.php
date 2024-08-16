<?php
session_start();

// Jika pengguna belum login, arahkan mereka ke halaman login
if (!isset($_SESSION['username'])) {
    header("Location: pages/login/view.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include "partials/head.php"?>
<?php include "database/database.php"?>


            <body class= "hold-transition sidebar-mini">
                <class="wrapper">
                <?php include "partials/nav.php"?>
                <?php include "partials/sidebar.php"?>
                <div class="content-wrapper">
                <?php include "routes.php"?>
                </div>
                <?php include "partials/control.php"?>
                <?php include "partials/footer.php"?>
                </div>
            </body>

</html>
