<?php
// backend/secure-php/public/login.php

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Debugging: Log that this specific file is being executed
error_log(">>> SECURE login.php EXECUTED: " . __FILE__);

// CORS headers - allow your frontend origin
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS"); // Allow GET if you send preflight for some reason
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle OPTIONS preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// --- DATABASE PATHS ---
// Path to the main database (users, login_attempts)
$mainDbPath = __DIR__ . '/../db/database.db';
// Path to the separate logs database
$logsDbPath = __DIR__ . '/logs.db'; // This is the corrected path to store logs outside public/

function sendResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

function logAction($action, $source, $email = null, $additionalData = null) {
    global $logsDbPath; // Use the globally defined logsDbPath

    try {
        // Debugging: Log that logAction is being called
        error_log("logAction called for action: " . $action . ", source: " . $source . ", email: " . ($email ?? 'N/A'));
        error_log("Attempting to open DB at: " . $logsDbPath);
        error_log("Does logs DB path exist? " . (file_exists($logsDbPath) ? 'Yes' : 'No'));
        error_log("Is logs DB directory writable? " . (is_writable(dirname($logsDbPath)) ? 'Yes' : 'No'));
        if (file_exists($logsDbPath)) {
            error_log("Is logs DB file writable? " . (is_writable($logsDbPath) ? 'Yes' : 'No'));
        }

        $logDb = new SQLite3($logsDbPath);
        $logDb->enableExceptions(true); // Enable exceptions for better error handling

        // Create the logs table if it does not exist. Added 'email' and 'additional_data' columns.
        $logDb->exec("CREATE TABLE IF NOT EXISTS logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            action TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            url TEXT,
            source TEXT,
            email TEXT,                -- Added for login logs
            additional_data TEXT,      -- Added for extra context
            timestamp DATETIME DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $logDb->prepare("INSERT INTO logs (action, ip_address, user_agent, url, source, email, additional_data) 
                                 VALUES (:action, :ip, :ua, :url, :source, :email, :additional_data)");
        
        // --- Add check for prepared statement here too for robustness ---
        if ($stmt === false) {
            throw new Exception('Failed to prepare log insert statement: ' . $logDb->lastErrorMsg());
        }
        
        $stmt->bindValue(':action', $action, SQLITE3_TEXT);
        $stmt->bindValue(':ip', $_SERVER['REMOTE_ADDR'] ?? 'unknown', SQLITE3_TEXT);
        $stmt->bindValue(':ua', $_SERVER['HTTP_USER_AGENT'] ?? 'unknown', SQLITE3_TEXT);
        $stmt->bindValue(':url', $_SERVER['REQUEST_URI'] ?? 'unknown', SQLITE3_TEXT);
        $stmt->bindValue(':source', $source, SQLITE3_TEXT);
        $stmt->bindValue(':email', $email, SQLITE3_TEXT);
        $stmt->bindValue(':additional_data', $additionalData ? json_encode($additionalData) : null, SQLITE3_TEXT);
        
        if (!$stmt->execute()) {
            throw new Exception('Failed to execute SQL statement: ' . $logDb->lastErrorMsg());
        }
        error_log("Log successfully written to " . $logsDbPath . " for action: " . $action);
        $logDb->close(); // Close the database connection after use
    } catch (Exception $e) {
        error_log("!!! CRITICAL LOGGING ERROR in logAction: " . $e->getMessage());
        error_log("Affected DB Path: " . $logsDbPath);
        // Add custom headers for debugging in browser's network tab
        header("X-Logging-Error: " . urlencode($e->getMessage()));
        header("X-Logging-DB-Path: " . urlencode($logsDbPath));
        header("X-Logging-DB-Exists: " . (file_exists($logsDbPath) ? 'yes' : 'no'));
        header("X-Logging-Dir-Writable: " . (is_writable(dirname($logsDbPath)) ? 'yes' : 'no'));
        if (file_exists($logsDbPath)) {
            header("X-Logging-File-Writable: " . (is_writable($logsDbPath) ? 'yes' : 'no'));
        }
    }
}

try {
    // Connect to the main database for users and login attempts
    $db = new SQLite3($mainDbPath);
    // --- IMPORTANT CHANGE 1: Enable exceptions immediately after creation ---
    // This ensures that subsequent errors, including prepare(), will throw an exception
    $db->enableExceptions(true); 

    // --- IMPORTANT CHANGE 2: More robust connection check ---
    // The constructor itself can return false or not throw an exception on certain failures.
    // Checking lastErrorMsg() immediately after can reveal issues.
    if (!$db || $db->lastErrorMsg() !== 'not an error') { // 'not an error' is SQLite's default success message
        $errorMsg = "Could not connect to main database: " . ($db ? $db->lastErrorMsg() : "Unknown SQLite3 connection error. Check path and permissions.");
        error_log("!!! CRITICAL MAIN DB CONNECTION ERROR: " . $errorMsg);
        sendResponse([
            "status" => "error",
            "message" => "Server configuration error: Unable to connect to user database."
        ], 500); // Send a generic error to frontend for security
    }
    
    // Make sure the login_attempts table exists
    $db->exec("CREATE TABLE IF NOT EXISTS login_attempts (
        email TEXT PRIMARY KEY,
        failed_count INTEGER DEFAULT 0,
        last_failed_at INTEGER DEFAULT 0
    )");
    // --- START OF NEW CODE FOR ALTERING TABLE ---
    // These blocks will attempt to add the columns if they don't exist.
    // They are wrapped in try-catch to handle the "duplicate column name" error
    // if the columns already exist (e.g., after the first run).

    try {
        $db->exec("ALTER TABLE login_attempts ADD COLUMN failed_count INTEGER DEFAULT 0");
        error_log("Added 'failed_count' column to login_attempts.");
    } catch (Exception $e) {
        // Ignore if column already exists or other ALTER TABLE error
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            error_log("Error adding 'failed_count' column: " . $e->getMessage());
        }
    }

    try {
        $db->exec("ALTER TABLE login_attempts ADD COLUMN last_failed_at INTEGER DEFAULT 0");
        error_log("Added 'last_failed_at' column to login_attempts.");
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'duplicate column name') === false) {
            error_log("Error adding 'last_failed_at' column: " . $e->getMessage());
        }
    }
    // --- END OF NEW CODE FOR ALTERING TABLE ---
    // Ensure the users table exists for new signups
    $db->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        first_name TEXT,
        last_name TEXT,
        phone TEXT,
        is_admin INTEGER DEFAULT 0
    )");

    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        logAction("Invalid JSON input to login.php", "backend", null, ['raw_input' => $json]); // Log this error
        sendResponse(["status" => "error", "message" => "Invalid JSON input"], 400);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = strtolower(trim($data['email'] ?? ''));
        $password = trim($data['password'] ?? '');

        // --- SIGNUP LOGIC ---
        if (isset($data['signup'])) {
            $first_name = trim($data['firstName'] ?? '');
            $last_name = trim($data['lastName'] ?? '');
            $phone = trim($data['phone'] ?? '');

            if (!$email || !$password || !$first_name || !$last_name) {
                logAction("Missing signup fields", "backend", $email);
                sendResponse(["status" => "error", "message" => "Missing required fields for signup"], 400);
            }

            // Check if user already exists
            $stmt = $db->prepare("SELECT id FROM users WHERE email = :email");
            // --- Add check for prepared statement here ---
            if ($stmt === false) {
                logAction("Signup failed: Failed to prepare user check statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                sendResponse(["status" => "error", "message" => "Server error during signup preparation."], 500);
            }
            $stmt->bindValue(':email', $email);
            $res = $stmt->execute();
            if ($res->fetchArray()) {
                logAction("Signup failed: User already exists", "backend", $email);
                sendResponse(["status" => "error", "message" => "User already exists with this email"], 409); // 409 Conflict
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $db->prepare("INSERT INTO users (email, password, first_name, last_name, phone)
                                     VALUES (:email, :password, :first_name, :last_name, :phone)");
            // --- Add check for prepared statement here ---
            if ($stmt === false) {
                logAction("Signup failed: Failed to prepare user insert statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                sendResponse(["status" => "error", "message" => "Server error during signup preparation."], 500);
            }
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $hashedPassword);
            $stmt->bindValue(':first_name', $first_name);
            $stmt->bindValue(':last_name', $last_name);
            $stmt->bindValue(':phone', $phone);

            if (!$stmt->execute()) {
                logAction("Signup failed: DB error during insert", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                sendResponse([
                    "status" => "error",
                    "message" => "Signup failed: " . $db->lastErrorMsg()
                ], 500);
            }

            $user = $db->querySingle("SELECT id, email, first_name, last_name, phone, is_admin FROM users WHERE email = '$email'", true);
            $token = base64_encode(json_encode([
                'id' => $user['id'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin'],
                'issued' => time()
            ]));

            logAction("User signed up successfully", "backend", $email); // Log successful signup
            sendResponse([
                "status" => "success",
                "token" => $token,
                "user" => $user
            ]);
        }

        // --- LOGIN LOGIC ---
        if (!$email || !$password) {
            logAction("Missing login credentials", "backend", null); // No email if missing
            sendResponse(["status" => "error", "message" => "Email and password are required"], 400);
        }

        // Rate limiting variables
        $blockDuration = 60; // 1 minute block
        $maxAttempts = 3; // Maximum failed attempts before block
        $now = time();

        // Check login attempts
        $stmt = $db->prepare("SELECT failed_count, last_failed_at FROM login_attempts WHERE email = :email");
        // --- Add check for prepared statement here ---
        if ($stmt === false) {
            logAction("Login failed: Failed to prepare login attempts check statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
            sendResponse(["status" => "error", "message" => "Server error during login attempts check."], 500);
        }
        $stmt->bindValue(':email', $email);
        $res = $stmt->execute();
        $attempt = $res->fetchArray(SQLITE3_ASSOC);

        $failed_count = $attempt['failed_count'] ?? 0;
        $last_failed_at = $attempt['last_failed_at'] ?? 0;

        if ($failed_count >= $maxAttempts && ($now - $last_failed_at) < $blockDuration) {
            $wait = $blockDuration - ($now - $last_failed_at);
            logAction("Login blocked: Too many attempts", "backend", $email, ['attempts' => $failed_count]); // Log block event
            sendResponse([
                "status" => "error",
                "message" => "Too many failed login attempts. Please try again after {$wait} seconds."
            ], 429); // 429 Too Many Requests
        } elseif (($now - $last_failed_at) >= $blockDuration && $failed_count > 0) {
            // Reset attempts after cooldown period
            $stmt = $db->prepare("UPDATE login_attempts SET failed_count = 0, last_failed_at = 0 WHERE email = :email");
            // --- Add check for prepared statement here ---
            if ($stmt === false) {
                logAction("Login failed: Failed to prepare login attempts reset statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                // Continue execution here as this is not critical for user login flow, but log the error.
            } else {
                $stmt->bindValue(':email', $email);
                $stmt->execute();
            }
            $failed_count = 0; // Reset local variable
            $last_failed_at = 0; // Reset local variable
        }

        // Get user record
        $stmt = $db->prepare("SELECT id, email, password, first_name, last_name, phone, is_admin FROM users WHERE email = :email");
        // --- THIS IS THE CRITICAL FIX FOR LINE 198 ---
        // If prepare fails, $stmt will be false. Check it immediately!
        if ($stmt === false) {
            // Log the error using your logAction function
            logAction("Login failed: Failed to prepare user selection statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
            sendResponse(["status" => "error", "message" => "Server error during user lookup preparation."], 500);
        }
        
        $stmt->bindValue(':email', $email);
        $result = $stmt->execute();
        $user = $result->fetchArray(SQLITE3_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            // Success: remove login attempts record for this email
            $stmt = $db->prepare("DELETE FROM login_attempts WHERE email = :email");
            // --- Add check for prepared statement here ---
            if ($stmt === false) {
                logAction("Login successful but failed to prepare login attempts delete statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                // Not critical to send error to frontend, log and continue.
            } else {
                $stmt->bindValue(':email', $email);
                $stmt->execute();
            }

            unset($user['password']); // Don't send password hash to frontend
            $token = base64_encode(json_encode([
                'id' => $user['id'],
                'email' => $user['email'],
                'is_admin' => $user['is_admin'],
                'issued' => $now
            ]));

            logAction("Successful login", "backend", $email); // Log successful login
            sendResponse([
                "status" => "success",
                "token" => $token,
                "user" => $user
            ]);
        } else {
            // Failed login: increment failed_count and update timestamp
            if ($attempt) { // If there's an existing record
                $stmt = $db->prepare("UPDATE login_attempts SET failed_count = failed_count + 1, last_failed_at = :now WHERE email = :email");
            } else { // First failed attempt for this email
                $stmt = $db->prepare("INSERT INTO login_attempts (email, failed_count, last_failed_at) VALUES (:email, 1, :now)");
            }
            
            // --- Add check for prepared statement here ---
            if ($stmt === false) {
                logAction("Failed login: Failed to prepare login attempts update/insert statement", "backend", $email, ['db_error' => $db->lastErrorMsg()]);
                // This is a critical failure. Send error to frontend.
                sendResponse(["status" => "error", "message" => "Server error updating login attempts."], 500);
            }
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':now', $now);
            $stmt->execute();
            
            // Get the updated failed_count for accurate logging and response
            $currentFailedCount = ($failed_count + 1); // Assuming the update succeeded

            // Log the failed attempt directly from the backend
            logAction("Failed login attempt", "backend", $email, [
                'attempt_count' => $currentFailedCount,
                'max_attempts_before_block' => $maxAttempts,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
                'blocked_status' => ($currentFailedCount >= $maxAttempts) ? 'will_be_blocked' : 'not_blocked'
            ]);

            // Determine message based on attempts for better user feedback
            if ($currentFailedCount >= $maxAttempts) {
                sendResponse(["status" => "error", "message" => "Invalid credentials. Too many attempts, account temporarily locked."], 401);
            } else {
                $remainingAttempts = $maxAttempts - $currentFailedCount;
                sendResponse([
                    "status" => "error", 
                    "message" => "Invalid credentials. Remaining attempts: {$remainingAttempts}"
                ], 401);
            }
        }
    } else {
        // Method not allowed for requests other than POST for login/signup
        sendResponse(["status" => "error", "message" => "Method not allowed"], 405);
    }

} catch (Exception $e) {
    // This catches errors from the main authentication logic
    logAction("Server error in login.php (main logic): " . $e->getMessage(), "backend", null, ['exception_file' => $e->getFile(), 'exception_line' => $e->getLine()]);
    sendResponse([
        "status" => "error",
        "message" => "Server error: " . $e->getMessage()
    ], 500);
} finally {
    if (isset($db) && $db instanceof SQLite3) {
        $db->close();
    }
}
?>