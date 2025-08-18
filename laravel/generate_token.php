<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$user = User::first();

if ($user) {
    $token = $user->createToken('test-token')->plainTextToken;
    echo "Token: " . $token;
} else {
    echo "No users exist in database";
}