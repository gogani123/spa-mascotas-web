<?php

use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\UserController; // <-- Agregado para que no falle la tabla de personal
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', '2fa'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Rutas del 2FA (Seguridad de Administrador)
Route::middleware('auth')->group(function () {
    Route::get('2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::get('2fa/verify', [TwoFactorController::class, 'index'])->name('2fa.index');
    Route::post('2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});

// Rutas de inicio de sesión con Google (OAuth 2.0)
Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// ========================================================================
// Rutas Exclusivas para el Administrador (Unificadas y Blindadas)
// ========================================================================
Route::middleware(['auth', 'verified', '2fa', CheckRole::class . ':1'])->prefix('admin')->name('admin.')->group(function () {
    
    // Gestión de Personal
    Route::resource('users', UserController::class);
    
    // Pantalla de Auditoría
    Route::get('auditoria', [AuditController::class, 'index'])->name('auditoria.index');

});

require __DIR__.'/auth.php';