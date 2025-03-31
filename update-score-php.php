<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['userId']) || !isset($data['coins'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$userId = $data['userId'];
$coins = $data['coins'];

try {
    $db = connectDB();
    $stmt = $db->prepare("UPDATE users SET coins = ? WHERE user_id = ?");
    $stmt->execute([$coins, $userId]);
    echo json_encode(['status' => 'success', 'message' => 'Score updated']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
