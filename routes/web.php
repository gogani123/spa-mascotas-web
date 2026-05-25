<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\BloqueoController;
use App\Http\Controllers\Admin\HorarioAtencionController;
use App\Http\Controllers\MascotaController;
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
    Route::resource('mascotas', MascotaController::class);
    Route::resource('citas', CitaController::class);
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


// RUTAS COMPARTIDAS: ADMINISTRADOR (1) Y RECEPCIÓN (2)

Route::middleware(['auth', 'verified', '2fa', CheckRole::class . ':1,2'])->prefix('admin')->name('admin.')->group(function () {
    
    // Configuración del Horario Laboral Global
    Route::get('horarios', [HorarioAtencionController::class, 'index'])->name('horarios.index');
    Route::put('horarios', [HorarioAtencionController::class, 'update'])->name('horarios.update');

    // Gestión de Bloqueos (Excepciones de agenda)
    Route::resource('bloqueos', BloqueoController::class)->except(['show', 'edit', 'update']);
});


// RUTAS EXCLUSIVAS: SOLO ADMINISTRADOR (1)
Route::middleware(['auth', 'verified', '2fa', CheckRole::class . ':1'])->prefix('admin')->name('admin.')->group(function () {
    
    // Gestión de Personal
    Route::resource('users', UserController::class);

    // Pantalla de Auditoría
    Route::get('auditoria', [AuditController::class, 'index'])->name('auditoria.index');

    // Catálogo de Servicios
    Route::resource('servicios', ServicioController::class)->except(['show', 'edit', 'update']);
});
require __DIR__.'/auth.php';