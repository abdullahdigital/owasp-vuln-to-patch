<?php
// backend/insecure-php/public/history.php

// Security Misconfiguration - Disable display errors in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173"); // Specify your Svelte dev server origin
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle preflight requests for CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database path
$dbPath = __DIR__ . '/../db/database.db';

/**
 * Sends a JSON response and terminates the script.
 * @param array $data The data to be encoded as JSON.
 * @param int $statusCode The HTTP status code to send with the response.
 */
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    $db = new SQLite3($dbPath);
    if (!$db) {
        throw new Exception("Could not open database: " . $db->lastErrorMsg());
    }

    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($token, 'Bearer ') === 0) {
        $token = substr($token, 7);
    }

    $decoded_token = json_decode(base64_decode($token), true);
    $current_user_id = $decoded_token['id'] ?? null;
    $is_admin = $decoded_token['is_admin'] ?? false;

    if (!$current_user_id) {
        sendResponse([
            'status' => 'error',
            'message' => 'Authentication required.'
        ], 401);
    }

    if (isset($_GET['id'])) {
        $requested_appointment_id = (int)$_GET['id'];

        // --- IDOR VULNERABILITY HERE ---
        // This query is intentionally vulnerable. It fetches an appointment by its ID
        // WITHOUT checking if that appointment belongs to the $current_user_id.
        // This allows an authenticated user to access any appointment by ID,
        // even if they are not the owner.
        $sql = "SELECT * FROM appointments WHERE id = :id";
        $query = $db->prepare($sql);

        if ($query === false) {
            sendResponse([
                'status' => 'error',
                'message' => 'Failed to prepare SQL statement for single appointment. Check SQL syntax or table/column existence.',
                'sql_error' => $db->lastErrorMsg(),
                'sql_query' => $sql
            ], 500);
        }

        $query->bindValue(':id', $requested_appointment_id, SQLITE3_INTEGER);
        $result = $query->execute();

        $appointment = $result->fetchArray(SQLITE3_ASSOC);

        if ($appointment) {
            sendResponse([
                'status' => 'success',
                'appointment' => $appointment
            ]);
        } else {
            sendResponse([
                'status' => 'error',
                'message' => 'Appointment not found.'
            ], 404);
        }

    } else {
        // Fetch all appointments for the current user (this part is secure)
        $sql = "SELECT * FROM appointments WHERE user_id = :user_id";
        $query = $db->prepare($sql);

        if ($query === false) {
            sendResponse([
                'status' => 'error',
                'message' => 'Failed to prepare SQL statement for all appointments. Check SQL syntax or table/column existence.',
                'sql_error' => $db->lastErrorMsg(),
                'sql_query' => $sql
            ], 500);
        }

        $query->bindValue(':user_id', $current_user_id, SQLITE3_INTEGER);
        $result = $query->execute();

        $appointments = [];
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $appointments[] = $row;
        }

        sendResponse([
            'status' => 'success',
            'appointments' => $appointments
        ]);
    }

} catch (Exception $e) {
    sendResponse([
        "status" => "error",
        "message" => $e->getMessage()
    ], 500);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>