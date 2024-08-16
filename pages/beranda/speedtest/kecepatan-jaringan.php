<?php


function saveNetworkSpeed($db, $tanggal, $download, $upload) {
    $query = "INSERT INTO kecepatan_jaringan (tanggal, download, upload) VALUES (:tanggal, :download, :upload)";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':tanggal', $tanggal);
    $stmt->bindParam(':download', $download);
    $stmt->bindParam(':upload', $upload);
    $stmt->execute();
}

function getNetworkSpeedData($db) {
    $query = "SELECT tanggal, download, upload FROM kecepatan_jaringan ORDER BY tanggal ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $download = $_POST['download'];
    $upload = $_POST['upload'];
    $tanggal = date('Y-m-d');
    saveNetworkSpeed($db, $tanggal, $download, $upload);
}

$networkData = getNetworkSpeedData($db);
$dates = [];
$downloadSpeeds = [];
$uploadSpeeds = [];
foreach ($networkData as $data) {
    $dates[] = $data['tanggal'];
    $downloadSpeeds[] = $data['download'];
    $uploadSpeeds[] = $data['upload'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Kecepatan Jaringan</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container">
        <h3 class="mt-5">Monitoring Kecepatan Jaringan</h3>
        <div class="card mt-3">
            <div class="card-body">
                <h5 class="card-title">Hasil Pengukuran Kecepatan Jaringan</h5>
                <canvas id="speedChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('speedChart').getContext('2d');
            const speedChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: <?php echo json_encode($dates); ?>,
                    datasets: [
                        {
                            label: 'Download Speed (Mbps)',
                            data: <?php echo json_encode($downloadSpeeds); ?>,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.2)',
                            fill: false,
                        },
                        {
                            label: 'Upload Speed (Mbps)',
                            data: <?php echo json_encode($uploadSpeeds); ?>,
                            borderColor: 'rgba(153, 102, 255, 1)',
                            backgroundColor: 'rgba(153, 102, 255, 0.2)',
                            fill: false,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            display: true,
                            title: {
                                display: true,
                                text: 'Speed (Mbps)'
                            }
                        }
                    }
                }
            });

            // Test speed and save to database
            testDownloadSpeed();
            testUploadSpeed();
        });

        function sendSpeedToServer(downloadSpeed, uploadSpeed) {
            const xhr = new XMLHttpRequest();
            xhr.open('POST', window.location.href, true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.send(`download=${downloadSpeed}&upload=${uploadSpeed}`);
        }

        function testDownloadSpeed() {
            const image = new Image();
            const startTime = new Date().getTime();
            const imageSize = 1000000; // Ukuran gambar dalam byte (misalnya 1MB)

            image.onload = function () {
                const endTime = new Date().getTime();
                const duration = (endTime - startTime) / 1000;
                const bitsLoaded = imageSize * 8;
                const speedBps = bitsLoaded / duration;
                const speedMbps = (speedBps / (1024 * 1024)).toFixed(2);
                sendSpeedToServer(speedMbps, null);
            };

            image.onerror = function () {
                console.log('Error measuring download speed');
            };

            image.src = "https://www.google.com/images/branding/googlelogo/1x/googlelogo_color_272x92dp.png?_=" + startTime;
        }

        function testUploadSpeed() {
            const xhr = new XMLHttpRequest();
            const startTime = new Date().getTime();
            const data = new ArrayBuffer(1000000); // 1MB data

            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4) {
                    const endTime = new Date().getTime();
                    const duration = (endTime - startTime) / 1000;
                    const bitsLoaded = data.byteLength * 8;
                    const speedBps = bitsLoaded / duration;
                    const speedMbps = (speedBps / (1024 * 1024)).toFixed(2);
                    sendSpeedToServer(null, speedMbps);
                }
            };

            xhr.open('POST', 'https://httpbin.org/post', true);
            xhr.send(data);
        }
    </script>
</body>
</html>
