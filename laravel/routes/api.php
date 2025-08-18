<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AdminController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('api.login');
Route::post('/contact', [AuthController::class, 'contact']);
Route::get('/services', [ServiceController::class, 'index']);

// Temporary test routes
Route::post('/create-test-user', function() {
    $user = new \App\Models\User();
    $user->first_name = 'Test';
    $user->last_name = 'User';
    $user->email = 'test@example.com';
    $user->password = bcrypt('password');
    $user->save();
    return response()->json(['message' => 'Test user created']);
});

Route::prefix('test')->group(function () {
    Route::get('/appointments', function () {
        return 'Appointments test route working';
    });
    
    Route::post('/appointments/create', function (\Illuminate\Http\Request $request) {
        return response()->json([
            'status' => 'success',
            'message' => 'Appointment created successfully',
            'data' => $request->all()
        ]);
    });

    Route::post('/appointments/no-middleware', function (\Illuminate\Http\Request $request) {
        return response()->json(['success' => true, 'data' => $request->all()]);
    })->withoutMiddleware([
        \Illuminate\Http\Middleware\ValidatePathEncoding::class,
        \App\Http\Middleware\OverrideValidatePathEncoding::class
    ]);
});

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
});

// Temporary bypass for appointments routes
// Temporarily bypassing auth middleware for testing
Route::middleware([])->group(function () {
    Route::apiResource('appointments', AppointmentController::class);
    Route::get('appointments/history', [AppointmentController::class, 'history']);
});

// Original route with auth middleware (commented out for now)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::apiResource('appointments', AppointmentController::class);
//     Route::get('appointments/history', [AppointmentController::class, 'history']);
// });

// Admin routes
Route::prefix('admin')->group(function () {
    Route::post('/login', [AdminController::class, 'login']);
    
    Route::middleware('auth')->group(function () {
        Route::get('/appointments', [AdminController::class, 'appointments']);
        Route::get('/users', [AdminController::class, 'users']);
        Route::put('/appointments/{id}', [AdminController::class, 'updateAppointment']);
        Route::delete('/appointments/{id}', [AdminController::class, 'destroyAppointment']);
    });
});