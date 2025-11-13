<?php
// Auto Anti-Bot Gateway - Runs all checks on /, blocks bots, allows humans (with API key auth)
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');  // CORS for your main app fetches
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Simple API Key Auth (replace 'your_secret_key_here' with a strong random string)
$validApiKey = 'a1b2c3d4e5f67890abcdef1234567890';  // Generate: openssl rand -hex 32 (keep secret!)
$providedKey = $_GET['api_key'] ?? '';  // From query param ?api_key=...

if ($providedKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode([
        'status' => 'unauthorized',
        'message' => 'Invalid or missing API key. Access denied.'
    ]);
    exit;
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
