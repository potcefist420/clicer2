<?php
header('Content-Type: application/json');
require_once 'config.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['referrerId']) || !isset($data['reward'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit;
}

$referrerId = $data['referrerId'];
$reward = $data['reward'];

try {
    $db = connectDB();
    
    // Update referrer's coins
    $stmt = $db->prepare("UPDATE users SET coins = coins + ? WHERE user_id = ?");
    $stmt->execute([$reward, $referrerId]);
    
    // Notify referrer
    sendTelegramMessage($referrerId, "🎉 Good news! Someone joined using your referral link. You've earned $reward coins!");
    
    echo json_encode(['status' => 'success', 'message' => 'Referrer rewarded']);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
