<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$dbPath = __DIR__ . '/../db/appointments.db';

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

if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true) ?? $_GET;

        $required = ['name', 'email', 'phone', 'date', 'time', 'service'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        $stmt = $db->prepare("INSERT INTO appointments (name, email, phone, date, time, service, message)
                              VALUES (:name, :email, :phone, :date, :time, :service, :message)");

        $stmt->bindValue(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindValue(':email', $data['email'], SQLITE3_TEXT);
        $stmt->bindValue(':phone', $data['phone'], SQLITE3_TEXT);
        $stmt->bindValue(':date', $data['date'], SQLITE3_TEXT);
        $stmt->bindValue(':time', $data['time'], SQLITE3_TEXT);
        $stmt->bindValue(':service', $data['service'], SQLITE3_TEXT);
        $stmt->bindValue(':message', $data['message'] ?? '', SQLITE3_TEXT);

        $result = $stmt->execute();

        echo json_encode([
            "status" => "success",
            "message" => "Appointment booked securely."
        ], JSON_PRETTY_PRINT);

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            "status" => "error",
            "message" => $e->getMessage()
        ], JSON_PRETTY_PRINT);
    }
} else {
    http_response_code(405);
    echo json_encode([
        "status" => "error",
        "message" => "Method not allowed"
    ]);
}
?>
