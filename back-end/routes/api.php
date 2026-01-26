<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkingController;
use App\Http\Controllers\ReservationController;

// Authentification
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');

// Partie Admin
Route::get('/parking-admin', [ParkingController::class, 'index'])->middleware('auth:sanctum');
Route::post('/create-parking', [ParkingController::class, 'create'])->middleware('auth:sanctum');
Route::get('/parking-admin-show/{id}', [ParkingController::class, 'show'])->middleware('auth:sanctum');
Route::put('/update-parking/{id}', [ParkingController::class, 'update'])->middleware('auth:sanctum');
Route::delete('/delete-parking/{id}', [ParkingController::class, 'delete'])->middleware('auth:sanctum');
Route::post('/create-user', [ParkingController::class, 'createUser'])->middleware('auth:sanctum');

// Partie User
Route::post('/reserver-parking', [ReservationController::class, 'reserverParking'])->middleware('auth:sanctum');
Route::get('/parkings', [ParkingController::class, 'parkings'])->middleware('auth:sanctum');