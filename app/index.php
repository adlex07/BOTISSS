<?php
// Anti-Bot API Index - Confirms setup and lists available checks from /bot/
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');  // Allow CORS for cross-origin fetches from your main app
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight OPTIONS request for CORS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Scan for anti-bot scripts in the /bot/ subfolder
    $botDir = __DIR__ . '/bot/';
    $scripts = [];
    if (is_dir($botDir)) {
        foreach (scandir($botDir) as $file) {
            if (preg_match('/^anti\d+\.php$/', $file)) {  // Matches anti1.php, anti2.php, etc.
                $scripts[] = 'bot/' . $file;  // Include subfolder path for full URLs
            }
        }
        sort($scripts);  // Alphabetical order
    } else {
        throw new Exception('bot/ folder not found');
    }

    $response = [
        'status' => 'success',
        'message' => 'Anti-bot PHP ready on Wasmer! (Scanning /bot/)',
        'php_version' => phpversion(),
        'available_scripts' => $scripts,  // e.g., ['bot/anti1.php', 'bot/anti2.php', ..., 'bot/anti8.php']
        'total_checks' => count($scripts),
        'example_usage' => 'GET /bot/anti1.php?ip=YOUR_IP&ua=YOUR_UA to run a check'
    ];

    // Optional: Run a quick demo check if no params (uncomment if you want)
    // if (empty($_GET)) {
    //     $demoUrl = 'bot/anti1.php?ip=demo&ua=TestBot';
    //     $demoResponse = file_get_contents('http://localhost/' . $demoUrl);  // Note: Use full Wasmer URL in prod
    //     $response['demo_check'] = json_decode($demoResponse, true) ?? 'Demo unavailable';
    // }

    echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Setup issue: ' . $e->getMessage()
    ]);
}
?>
