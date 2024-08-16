<?php include "partials/scripts.php" ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Penjadwalan Acara</title>
    <!-- Panggil sumber daya FullCalendar -->
    <link href="assets/fullcalendar.css" rel="stylesheet">
    <script src="assets/jquery.min.js"></script>
    <script src="assets/moment.min.js"></script>
    <script src="assets/fullcalendar.min.js"></script>

    <style>
    .btn-success.checkmark::after {
    content: "\2714"; /* Unicode untuk tanda ceklis */
    color: white;
    margin-left: 5px;
    font-size: 14px;
}
    </style>
</head>
<body>


    <div id="calendar"></div>
        <!-- Konten Utama -->
        <div id="content">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Jadwal</h3>
                    </div>  
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0" style="height: 300px;">
                        <table class="table table-head-fixed text-nowrap" id="jadwalTable">
                        <thead>
                            <tr>
                            <th>Lokasi</th>
                            <th>tanggal</th>
                            <th>Aksi</th> <!-- Kolom untuk tombol Edit -->
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $database = new Database();
                            $db = $database->getConnection();

                            function indoDate($datetime) {
                                $indoDays = array('Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu');
                                $indoMonths = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                                
                                $day = date('w', strtotime($datetime));
                                $month = date('n', strtotime($datetime));
                                $date = date('j', strtotime($datetime)); // Mendapatkan angka tanggal
                            
                                return $indoDays[$day] . ', ' . $date . ' ' . $indoMonths[$month - 1] . date(' Y - H:i', strtotime($datetime));
                            }
                            

                            $selectSql = "SELECT * FROM events";
                            $stmt = $db->prepare($selectSql);
                            $stmt->execute();
                            $no = 1;
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo "<tr>";
                                echo "<td>" . $row['title'] . "</td>";
                                echo "<td>" . indoDate($row['tanggal']) . "</td>";
                                echo "<td>";
                                echo "<a class='btn btn-info mr-1' href='?page=detailJadwal&id=" . $row['id'] . "'>Detail</a>";
                                echo "<a class='btn btn-secondary mr-1' href='?page=surat-tugas&id=" . $row['id'] . "'>Surat Tugas</a>";
                                echo "<a class='btn btn-warning mr-1' href='?page=editJadwal&id=" . $row['id'] . "'>Edit</a>";
                                echo "<a class='btn btn-danger mr-1' href='hapus.php?id=" . $row['id'] . "'>Hapus</a>";
                                echo "<a class='btn btn-success mr-1 btn-selesai' id='selesai_" . $row['id'] . "' data-no-pengajuan='" . $row['no_pengajuan'] . "' data-tempat='" . $row['title'] . "' href='#'>Selesai</a>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            ?>

            </tbody>
                        </table>
                        <!-- <a href="?page=tambahJadwal" class="btn btn-primary" style="margin:20px">Tambah</a> -->
                        <!-- <a href="?page=cetak-pdf-jadwal" class="btn btn-secondary" style="margin:20px">Cetak PDF</a> -->
                    </div>
                    <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
                </div>

        </div>
        </div>


    <script>
        $(document).ready(function() {
                                // Tombol "Selesai" click event handler
                                $('body').on('click', 'a.btn-success', function(e) {
                                e.preventDefault(); // Menghentikan perilaku default dari tombol
                                var button = $(this);
                                var buttonId = button.attr('id'); // Mendapatkan id tombol
                                var noPengajuan = button.data('no-pengajuan'); // Mendapatkan no_pengajuan
                                var tempat = button.data('tempat'); // Mendapatkan tempat

                                // Mengarahkan ke halaman tambah pengerjaan dengan menyertakan ID jadwal, no_pengajuan, dan tempat
                                window.location.href = '?page=upload-pengerjaan&id=' + buttonId + '&no_pengajuan=' + noPengajuan + '&title=' + tempat;

                                // Ubah teks dan gaya tombol menjadi "Selesai" dengan ikon ceklis
                                button.removeClass('btn-success');
                                button.addClass('btn-secondary checkmark'); // Tambahkan kelas untuk menampilkan ikon
                                button.text('Selesai');
                                button.append('<span>&#10004;</span>'); // Tambahkan ikon ceklis

                                // Simpan status tombol di localStorage dengan kunci berdasarkan ID tombol "Selesai"
                                localStorage.setItem('buttonStatus_' + buttonId, 'Selesai');
                            });

                                    // Cek status tombol saat halaman dimuat ulang
                                    $('a.btn-success').each(function() {
                                        var button = $(this);
                                        var buttonStatus = localStorage.getItem('buttonStatus_' + button.attr('id'));

                                        if (buttonStatus === 'Selesai') {
                                            button.removeClass('btn-success');
                                            button.addClass('btn-secondary checkmark');
                                            button.text('Selesai');
                                            button.append('<span>&#10004;</span>');
                                        }
                                    });

            var calendar = $('#calendar').fullCalendar({
                // Konfigurasi FullCalendar di sini
                // ...
                        //izinkan tabel bisa di edit
                        editable: true,
                        //ATUR HEADER KALENDER
                        header: {
                            left : 'prev, next today',
                            center : 'title',
                            right : 'month, agendaWeek, agendaDay'
                        },
                        //tampilkan data dari database
                        events : 'tampil.php',
                        //izinkan tabel/kalender bisa di pilih /edit
                        selectable :true,
                        selectHelper :true,
                        
                        eventDrop: function(event, delta, revertFunc) {
                            // Tangani event drop di sini
                            var eventId = event.id;
                            var newStartDate = event.start.format(); // Tanggal baru dalam format ISO8601

                            // Kirim data ke server untuk diperbarui (anda perlu menambahkan kode AJAX di sini)
                            // Contoh pengiriman data dengan AJAX (perlu disesuaikan):
                            $.ajax({
                                url: 'ubahGeser.php',
                                type: 'POST',
                                data: {
                                    id: eventId,
                                    newStartDate: newStartDate
                                },
                                success: function(response) {
                                    alert("Perubahan Berhasil");
                                    location.reload();
                                    console.log(response); // Tampilkan pesan sukses atau periksa respon dari server
                                },
                                error: function(xhr, status, error) {
                                    revertFunc(); // Kembalikan event jika ada kesalahan
                                    console.error(error);
                                }
                    });
                }
            });
        });
    </script>
</body>
</html>