<?php
// Enable error reporting but don't display to users
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set CORS and content type
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';

    // Basic response structure
    $response = [
        'status' => 'success',
        'name' => $name,
        'email' => $email,
        'message' => htmlspecialchars($message), // safe output
        'raw_message' => $message, // unsafe (for XSS demo)
        'xss_payload' => $message, // persistent XSS demo
        'timestamp' => date('Y-m-d H:i:s')
    ];

    try {
        // Handle file upload
        if (isset($_FILES['file'])) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $file = $_FILES['file'];
            $filePath = $uploadDir . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $filePath)) {
                $response['file'] = $file['name'];

                // XXE processing
                $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                if ($ext === 'xml') {
                    libxml_disable_entity_loader(false); // allow external entities

                    $dom = new DOMDocument();
                    $dom->loadXML(file_get_contents($filePath), LIBXML_NOENT | LIBXML_DTDLOAD);

                    $dataNode = $dom->getElementsByTagName('data')->item(0);
                    $response['xml_content'] = $dataNode ? $dataNode->nodeValue : $dom->saveXML();
                }
            }
        }

        // Store to contact.json
        $dbPath = __DIR__ . '/../db/contact.json';
        $existing = file_exists($dbPath) ? json_decode(file_get_contents($dbPath), true) : [];
        $existing[] = $response;

        file_put_contents($dbPath, json_encode($existing, JSON_PRETTY_PRINT));

        echo json_encode($response);
        exit;

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => 'Processing failed',
            'error' => $e->getMessage()
        ]);
        exit;
    }
}

// Invalid method
http_response_code(400);
echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request method'
]);
