<?php
use App\Http\Controllers\SalidaInsumoController;
use Illuminate\Http\Request;
use Carbon\Carbon;
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
use App\Http\Controllers\InventarioController;

// ====================================================================
// RUTAS COMUNES / GLOBALES AUTENTICADAS
// ====================================================================
Route::middleware(['auth'])->group(function () {
    
    // Rutas de flujo operativo de Insumos (Groomer / Recepción / Admin)
    Route::post('/salidas-insumos/entregar', [SalidaInsumoController::class, 'entregar'])->name('salidas.entregar');
    Route::post('/salidas-insumos/{salida}/actualizar-uso', [SalidaInsumoController::class, 'actualizarUso'])->name('salidas.actualizarUso');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Módulo de Mascotas
    Route::resource('mascotas', MascotaController::class);
    Route::get('/mascotas/crear', [MascotaController::class, 'create'])->name('mascotas.create');
    Route::post('/mascotas', [MascotaController::class, 'store'])->name('mascotas.store');
    
    // Módulo de Citas y Cobros
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
    Route::get('/citas/crear', [CitaController::class, 'create'])->name('citas.create');
    Route::post('/citas', [CitaController::class, 'store'])->name('citas.store');
    Route::get('/citas/{cita}/recibo', [CitaController::class, 'generarRecibo'])->name('citas.recibo');
    Route::get('/admin/cierre-caja', [CitaController::class, 'cierreCaja'])->name('admin.cierre_caja')->middleware(CheckRole::class . ':1,2');
    Route::get('/citas/{cita}/cobrar', [CitaController::class, 'cobrar'])->name('citas.cobrar');
    Route::post('/citas/{cita}/aprobar', [CitaController::class, 'aprobar'])->name('citas.aprobar');
    Route::post('/citas/{cita}/pagar', [CitaController::class, 'pagar'])->name('citas.pagar');
    Route::get('/calendario-interactivo', [CitaController::class, 'calendario'])->name('citas.calendario');
    Route::get('/api/citas-eventos', [CitaController::class, 'apiEventos']);
    Route::post('/api/citas-mover/{id}', [CitaController::class, 'apiMover']);
    Route::get('/api/horarios-disponibles', [CitaController::class, 'obtenerHorariosDisponibles'])->name('api.horarios_disponibles');
    Route::get('/citas/{cita}/atender', [CitaController::class, 'atender'])->name('citas.atender');
    Route::post('/citas/{cita}/completar', [CitaController::class, 'completar'])->name('citas.completar');
    Route::post('/citas/{cita}/cancelar', [CitaController::class, 'cancelar'])->name('citas.cancelar');
    
    // Módulo de la Tienda (Protegido contra Groomers)
    Route::middleware([CheckRole::class . ':1,2,4'])->group(function () {
        Route::get('/tienda', [TiendaController::class, 'index'])->name('tienda.index');
        Route::post('/tienda/agregar/{id}', [TiendaController::class, 'agregar'])->name('tienda.agregar');
        Route::post('/tienda/vaciar', [TiendaController::class, 'vaciar'])->name('tienda.vaciar');
        Route::post('/tienda/cupon', [TiendaController::class, 'aplicarCupon'])->name('tienda.cupon');  
        Route::post('/tienda/comprar', [TiendaController::class, 'comprarPresencial'])->name('tienda.comprar');
    });

    // Validación OTP por Email
    Route::post('/verify-email/code', function (Request $request) {
        $request->validate(['codigo' => 'required|string|size:6']);
        $usuario = auth()->user();
        $codigoIngresado = strtoupper(trim($request->codigo));

        if ($usuario->verification_code && $codigoIngresado === $usuario->verification_code) {
            $usuario->forceFill([
                'email_verified_at' => Carbon::now('America/La_Paz'),
                'verification_code' => null,
            ])->save();
            return redirect()->route('dashboard')->with('success', '¡Tu cuenta ha sido verificada con éxito!');
        }
        return back()->withErrors(['codigo' => 'El código de verificación ingresado es incorrecto.']);
    })->middleware(['throttle:6,1'])->name('verification.verify_code');
});

// ====================================================================
// SEGURIDAD Y OAUTH
// ====================================================================
Route::get('/', function () { return view('welcome'); });
Route::get('/dashboard', function () { return view('dashboard'); })->middleware(['auth', 'verified', '2fa'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('2fa/setup', [TwoFactorController::class, 'setup'])->name('2fa.setup');
    Route::get('2fa/verify', [TwoFactorController::class, 'index'])->name('2fa.index');
    Route::post('2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
});

Route::get('/auth/google/redirect', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// ====================================================================
// RUTAS COMPARTIDAS: ADMINISTRADOR (1) Y RECEPCIÓN (2)
// ====================================================================
Route::middleware(['auth', 'verified', '2fa', CheckRole::class . ':1,2'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('horarios', [HorarioAtencionController::class, 'index'])->name('horarios.index');
    Route::put('horarios', [HorarioAtencionController::class, 'update'])->name('horarios.update');
    Route::resource('bloqueos', BloqueoController::class)->except(['show', 'edit', 'update']);
});

// ====================================================================
// RUTAS EXCLUSIVAS: SOLO ADMINISTRADOR (1)
// ====================================================================
Route::middleware(['auth', 'verified', '2fa', CheckRole::class . ':1'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('auditoria', [AuditController::class, 'index'])->name('auditoria.index');
    Route::resource('servicios', ServicioController::class)->except(['show', 'edit', 'update']);
    
    // Subgrupo de Inventario - Genera nombres: admin.inventario.*
    Route::prefix('inventario')->name('inventario.')->group(function () {
        Route::get('/', [InventarioController::class, 'index'])->name('index');
        Route::get('crear', [InventarioController::class, 'create'])->name('create');
        Route::post('/', [InventarioController::class, 'store'])->name('store');
        Route::get('{insumo}/editar', [InventarioController::class, 'edit'])->name('edit');
        Route::put('{insumo}', [InventarioController::class, 'update'])->name('update');
        Route::delete('{insumo}', [InventarioController::class, 'destroy'])->name('destroy');
        Route::get('alertas', [InventarioController::class, 'alertas'])->name('alertas'); // 👈 RUTA DE CONTROL OPERATIVO
        Route::post('{insumo}/entrada', [InventarioController::class, 'registrarEntrada'])->name('entrada');
    });
});

// ====================================================================
// RUTAS EXCLUSIVAS DEL GROOMER (3)
// ====================================================================
Route::middleware(['auth', 'verified', CheckRole::class . ':3'])->prefix('groomer')->name('groomer.')->group(function () {
    Route::get('agenda', [GroomerController::class, 'agendaPersonal'])->name('agenda');
    Route::get('ficha/{cita}', [GroomerController::class, 'fichaPanel'])->name('ficha.panel');
    Route::post('ficha/{cita}/guardar', [GroomerController::class, 'guardarFicha'])->name('ficha.guardar');
    Route::post('ficha/{cita}/checklist', [GroomerController::class, 'guardarChecklist'])->name('checklist.guardar');
    Route::post('ficha/{cita}/fotos', [GroomerController::class, 'cargarFotos'])->name('fotos.cargar');
    Route::post('ficha/{cita}/cerrar', [GroomerController::class, 'cerrarServicio'])->name('servicio.cerrar');
    Route::get('insumos/{cita}', [GroomerController::class, 'panelInsumos'])->name('insumos.panel');
    Route::post('insumos/{cita}/usar', [GroomerController::class, 'registrarUsoInsumos'])->name('insumos.usar');
});

// Flujo de Registro Asíncrono desde el Almacén General
Route::post('/groomer/insumos/{citaId}/registrar', [CitaController::class, 'registrarSalida'])->name('groomer.insumos.registrar')->middleware('auth');

require __DIR__.'/auth.php';