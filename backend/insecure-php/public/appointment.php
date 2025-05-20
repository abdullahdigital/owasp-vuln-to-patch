<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle CORS preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    // Initialize database connection
    $db = new SQLite3(__DIR__ . '/../db/appointments.db');
    
    // Create table if not exists (you can remove this after first run)
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
    echo json_encode([
        "status" => "error", 
        "message" => "Database connection failed"
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode([
            "status" => "error", 
            "message" => "Invalid JSON data"
        ]);
        exit;
    }

    // Validate required fields
    $required = ['name', 'email', 'phone', 'date', 'time', 'service'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            echo json_encode([
                "status" => "error", 
                "message" => "Missing required field: $field"
            ]);
            exit;
        }
    }

    try {
        $stmt = $db->prepare("
            INSERT INTO appointments (name, email, phone, date, time, service, message)
            VALUES (:name, :email, :phone, :date, :time, :service, :message)
        ");
        
        $stmt->bindValue(':name', $data['name']);
        $stmt->bindValue(':email', $data['email']);
        $stmt->bindValue(':phone', $data['phone']);
        $stmt->bindValue(':date', $data['date']);
        $stmt->bindValue(':time', $data['time']);
        $stmt->bindValue(':service', $data['service']);
        $stmt->bindValue(':message', $data['message'] ?? '');
        
        if ($stmt->execute()) {
            echo json_encode([
                "status" => "success", 
                "message" => "Appointment booked successfully"
            ]);
        } else {
            throw new Exception("Failed to execute query");
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "status" => "error", 
            "message" => "Database error"
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error", 
        "message" => "Method Not Allowed"
    ]);
}
?>