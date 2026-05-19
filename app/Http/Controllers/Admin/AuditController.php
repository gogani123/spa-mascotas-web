<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class AuditController extends Controller
{
    public function index()
    {
        $logPath = storage_path('logs/auditoria.log');
        $logs = [];

        if (File::exists($logPath)) {
            $fileLines = array_reverse(file($logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES));

            foreach ($fileLines as $line) {
                // 1. Extraer la Fecha
                preg_match('/^\[(.*?)\]/', $line, $dateMatch);
                $fecha = $dateMatch[1] ?? 'Desconocida';

                // 2. Quitar la fecha y el "local.INFO:" para dejar solo el mensaje y los datos
                $resto = preg_replace('/^\[.*?\] .*?\.INFO: /', '', $line);

                // 3. Separar el texto de la acción del JSON de detalles
                $jsonStart = strpos($resto, '{');
                
                if ($jsonStart !== false) {
                    $accion = trim(substr($resto, 0, $jsonStart));
                    $jsonString = substr($resto, $jsonStart);
                    $detalles = json_decode($jsonString, true) ?? [];
                } else {
                    $accion = trim($resto);
                    $detalles = [];
                }

                $logs[] = [
                    'fecha' => $fecha,
                    'accion' => $accion,
                    'detalles' => $detalles
                ];
            }
        }

        return view('admin.auditoria.index', compact('logs'));
    }
}