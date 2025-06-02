<?php
// backend/secure-php/public/logs.php

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging: Log that this specific file is being executed
error_log(">>> SECURE logs.php EXECUTED: " . __FILE__);

// CORS headers - allow your frontend origin
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, X-Debug, Origin, X-Requested-With, Accept");
header("Content-Type: application/json");

// Handle OPTIONS preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Path to the logs database (shared with login.php)
$dbPath = __DIR__ . '/logs.db'; // This is the corrected path to read logs from outside public/

try {
    // Debugging: Log database access
    error_log("logs.php attempting to access DB at: " . $dbPath);
    error_log("Does logs.php DB path exist? " . (file_exists($dbPath) ? 'Yes' : 'No'));

    $db = new SQLite3($dbPath);
    $db->enableExceptions(true); // Enable exceptions for better error handling

    // Create the logs table if it does not exist (must match login.php's schema)
    $db->exec("CREATE TABLE IF NOT EXISTS logs (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        action TEXT NOT NULL,
        ip_address TEXT,
        user_agent TEXT,
        url TEXT,
        source TEXT,
        email TEXT,                -- Added for login logs
        additional_data TEXT,      -- Added for extra context
        timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Invalid JSON input received by logs.php: " . file_get_contents('php://input'));
            throw new Exception('Invalid JSON input for logs.php');
        }

        // Validate required fields for a log entry
        if (!isset($input['action']) || !isset($input['source'])) {
            throw new Exception('Missing required log parameters (action or source).');
        }

        $action = $input['action'];
        $source = $input['source'];
        $email = $input['email'] ?? null; // Optional email
        $additionalData = $input['additional_data'] ?? null; // Optional additional data

        $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $url = $_SERVER['REQUEST_URI'] ?? 'unknown';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        
        $stmt = $db->prepare("INSERT INTO logs (action, ip_address, user_agent, url, source, email, additional_data) 
                             VALUES (:action, :ip, :ua, :url, :source, :email, :additional_data)");
        $stmt->bindValue(':action', $action, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $ip, SQLITE3_TEXT);
        $stmt->bindValue(':ua', $userAgent, SQLITE3_TEXT);
        $stmt->bindValue(':url', $url, SQLITE3_TEXT);
        $stmt->bindValue(':source', $source, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':additional_data', $additionalData ? json_encode($additionalData) : null, SQLITE3_TEXT);

        if (!$stmt->execute()) {
            throw new Exception('Failed to execute SQL statement in logs.php: ' . $db->lastErrorMsg());
        }

        error_log("logs.php: Log saved successfully for action: " . $action);
        echo json_encode([
            'status' => 'success',
            'message' => 'Log saved successfully',
            'logged_action' => $action
        ]);
        exit;
    }

    // This is the key change: Check for the 'fetch_logs' GET parameter
    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['fetch_logs']) && $_GET['fetch_logs'] === 'true') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100; // Increased default limit for "all"
        $limit = max(1, min(500, $limit)); // Ensure limit is within a reasonable range, max 500 for "all"

        $stmt = $db->prepare("SELECT * FROM logs ORDER BY timestamp DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $logs = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Decode additional_data if it's stored as JSON string
            if (isset($row['additional_data']) && $row['additional_data'] !== null) {
                $decodedData = json_decode($row['additional_data'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row['additional_data'] = $decodedData;
                }
            }
            $logs[] = $row;
        }
        echo json_encode($logs);
        exit;
    }

    // Default GET request without specific parameter will now also fetch logs
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100; // Set a reasonable default if no param
        $limit = max(1, min(500, $limit)); 

        $stmt = $db->prepare("SELECT * FROM logs ORDER BY timestamp DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        $result = $stmt->execute();
        
        $logs = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            if (isset($row['additional_data']) && $row['additional_data'] !== null) {
                $decodedData = json_decode($row['additional_data'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $row['additional_data'] = $decodedData;
                }
            }
            $logs[] = $row;
        }
        echo json_encode($logs);
        exit;
    }


    // Method not allowed for other request methods
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);

} catch (Exception $e) {
    error_log("!!! logs.php Server error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'db_path' => $dbPath,
        'db_exists' => file_exists($dbPath) ? 'yes' : 'no',
        'db_dir_writable' => is_writable(dirname($dbPath)) ? 'yes' : 'no'
    ]);
} finally {
    if (isset($db) && $db instanceof SQLite3) {
        $db->close();
    }
}
?>