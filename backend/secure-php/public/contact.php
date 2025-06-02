<?php
// backend/secure-php/public/contact.php

// Enable error reporting but don't display to users in production
error_reporting(E_ALL);
ini_set('display_errors', 0); // Hide errors from public, log them instead

// --- SECURITY: XXE Prevention - ABSOLUTELY CRUCIAL ---
// Disable external entity loading globally for libxml.
// This must be the very first executable PHP line after <?php
libxml_disable_entity_loader(true);
// Also manage internal errors for better control over DOMDocument's error reporting
// We'll enable it for parsing, then clear and disable it.
libxml_use_internal_errors(true); 

// Set CORS and content type
header("Access-Control-Allow-Origin: http://localhost:5173"); // Specify your exact frontend origin
header("Access-Control-Allow-Methods: POST, OPTIONS"); // Only allow POST and OPTIONS for this endpoint
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With, Accept");
header("Access-Control-Allow-Credentials: true");
header("Content-Type: application/json");

// Handle OPTIONS preflight request (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize all incoming text data for XSS prevention on the backend
    // htmlspecialchars with ENT_QUOTES and UTF-8 encoding is robust
    $name = htmlspecialchars(trim($_POST['name'] ?? ''), ENT_QUOTES, 'UTF-8');
    $email = htmlspecialchars(trim($_POST['email'] ?? ''), ENT_QUOTES, 'UTF-8');
    $message = htmlspecialchars(trim($_POST['message'] ?? ''), ENT_QUOTES, 'UTF-8');

    // Basic validation for required fields
    if (empty($name) || empty($email) || empty($message)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Name, email, and message are required fields.'
        ]);
        exit;
    }

    // Basic email format validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Invalid email format.'
        ]);
        exit;
    }

    // Prepare response structure. 'display_message' is the safe version for frontend.
    $response = [
        'status' => 'success',
        'name' => $name,
        'email' => $email,
        'display_message' => $message, // Use the already HTML-escaped message
        'timestamp' => date('Y-m-d H:i:s')
    ];

    try {
        // Handle file upload
        if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!file_exists($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) { // Use 0755 for directories, better than 0777
                    throw new Exception("Failed to create upload directory: " . $uploadDir);
                }
            }

            $file = $_FILES['file'];
            $fileName = basename($file['name']); // Get original filename
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = mime_content_type($fileTmpName); // Get actual MIME type

            // --- SECURITY: File Upload Validation ---
            // 1. Whitelist allowed extensions
            $allowedExtensions = ['xml', 'pdf', 'txt', 'jpg', 'jpeg', 'png']; // Add more as needed
            $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

            if (!in_array($fileExt, $allowedExtensions)) {
                throw new Exception("Invalid file type. Only XML, PDF, TXT, JPG, PNG are allowed.");
            }

            // 2. Whitelist allowed MIME types (more reliable than extension alone)
            $allowedMimeTypes = [
                'application/xml',
                'text/xml',
                'application/pdf',
                'text/plain',
                'image/jpeg',
                'image/png'
            ];

            if (!in_array($fileType, $allowedMimeTypes)) {
                throw new Exception("Invalid file MIME type detected: {$fileType}.");
            }
            
            // 3. Limit file size
            $maxFileSize = 5 * 1024 * 1024; // 5 MB
            if ($fileSize > $maxFileSize) {
                throw new Exception("File size exceeds the limit of 5MB.");
            }

            // 4. Generate a unique filename to prevent overwriting and path traversal
            $uniqueFileName = uniqid('upload_', true) . '.' . $fileExt;
            $filePath = $uploadDir . $uniqueFileName;

            if (move_uploaded_file($fileTmpName, $filePath)) {
                $response['uploaded_file'] = $fileName; // Respond with original name
                $response['stored_as'] = $uniqueFileName; // For debugging, internal name

                // XXE processing (NOW SECURE DUE TO libxml_disable_entity_loader(true) at the top)
                if ($fileExt === 'xml') {
                    $dom = new DOMDocument();
                    // Clear any previous libxml errors before attempting to load
                    libxml_clear_errors(); // Clear existing errors
                    // libxml_use_internal_errors(true) was set at the top.
                    
                    $xmlContent = file_get_contents($filePath);
                    if ($xmlContent === false) {
                        throw new Exception("Failed to read uploaded XML file.");
                    }
                    
                    // Attempt to load XML. If it's malformed or tries to use external entities
                    // which are now disabled, libxml will report errors internally.
                    $loadSuccess = $dom->loadXML($xmlContent);

                    // --- IMPORTANT: Explicitly check for libxml errors after loading XML ---
                    if (!$loadSuccess || libxml_get_errors()) {
                        $libxmlErrors = libxml_get_errors();
                        libxml_clear_errors(); // Clear errors after getting them
                        $errorMessages = array_map(function($error) {
                            return trim($error->message);
                        }, $libxmlErrors);
                        
                        // Log detailed XML parsing errors internally
                        error_log("XML Parsing Error for {$uniqueFileName}: " . implode(", ", $errorMessages));
                        
                        // Throw a generic exception to the main catch block for frontend response
                        throw new Exception("Invalid XML file provided or parsing error detected.");
                    }
                    
                    libxml_use_internal_errors(false); // Reset libxml error handling

                    $dataNode = $dom->getElementsByTagName('data')->item(0);
                    // --- IMPORTANT: Sanitize XML content before including in JSON response ---
                    $response['xml_content_extracted'] = htmlspecialchars($dataNode ? $dataNode->nodeValue : 'No <data> node found.', ENT_QUOTES, 'UTF-8');
                    // --- IMPORTANT: Sanitize saved XML output before including in JSON response ---
                    $response['xml_raw_parsed_output'] = htmlspecialchars($dom->saveXML(), ENT_QUOTES, 'UTF-8');
                }
            } else {
                throw new Exception("Failed to move uploaded file.");
            }
        } else if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
            // Handle other file upload errors (e.g., UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_PARTIAL)
            $phpUploadErrors = [
                UPLOAD_ERR_INI_SIZE => 'The uploaded file exceeds the upload_max_filesize directive in php.ini.',
                UPLOAD_ERR_FORM_SIZE => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form.',
                UPLOAD_ERR_PARTIAL => 'The uploaded file was only partially uploaded.',
                UPLOAD_ERR_NO_TMP_DIR => 'Missing a temporary folder.',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
                UPLOAD_ERR_EXTENSION => 'A PHP extension stopped the file upload.'
            ];
            $errorMsg = $phpUploadErrors[$_FILES['file']['error']] ?? 'Unknown file upload error.';
            throw new Exception("File upload error: " . $errorMsg);
        }

        // Store to contact.json
        $dbPath = __DIR__ . '/../db/contact.json';
        $existing = [];
        if (file_exists($dbPath)) {
            $existingContent = file_get_contents($dbPath);
            $decoded = json_decode($existingContent, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $existing = $decoded;
            } else {
                error_log("Error decoding contact.json: " . json_last_error_msg() . ". File might be corrupted. Old data will be discarded.");
                // Optionally backup corrupted file here before overwriting
                // rename($dbPath, $dbPath . '.bak_' . time());
            }
        }
        
        $existing[] = $response; // Add the current response to the array

        // Write back, ensuring JSON_PRETTY_PRINT is for readability, not production
        if (!file_put_contents($dbPath, json_encode($existing, JSON_PRETTY_PRINT))) {
            throw new Exception("Failed to write contact data to database. Check directory permissions for " . dirname($dbPath));
        }

        echo json_encode($response);
        exit;

    } catch (Exception $e) {
        // Log the detailed error internally
        error_log("Contact form processing error: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        
        // Send a generic error message to the client for security
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'An internal server error occurred. Please try again later.'
            // For debugging in development, you might include: 'debug_error' => $e->getMessage()
        ]);
        exit;
    }
}

// Invalid method
http_response_code(405); // 405 Method Not Allowed
echo json_encode([
    'status' => 'error',
    'message' => 'Method Not Allowed. Only POST requests are accepted.'
]);
exit;

?>