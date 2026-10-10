<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // comando para limpiar registro de tabla user
        $schedule->command('registros:limpiar')
             ->everyMinute();


        //recordatorio general para todos los clientes
        $schedule->command('notificaciones:recordatorios-pago pago')
            //->monthlyOn(3, '10:00')
            ->everyMinute()
            ->timezone('America/Mexico_City')
            ->withoutOverlapping();

        //recordatorio para clientes con deuda
        $schedule->command('notificaciones:recordatorios-pago deuda')
            //->monthlyOn(10, '10:00')
             ->everyMinute()
            ->timezone('America/Mexico_City')
            ->withoutOverlapping();        
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');

        \App\Console\Commands\LimpiarRegistrosTemporales::class;

    }
}
