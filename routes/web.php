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
    Route::resource('categories', \App\Http\Controllers\Web\CategoryController::class);

    // Access Requests
    Route::get('/access-requests', [\App\Http\Controllers\Web\AccessRequestController::class, 'index'])->name('web.access-requests.index');
    Route::get('/access-requests/create', [\App\Http\Controllers\Web\AccessRequestController::class, 'create'])->name('web.access-requests.create');
    Route::post('/access-requests', [\App\Http\Controllers\Web\AccessRequestController::class, 'store'])->name('web.access-requests.store');
    Route::put('/access-requests/{accessRequest}', [\App\Http\Controllers\Web\AccessRequestController::class, 'update'])->name('web.access-requests.update');

    // Secure Document User View (for Dependencies)
    Route::get('/documents/{document}/secure-view', function (\App\Models\Document $document) {
        return App::call([\App\Http\Controllers\Web\AccessRequestController::class, 'secureView'], ['document' => $document]);
    });

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('dependencies', \App\Http\Controllers\Admin\DependencyController::class);
    });
});
