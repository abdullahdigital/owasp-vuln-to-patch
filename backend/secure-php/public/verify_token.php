<?php
// backend/insecure-php/public/verify_token.php

ini_set('display_errors', 0);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    // Decode the token
    $decoded = json_decode(base64_decode($token), true);
    
    if (!$decoded) {
        sendResponse(['is_admin' => false, 'error' => 'Invalid token format'], 401);
    }

    // Add additional verification checks here:
    // 1. Check token expiration
    $max_token_age = 3600; // 1 hour
    if (time() - ($decoded['issued'] ?? 0) > $max_token_age) {
        sendResponse(['is_admin' => false, 'error' => 'Token expired'], 401);
    }

    // 2. Verify the user exists and is admin
    $dbPath = __DIR__ . '/../db/database.db';
    $db = new SQLite3($dbPath);
    
    $stmt = $db->prepare("SELECT is_admin FROM users WHERE id = :id AND email = :email");
    $stmt->bindValue(':id', $decoded['id'] ?? 0, SQLITE3_INTEGER);
    $stmt->bindValue(':email', $decoded['email'] ?? '', SQLITE3_TEXT);
    $result = $stmt->execute();
    $user = $result->fetchArray(SQLITE3_ASSOC);
    
    if (!$user) {
        sendResponse(['is_admin' => false, 'error' => 'User not found'], 401);
    }

    sendResponse(['is_admin' => (bool)$user['is_admin']]);

} catch (Exception $e) {
    sendResponse(['is_admin' => false, 'error' => $e->getMessage()], 500);
}
?>