<?php

namespace yura\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use yura\Console\Commands\DeleteRecepciones;
use yura\Console\Commands\FechaFinalCiclo;
use yura\Console\Commands\UpdateHistoricoVentas;

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
        DeleteRecepciones::class
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

        $schedule->command('ciclo:fecha_fin')->everyMinute()->runInBackground();    // FechaFinalCiclo::class
        $schedule->command('recepciones:delete')->everyMinute()->runInBackground(); // DeleteRecepciones::class
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
