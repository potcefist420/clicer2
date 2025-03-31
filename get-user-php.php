<?php
header('Content-Type: application/json');
require_once 'config.php';

// Validate required parameters
if (!isset($_GET['userId'])) {
    echo json_encode(['error' => 'Missing user ID']);
    exit;
}

$userId = $_GET['userId'];

try {
    $db = connectDB();
    $stmt = $db->prepare("SELECT user_id, username, coins FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($user);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
