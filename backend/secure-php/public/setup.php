<?php
// Security Configuration - Disable display errors in production
ini_set('display_errors', 0);
error_reporting(E_ALL);

// CORS headers - For local development, allows requests from any origin
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Database path
$dbPath = __DIR__ . '/../db/database.db';

// Ensure the directory for the database exists
if (!is_dir(dirname($dbPath))) {
    mkdir(dirname($dbPath), 0777, true);
}

try {
    // Open SQLite database connection
    $db = new SQLite3($dbPath);

    // Create users table
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        first_name TEXT,
        last_name TEXT,
        phone TEXT,
        is_admin BOOLEAN DEFAULT 0,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Create appointments table with sensitive fields for IDOR demo
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        user_id INTEGER NOT NULL,
        date TEXT NOT NULL,
        time TEXT NOT NULL,
        doctor TEXT NOT NULL,
        notes TEXT,
        internal_billing_code TEXT,      -- Sensitive data
        patient_private_notes TEXT,      -- Sensitive data
        doctor_private_comment TEXT,     -- Sensitive data
        FOREIGN KEY (user_id) REFERENCES users(id)
    )");

    // Insert dummy users if they don't exist
    $user1Exists = $db->querySingle("SELECT COUNT(*) FROM users WHERE email='user1@example.com'");
    if ($user1Exists == 0) {
        $db->exec("INSERT INTO users (email, password, first_name, last_name, is_admin) 
                    VALUES ('user1@example.com', 'password123', 'Alice', 'Smith', 0)");
    }

    $user2Exists = $db->querySingle("SELECT COUNT(*) FROM users WHERE email='user2@example.com'");
    if ($user2Exists == 0) {
        $db->exec("INSERT INTO users (email, password, first_name, last_name, is_admin) 
                    VALUES ('user2@example.com', 'Bob', 'Johnson', 'password123', 0)");
    }
    
    $adminExists = $db->querySingle("SELECT COUNT(*) FROM users WHERE email='admin@clinic.com'");
    if ($adminExists == 0) {
        $db->exec("INSERT INTO users (email, password, first_name, last_name, is_admin) 
                    VALUES ('admin@clinic.com', 'admin123', 'Admin', 'User', 1)");
    }

    // Insert dummy appointments if they don't exist
    $appt1Exists = $db->querySingle("SELECT COUNT(*) FROM appointments WHERE id=1");
    if ($appt1Exists == 0) {
        // Get user IDs dynamically for proper foreign key linking
        $user1_id = $db->querySingle("SELECT id FROM users WHERE email='user1@example.com'");
        $user2_id = $db->querySingle("SELECT id FROM users WHERE email='user2@example.com'");

        if ($user1_id && $user2_id) {
            $db->exec("INSERT INTO appointments (user_id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment) VALUES 
                ({$user1_id}, '2025-06-10', '10:00 AM', 'Dr. Smith', 'Annual checkup', 'BILL-S101-USER1', 'Patient prefers morning appointments.', 'Needs follow-up on gum health.'),
                ({$user1_id}, '2025-06-25', '02:30 PM', 'Dr. Evans', 'Cavity filling', 'BILL-E202-USER1', 'Sensitive to cold.', 'Filling completed successfully.'),
                ({$user2_id}, '2025-07-01', '09:00 AM', 'Dr. Lee', 'Teeth cleaning', 'BILL-L303-USER2', 'History of wisdom tooth pain.', 'Routine cleaning. Advised flossing.'),
                ({$user2_id}, '2025-07-15', '04:00 PM', 'Dr. Chen', 'Root canal consultation', 'BILL-C404-USER2', 'Expresses anxiety about dental procedures.', 'Referred to specialist.')
            ");
        }
    }

    echo json_encode(["status" => "success", "message" => "Database setup complete and dummy data inserted."]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["status" => "error", "message" => "Database setup failed: " . $e->getMessage()]);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>