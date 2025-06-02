<?php
// Enable full error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// CORS Headers
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Database setup
$dbPath = __DIR__ . '/../db/appointments.db';

// Ensure directory exists
if (!file_exists(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0777, true);
}

try {
    $db = new SQLite3($dbPath);
    $db->enableExceptions(true);
    
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT NOT NULL,
        date TEXT NOT NULL,
        time TEXT NOT NULL,
        service TEXT NOT NULL,
        message TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
    )");
} catch (Exception $e) {
    http_response_code(500);
    die(json_encode([
        "status" => "error",
        "message" => "Database connection failed",
        "error" => $e->getMessage()
    ]));
}

// Handle both POST and GET (for testing)
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        // Get input data
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? $_GET;
        
        // Validate required fields
        $required = ['name', 'email', 'phone', 'date', 'time', 'service'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        // Extract values
        $name = $data['name'];
        $email = $data['email'];
        $phone = $data['phone'];
        $date = $data['date'];
        $time = $data['time'];
        $service = $data['service'];
        $message = $data['message'] ?? '';

        // Vulnerable SQL - for demonstration only!
        $insertSql = "INSERT INTO appointments (name, email, phone, date, time, service, message) 
                     VALUES ('$name', '$email', '$phone', '$date', '$time', '$service', '$message')";
        
        // Execute with error handling
        try {
            $insertSuccess = $db->exec($insertSql);
        } catch (Exception $e) {
            $insertSuccess = false;
            $insertError = $e->getMessage();
        }

        // Prepare response
        $response = [
            'status' => $insertSuccess ? 'success' : 'error',
            'message' => $insertSuccess ? 'Appointment booked' : ($insertError ?? 'Failed to book appointment'),
            'sql_injection' => false,
            'executed_sql' => $insertSql
        ];

        // Check for SQL injection patterns
        if (strpos($name, "'") !== false || strpos($message, "'") !== false) {
            $response['sql_injection'] = true;
            
            // Try to leak data
            try {
                $leakQuery = "SELECT * FROM appointments ORDER BY id DESC LIMIT 5";
                $result = $db->query($leakQuery);
                $response['leaked_data'] = [];
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $response['leaked_data'][] = $row;
                }
            } catch (Exception $e) {
                $response['leak_error'] = $e->getMessage();
            }
        }

        // Execute custom query if provided
        if (!empty($message) && preg_match('/^SELECT/i', trim($message))) {
            try {
                $result = $db->query($message);
                $response['custom_query'] = [];
                while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                    $response['custom_query'][] = $row;
                }
            } catch (Exception $e) {
                $response['query_error'] = $e->getMessage();
            }
        }

        echo json_encode($response, JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>