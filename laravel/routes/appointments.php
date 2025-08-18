<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AppointmentController;

Route::post('/appointments', [AppointmentController::class, 'store']);
Route::get('/appointments/history', [AppointmentController::class, 'history']);