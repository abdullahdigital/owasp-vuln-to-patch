<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $pdo = DB::connection()->getPdo();
    echo $pdo ? "Connected successfully to: " . DB::connection()->getDatabaseName() : "Not connected";
} catch (\Exception $e) {
    echo "Connection failed: " . $e->getMessage();
}