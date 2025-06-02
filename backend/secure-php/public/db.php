<?php
try {
    $db = new SQLite3(__DIR__ . '/../appointments.db');
    $db->exec("CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT,
        email TEXT,
        phone TEXT,
        date TEXT,
        time TEXT,
        service TEXT,
        message TEXT
    )");
} catch (Exception $e) {
    die("Database connection failed: " . $e->getMessage());
}
?>
