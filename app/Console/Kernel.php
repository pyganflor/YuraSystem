<?php

namespace yura\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use yura\Console\Commands\CicloPrimeraFlor;
use yura\Console\Commands\DeleteRecepciones;
use yura\Console\Commands\EmpaquetarPedidosAnulados;
use yura\Console\Commands\FechaFinalCiclo;
use yura\Console\Commands\NotificacionesSistema;
use yura\Console\Commands\UpdateHistoricoVentas;
use yura\Console\Commands\UpdateIndicador;
use yura\Console\Commands\UpdateProyeccionSemanal;
use yura\Console\Commands\ResumenVentaDiariaMesAnterior;
use yura\Console\Commands\VentaSemanalReal;
use yura\Console\Commands\PrecioVariedadCliente;
use yura\Console\Commands\ResumenSemanaCosecha;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        UpdateHistoricoVentas::class,
        FechaFinalCiclo::class,
        DeleteRecepciones::class,
        NotificacionesSistema::class,
        CicloPrimeraFlor::class,
        UpdateProyeccionSemanal::class,
        EmpaquetarPedidosAnulados::class,
        VentaSemanalReal::class,
        PrecioVariedadCliente::class,
        ResumenSemanaCosecha::class,
        UpdateIndicador::class,
        ResumenVentaDiariaMesAnterior::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        $schedule->command('historico_ventas:update')->daily()->runInBackground();    // UpdateHistoricoVentas::class
        $schedule->command('ciclo:fecha_fin')->everyTenMinutes()->runInBackground();    // FechaFinalCiclo::class
        $schedule->command('recepciones:delete')->everyThirtyMinutes()->runInBackground(); // DeleteRecepciones::class
        $schedule->command('notificaciones:sistema')->everyTenMinutes()->runInBackground(); // NotificacionesSistema::class
        $schedule->command('ciclo:primera_flor')->hourly()->runInBackground(); // CicloPrimeraFlor::class
        $schedule->command('proyeccion:update_semanal')->hourly()->runInBackground(); // UpdateProyeccionSemanal::class
        $schedule->command('pedido:empaquetar_anulados')->daily()->runInBackground(); // EmpaquetarPedidosAnulados::class
        $schedule->command('precio:variedad_x_cliente')->sundays()->between('7:00', '22:00')->runInBackground(); // PrecioVariedadCliente::class
        $schedule->command('resumen:semana_cosecha')->hourly()->runInBackground(); // ResumenSemanaCosecha::class
        $schedule->command('indicador:update')->hourly()->runInBackground(); // UpdateIndicador::class
        $schedule->command('resumen_venta_diaria:mes_anterior')->everyMinute()->runInBackground(); // ResumenVentaDiariaMesAnterior::class
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
