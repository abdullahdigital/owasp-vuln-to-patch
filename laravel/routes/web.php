<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;

Route::get('/create-test-user', function() {
    $user = new User();
    $user->first_name = 'Test';
    $user->last_name = 'User';
    $user->email = 'test@example.com';
    $user->password = bcrypt('password');
    $user->save();
    return 'Test user created';
});
use Illuminate\Support\Facades\DB;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/check-tables', function() {
    return response()->json(DB::select("SELECT name FROM sqlite_master WHERE type='table'"));
});
