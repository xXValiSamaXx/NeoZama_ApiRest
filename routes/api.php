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

// Debug route for upload config
Route::get('/test-upload-config', function () {
    return response()->json([
        'upload_max_filesize' => ini_get('upload_max_filesize'),
        'post_max_size' => ini_get('post_max_size'),
        'memory_limit' => ini_get('memory_limit'),
    ]);
});

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
    // Special route for audit-logged viewing
    Route::get('/documents/{document}/secure-view', [\App\Http\Controllers\Api\DocumentAccessController::class, 'show']);
    Route::get('/documents/{document}/download', [DocumentController::class, 'download']);
    Route::post('/documents/{document}/share', [DocumentController::class, 'share']);
    Route::apiResource('documents', DocumentController::class);

    // Solicitudes de Acceso (Dependencies)
    Route::apiResource('access-requests', \App\Http\Controllers\Api\AccessRequestController::class)
        ->only(['index', 'store', 'update']);
});
