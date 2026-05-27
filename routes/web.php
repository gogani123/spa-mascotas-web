<?php

use App\Http\Controllers\CitaController;
use App\Http\Controllers\Admin\ServicioController;
use App\Http\Controllers\Admin\BloqueoController;
use App\Http\Controllers\Admin\HorarioAtencionController;
use App\Http\Controllers\MascotaController;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\UserController; 
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TiendaController;
use App\Http\Controllers\GroomerController;


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
    Route::get('/citas/{cita}/cobrar', [App\Http\Controllers\CitaController::class, 'cobrar'])->name('citas.cobrar');
    Route::post('/citas/{cita}/aprobar', [App\Http\Controllers\CitaController::class, 'aprobar'])->name('citas.aprobar');
    Route::post('/citas/{cita}/pagar', [App\Http\Controllers\CitaController::class, 'pagar'])->name('citas.pagar');
    Route::get('/calendario-interactivo', [App\Http\Controllers\CitaController::class, 'calendario'])->name('citas.calendario');
    Route::get('/api/citas-eventos', [App\Http\Controllers\CitaController::class, 'apiEventos']);
    Route::post('/api/citas-mover/{id}', [App\Http\Controllers\CitaController::class, 'apiMover']);
    Route::get('/citas/{cita}/atender', [App\Http\Controllers\CitaController::class, 'atender'])->name('citas.atender');
    Route::post('/citas/{cita}/completar', [App\Http\Controllers\CitaController::class, 'completar'])->name('citas.completar');
    Route::post('/citas/{id}/completar', [App\Http\Controllers\CitaController::class, 'completar'])->name('citas.completar');
    Route::middleware(['auth'])->group(function () {
    Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.index');
    Route::post('/tienda/agregar/{id}', [TiendaController::class, 'agregar'])->name('tienda.agregar');
    Route::post('/tienda/vaciar', [TiendaController::class, 'vaciar'])->name('tienda.vaciar');
    Route::post('/tienda/cupon', [TiendaController::class, 'aplicarCupon'])->name('tienda.cupon');  
    });
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


// RUTAS EXCLUSIVAS: GROOMER (3)
Route::middleware(['auth', 'verified', CheckRole::class . ':3'])->prefix('groomer')->name('groomer.')->group(function () {
    
    // Agenda personal del groomer (día o semana)
    Route::get('agenda', [GroomerController::class, 'agendaPersonal'])->name('agenda');
    
    // Ficha técnica de atención
    Route::get('ficha/{cita}', [GroomerController::class, 'fichaPanel'])->name('ficha.panel');
    Route::post('ficha/{cita}/guardar', [GroomerController::class, 'guardarFicha'])->name('ficha.guardar');
    
    // Checklist de tareas
    Route::post('ficha/{cita}/checklist', [GroomerController::class, 'guardarChecklist'])->name('checklist.guardar');
    
    // Galería de fotos (antes y después)
    Route::post('ficha/{cita}/fotos', [GroomerController::class, 'cargarFotos'])->name('fotos.cargar');
    
    // Panel de insumos
    Route::get('insumos/{cita}', [GroomerController::class, 'panelInsumos'])->name('insumos.panel');
    Route::post('insumos/{cita}/usar', [GroomerController::class, 'registrarUsoInsumos'])->name('insumos.usar');
    
    // Cierre del servicio
    Route::post('ficha/{cita}/cerrar', [GroomerController::class, 'cerrarServicio'])->name('servicio.cerrar');
});

require __DIR__.'/auth.php';