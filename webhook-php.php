<?php
require_once 'config.php';

// Get incoming data from Telegram
$update = json_decode(file_get_contents('php://input'), true);

// Log update for debugging
file_put_contents('telegram_log.txt', print_r($update, true), FILE_APPEND);

// Handle /start command
if (isset($update['message']['text']) && $update['message']['text'] === '/start') {
    $chat_id = $update['message']['chat']['id'];
    $username = $update['message']['from']['username'] ?? 'User' . $update['message']['from']['id'];
    $first_name = $update['message']['from']['first_name'] ?? '';
    $last_name = $update['message']['from']['last_name'] ?? '';
    
    // Check if user has a referral
    if (isset($update['message']['text']) && strpos($update['message']['text'], '/start ') === 0) {
        $referrer_id = substr($update['message']['text'], 7);
        handleReferral($chat_id, $referrer_id);
    }
    
    // Register user if new
    registerUser($chat_id, $username, $first_name, $last_name);
    
    // Send welcome message with game link
    $welcome_message = "Welcome to Coin Clicker! 💰\n\nClick the coins to earn money and compete with friends!\n\nUse the referral system to earn bonus coins!";
    $keyboard = [
        'inline_keyboard' => [
            [
                ['text' => '🎮 Play Game', 'web_app' => ['url' => 'https://your-server.com/coin-clicker.html']]
            ],
            [
                ['text' => '👥 Invite Friends', 'callback_data' => 'invite']
            ],
            [
                ['text' => '🏆 Leaderboard', 'callback_data' => 'leaderboard']
            ]
        ]
    ];
    
    sendTelegramMessage($chat_id, $welcome_message, $keyboard);
}

// Handle callback queries (button clicks)
if (isset($update['callback_query'])) {
    $callback_data = $update['callback_query']['data'];
    $chat_id = $update['callback_query']['from']['id'];
    
    if ($callback_data === 'invite') {
        $referral_link = "https://t.me/" . getBotUsername() . "?start=$chat_id";
        $invite_message = "Share this link with your friends. You'll get 100 coins for each friend who joins!\n\n$referral_link";
        sendTelegramMessage($chat_id, $invite_message);
    }
    
    if ($callback_data === 'leaderboard') {
        $leaderboard = getLeaderboard();
        $leaderboard_message = "🏆 TOP PLAYERS 🏆\n\n";
        
        foreach ($leaderboard as $index => $player) {
            $rank = $index + 1;
            $leaderboard_message .= "$rank. {$player['username']}: {$player['coins']} coins\n";
        }
        
        sendTelegramMessage($chat_id, $leaderboard_message);
    }
    
    // Acknowledge the callback query
    answerCallbackQuery($update['callback_query']['id']);
}

// Handle inline data from web app
if (isset($update['message']['web_app_data'])) {
    $data = json_decode($update['message']['web_app_data']['data'], true);
    $chat_id = $update['message']['chat']['id'];
    
    if (isset($data['action']) && $data['action'] === 'click') {
        updateUserScore($chat_id, $data['count']);
    }
}

function handleReferral($user_id, $referrer_id) {
    // Give bonus to new user
    $db = connectDB();
    $stmt = $db->prepare("UPDATE users SET coins = coins + 50 WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    // Give reward to referrer
    $stmt = $db->prepare("UPDATE users SET coins = coins + 100 WHERE user_id = ?");
    $stmt->execute([$referrer_id]);
    
    // Notify referrer
    sendTelegramMessage($referrer_id, "🎉 Good news! Someone joined using your referral link. You've earned 100 coins!");
}

function registerUser($user_id, $username, $first_name, $last_name) {
    $db = connectDB();
    
    // Check if user already exists
    $stmt = $db->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$user_id]);
    
    if ($stmt->rowCount() === 0) {
        // New user, insert into database
        $stmt = $db->prepare("INSERT INTO users (user_id, username, first_name, last_name, coins) VALUES (?, ?, ?, ?, 0)");
        $stmt->execute([$user_id, $username, $first_name, $last_name]);
    }
}

function updateUserScore($user_id, $coins) {
    $db = connectDB();
    $stmt = $db->prepare("UPDATE users SET coins = ? WHERE user_id = ?");
    $stmt->execute([$coins, $user_id]);
}

function getLeaderboard($limit = 10) {
    $db = connectDB();
    $stmt = $db->prepare("SELECT username, coins FROM users ORDER BY coins DESC LIMIT ?");
    $stmt->execute([$limit]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
