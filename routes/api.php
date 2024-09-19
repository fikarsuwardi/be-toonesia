<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes (Require user to be authenticated via JWT token)
Route::middleware('auth:api')->group(function () {
    // User profile management
    Route::get('/profile', [ProfileController::class, 'show']); // Fetch profile
    Route::post('/profile', [ProfileController::class, 'store']); // Create profile
    Route::put('/profile', [ProfileController::class, 'update']); // Update profile
    Route::delete('/profile', [ProfileController::class, 'destroy']); // Delete profile

    // Logout and get authenticated user's details
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});
