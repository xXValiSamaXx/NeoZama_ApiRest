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

    // Profile Routes
    Route::get('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Web\ProfileController::class, 'update'])->name('profile.update');

    // Access Requests
    Route::get('/access-requests', [\App\Http\Controllers\Web\AccessRequestController::class, 'index'])->name('web.access-requests.index');
    Route::get('/access-requests/create', [\App\Http\Controllers\Web\AccessRequestController::class, 'create'])->name('web.access-requests.create');
    Route::post('/access-requests', [\App\Http\Controllers\Web\AccessRequestController::class, 'store'])->name('web.access-requests.store');
    Route::put('/access-requests/{accessRequest}', [\App\Http\Controllers\Web\AccessRequestController::class, 'update'])->name('web.access-requests.update');

    // Secure Document User View (for Dependencies)
    Route::get('/documents/{document}/secure-view', function (\App\Models\Document $document) {
        return App::call([\App\Http\Controllers\Web\AccessRequestController::class, 'secureView'], ['document' => $document]);
    })->name('documents.secure-view');

    Route::get('/documents/{document}/stream', function (\App\Models\Document $document) {
        return App::call([\App\Http\Controllers\Web\AccessRequestController::class, 'streamFile'], ['document' => $document]);
    })->name('documents.stream');

    // Document Actions
    Route::get('/documents/{document}/download', [\App\Http\Controllers\Web\DocumentController::class, 'download'])->name('documents.download');
    Route::delete('/documents/{document}', [\App\Http\Controllers\Web\DocumentController::class, 'destroy'])->name('documents.destroy');
    Route::get('/documents/{document}/show', [\App\Http\Controllers\Web\DocumentController::class, 'show'])->name('documents.show');

    // Admin Routes
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::resource('categories', \App\Http\Controllers\Web\CategoryController::class);
        Route::resource('dependencies', \App\Http\Controllers\Admin\DependencyController::class);
    });
});
