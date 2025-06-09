<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\AttendeeController;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// ========================
// Public Routes
// ========================

// Attendees can register without authentication
Route::post('/attendees', [AttendeeController::class, 'store']);

// ========================
// Protected Routes (Require Auth)
// ========================

Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('is_admin')->group(function () {
        // Event Management
        Route::get('/events', [EventController::class, 'index']);
        Route::post('/events', [EventController::class, 'store']);
        Route::get('/events/{event}', [EventController::class, 'show']);
        Route::put('/events/{event}', [EventController::class, 'update']);
        Route::delete('/events/{event}', [EventController::class, 'destroy']);
    });

    //Only allowed for authenticated API consumers
    
    // Attendee Management 
    Route::get('/attendees', [AttendeeController::class, 'show']);
    Route::put('/attendees', [AttendeeController::class, 'update']);
    Route::delete('/attendees', [AttendeeController::class, 'destroy']);

    // Booking System
    Route::post('/bookings', [BookingController::class, 'store']);
});
