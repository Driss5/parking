<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Partie Admin