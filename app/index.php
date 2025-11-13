<?php
// Auto Anti-Bot Gateway - Runs all checks on /, blocks bots, allows humans
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');  // CORS for your main app fetches
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$isBot = false;

// Chain all anti-bot checks (requires each script to set $isBot = true if detected)
require __DIR__ . '/bot/anti1.php';
require __DIR__ . '/bot/anti2.php';
require __DIR__ . '/bot/anti3.php';
require __DIR__ . '/bot/anti4.php';
require __DIR__ . '/bot/anti5.php';
require __DIR__ . '/bot/anti6.php';
require __DIR__ . '/bot/anti7.php';
require __DIR__ . '/bot/anti8.php';

if ($isBot) {
    http_response_code(403);
    echo json_encode([
        'status' => 'blocked',
        'message' => 'Bot detected - Access denied.'
    ]);
    exit;
}

// If clean, output success (your main app can parse/use this)
echo json_encode([
    'status' => 'allowed',
    'message' => 'Human verified - Proceed.',
    'visitor_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
    'checks_passed' => 8  // All passed
]);
?>
