<?php
if (isset($_GET['id_stok'])) {
    $id_stok = $_GET['id_stok'];

    $database = new Database();
    $db = $database->getConnection();

    $deleteSql = "DELETE FROM stok_alat WHERE id_stok = ?";
    $stmt = $db->prepare($deleteSql);
    $stmt->bindParam(1, $_GET['id_stok']);
    if ($stmt->execute()){
        $_SESSION['hasil'] = true;
        $_SESSION['pesan'] = "Berhasil hapus data";
    }else{
        $_SESSION['hasil'] = false;
        $_SESSION['pesan'] = "Gagal ubah data";
        
    }
}
echo"<meta http-equiv='refresh' content='0;url=?page=tampil-stok-alat'>";