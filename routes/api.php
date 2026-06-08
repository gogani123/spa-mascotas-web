<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes - Módulo de Seguridad Operativa (Token-Based Auth)
|--------------------------------------------------------------------------
*/

// Endpoint público para solicitar el token de sesión
Route::post('/login-token', [AuthController::class, 'login']);

// Rutas protegidas por Token (Requieren el Bearer Token en la cabecera)
Route::middleware('auth:sanctum')->group(function () {
    
    // Endpoint seguro para destruir el token al cerrar sesión
    Route::post('/logout-token', [AuthController::class, 'logout']);
});