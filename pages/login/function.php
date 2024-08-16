<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

function is_logged_in() {
    return isset($_SESSION['id']);
}

function is_role($role) {
    return isset($_SESSION['peran']) && $_SESSION['peran'] === $role;
}

function check_access($role) {
    if (!is_logged_in() || $_SESSION['peran'] !== $role) {
        header('Location: pages/login/view.php');
        exit();
    }
}


function sendTelegramMessageWithPhoto($message, $photoPath) {
    $telegramToken = '7279198446:AAFw3YGaAZGvBaor3pjlnuNaOvIvgSni0_4';
    $telegramChatId = '-4241210045';

    // Kirim pesan teks
    $textUrl = "https://api.telegram.org/bot$telegramToken/sendMessage";
    $textData = [
        'chat_id' => $telegramChatId,
        'text'    => $message
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $textUrl);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $textData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $textResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    // Kirim foto
    $photoUrl = "https://api.telegram.org/bot$telegramToken/sendPhoto";
    $photoData = [
        'chat_id' => $telegramChatId,
        'photo'   => new CURLFile(realpath($photoPath))
    ];

    curl_setopt($ch, CURLOPT_URL, $photoUrl);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $photoData);

    $photoResponse = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }

    curl_close($ch);

    return [$textResponse, $photoResponse];
}

function sendTelegramMessage($message) {
    $telegramToken = '7279198446:AAFw3YGaAZGvBaor3pjlnuNaOvIvgSni0_4';
    $telegramChatId = '-4241210045'; // Ganti dengan chat ID grup Anda

    $url = "https://api.telegram.org/bot$telegramToken/sendMessage";
    $data = [
        'chat_id' => $telegramChatId,
        'text' => $message
    ];

    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data),
        ],
    ];
    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        echo "Error sending message.";
    }
}


?>
