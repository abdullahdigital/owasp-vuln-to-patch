<?php
// backend/secure-php/public/history.php

ini_set('display_errors', 0);
error_reporting(E_ALL);

// Handle CORS preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(204);
    exit(0);
}

// Set CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

$dbPath = __DIR__ . '/../db/database.db';

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// 🚫 Block insecure deserialization completely
if (isset($_GET['exploit'])) {
    sendResponse([
        'status' => 'error',
        'message' => 'Deserialization of user input is disabled for security reasons.'
    ], 400);
}

try {
    $db = new SQLite3($dbPath);
    if (!$db) throw new Exception("Database connection failed.");

    // 🛡 Parse and validate Authorization token
    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }
    $decoded_token = json_decode(base64_decode($token), true);
    $current_user_id = $decoded_token['id'] ?? null;
    $is_admin = $decoded_token['is_admin'] ?? false;

    if (!$current_user_id) {
        sendResponse(['status' => 'error', 'message' => 'Authentication required.'], 401);
    }

    // 🔒 FIX IDOR: Ensure user can access only their own appointment
    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $appointment_id = (int)$_GET['id'];

        // Restrict by appointment id *and* user_id (except for admin)
        if ($is_admin) {
            $stmt = $db->prepare("SELECT * FROM appointments WHERE id = :id");
        } else {
            $stmt = $db->prepare("SELECT * FROM appointments WHERE id = :id AND user_id = :user_id");
            $stmt->bindValue(':user_id', $current_user_id, SQLITE3_INTEGER);
        }

        if (!$stmt) sendResponse(['status' => 'error', 'message' => 'DB prepare failed.'], 500);

        $stmt->bindValue(':id', $appointment_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $appointment = $result->fetchArray(SQLITE3_ASSOC);

        if ($appointment) {
            sendResponse(['status' => 'success', 'appointment' => $appointment]);
        } else {
            sendResponse(['status' => 'error', 'message' => 'Appointment not found or access denied.'], 403);
        }
    } else {
        // 🧑‍💻 User-specific appointments
        $stmt = $db->prepare("SELECT * FROM appointments WHERE user_id = :user_id");
        if (!$stmt) sendResponse(['status' => 'error', 'message' => 'DB prepare failed.'], 500);

        $stmt->bindValue(':user_id', $current_user_id, SQLITE3_INTEGER);
        $result = $stmt->execute();

        $appointments = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $appointments[] = $row;
        }

        sendResponse(['status' => 'success', 'appointments' => $appointments]);
    }

} catch (Exception $e) {
    sendResponse(['status' => 'error', 'message' => $e->getMessage()], 500);
} finally {
    if (isset($db)) $db->close();
}
?>
