<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Serah Terima</title>
    <link rel="stylesheet" href="path/to/bootstrap.min.css"> <!-- Adjust the path as needed -->
</head>
<body>
    <section class="content">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Serah Terima</h3>
            </div>
            <div class="card-body">
                <form method="POST" action="?page=cetak-serah-terima">
                    <div class="form-group">
                        <label for="instansi">Instansi</label>
                        <input type="text" class="form-control" name="instansi" required>
                    </div>
                    <div class="form-group">
                        <label for="alamat">Alamat</label>
                        <input type="text" class="form-control" name="alamat" required>
                    </div>
                    <div class="form-group">
                        <label for="nama_barang">Nama Barang</label>
                        <input type="text" class="form-control" name="nama_barang" required>
                    </div>
                    <div class="form-group">
                        <label for="jumlah">Jumlah</label>
                        <input type="number" class="form-control" name="jumlah" required>
                    </div>
                    <div class="form-group">
                        <label for="kondisi">Kondisi</label>
                        <input type="text" class="form-control" name="kondisi" required>
                    </div>
                    <div class="form-group">
                        <label for="penerima">Penerima</label>
                        <input type="text" class="form-control" name="penerima" required>
                    </div>
                    <div class="form-group">
                        <label for="nip_penerima">NIP Penerima</label>
                        <input type="text" class="form-control" name="nip_penerima" required>
                    </div>
                    <button type="submit" class="btn btn-success btn-sm float-right">
                        <i class="fa fa-save"></i> Buat PDF
                    </button>
                </form>
            </div>
        </div>
    </section>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
