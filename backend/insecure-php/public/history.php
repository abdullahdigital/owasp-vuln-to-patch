<?php
// backend/insecure-php/public/history.php

ini_set('display_errors', 0);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: http://localhost:5173");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    http_response_code(204);
    exit(0);
}

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

class DentalExploit {
    public $message = "Safe message";

    public function __destruct() {
        file_put_contents(
            __DIR__ . '/hacked.txt',
            "💥 Insecure Deserialization Exploited!\n" .
            "🗑 Delete files -> unlink('config.php')\n" .
            "🕵 Exfiltrate data -> file_get_contents('/etc/passwd')\n" .
            "💉 Plant backdoors -> file_put_contents('shell.php', '<?php system(\$_GET[\"cmd\"]); ?>')\n" .
            "🧬 Run system commands -> system('rm -rf /')\n" .
            "👤 Hijack sessions or escalate privileges\n",
            FILE_APPEND
        );
    }
}

// 🧨 Insecure Deserialization Vulnerability
if (isset($_GET['exploit'])) {
    $data = base64_decode($_GET['exploit']);
    $obj = unserialize($data); // This is the vulnerable line
    
    // For demo purposes - show what was deserialized
    echo json_encode([
        'status' => 'success',
        'message' => 'Object deserialized',
        'object' => print_r($obj, true)
    ]);
    exit;
}

try {
    $db = new SQLite3($dbPath);
    if (!$db) throw new Exception("Database connection failed.");

    $token = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
    if (strpos($token, 'Bearer ') === 0) $token = substr($token, 7);
    $decoded_token = json_decode(base64_decode($token), true);
    $current_user_id = $decoded_token['id'] ?? null;
    $is_admin = $decoded_token['is_admin'] ?? false;

    if (!$current_user_id) {
        sendResponse(['status' => 'error', 'message' => 'Authentication required.'], 401);
    }

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $appointment_id = (int)$_GET['id'];
        $stmt = $db->prepare("SELECT * FROM appointments WHERE id = :id");
        if (!$stmt) sendResponse(['status' => 'error', 'message' => 'DB prepare failed.'], 500);
        $stmt->bindValue(':id', $appointment_id, SQLITE3_INTEGER);
        $result = $stmt->execute();
        $appointment = $result->fetchArray(SQLITE3_ASSOC);
        if ($appointment) {
            sendResponse(['status' => 'success', 'appointment' => $appointment]);
        } else {
            sendResponse(['status' => 'error', 'message' => 'Appointment not found.'], 404);
        }
    } else {
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
