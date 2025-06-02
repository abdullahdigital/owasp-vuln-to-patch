<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers - must match exactly what your frontend sends
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Debug"); // Added X-Debug here
header("Content-Type: application/json");

// Handle OPTIONS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$dbPath = __DIR__ . '/logs.db'; // Changed to simpler path

try {
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);

    // Create table if it doesn't exist
    $db->exec("CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        action TEXT NOT NULL,
        ip_address TEXT,
        user_agent TEXT,
        url TEXT,
        source TEXT,
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON input');
        }

        $action = $input['action'] ?? 'Unknown action';
        $source = $input['source'] ?? 'unknown';

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $url = $_SERVER['HTTP_REFERER'] ?? 'unknown';

        $stmt = $db->prepare("INSERT INTO logs (action, ip_address, user_agent, url, source) VALUES (:action, :ip, :ua, :url, :source)");
        $stmt->bindValue(':action', $action, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':ua', $userAgent, SQLITE3_TEXT);
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);
        $stmt->bindValue(':source', $source, SQLITE3_TEXT);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute SQL statement');
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'XSS attempt logged',
            'logged_action' => $action
        ]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $result = $db->query("SELECT * FROM logs ORDER BY timestamp DESC LIMIT 10");
        $logs = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $logs[] = $row;
        }
        echo json_encode($logs);
        exit;
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => $e->getMessage(),
        'db_path' => $dbPath,
        'db_exists' => file_exists($dbPath) ? 'yes' : 'no'
    ]);
}