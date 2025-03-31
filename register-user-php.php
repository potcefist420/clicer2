<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['userId'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$userId = $data['userId'];
$username = $data['username'] ?? 'User' . $userId;
$firstName = $data['firstName'] ?? '';
$lastName = $data['lastName'] ?? '';
$coins = $data['coins'] ?? 0;

try {
    $db = connectDB();
    
    // Check if user exists
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);
    
    if ($stmt->rowCount() === 0) {
        // New user
        $stmt = $db->prepare("INSERT INTO users (user_id, username, first_name, last_name, coins) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $username, $firstName, $lastName, $coins]);
        echo json_encode(['status' => 'success', 'message' => 'User registered']);
    } else {
        // Existing user, update info
        $stmt = $db->prepare("UPDATE users SET username = ?, first_name = ?, last_name = ? WHERE user_id = ?");
        $stmt->execute([$username, $firstName, $lastName, $userId]);
        echo json_encode(['status' => 'success', 'message' => 'User updated']);
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
