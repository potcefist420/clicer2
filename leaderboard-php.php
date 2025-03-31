<?php
header('Content-Type: application/json');
require_once 'config.php';

$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10;

try {
    $db = connectDB();
    $stmt = $db->prepare("SELECT username, coins FROM users ORDER BY coins DESC LIMIT ?");
    $stmt->execute([$limit]);
    $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($leaderboard);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
