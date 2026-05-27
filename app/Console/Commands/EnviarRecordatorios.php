<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cita;
use App\Services\NotificacionService;
use Carbon\Carbon;

class EnviarRecordatorios extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'recordatorios:enviar';

    /**
     * The description of the console command.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios automáticos 24h y 2h antes de las citas';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $ahora = Carbon::now();
        
        // RECORDATORIOS 24 HORAS ANTES
        $mañana = $ahora->copy()->addDay();
        $horaMinima = $mañana->copy()->startOfDay()->format('H:i');
        $horaMaxima = $mañana->copy()->startOfDay()->format('H:i');
        
        $citasMañana = Cita::where('fecha', $mañana->format('Y-m-d'))
            ->where('estado', 'Confirmada')
            ->where('hora_inicio', $horaMinima)
            ->get();

        foreach ($citasMañana as $cita) {
            // Verificar que no ya se haya enviado el recordatorio
            $notificacionExiste = $cita->notificaciones()
                ->where('tipo', 'recordatorio_24h')
                ->where('created_at', '>=', $ahora->copy()->subHours(1))
                ->exists();

            if (!$notificacionExiste) {
                NotificacionService::notificarRecordatorio24h($cita);
                $this->info("✅ Recordatorio 24h enviado a: " . $cita->cliente->email . " para " . $cita->mascota->nombre);
            }
        }

        // RECORDATORIOS 2 HORAS ANTES
        $hace2Horas = $ahora->copy()->subHours(2);
        $en2Horas = $ahora->copy()->addHours(2);
        
        $citasProximas = Cita::where('estado', 'Confirmada')
            ->where('fecha', '>=', $ahora->format('Y-m-d'))
            ->where('fecha', '<=', $en2Horas->format('Y-m-d'))
            ->whereTime('hora_inicio', '>=', $hace2Horas->format('H:i'))
            ->whereTime('hora_inicio', '<=', $en2Horas->format('H:i'))
            ->get();

        foreach ($citasProximas as $cita) {
            // Verificar que no ya se haya enviado el recordatorio
            $notificacionExiste = $cita->notificaciones()
                ->where('tipo', 'recordatorio_2h')
                ->where('created_at', '>=', $ahora->copy()->subHours(1))
                ->exists();

            if (!$notificacionExiste) {
                NotificacionService::notificarRecordatorio2h($cita);
                $this->info("✅ Recordatorio 2h enviado a: " . $cita->cliente->email . " para " . $cita->mascota->nombre);
            }
        }

        return Command::SUCCESS;
    }
}
