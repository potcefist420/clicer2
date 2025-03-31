<?php
header('Content-Type: application/json');

// Загрузка данных из localStorage или базы данных
$playerScores = json_decode(file_get_contents('playerScores.json'), true);

// Отправка данных в формате JSON
echo json_encode($playerScores);
?>
