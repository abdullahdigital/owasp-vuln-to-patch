<?php
// backend/secure-php/public/attempts.php

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// Handle OPTIONS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Path to your main database (not logs.db)
$dbPath = __DIR__ . '/../db/database.db';

try {
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);

    // Verify login_attempts table exists
    $tableCheck = $db->querySingle("SELECT name FROM sqlite_master WHERE type='table' AND name='login_attempts'");
    if (!$tableCheck) {
        throw new Exception("login_attempts table does not exist");
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        // Get all login attempts with optional filters
        $emailFilter = isset($_GET['email']) ? $_GET['email'] : null;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 100;
        $limit = max(1, min(500, $limit));

        if ($emailFilter) {
            $stmt = $db->prepare("SELECT * FROM login_attempts WHERE email = :email ORDER BY last_failed_at DESC LIMIT :limit");
            $stmt->bindValue(':email', $emailFilter, SQLITE3_TEXT);
        } else {
            $stmt = $db->prepare("SELECT * FROM login_attempts ORDER BY last_failed_at DESC LIMIT :limit");
        }
        $stmt->bindValue(':limit', $limit, SQLITE3_INTEGER);
        
        $result = $stmt->execute();
        $attempts = [];
        
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            // Convert timestamp to readable format
            $row['last_failed_at_readable'] = date('Y-m-d H:i:s', $row['last_failed_at']);
            $attempts[] = $row;
        }
        
        echo json_encode([
            'status' => 'success',
            'data' => $attempts,
            'count' => count($attempts)
        ]);
        exit;
    }

    // Method not allowed
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'db_path' => $dbPath,
        'db_exists' => file_exists($dbPath) ? 'yes' : 'no'
    ]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>