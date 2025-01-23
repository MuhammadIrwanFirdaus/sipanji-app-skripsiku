<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        body, html {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
        }

        .container {
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('img/Balai Kota BJB.jpeg');
            background-size: cover;
            background-position: center;
            width: 100%;
        }

        .login-container {
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .login-form input[type="text"],
        .login-form input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-form input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .actions {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .error-message {
            color: red;
        }

        .telegram-button {
            display: flex;
            align-items: center;
            text-decoration: none;
            background-color: #0088cc;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
        }

        .telegram-button img {
            margin-right: 10px;
            width: 20px;
            height: 20px;
        }

        .register-link {
            display: flex;
            align-items: center;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
            background-color: rgba(0, 123, 255, 0.1);
            padding: 10px 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2>Login SIPANJI</h2>
            <form class="login-form" action="process_login.php" method="post">
                <input type="text" name="username" placeholder="Username" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="submit" value="Login">
            </form>
            <div class="actions">
                <a href="tambah-umum.php" class="register-link">Daftar</a>
                <!-- <a href="https://t.me/+Ltzh2q-NaVwwZTM1" class="telegram-button" target="_blank">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/8/82/Telegram_logo.svg" alt="Telegram Logo">
                    Telegram
                </a> -->
            </div>
            <!-- <p>sebelum melakukan pengajuan diharapkan masuk kedalam grup telegram agar bisa dikirim notifikasi</p> -->
            <?php
                // Tampilkan pesan kesalahan jika ada
                if (isset($_GET['error'])) {
                    echo '<p class="error-message">Username atau password salah.</p>';
                }
            ?>
        </div>
    </div>
</body>
</html>
