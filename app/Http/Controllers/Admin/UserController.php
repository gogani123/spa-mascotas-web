<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = DB::table('roles')->whereIn('id', [2, 3])->get(); 
        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request) {
        // 1. Validamos todos los campos, incluyendo CI y Teléfono
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'rol_id' => 'required|exists:roles,id',
            'ci' => 'required|string|max:8',        
            'telefono' => 'required|string|max:8',  
            'capacidad_diaria' => 'required|integer|min:1|max:50',
        ]);

        // 2. Guardamos al usuario en la base de datos
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'rol_id' => $request->rol_id,
            'ci' => $request->ci,                    
            'telefono' => $request->telefono,        
            'especialidad' => $request->especialidad,
            'turno' => $request->turno,
            'estado' => true,
            'capacidad_diaria' => $request->capacidad_diaria,
            'email_verified_at' => now(),
        ]);
        
        // 3. Registramos la Auditoría
        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/auditoria.log'),
        ])->info('Creación de nuevo empleado', [
            'Admin (ID)' => auth()->user()->id,
            'Nuevo Empleado Email' => $request->email,
            'Desde donde (IP)' => $request->ip(),
            'Navegador' => $request->userAgent(),
        ]);

        // 4. Enviamos el correo de credenciales
        Mail::send('emails.bienvenida', ['user' => $user, 'password' => $request->password], function($message) use ($user) {
            $message->to($user->email)->subject('Tus Accesos - Spa Mascotas');
        });

        return redirect()->route('admin.users.index');
    }
}