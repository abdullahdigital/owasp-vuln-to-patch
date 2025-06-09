<?php
// backend/insecure-php/public/generate_sample_data.php

// Disable display errors for security (though this is a setup script)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// CORS headers to allow access from your Svelte dev server
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
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

    // --- IMPORTANT: Adjust User IDs Below ---
    // These are sample user IDs. You might need to change them based on the
    // actual IDs of users registered in your 'users' table (e.g., goku@gmail.com, test@test.com, patient2@clinic.com, admin user).
    // You can inspect your 'users' table in database.db using a SQLite browser
    // to find the correct IDs for your test accounts.
    $goku_user_id = 7; // Example: Assuming goku@gmail.com is user_id 1
    $test_user_id = 3; // Example: Assuming test@test.com is user_id 2
    $patient2_user_id = 3; // Example: Assuming patient2@clinic.com is user_id 3
    $admin_user_id = 5; // Example: Assuming your admin user is user_id 5

    // Delete existing appointments to avoid duplicates on re-run
    $db->exec("DELETE FROM appointments");
    // Reset the auto-increment counter for 'id' if desired (optional)
    $db->exec("DELETE FROM sqlite_sequence WHERE name='appointments'");

    // Insert sample data using direct exec() for broader PHP compatibility
    // This avoids the clearBindings() method not available in older PHP versions.
    $db->exec("INSERT INTO appointments (id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment, user_id) VALUES
    (101, '2025-06-10', '10:00 AM', 'Dr. Smith', 'Routine check-up.', 'BILL001', 'Patient expressed slight sensitivity in molars.', 'Advised regular flossing and fluoride rinse.', " . $goku_user_id . ")");

    $db->exec("INSERT INTO appointments (id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment, user_id) VALUES
    (201, '2025-06-15', '02:00 PM', 'Dr. Jones', 'Dental cleaning.', 'BILL002', 'Needs reminder for next 6-month appointment.', 'Patient has good oral hygiene, minor plaque buildup.', " . $test_user_id . ")");

    $db->exec("INSERT INTO appointments (id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment, user_id) VALUES
    (202, '2025-06-20', '09:00 AM', 'Dr. Smith', 'Filling replacement on lower-left first molar.', 'BILL003', 'Concerned about cost, discussed insurance options.', 'Discussed payment plan and material choices.', " . $test_user_id . ")");

    $db->exec("INSERT INTO appointments (id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment, user_id) VALUES
    (301, '2025-06-25', '11:00 AM', 'Dr. Wilson', 'Wisdom tooth extraction consultation (upper-right).', 'BILL004', 'Very anxious about procedure, requested sedation options.', 'Prescribed mild sedative for pre-op anxiety. Booked for next month.', " . $patient2_user_id . ")");

    $db->exec("INSERT INTO appointments (id, date, time, doctor, notes, internal_billing_code, patient_private_notes, doctor_private_comment, user_id) VALUES
    (501, '2025-07-01', '04:00 PM', 'Dr. White', 'Specialized cosmetic procedure for high-profile client.', 'CONFIDENTIAL_ADMIN_BILL', 'Client is a well-known public figure, extreme privacy and discretion required. Avoid any media leaks.', 'Ensure all staff involved are briefed on strict non-disclosure agreements and protocols.', " . $admin_user_id . ")");

    sendResponse([
        'status' => 'success',
        'message' => 'Sample appointment data inserted successfully. Existing appointments were cleared.',
        'note' => 'Verify user_id values in this script match your actual user IDs in the "users" table.'
    ]);

} catch (Exception $e) {
    sendResponse([
        "status" => "error",
        "message" => "Failed to generate sample data: " . $e->getMessage()
    ], 500);
} finally {
    if (isset($db)) {
        $db->close();
    }
}
?>
