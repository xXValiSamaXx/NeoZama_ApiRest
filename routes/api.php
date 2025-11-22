<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DocumentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas de autenticación
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// REQUISITO: "Sistema de seguridad básico... uso de un complemento como sanctum".
// El middleware 'auth:sanctum' protege todas las rutas dentro de este grupo.
Route::middleware('auth:sanctum')->group(function () {
    
    // Autenticación
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // REQUISITO: "Uso de rutas... creación de una API para una tabla".
    // apiResource crea automáticamente las rutas: index, store, show, update, destroy.
    
    // Categorías
    Route::apiResource('categories', CategoryController::class);
    
    // Documentos
    Route::get('/documents/shared', [DocumentController::class, 'shared']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::post('/documents/{document}/share', [DocumentController::class, 'share']);
    Route::apiResource('documents', DocumentController::class);
});
