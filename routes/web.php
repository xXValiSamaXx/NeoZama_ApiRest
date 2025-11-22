<?php

use App\Http\Controllers\Web\WebController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Auth Routes (Web)
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'loginWeb'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logoutWeb'])->name('logout');

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    Route::get('/documents', [WebController::class, 'documents'])->name('documents.index');
});
