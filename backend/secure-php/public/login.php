<?php
// backend/insecure-php/public/auth.php

// Security Misconfiguration - Disable display errors in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// CORS headers
header("Access-Control-Allow-Origin: http://localhost:5173"); // Specify your Svelte dev server origin
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization"); // Ensure Authorization is allowed
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Database path
$dbPath = __DIR__ . '/../db/database.db';

// Response function
function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

try {
    // --- Database Initialization and Dummy Data Population ---
    // This block will run ONLY if the database file does not exist.
    // It creates the tables and inserts some initial users and appointments.
    if (!file_exists($dbPath)) {
        if (!is_dir(dirname($dbPath))) {
            mkdir(dirname($dbPath), 0777, true);
        }

        $db_init = new SQLite3($dbPath);
        if (!$db_init) {
            throw new Exception("Could not create database: " . $db_init->lastErrorMsg());
        }

        // Create users table
        $db_init->exec("CREATE TABLE users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT UNIQUE,
            password TEXT,
            first_name TEXT,
            last_name TEXT,
            phone TEXT,
            is_admin BOOLEAN DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        // Create appointments table
        $db_init->exec("CREATE TABLE appointments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            date TEXT NOT NULL,
            time TEXT NOT NULL,
            doctor TEXT NOT NULL,
            notes TEXT,
            internal_billing_code TEXT,
            patient_private_notes TEXT,
            doctor_private_comment TEXT,
            FOREIGN KEY (user_id) REFERENCES users(id)
        )");

        // Insert default admin and test user
        $db_init->exec("INSERT INTO users (email, password, first_name, last_name, is_admin)
                        VALUES ('admin@clinic.com', 'admin123', 'Admin', 'User', 1)");
        $db_init->exec("INSERT INTO users (email, password, first_name, last_name)
                        VALUES ('test@test.com', 'password123', 'Test', 'User')");
        $db_init->exec("INSERT INTO users (email, password, first_name, last_name)
                        VALUES ('patient2@clinic.com', 'patient123', 'Jane', 'Doe')");

        // Get user IDs for inserting appointments
        $admin_id = $db_init->querySingle("SELECT id FROM users WHERE email='admin@clinic.com'");
        $test_user_id = $db_init->querySingle("SELECT id FROM users WHERE email='test@test.com'");
        $patient2_id = $db_init->querySingle("SELECT id FROM users WHERE email='patient2@clinic.com'");


        // Insert dummy appointments for test@test.com (user_id = 2)
        $db_init->exec("INSERT INTO appointments (user_id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment) VALUES
            ($test_user_id, '2025-06-10', '10:00 AM', 'Dr. Smith', 'Annual check-up', 'BILL-001', 'Patient requested allergy test.', 'Needs follow-up on lab results.'),
            ($test_user_id, '2025-07-01', '02:30 PM', 'Dr. Jones', 'Follow-up on blood work', 'BILL-002', 'Concerned about fatigue.', 'Suggested Vitamin D supplement.')");

        // Insert dummy appointments for patient2@clinic.com (user_id = 3)
        $db_init->exec("INSERT INTO appointments (user_id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment) VALUES
            ($patient2_id, '2025-06-15', '11:00 AM', 'Dr. Jones', 'Dental cleaning', 'BILL-003', 'Has sensitive teeth.', 'Recommended softer toothbrush.'),
            ($patient2_id, '2025-07-20', '09:00 AM', 'Dr. Garcia', 'Eye exam', 'BILL-004', 'Vision blurry in left eye.', 'Prescribed new glasses.')");

        // Insert a "sensitive" appointment for admin@clinic.com (user_id = 1)
        $db_init->exec("INSERT INTO appointments (user_id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment) VALUES
            ($admin_id, '2025-06-25', '03:00 PM', 'Dr. Lee', 'Admin consultation', 'BILL-005-ADMIN', 'Discussed clinic finances.', 'Confidential financial review.')");


        $db_init->close(); // Close the initial connection
    }
    // --- End Database Initialization ---

    $db = new SQLite3($dbPath); // Open a new connection for processing request
    if (!$db) {
        throw new Exception("Could not open database: " . $db->lastErrorMsg());
    }

    // Debug endpoint (can be useful during development)
    if (isset($_GET['debug_users'])) {
        $users = [];
        $result = $db->query("SELECT * FROM users");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            unset($row['password']); // Don't expose passwords!
            $users[] = $row;
        }
        sendResponse([
            "status" => "debug",
            "users" => $users
        ]);
    }
    if (isset($_GET['debug_appointments'])) {
        $appointments = [];
        $result = $db->query("SELECT * FROM appointments");
        while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
            $appointments[] = $row;
        }
        sendResponse([
            "status" => "debug",
            "appointments" => $appointments
        ]);
    }

    // Process POST data for login/signup
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Signup
        if (isset($data['signup'])) {
            $first_name = $data['firstName'] ?? '';
            $last_name = $data['lastName'] ?? '';
            $email = $db->escapeString($data['email']);
            $password = $db->escapeString($data['password']);
            $phone = $db->escapeString($data['phone'] ?? '');

            // Vulnerable direct string concatenation for signup query
            $query = "INSERT INTO users (email, password, first_name, last_name, phone)
                      VALUES ('$email', '$password', '$first_name', '$last_name', '$phone')";

            $result = $db->exec($query);

            if (!$result) {
                sendResponse([
                    "status" => "error",
                    "message" => "Signup failed: " . $db->lastErrorMsg()
                ], 400);
            }

            $user_sql = "SELECT id, email, first_name, last_name, phone, is_admin FROM users WHERE email = '$email'";
            $user = $db->querySingle($user_sql, true);

            $token = base64_encode(json_encode([
                'id' => $user['id'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin'],
                'issued' => time()
            ]));

            sendResponse([
                "status" => "success",
                "token" => $token,
                "user" => $user
            ]);
        }

        // Login
        $email = $db->escapeString($data['email'] ?? '');
        $password = $db->escapeString($data['password'] ?? '');

        // Vulnerable direct string concatenation for login query
        $user_sql = "SELECT id, email, password, first_name, last_name, phone, is_admin FROM users WHERE email = '$email'";
        $user = $db->querySingle($user_sql, true);

        if ($user && $user['password'] === $password) { // Weak password check
            unset($user['password']); // Don't send password back to client
            $token = base64_encode(json_encode([
                'id' => $user['id'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin'],
                'issued' => time()
            ]));

            sendResponse([
                "status" => "success",
                "token" => $token,
                "user" => $user
            ]);
        } else {
            sendResponse([
                "status" => "error",
                "message" => "Invalid credentials"
            ], 401);
        }
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