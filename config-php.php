<?php
// config.php - Configuration file
define('TOKEN', '6941115045:AAEfC22K5El2_ggic4X91EVKLPIGEOz-Oto');
define('DB_HOST', 'localhost');
define('DB_NAME', 'coin_clicker');
define('DB_USER', 'your_db_user');
define('DB_PASS', 'your_db_password');

// Function to connect to database
function connectDB() {
    try {
        $db = new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}

// Function to send message to Telegram
function sendTelegramMessage($chat_id, $text, $reply_markup = null) {
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    
    if ($reply_markup) {
        $data['reply_markup'] = json_encode($reply_markup);
    }
    
    $url = "https://api.telegram.org/bot" . TOKEN . "/sendMessage";
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    
    return $result;
}

function answerCallbackQuery($callback_query_id) {
    $url = "https://api.telegram.org/bot" . TOKEN . "/answerCallbackQuery";
    $data = ['callback_query_id' => $callback_query_id];
    
    $options = [
        'http' => [
            'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        ]
    ];
    
    $context = stream_context_create($options);
    return file_get_contents($url, false, $context);
}

function getBotUsername() {
    $url = "https://api.telegram.org/bot" . TOKEN . "/getMe";
    $response = json_decode(file_get_contents($url), true);
    return $response['result']['username'] ?? 'YourBotUsername';
}
?>
