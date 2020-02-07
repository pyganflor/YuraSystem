<?php

namespace yura\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use yura\Console\Commands\CicloPrimeraFlor;
use yura\Console\Commands\DeleteRecepciones;
use yura\Console\Commands\EmpaquetarPedidosAnulados;
use yura\Console\Commands\FechaFinalCiclo;
use yura\Console\Commands\IndicadorSemanal;
use yura\Console\Commands\NotificacionesSistema;
use yura\Console\Commands\ResumenAreaSemanal;
use yura\Console\Commands\ResumenCostosSemanal;
use yura\Console\Commands\ResumenSaldoProyeccionVentaSemanal;
use yura\Console\Commands\ResumenSemanalTotal;
use yura\Console\Commands\UpdateHistoricoVentas;
use yura\Console\Commands\UpdateIndicador;
use yura\Console\Commands\UpdateOtrosGastos;
use yura\Console\Commands\UpdateProyeccionSemanal;
use yura\Console\Commands\ResumenVentaDiariaMesAnterior;
use yura\Console\Commands\UpdateRegalias;
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
        //DeleteRecepciones::class,
        NotificacionesSistema::class,
        CicloPrimeraFlor::class,
        UpdateProyeccionSemanal::class,
        EmpaquetarPedidosAnulados::class,
        VentaSemanalReal::class,
        PrecioVariedadCliente::class,
        ResumenSemanaCosecha::class,
        UpdateIndicador::class,
        ResumenVentaDiariaMesAnterior::class,
        ResumenAreaSemanal::class,
        ResumenSemanalTotal::class,
        UpdateOtrosGastos::class,
        UpdateRegalias::class,
        ResumenCostosSemanal::class,
        ResumenSaldoProyeccionVentaSemanal::class,
        IndicadorSemanal::class,
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

        $schedule->command('ciclo:fecha_fin')->cron('0 * * * *')->runInBackground();    // FechaFinalCiclo::class
        $schedule->command('historico_ventas:update')->cron('0 0 * * *')->runInBackground();    // UpdateHistoricoVentas::class
        //$schedule->command('recepciones:delete')->hourly()->runInBackground(); // DeleteRecepciones::class
        $schedule->command('notificaciones:sistema')->cron('10 * * * *')->runInBackground(); // NotificacionesSistema::class
        $schedule->command('ciclo:primera_flor')->cron('5 * * * *')->runInBackground(); // CicloPrimeraFlor::class
        $schedule->command('proyeccion:update_semanal')->cron('30 * * * *')->runInBackground(); // UpdateProyeccionSemanal::class
        $schedule->command('pedido:empaquetar_anulados')->cron('10 0 * * *')->runInBackground(); // EmpaquetarPedidosAnulados::class
        $schedule->command('precio:variedad_x_cliente')->sundays()->between('7:00', '22:00')->runInBackground(); // PrecioVariedadCliente::class
        $schedule->command('resumen:semana_cosecha')->cron('20 * * * *')->runInBackground(); // ResumenSemanaCosecha::class
        $schedule->command('indicador:update')->cron('40 * * * *')->runInBackground(); // UpdateIndicador::class
        $schedule->command('resumen_venta_diaria:mes_anterior')->cron('0 6 * * *')->runInBackground(); // ResumenVentaDiariaMesAnterior::class
        $schedule->command('area:update_semanal')->cron('20 * * * *')->runInBackground(); // ResumenAreaSemanal::class
        $schedule->command('resumen_total:semanal')->cron('15 * * * *')->runInBackground(); // ResumenSemanalTotal::class
        $schedule->command('otros_gastos:update')->cron('20 0 * * *')->runInBackground(); // UpdateOtrosGastos::class
        $schedule->command('regalias:update')->cron('30 0 * * *')->runInBackground(); // UpdateRegalias::class
        $schedule->command('costos:update_semanal')->cron('25 * * * *')->runInBackground(); // ResumenCostosSemanal::class
        $schedule->command('indicador_semana:update')->cron('40 0 * * *')->runInBackground(); // IndicadorSemanal::class
        $schedule->command('resumen_saldo_proyeccion:venta_semanal')->cron('*/15 * * * *')->runInBackground(); // ResumenSaldoProyeccionVentaSemanal::class
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