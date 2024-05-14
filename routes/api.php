<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;


Route::post('/login', [AuthController::class, 'login']);
Route::post('/login-driver', [AuthController::class, 'loginDriver']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/register-driver', [AuthController::class, 'registerDriver']);
});


