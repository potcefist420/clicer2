<?php
define('token','6941115045:AAEfC22K5El2_ggic4X91EVKLPIGEOz-Oto'); // вместо ''XXXXXXXXXXXXXXXXXXXXXXXXX'' укажите ваш токен
$result = json_decode(file_get_contents('php://input'), true); // передаём в переменную $result полную информацию о сообщении пользователя
if ($result['message']['text'] == '/start') {
    file_get_contents("https://api.telegram.org/bot" . token . "/sendMessage?chat_id=" . $result['message']['chat']['id'] . "&text=" . urlencode('Hi')); // отправляем ответ пользователю, используя его уникальный идентификатор $result['message']['chat']['id'] в качестве получателя
}
